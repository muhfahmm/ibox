<?php
session_start();
require_once '../../db.php';

// Cek login
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

// Konfigurasi upload
$upload_dir = '../../../uploads/';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

$allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
$max_size = 2 * 1024 * 1024; // 2MB

// Fungsi untuk generate nama file unik
function generateFileName($original_name) {
    $extension = pathinfo($original_name, PATHINFO_EXTENSION);
    return uniqid() . '_' . time() . '.' . $extension;
}

$response = ['success' => false, 'message' => ''];

try {
    // Validasi data dasar
    if (empty($_POST['nama_produk'])) {
        throw new Exception("Nama produk harus diisi");
    }

    // Nonaktifkan foreign key checks sementara
    mysqli_query($db, "SET FOREIGN_KEY_CHECKS=0");

    $nama_produk = escape($_POST['nama_produk']);
    $deskripsi_produk = escape($_POST['deskripsi_produk'] ?? '');
    
    $total_products_created = 0;
    $variant_errors = [];

    // Proses setiap varian
    if (isset($_POST['variant']) && is_array($_POST['variant'])) {
        foreach ($_POST['variant'] as $variant_id => $variant_data) {
            // Validasi varian
            if (empty($variant_data['warna']) || !is_array($variant_data['warna']) || 
                empty($variant_data['penyimpanan']) || !is_array($variant_data['penyimpanan']) ||
                empty($variant_data['konektivitas']) || !is_array($variant_data['konektivitas'])) {
                $variant_errors[] = "Varian #$variant_id: Data tidak lengkap";
                continue;
            }

            // Filter nilai yang tidak kosong
            $warna_array = array_filter(array_map('trim', $variant_data['warna']));
            $penyimpanan_array = array_filter(array_map('trim', $variant_data['penyimpanan']));
            $konektivitas_array = array_filter(array_map('trim', $variant_data['konektivitas']));

            if (empty($warna_array) || empty($penyimpanan_array) || empty($konektivitas_array)) {
                $variant_errors[] = "Varian #$variant_id: Warna, penyimpanan, atau konektivitas tidak boleh kosong";
                continue;
            }

            // Proses upload thumbnail untuk varian ini
            if (!isset($_FILES['variant'][$variant_id]['thumbnail']) || 
                $_FILES['variant'][$variant_id]['thumbnail']['error'] != 0) {
                $variant_errors[] = "Varian #$variant_id: Thumbnail harus diupload";
                continue;
            }

            $thumbnail = $_FILES['variant'][$variant_id]['thumbnail'];
            if (!in_array($thumbnail['type'], $allowed_types)) {
                $variant_errors[] = "Varian #$variant_id: Format thumbnail tidak didukung";
                continue;
            }
            if ($thumbnail['size'] > $max_size) {
                $variant_errors[] = "Varian #$variant_id: Ukuran thumbnail terlalu besar (max 2MB)";
                continue;
            }

            $thumbnail_name = generateFileName($thumbnail['name']);
            $thumbnail_path = $upload_dir . $thumbnail_name;

            if (!move_uploaded_file($thumbnail['tmp_name'], $thumbnail_path)) {
                $variant_errors[] = "Varian #$variant_id: Gagal upload thumbnail";
                continue;
            }

            // Proses upload foto produk untuk varian ini
            $uploaded_images = [];
            if (isset($_FILES['variant'][$variant_id]['product_images']) && 
                is_array($_FILES['variant'][$variant_id]['product_images']['name'])) {
                
                $product_images = $_FILES['variant'][$variant_id]['product_images'];
                
                for ($i = 0; $i < count($product_images['name']); $i++) {
                    if ($product_images['error'][$i] == 0) {
                        if (in_array($product_images['type'][$i], $allowed_types) && 
                            $product_images['size'][$i] <= $max_size) {
                            
                            $image_name = generateFileName($product_images['name'][$i]);
                            $image_path = $upload_dir . $image_name;
                            
                            if (move_uploaded_file($product_images['tmp_name'][$i], $image_path)) {
                                $uploaded_images[] = $image_name;
                            }
                        }
                    }
                }
            }

            // Data varian
            $harga = escape($variant_data['harga'] ?? 0);
            $harga_diskon = !empty($variant_data['harga_diskon']) ? escape($variant_data['harga_diskon']) : NULL;
            $status_stok = escape($variant_data['status_stok'] ?? 'tersedia');

            // Buat semua kombinasi untuk varian ini
            foreach ($warna_array as $warna) {
                foreach ($penyimpanan_array as $penyimpanan) {
                    foreach ($konektivitas_array as $konektivitas) {
                        // Insert ke tabel produk iPad
                        $query = "INSERT INTO admin_produk_ipad (
                            nama_produk, harga, warna, penyimpanan, konektivitas, 
                            deskripsi_produk, harga_diskon, jumlah_stok, status_stok
                        ) VALUES (
                            '$nama_produk', '$harga', '" . escape($warna) . "', '" . escape($penyimpanan) . "', '" . escape($konektivitas) . "',
                            '$deskripsi_produk', " . ($harga_diskon ? "'$harga_diskon'" : "NULL") . ", 
                            '0', '$status_stok'
                        )";

                        if (!mysqli_query($db, $query)) {
                            $variant_errors[] = "Varian #$variant_id: Gagal menyimpan data produk - " . mysqli_error($db);
                            continue;
                        }

                        $product_id = mysqli_insert_id($db);
                        $total_products_created++;

                        // Insert thumbnail ke tabel gambar
                        $query_thumb = "INSERT INTO admin_gambar_produk (produk_id, foto_thumbnail, tipe_produk) 
                                        VALUES ('$product_id', '$thumbnail_name', 'ipad')";
                        
                        if (!mysqli_query($db, $query_thumb)) {
                            // Hapus produk jika gagal insert gambar
                            mysqli_query($db, "DELETE FROM admin_produk_ipad WHERE id = '$product_id'");
                            $total_products_created--;
                            $variant_errors[] = "Varian #$variant_id: Gagal menyimpan thumbnail";
                            continue;
                        }

                        // Insert foto produk
                        foreach ($uploaded_images as $image_name) {
                            $query_img = "INSERT INTO admin_gambar_produk (produk_id, foto_produk, tipe_produk) 
                                         VALUES ('$product_id', '$image_name', 'ipad')";
                            mysqli_query($db, $query_img);
                        }
                    }
                }
            }
        }
    } else {
        throw new Exception("Tidak ada varian produk yang dimasukkan");
    }

    // Aktifkan kembali foreign key checks
    mysqli_query($db, "SET FOREIGN_KEY_CHECKS=1");

    if ($total_products_created > 0) {
        $response['success'] = true;
        $response['message'] = "Berhasil menambahkan $total_products_created produk";
        $response['total_created'] = $total_products_created;
        
        if (!empty($variant_errors)) {
            $response['warnings'] = $variant_errors;
        }
    } else {
        $response['message'] = "Gagal menambahkan produk. " . implode("; ", $variant_errors);
    }

} catch (Exception $e) {
    // Pastikan foreign key checks diaktifkan kembali
    mysqli_query($db, "SET FOREIGN_KEY_CHECKS=1");
    $response['message'] = $e->getMessage();
}

header('Content-Type: application/json');
echo json_encode($response);
?>