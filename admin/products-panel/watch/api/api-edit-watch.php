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

    $product_id = $_POST['product_id'] ?? null;
    if (!$product_id) throw new Exception('Product ID is required');

    // Validasi data basics
    if (empty($_POST['nama_produk'])) throw new Exception("Nama produk harus diisi");
    if (empty($_POST['warna_case']) || !is_array($_POST['warna_case'])) throw new Exception("Minimal satu warna case harus diisi");
    if (empty($_POST['combinations']) || !is_array($_POST['combinations'])) throw new Exception("Kombinasi produk tidak valid");

    // Start Transaction
    mysqli_begin_transaction($db);

    // 1. Update Produk Utama
    $nama_produk = mysqli_real_escape_string($db, $_POST['nama_produk']);
    $deskripsi_produk = mysqli_real_escape_string($db, $_POST['deskripsi_produk'] ?? '');
    $seri = mysqli_real_escape_string($db, $_POST['seri'] ?? '');
    
    $query_update_produk = "UPDATE admin_produk_watch SET 
                           nama_produk = '$nama_produk', 
                           seri = '$seri',
                           deskripsi_produk = '$deskripsi_produk',
                           updated_at = NOW() 
                           WHERE id = '$product_id'";
    
    if (!mysqli_query($db, $query_update_produk)) {
        throw new Exception("Gagal update produk: " . mysqli_error($db));
    }

    // 2. Update Gambar
    mysqli_query($db, "DELETE FROM admin_produk_watch_gambar WHERE produk_id = '$product_id'");

    $warna_data = $_POST['warna_case'];
    
    foreach ($warna_data as $color_index => $color_info) {
        $warna_case = mysqli_real_escape_string($db, $color_info['nama'] ?? '');
        if (empty($warna_case)) continue;

        // --- Handle Thumbnail ---
        $thumbnail_name = $color_info['existing_thumbnail'] ?? null;

        // Check if new thumbnail uploaded
        if (isset($_FILES['warna_case']['name'][$color_index]['thumbnail']) && 
            $_FILES['warna_case']['error'][$color_index]['thumbnail'] == 0) {
            
            $file = [
                'name' => $_FILES['warna_case']['name'][$color_index]['thumbnail'],
                'type' => $_FILES['warna_case']['type'][$color_index]['thumbnail'],
                'tmp_name' => $_FILES['warna_case']['tmp_name'][$color_index]['thumbnail'],
                'size' => $_FILES['warna_case']['size'][$color_index]['thumbnail']
            ];

            if (in_array($file['type'], $allowed_types) && $file['size'] <= $max_size) {
                $new_name = generateFileName($file['name']);
                if (move_uploaded_file($file['tmp_name'], $upload_dir . $new_name)) {
                    $thumbnail_name = $new_name; // Replace with new
                }
            }
        }

        if (!$thumbnail_name) {
            throw new Exception("Thumbnail untuk warna case $warna_case harus ada");
        }

        // --- Handle Product Images ---
        $product_images = $color_info['existing_images'] ?? [];
        
        // Check for new uploads
        if (isset($_FILES['warna_case']['name'][$color_index]['product_images'])) {
            $count = count($_FILES['warna_case']['name'][$color_index]['product_images']);
            for ($i = 0; $i < $count; $i++) {
                if ($_FILES['warna_case']['error'][$color_index]['product_images'][$i] == 0) {
                    $file = [
                        'name' => $_FILES['warna_case']['name'][$color_index]['product_images'][$i],
                        'type' => $_FILES['warna_case']['type'][$color_index]['product_images'][$i],
                        'tmp_name' => $_FILES['warna_case']['tmp_name'][$color_index]['product_images'][$i],
                        'size' => $_FILES['warna_case']['size'][$color_index]['product_images'][$i]
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

        $product_images_json = json_encode(array_values($product_images));

        // Insert into DB
        $query_gambar = "INSERT INTO admin_produk_watch_gambar 
                        (produk_id, warna_case, foto_thumbnail, foto_produk) 
                        VALUES ('$product_id', '$warna_case', '$thumbnail_name', '$product_images_json')";
        
        if (!mysqli_query($db, $query_gambar)) {
            throw new Exception("Gagal menyimpan gambar warna case $warna_case: " . mysqli_error($db));
        }
    }

    // 3. Update Kombinasi
    mysqli_query($db, "DELETE FROM admin_produk_watch_kombinasi WHERE produk_id = '$product_id'");

    $combinations = $_POST['combinations'] ?? [];
    $count = 0;

    foreach ($combinations as $unique_id => $combo) {
        $warna_case = mysqli_real_escape_string($db, $_POST['warna_case'][explode('_', $unique_id)[0]]['nama'] ?? '');
        $ukuran_case = mysqli_real_escape_string($db, $_POST['ukuran_case'][explode('_', $unique_id)[1]] ?? '');
        $tipe_koneksi = mysqli_real_escape_string($db, $_POST['tipe_koneksi'][explode('_', $unique_id)[2]] ?? '');
        $material = mysqli_real_escape_string($db, $_POST['material'][explode('_', $unique_id)[3]] ?? '');
        
        $harga = mysqli_real_escape_string($db, $combo['harga']);
        $harga_diskon = !empty($combo['harga_diskon']) ? mysqli_real_escape_string($db, $combo['harga_diskon']) : "NULL";
        $jumlah_stok = mysqli_real_escape_string($db, $combo['jumlah_stok']);
        $status_stok = ($jumlah_stok > 0) ? 'tersedia' : 'habis';

        $query_kombinasi = "INSERT INTO admin_produk_watch_kombinasi 
                           (produk_id, warna_case, ukuran_case, tipe_koneksi, material, harga, harga_diskon, jumlah_stok, status_stok) 
                           VALUES ('$product_id', '$warna_case', '$ukuran_case', '$tipe_koneksi', '$material',
                                   '$harga', " . ($harga_diskon === "NULL" ? "NULL" : "'$harga_diskon'") . ", 
                                   '$jumlah_stok', '$status_stok')";
        
        if (mysqli_query($db, $query_kombinasi)) {
            $count++;
        }
    }

    mysqli_commit($db);

    $response['success'] = true;
    $response['message'] = "Produk berhasil diperbarui dengan $count kombinasi data.";

} catch (Exception $e) {
    mysqli_rollback($db);
    $response['message'] = $e->getMessage();
    error_log("Edit Watch Error: " . $e->getMessage());
}

header('Content-Type: application/json');
echo json_encode($response);
?>