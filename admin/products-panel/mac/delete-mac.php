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
    header('Location: mac.php?error=invalid_id');
    exit();
}

$product_id = mysqli_real_escape_string($db, $_GET['id']);

// Ambil data gambar untuk dihapus filenya
$query_images = "SELECT * FROM admin_produk_mac_gambar WHERE produk_id = '$product_id'";
$result_images = mysqli_query($db, $query_images);

// Hapus file gambar dari server
$upload_dir = '../../uploads/';
while ($image = mysqli_fetch_assoc($result_images)) {
    // Hapus thumbnail
    if (!empty($image['foto_thumbnail'])) {
        $thumb_path = $upload_dir . $image['foto_thumbnail'];
        if (file_exists($thumb_path)) {
            unlink($thumb_path);
        }
    }
    
    // Hapus foto produk (array JSON)
    if (!empty($image['foto_produk'])) {
        $product_images = json_decode($image['foto_produk'], true);
        if (is_array($product_images)) {
            foreach ($product_images as $img_file) {
                $img_path = $upload_dir . $img_file;
                if (file_exists($img_path)) {
                    unlink($img_path);
                }
            }
        }
    }
}

// Hapus data produk (Cascade akan menghapus data di tabel kombinasi dan gambar)
$delete_product = "DELETE FROM admin_produk_mac WHERE id = '$product_id'";

if (mysqli_query($db, $delete_product)) {
    header('Location: mac.php?success=product_deleted');
} else {
    // Jika gagal, log error
    error_log("Gagal menghapus produk Mac ID $product_id: " . mysqli_error($db));
    header('Location: mac.php?error=delete_failed');
}
exit();
?>
