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
    // Debug: Lihat data yang dikirim
    error_log("=== DEBUG DATA DITERIMA (Watch) ===");
    error_log("Nama Produk: " . ($_POST['nama_produk'] ?? ''));
    error_log("Seri: " . ($_POST['seri'] ?? ''));
    error_log("Jumlah Warna: " . (is_array($_POST['warna_case'] ?? []) ? count($_POST['warna_case']) : 0));
    error_log("Jumlah Ukuran: " . (is_array($_POST['ukuran_case'] ?? []) ? count($_POST['ukuran_case']) : 0));
    error_log("Jumlah Kombinasi: " . (is_array($_POST['combinations'] ?? []) ? count($_POST['combinations']) : 0));

    // Validasi data
    $required_fields = ['nama_produk', 'kategori'];
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            throw new Exception("Field $field harus diisi");
        }
    }

    // Validasi minimal data
    if (empty($_POST['warna_case']) || !is_array($_POST['warna_case'])) {
        throw new Exception("Minimal satu warna case harus diisi");
    }
    
    if (empty($_POST['ukuran_case']) || !is_array($_POST['ukuran_case'])) {
        throw new Exception("Minimal satu ukuran case harus diisi");
    }

    // Insert data produk utama
    $nama_produk = mysqli_real_escape_string($db, $_POST['nama_produk']);
    $deskripsi_produk = mysqli_real_escape_string($db, $_POST['deskripsi_produk'] ?? '');
    $seri = mysqli_real_escape_string($db, $_POST['seri'] ?? '');
    $kategori = mysqli_real_escape_string($db, $_POST['kategori']);

    $query = "INSERT INTO admin_produk_watch (nama_produk, deskripsi_produk, seri, kategori) 
              VALUES ('$nama_produk', '$deskripsi_produk', '$seri', '$kategori')";
    
    if (!mysqli_query($db, $query)) {
        throw new Exception("Gagal menyimpan data produk: " . mysqli_error($db));
    }

    $product_id = mysqli_insert_id($db);
    error_log("Product ID created: " . $product_id);

    // Proses data warna case dengan gambar
    $warna_case_data = $_POST['warna_case'];
    
    foreach ($warna_case_data as $color_index => $color_info) {
        $warna_nama = mysqli_real_escape_string($db, $color_info['nama'] ?? '');
        $hex_code = mysqli_real_escape_string($db, $color_info['hex_code'] ?? '');
        
        if (empty($warna_nama)) {
            error_log("Warna case nama kosong pada index: " . $color_index);
            continue;
        }
        
        // Proses upload thumbnail untuk warna
        $thumbnail_name = null;
        if (isset($_FILES['warna_case']['name'][$color_index]['thumbnail']) && 
            $_FILES['warna_case']['error'][$color_index]['thumbnail'] == 0) {
            
            $thumbnail = [
                'name' => $_FILES['warna_case']['name'][$color_index]['thumbnail'],
                'type' => $_FILES['warna_case']['type'][$color_index]['thumbnail'],
                'tmp_name' => $_FILES['warna_case']['tmp_name'][$color_index]['thumbnail'],
                'size' => $_FILES['warna_case']['size'][$color_index]['thumbnail']
            ];
            
            if (in_array($thumbnail['type'], $allowed_types) && $thumbnail['size'] <= $max_size) {
                $thumbnail_name = generateFileName($thumbnail['name']);
                $thumbnail_path = $upload_dir . $thumbnail_name;
                
                if (!move_uploaded_file($thumbnail['tmp_name'], $thumbnail_path)) {
                    throw new Exception("Gagal upload thumbnail untuk warna: $warna_nama");
                }
            } else {
                throw new Exception("Format atau ukuran thumbnail untuk warna $warna_nama tidak valid");
            }
        } else {
            throw new Exception("Thumbnail untuk warna case $warna_nama harus diupload");
        }
        
        // Proses upload foto produk untuk warna
        $product_images = [];
        if (isset($_FILES['warna_case']['name'][$color_index]['product_images'])) {
            $images_count = count($_FILES['warna_case']['name'][$color_index]['product_images']);
            
            for ($i = 0; $i < $images_count; $i++) {
                if ($_FILES['warna_case']['error'][$color_index]['product_images'][$i] == 0) {
                    $image = [
                        'name' => $_FILES['warna_case']['name'][$color_index]['product_images'][$i],
                        'type' => $_FILES['warna_case']['type'][$color_index]['product_images'][$i],
                        'tmp_name' => $_FILES['warna_case']['tmp_name'][$color_index]['product_images'][$i],
                        'size' => $_FILES['warna_case']['size'][$color_index]['product_images'][$i]
                    ];
                    
                    if (in_array($image['type'], $allowed_types) && $image['size'] <= $max_size) {
                        $image_name = generateFileName($image['name']);
                        $image_path = $upload_dir . $image_name;
                        
                        if (move_uploaded_file($image['tmp_name'], $image_path)) {
                            $product_images[] = $image_name;
                        }
                    }
                }
            }
        }
        
        // Simpan data gambar untuk warna case
        $product_images_json = json_encode($product_images);
        
        $query_gambar = "INSERT INTO admin_produk_watch_gambar 
                        (produk_id, warna_case, hex_code, foto_thumbnail, foto_produk) 
                        VALUES ('$product_id', '$warna_nama', '$hex_code', '$thumbnail_name', '$product_images_json')";
        
        if (!mysqli_query($db, $query_gambar)) {
            throw new Exception("Gagal menyimpan gambar untuk warna case: $warna_nama - " . mysqli_error($db));
        }
        
        error_log("Warna case berhasil disimpan: " . $warna_nama);
    }
    
    // Proses kombinasi dan stok
    $combinations = $_POST['combinations'] ?? [];
    $combination_count = 0;
    
    error_log("Memproses " . count($combinations) . " kombinasi...");
    
    foreach ($combinations as $combination_id => $combination_data) {
        $warna_case = mysqli_real_escape_string($db, $combination_data['warna_case'] ?? '');
        $ukuran_case = mysqli_real_escape_string($db, $combination_data['ukuran_case'] ?? '');
        $tipe_koneksi = mysqli_real_escape_string($db, $combination_data['tipe_koneksi'] ?? '');
        $material = mysqli_real_escape_string($db, $combination_data['material'] ?? '');
        $harga = mysqli_real_escape_string($db, $combination_data['harga'] ?? 0);
        $harga_diskon = !empty($combination_data['harga_diskon']) ? 
                       mysqli_real_escape_string($db, $combination_data['harga_diskon']) : NULL;
        $jumlah_stok = mysqli_real_escape_string($db, $combination_data['jumlah_stok'] ?? 0);
        
        $status_stok = ($jumlah_stok > 0) ? 'tersedia' : 'habis';
        
        if (empty($warna_case) || empty($ukuran_case)) {
            error_log("Kombinasi tidak valid - Warna: $warna_case, Ukuran: $ukuran_case");
            continue;
        }
        
        if ($harga <= 0) {
            error_log("Harga tidak valid untuk kombinasi: " . $combination_id);
            continue;
        }
        
        $query_kombinasi = "INSERT INTO admin_produk_watch_kombinasi 
                           (produk_id, warna_case, ukuran_case, tipe_koneksi, material, harga, harga_diskon, jumlah_stok, status_stok) 
                           VALUES ('$product_id', '$warna_case', '$ukuran_case', '$tipe_koneksi', '$material', 
                                   '$harga', " . ($harga_diskon ? "'$harga_diskon'" : "NULL") . ", 
                                   '$jumlah_stok', '$status_stok')";
        
        if (mysqli_query($db, $query_kombinasi)) {
            $combination_count++;
        } else {
            error_log("Gagal menyimpan kombinasi: " . mysqli_error($db));
        }
    }
    
    if ($combination_count === 0) {
        // Metode alternatif jika kombinasi count masih 0
        error_log("Mencoba metode alternatif untuk menyimpan kombinasi...");
        $warna_list = $warna_case_data;
        $ukuran_list = $_POST['ukuran_case'] ?? [];
        $koneksi_list = $_POST['tipe_koneksi'] ?? [];
        $material_list = $_POST['material'] ?? [];
        
        foreach ($warna_list as $warna_item) {
            $warna_nama = mysqli_real_escape_string($db, $warna_item['nama'] ?? '');
            if (empty($warna_nama)) continue;
            
            foreach ($ukuran_list as $ukuran_item) {
                $ukuran_size = mysqli_real_escape_string($db, $ukuran_item['size'] ?? '');
                $ukuran_harga = mysqli_real_escape_string($db, $ukuran_item['harga'] ?? 0);
                
                if (empty($ukuran_size) || $ukuran_harga <= 0) continue;
                
                // Jika tidak ada koneksi, gunakan default
                if (empty($koneksi_list)) {
                    $koneksi_list = ['GPS'];
                }
                
                foreach ($koneksi_list as $koneksi_nama) {
                    $koneksi_clean = mysqli_real_escape_string($db, $koneksi_nama);
                    if (empty($koneksi_clean)) continue;
                    
                    // Jika tidak ada material, gunakan default
                    if (empty($material_list)) {
                        $material_list = ['Aluminum'];
                    }
                    
                    foreach ($material_list as $material_nama) {
                        $material_clean = mysqli_real_escape_string($db, $material_nama);
                        
                        $jumlah_stok = 0;
                        $status_stok = 'habis';
                        
                        $query_kombinasi = "INSERT INTO admin_produk_watch_kombinasi 
                                           (produk_id, warna_case, ukuran_case, tipe_koneksi, material, harga, harga_diskon, jumlah_stok, status_stok) 
                                           VALUES ('$product_id', '$warna_nama', '$ukuran_size', '$koneksi_clean', '$material_clean', 
                                                   '$ukuran_harga', NULL, '$jumlah_stok', '$status_stok')";
                        
                        if (mysqli_query($db, $query_kombinasi)) {
                            $combination_count++;
                        }
                    }
                }
            }
        }
    }
    
    if ($combination_count === 0) {
        mysqli_query($db, "DELETE FROM admin_produk_watch WHERE id = '$product_id'");
        throw new Exception("Tidak ada kombinasi yang berhasil disimpan. Periksa data yang dikirim.");
    }

    $response['success'] = true;
    $response['message'] = "Produk Apple Watch berhasil ditambahkan dengan $combination_count kombinasi";
    $response['product_id'] = $product_id;

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
    error_log("ERROR (Watch): " . $e->getMessage());
}

header('Content-Type: application/json');
echo json_encode($response);
?>