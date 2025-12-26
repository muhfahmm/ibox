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
    return uniqid() . '_' . time() . '.' . $extension;
}

$response = ['success' => false, 'message' => ''];

try {
    // Validasi data
    $required_fields = ['nama_produk', 'harga'];
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            throw new Exception("Field $field harus diisi");
        }
    }

    // Nonaktifkan foreign key checks sementara
    mysqli_query($db, "SET FOREIGN_KEY_CHECKS=0");

    // Proses upload thumbnail
    if (!isset($_FILES['thumbnail']) || $_FILES['thumbnail']['error'] != 0) {
        throw new Exception("Thumbnail harus diupload");
    }

    $thumbnail = $_FILES['thumbnail'];
    if (!in_array($thumbnail['type'], $allowed_types)) {
        throw new Exception("Format thumbnail tidak didukung");
    }
    if ($thumbnail['size'] > $max_size) {
        throw new Exception("Ukuran thumbnail terlalu besar (max 2MB)");
    }

    $thumbnail_name = generateFileName($thumbnail['name']);
    $thumbnail_path = $upload_dir . $thumbnail_name;

    if (!move_uploaded_file($thumbnail['tmp_name'], $thumbnail_path)) {
        throw new Exception("Gagal upload thumbnail");
    }

    // Insert data produk
    $nama_produk = escape($_POST['nama_produk']);
    $harga = escape($_POST['harga']);
    $warna = escape($_POST['warna'] ?? '');
    $penyimpanan = escape($_POST['penyimpanan'] ?? '');
    $konektivitas = escape($_POST['konektivitas'] ?? '');
    $deskripsi_produk = escape($_POST['deskripsi_produk'] ?? '');
    $harga_diskon = !empty($_POST['harga_diskon']) ? escape($_POST['harga_diskon']) : NULL;
    $jumlah_stok = escape($_POST['jumlah_stok'] ?? 0);
    $status_stok = escape($_POST['status_stok'] ?? 'tersedia');

    // Insert ke tabel produk iPad
    $query = "INSERT INTO admin_produk_ipad (
        nama_produk, harga, warna, penyimpanan, konektivitas, 
        deskripsi_produk, harga_diskon, jumlah_stok, status_stok
    ) VALUES (
        '$nama_produk', '$harga', '$warna', '$penyimpanan', '$konektivitas',
        '$deskripsi_produk', " . ($harga_diskon ? "'$harga_diskon'" : "NULL") . ", 
        '$jumlah_stok', '$status_stok'
    )";

    if (!mysqli_query($db, $query)) {
        throw new Exception("Gagal menyimpan data produk: " . mysqli_error($db));
    }

    $product_id = mysqli_insert_id($db);

    // Insert thumbnail ke tabel gambar
    $query_thumb = "INSERT INTO admin_gambar_produk (produk_id, foto_thumbnail, tipe_produk) 
                    VALUES ('$product_id', '$thumbnail_name', 'ipad')";
    
    if (!mysqli_query($db, $query_thumb)) {
        // Hapus produk jika gagal insert gambar
        mysqli_query($db, "DELETE FROM admin_produk_ipad WHERE id = '$product_id'");
        throw new Exception("Gagal menyimpan thumbnail: " . mysqli_error($db));
    }

    // Proses upload foto produk (multiple)
    if (isset($_FILES['product_images']) && is_array($_FILES['product_images']['name'])) {
        $product_images = $_FILES['product_images'];
        
        for ($i = 0; $i < count($product_images['name']); $i++) {
            if ($product_images['error'][$i] == 0) {
                if (in_array($product_images['type'][$i], $allowed_types) && 
                    $product_images['size'][$i] <= $max_size) {
                    
                    $image_name = generateFileName($product_images['name'][$i]);
                    $image_path = $upload_dir . $image_name;
                    
                    if (move_uploaded_file($product_images['tmp_name'][$i], $image_path)) {
                        // Insert ke tabel gambar
                        $query_img = "INSERT INTO admin_gambar_produk (produk_id, foto_produk, tipe_produk) 
                                     VALUES ('$product_id', '$image_name', 'ipad')";
                        mysqli_query($db, $query_img);
                    }
                }
            }
        }
    }

    // Aktifkan kembali foreign key checks
    mysqli_query($db, "SET FOREIGN_KEY_CHECKS=1");

    $response['success'] = true;
    $response['message'] = 'Produk berhasil ditambahkan';
    $response['product_id'] = $product_id;

} catch (Exception $e) {
    // Pastikan foreign key checks diaktifkan kembali
    mysqli_query($db, "SET FOREIGN_KEY_CHECKS=1");
    $response['message'] = $e->getMessage();
}

header('Content-Type: application/json');
echo json_encode($response);
?>