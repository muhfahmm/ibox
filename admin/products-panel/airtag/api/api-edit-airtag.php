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
    mysqli_query($db, "DELETE FROM admin_produk_airtag_gambar WHERE produk_id='$product_id'");
    $warna_names = $_POST['warna_nama'] ?? [];
    
    foreach ($warna_names as $index => $warna_nama) {
        $warna_nama = mysqli_real_escape_string($db, $warna_nama);
        if (empty($warna_nama)) continue;

        $thumbnail_name = $_POST["existing_thumbnail_$index"] ?? null;
        if (isset($_FILES["thumbnail_$index"]) && $_FILES["thumbnail_$index"]['error'] == 0) {
            $file = $_FILES["thumbnail_$index"];
            if (in_array($file['type'], $allowed_types) && $file['size'] <= $max_size) {
                $new_name = generateFileName($file['name']);
                if (move_uploaded_file($file['tmp_name'], $upload_dir . $new_name)) $thumbnail_name = $new_name;
            }
        }
        
        $product_images = json_decode($_POST["existing_galeri_$index"] ?? '[]', true) ?? [];
        if (isset($_FILES["galeri_$index"])) {
            foreach ($_FILES["galeri_$index"]['name'] as $i => $name) {
                if ($_FILES["galeri_$index"]['error'][$i] == 0) {
                     $file = [
                        'name' => $_FILES["galeri_$index"]['name'][$i],
                        'type' => $_FILES["galeri_$index"]['type'][$i],
                        'tmp_name' => $_FILES["galeri_$index"]['tmp_name'][$i],
                        'size' => $_FILES["galeri_$index"]['size'][$i]
                    ];
                    if (in_array($file['type'], $allowed_types) && $file['size'] <= $max_size) {
                        $new_name = generateFileName($file['name']);
                        if (move_uploaded_file($file['tmp_name'], $upload_dir . $new_name)) $product_images[] = $new_name;
                    }
                }
            }
        }

        $json_imgs = json_encode(array_values($product_images));
        mysqli_query($db, "INSERT INTO admin_produk_airtag_gambar (produk_id, warna, foto_thumbnail, foto_produk) VALUES ('$product_id', '$warna_nama', '$thumbnail_name', '$json_imgs')");
    }

    // 3. Update Kombinasi
    mysqli_query($db, "DELETE FROM admin_produk_airtag_kombinasi WHERE produk_id='$product_id'");
    $c_warnas = $_POST['combi_warna'] ?? [];
    $c_packs = $_POST['combi_pack'] ?? [];
    $c_aks = $_POST['combi_aksesoris'] ?? [];
    $c_prices = $_POST['combi_price'] ?? [];
    $c_stocks = $_POST['combi_stock'] ?? [];

    for ($i = 0; $i < count($c_warnas); $i++) {
        $w = mysqli_real_escape_string($db, $c_warnas[$i]);
        $p = mysqli_real_escape_string($db, $c_packs[$i]);
        $a = $c_aks[$i] !== '-' ? mysqli_real_escape_string($db, $c_aks[$i]) : NULL;
        $pr = mysqli_real_escape_string($db, $c_prices[$i]);
        $st = mysqli_real_escape_string($db, $c_stocks[$i]);
        $status = ($st > 0) ? 'tersedia' : 'habis';

        $val_aks = $a ? "'$a'" : "NULL";
        mysqli_query($db, "INSERT INTO admin_produk_airtag_kombinasi (produk_id, warna, pack, aksesoris, harga, jumlah_stok, status_stok) VALUES ('$product_id', '$w', '$p', $val_aks, '$pr', '$st', '$status')");
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
