<?php
session_start();
require_once '../../../db.php';

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
    return uniqid() . '_' . time() . '_' . rand(1000, 9999) . '.' . $extension;
}

$response = ['success' => false, 'message' => ''];

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    // Validasi data basics
    if (empty($_POST['nama_produk'])) throw new Exception("Nama produk harus diisi");
    if (empty($_POST['kategori'])) throw new Exception("Kategori harus dipilih");

    // Start Transaction
    mysqli_begin_transaction($db);
    
    // 1. Insert Data Produk Utama
    $nama_produk = mysqli_real_escape_string($db, $_POST['nama_produk']);
    $deskripsi_produk = mysqli_real_escape_string($db, $_POST['deskripsi_produk'] ?? '');
    $kategori = mysqli_real_escape_string($db, $_POST['kategori']);

    $query = "INSERT INTO admin_produk_airtag (nama_produk, deskripsi_produk, kategori) VALUES ('$nama_produk', '$deskripsi_produk', '$kategori')";
    
    if (!mysqli_query($db, $query)) {
        throw new Exception("Gagal menyimpan data produk: " . mysqli_error($db));
    }

    $product_id = mysqli_insert_id($db);

    // 2. Process Colors & Images
    $warna_data = $_POST['warna'] ?? [];
    
    foreach ($warna_data as $color_index => $color_info) {
        $warna_nama = mysqli_real_escape_string($db, $color_info['nama'] ?? '');
        if (empty($warna_nama)) continue; 
        
        // Handle Thumbnail
        $thumbnail_name = null;
        if (isset($_FILES['warna']['name'][$color_index]['thumbnail']) && 
            $_FILES['warna']['error'][$color_index]['thumbnail'] == 0) {
            
            $file = [
                'name' => $_FILES['warna']['name'][$color_index]['thumbnail'],
                'type' => $_FILES['warna']['type'][$color_index]['thumbnail'],
                'tmp_name' => $_FILES['warna']['tmp_name'][$color_index]['thumbnail'],
                'size' => $_FILES['warna']['size'][$color_index]['thumbnail']
            ];

            if (in_array($file['type'], $allowed_types) && $file['size'] <= $max_size) {
                $unique_name = generateFileName($file['name']);
                if (move_uploaded_file($file['tmp_name'], $upload_dir . $unique_name)) {
                    $thumbnail_name = $unique_name;
                }
            }
        }
        
        if (!$thumbnail_name) {
             throw new Exception("Thumbnail untuk warna $warna_nama gagal diupload atau tidak valid.");
        }

        // Handle Gallery
        $product_images = [];
        if (isset($_FILES['warna']['name'][$color_index]['product_images'])) {
            $count = count($_FILES['warna']['name'][$color_index]['product_images']);
            for ($i = 0; $i < $count; $i++) {
                if ($_FILES['warna']['error'][$color_index]['product_images'][$i] == 0) {
                     $file = [
                        'name' => $_FILES['warna']['name'][$color_index]['product_images'][$i],
                        'type' => $_FILES['warna']['type'][$color_index]['product_images'][$i],
                        'tmp_name' => $_FILES['warna']['tmp_name'][$color_index]['product_images'][$i],
                        'size' => $_FILES['warna']['size'][$color_index]['product_images'][$i]
                    ];
                    
                    if (in_array($file['type'], $allowed_types) && $file['size'] <= $max_size) {
                        $unique_name = generateFileName($file['name']);
                        if (move_uploaded_file($file['tmp_name'], $upload_dir . $unique_name)) {
                            $product_images[] = $unique_name;
                        }
                    }
                }
            }
        }
        
        $json_images = json_encode($product_images);

        // Insert Image Record
        $q_img = "INSERT INTO admin_produk_airtag_gambar (produk_id, warna, foto_thumbnail, foto_produk) 
                  VALUES ('$product_id', '$warna_nama', '$thumbnail_name', '$json_images')";
        if (!mysqli_query($db, $q_img)) {
            throw new Exception("Gagal menyimpan gambar: " . mysqli_error($db));
        }
    }

    // 3. Process Combinations
    $combinations = $_POST['combinations'] ?? [];
    $count = 0;

    if (!empty($combinations)) {
        foreach ($combinations as $combo) {
            $warna = mysqli_real_escape_string($db, $combo['warna']);
            $pack = mysqli_real_escape_string($db, $combo['pack']);
            $aksesoris = !empty($combo['aksesoris']) && $combo['aksesoris'] !== '-' ? mysqli_real_escape_string($db, $combo['aksesoris']) : NULL;
            $harga = mysqli_real_escape_string($db, $combo['harga']);
            $harga_diskon = !empty($combo['harga_diskon']) ? mysqli_real_escape_string($db, $combo['harga_diskon']) : "NULL";
            $jumlah_stok = mysqli_real_escape_string($db, $combo['jumlah_stok']);
            $status_stok = ($jumlah_stok > 0) ? 'tersedia' : 'habis';

            $val_aksesoris = $aksesoris ? "'$aksesoris'" : "NULL";
            $val_diskon = ($harga_diskon === "NULL") ? "NULL" : "'$harga_diskon'";

            $q_combo = "INSERT INTO admin_produk_airtag_kombinasi 
                       (produk_id, warna, pack, aksesoris, harga, harga_diskon, jumlah_stok, status_stok) 
                       VALUES ('$product_id', '$warna', '$pack', $val_aksesoris, '$harga', $val_diskon, '$jumlah_stok', '$status_stok')";
            
            if (mysqli_query($db, $q_combo)) {
                $count++;
            } else {
                throw new Exception("Gagal insert kombinasi: " . mysqli_error($db));
            }
        }
    }

    mysqli_commit($db);
    
    $response['success'] = true;
    $response['message'] = "Produk AirTag berhasil ditambahkan dengan $count variasi/kombinasi.";

} catch (Exception $e) {
    mysqli_rollback($db);
    $response['message'] = $e->getMessage();
}

header('Content-Type: application/json');
echo json_encode($response);
?>
