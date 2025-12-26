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
$allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
$max_size = 2 * 1024 * 1024;

function generateFileName($original_name) {
    $extension = pathinfo($original_name, PATHINFO_EXTENSION);
    return uniqid() . '_' . time() . '.' . $extension;
}

$response = ['success' => false, 'message' => ''];

try {
    // Nonaktifkan foreign key checks sementara
    mysqli_query($db, "SET FOREIGN_KEY_CHECKS=0");

    // Validasi ID produk
    if (empty($_POST['product_id'])) {
        throw new Exception("Product ID tidak valid");
    }

    $product_id = escape($_POST['product_id']);

    // Cek apakah produk ada
    $check_query = "SELECT id FROM admin_produk_ipad WHERE id = '$product_id'";
    $check_result = mysqli_query($db, $check_query);
    
    if (mysqli_num_rows($check_result) == 0) {
        throw new Exception("Produk tidak ditemukan");
    }

    // Update data produk
    $nama_produk = escape($_POST['nama_produk']);
    $harga = escape($_POST['harga']);
    $warna = escape($_POST['warna'] ?? '');
    $penyimpanan = escape($_POST['penyimpanan'] ?? '');
    $konektivitas = escape($_POST['konektivitas'] ?? '');
    $deskripsi_produk = escape($_POST['deskripsi_produk'] ?? '');
    $harga_diskon = !empty($_POST['harga_diskon']) ? escape($_POST['harga_diskon']) : NULL;
    $jumlah_stok = escape($_POST['jumlah_stok'] ?? 0);
    $status_stok = escape($_POST['status_stok'] ?? 'tersedia');

    $update_query = "UPDATE admin_produk_ipad SET
        nama_produk = '$nama_produk',
        harga = '$harga',
        warna = '$warna',
        penyimpanan = '$penyimpanan',
        konektivitas = '$konektivitas',
        deskripsi_produk = '$deskripsi_produk',
        harga_diskon = " . ($harga_diskon ? "'$harga_diskon'" : "NULL") . ",
        jumlah_stok = '$jumlah_stok',
        status_stok = '$status_stok'
        WHERE id = '$product_id'";

    if (!mysqli_query($db, $update_query)) {
        throw new Exception("Gagal update data produk: " . mysqli_error($db));
    }

    // Proses penghapusan gambar yang dipilih
    if (!empty($_POST['images_to_delete'])) {
        $images_to_delete = json_decode($_POST['images_to_delete'], true);
        
        foreach ($images_to_delete as $image) {
            // Hapus dari database
            $delete_query = "DELETE FROM admin_gambar_produk WHERE id = '" . escape($image['id']) . "'";
            mysqli_query($db, $delete_query);
            
            // Hapus file dari server
            $img_query = "SELECT foto_thumbnail, foto_produk FROM admin_gambar_produk WHERE id = '" . escape($image['id']) . "'";
            $img_result = mysqli_query($db, $img_query);
            if ($img_row = mysqli_fetch_assoc($img_result)) {
                if (!empty($img_row['foto_thumbnail'])) {
                    $thumb_path = $upload_dir . $img_row['foto_thumbnail'];
                    if (file_exists($thumb_path)) unlink($thumb_path);
                }
                if (!empty($img_row['foto_produk'])) {
                    $img_path = $upload_dir . $img_row['foto_produk'];
                    if (file_exists($img_path)) unlink($img_path);
                }
            }
        }
    }

    // Proses upload thumbnail baru (jika ada)
    if (isset($_FILES['new_thumbnail']) && $_FILES['new_thumbnail']['error'] == 0) {
        $thumbnail = $_FILES['new_thumbnail'];
        
        if (in_array($thumbnail['type'], $allowed_types) && $thumbnail['size'] <= $max_size) {
            $thumbnail_name = generateFileName($thumbnail['name']);
            $thumbnail_path = $upload_dir . $thumbnail_name;
            
            if (move_uploaded_file($thumbnail['tmp_name'], $thumbnail_path)) {
                // Hapus thumbnail lama jika ada
                if (!empty($_POST['existing_thumbnail'])) {
                    $old_thumb_path = $upload_dir . $_POST['existing_thumbnail'];
                    if (file_exists($old_thumb_path)) {
                        unlink($old_thumb_path);
                    }
                }
                
                // Update atau insert thumbnail baru
                $check_thumb_query = "SELECT id FROM admin_gambar_produk WHERE produk_id = '$product_id' AND tipe_produk = 'ipad' AND foto_thumbnail IS NOT NULL";
                $thumb_result = mysqli_query($db, $check_thumb_query);
                
                if (mysqli_num_rows($thumb_result) > 0) {
                    // Update existing thumbnail
                    $update_thumb_query = "UPDATE admin_gambar_produk SET foto_thumbnail = '$thumbnail_name' 
                                          WHERE produk_id = '$product_id' AND tipe_produk = 'ipad' AND foto_thumbnail IS NOT NULL";
                } else {
                    // Insert new thumbnail
                    $update_thumb_query = "INSERT INTO admin_gambar_produk (produk_id, foto_thumbnail, tipe_produk) 
                                          VALUES ('$product_id', '$thumbnail_name', 'ipad')";
                }
                
                mysqli_query($db, $update_thumb_query);
            }
        }
    }

    // Proses upload foto produk baru (jika ada)
    if (isset($_FILES['new_product_images']) && is_array($_FILES['new_product_images']['name'])) {
        $product_images = $_FILES['new_product_images'];
        
        for ($i = 0; $i < count($product_images['name']); $i++) {
            if ($product_images['error'][$i] == 0) {
                if (in_array($product_images['type'][$i], $allowed_types) && 
                    $product_images['size'][$i] <= $max_size) {
                    
                    $image_name = generateFileName($product_images['name'][$i]);
                    $image_path = $upload_dir . $image_name;
                    
                    if (move_uploaded_file($product_images['tmp_name'][$i], $image_path)) {
                        // Insert foto produk baru
                        $insert_img_query = "INSERT INTO admin_gambar_produk (produk_id, foto_produk, tipe_produk) 
                                           VALUES ('$product_id', '$image_name', 'ipad')";
                        mysqli_query($db, $insert_img_query);
                    }
                }
            }
        }
    }

    // Aktifkan kembali foreign key checks
    mysqli_query($db, "SET FOREIGN_KEY_CHECKS=1");

    $response['success'] = true;
    $response['message'] = 'Produk berhasil diperbarui';

} catch (Exception $e) {
    // Pastikan foreign key checks diaktifkan kembali
    mysqli_query($db, "SET FOREIGN_KEY_CHECKS=1");
    $response['message'] = $e->getMessage();
}

header('Content-Type: application/json');
echo json_encode($response);
?>