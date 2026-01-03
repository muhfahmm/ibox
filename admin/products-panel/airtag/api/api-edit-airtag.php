<?php
session_start();
require_once '../../db.php';

// Cek login
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

$upload_dir = '../../../uploads/';
if (!file_exists($upload_dir)) mkdir($upload_dir, 0777, true);

$allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
$max_size = 2 * 1024 * 1024;

function generateFileName($original_name) {
    $extension = pathinfo($original_name, PATHINFO_EXTENSION);
    return uniqid() . '_' . time() . '_' . rand(1000, 9999) . '.' . $extension;
}

$response = ['success' => false, 'message' => ''];

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') throw new Exception('Invalid request method');

    $product_id = $_POST['product_id'] ?? null;
    if (!$product_id) throw new Exception('Product ID is required');

    mysqli_begin_transaction($db);

    // 1. Update Produk Utama
    $nama = mysqli_real_escape_string($db, $_POST['nama_produk']);
    $desc = mysqli_real_escape_string($db, $_POST['deskripsi_produk'] ?? '');
    $kategori = mysqli_real_escape_string($db, $_POST['kategori'] ?? '');
    
    if (empty($kategori)) {
        throw new Exception('Kategori harus dipilih');
    }
    
    mysqli_query($db, "UPDATE admin_produk_airtag SET nama_produk='$nama', deskripsi_produk='$desc', kategori='$kategori' WHERE id='$product_id'");

    // 2. Update Gambar
    // First, delete old images records (files remain)
    mysqli_query($db, "DELETE FROM admin_produk_airtag_gambar WHERE produk_id='$product_id'");
    
    $warna_data = $_POST['warna'] ?? [];
    
    foreach ($warna_data as $index => $w_data) {
        $warna_nama = mysqli_real_escape_string($db, $w_data['nama'] ?? '');
        if (empty($warna_nama)) continue;

        // Handle Thumbnail
        $thumbnail_name = $w_data['existing_thumbnail'] ?? null;
        
        // Check if new thumbnail uploaded
        if (isset($_FILES['warna']['name'][$index]['thumbnail']) && $_FILES['warna']['error'][$index]['thumbnail'] == 0) {
             $file = [
                'name' => $_FILES['warna']['name'][$index]['thumbnail'],
                'type' => $_FILES['warna']['type'][$index]['thumbnail'],
                'tmp_name' => $_FILES['warna']['tmp_name'][$index]['thumbnail'],
                'size' => $_FILES['warna']['size'][$index]['thumbnail']
            ];
            if (in_array($file['type'], $allowed_types) && $file['size'] <= $max_size) {
                $new_name = generateFileName($file['name']);
                if (move_uploaded_file($file['tmp_name'], $upload_dir . $new_name)) {
                    $thumbnail_name = $new_name;
                }
            }
        }
        
        // Handle Gallery
        $product_images = [];
        if (isset($w_data['existing_gallery'])) {
            $existing = json_decode($w_data['existing_gallery'], true);
            if (is_array($existing)) $product_images = $existing;
        }

        // New Gallery uploads
        if (isset($_FILES['warna']['name'][$index]['product_images'])) {
            $count = count($_FILES['warna']['name'][$index]['product_images']);
            for ($i = 0; $i < $count; $i++) {
                 if ($_FILES['warna']['error'][$index]['product_images'][$i] == 0) {
                     $file = [
                        'name' => $_FILES['warna']['name'][$index]['product_images'][$i],
                        'type' => $_FILES['warna']['type'][$index]['product_images'][$i],
                        'tmp_name' => $_FILES['warna']['tmp_name'][$index]['product_images'][$i],
                        'size' => $_FILES['warna']['size'][$index]['product_images'][$i]
                    ];
                    if (in_array($file['type'], $allowed_types) && $file['size'] <= $max_size) {
                        $new_name = generateFileName($file['name']);
                        if (move_uploaded_file($file['tmp_name'], $upload_dir . $new_name)) {
                            $product_images[] = $new_name;
                        }
                    }
                 }
            }
        }

        $json_imgs = json_encode(array_values($product_images));
        $q_img = "INSERT INTO admin_produk_airtag_gambar (produk_id, warna, foto_thumbnail, foto_produk) VALUES ('$product_id', '$warna_nama', '$thumbnail_name', '$json_imgs')";
        if (!mysqli_query($db, $q_img)) throw new Exception("Gagal simpan gambar: " . mysqli_error($db));
    }

    // 3. Update Kombinasi
    mysqli_query($db, "DELETE FROM admin_produk_airtag_kombinasi WHERE produk_id='$product_id'");
    
    $combinations = $_POST['combinations'] ?? [];
    
    foreach ($combinations as $combo) {
        $warna = mysqli_real_escape_string($db, $combo['warna']);
        $pack = mysqli_real_escape_string($db, $combo['pack']);
        $aksesoris = !empty($combo['aksesoris']) && $combo['aksesoris'] !== '-' ? mysqli_real_escape_string($db, $combo['aksesoris']) : NULL;
        $harga = mysqli_real_escape_string($db, $combo['harga']);
        $diskon_persen = !empty($combo['diskon_persen']) ? floatval($combo['diskon_persen']) : 0;
        $jumlah_stok = mysqli_real_escape_string($db, $combo['jumlah_stok']);
        $status_stok = ($jumlah_stok > 0) ? 'tersedia' : 'habis';

        $harga_diskon = "NULL";
        if ($diskon_persen > 0 && $harga > 0) {
            $calc_diskon = $harga - ($harga * ($diskon_persen / 100));
            $harga_diskon = "'" . round($calc_diskon) . "'";
        }

        $val_aksesoris = $aksesoris ? "'$aksesoris'" : "NULL";
        
        $q_combo = "INSERT INTO admin_produk_airtag_kombinasi 
                   (produk_id, warna, pack, aksesoris, harga, harga_diskon, jumlah_stok, status_stok) 
                   VALUES ('$product_id', '$warna', '$pack', $val_aksesoris, '$harga', $harga_diskon, '$jumlah_stok', '$status_stok')";
                   
        if (!mysqli_query($db, $q_combo)) throw new Exception("Gagal insert kombinasi: " . mysqli_error($db));
    }

    mysqli_commit($db);
    $response['success'] = true;
    $response['message'] = "Produk berhasil diperbarui.";

} catch (Exception $e) {
    mysqli_rollback($db);
    $response['message'] = $e->getMessage();
}

header('Content-Type: application/json');
echo json_encode($response);
?>
