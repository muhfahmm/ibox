<?php
session_start();
require_once '../../db.php';

// Cek login
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ../../auth/login.php?error=not_logged_in');
    exit();
}

// Cek ID produk
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: aksesoris.php?error=invalid_id');
    exit();
}

$product_id = escape($_GET['id']);

// Ambil data gambar untuk dihapus filenya
$query_images = "SELECT * FROM admin_produk_aksesoris_gambar WHERE produk_id = '$product_id'";
$result_images = mysqli_query($db, $query_images);

// Hapus file gambar dari server
$upload_dir = '../../../uploads/';
while ($image = mysqli_fetch_assoc($result_images)) {
    if (!empty($image['foto_thumbnail'])) {
        $thumb_path = $upload_dir . $image['foto_thumbnail'];
        if (file_exists($thumb_path)) {
            unlink($thumb_path);
        }
    }
    if (!empty($image['foto_produk'])) {
        $images = json_decode($image['foto_produk'], true);
        if (is_array($images)) {
            foreach ($images as $img) {
                $img_path = $upload_dir . $img;
                if (file_exists($img_path)) {
                    unlink($img_path);
                }
            }
        }
    }
}

// Hapus data gambar dari database
$delete_images = "DELETE FROM admin_produk_aksesoris_gambar WHERE produk_id = '$product_id'";
mysqli_query($db, $delete_images);

// Hapus data kombinasi
$delete_combinations = "DELETE FROM admin_produk_aksesoris_kombinasi WHERE produk_id = '$product_id'";
mysqli_query($db, $delete_combinations);

// Hapus data produk
$delete_product = "DELETE FROM admin_produk_aksesoris WHERE id = '$product_id'";
if (mysqli_query($db, $delete_product)) {
    header('Location: aksesoris.php?success=product_deleted');
} else {
    header('Location: aksesoris.php?error=delete_failed');
}
exit();
?>