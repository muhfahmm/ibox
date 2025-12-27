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
    // Validasi data
    $required_fields = ['nama_produk'];
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            throw new Exception("Field $field harus diisi");
        }
    }

    // Validasi minimal data
    if (empty($_POST['warna']) || empty($_POST['processor']) || empty($_POST['penyimpanan']) || empty($_POST['ram'])) {
        throw new Exception("Minimal satu warna, processor, penyimpanan, dan RAM harus diisi");
    }

    // Insert data produk utama
    $nama_produk = mysqli_real_escape_string($db, $_POST['nama_produk']);
    $deskripsi_produk = mysqli_real_escape_string($db, $_POST['deskripsi_produk'] ?? '');

    $query = "INSERT INTO admin_produk_mac (nama_produk, deskripsi_produk) VALUES ('$nama_produk', '$deskripsi_produk')";
    
    if (!mysqli_query($db, $query)) {
        throw new Exception("Gagal menyimpan data produk: " . mysqli_error($db));
    }

    $product_id = mysqli_insert_id($db);

    // Proses data warna dengan gambar
    $warna_data = $_POST['warna'] ?? [];
    $warna_gambar = [];
    
    foreach ($warna_data as $color_index => $color_info) {
        $warna_nama = mysqli_real_escape_string($db, $color_info['nama'] ?? '');
        
        if (empty($warna_nama)) {
            continue;
        }
        
        // Proses upload thumbnail untuk warna
        $thumbnail_name = null;
        if (isset($_FILES['warna']['name'][$color_index]['thumbnail']) && 
            $_FILES['warna']['error'][$color_index]['thumbnail'] == 0) {
            
            $thumbnail = [
                'name' => $_FILES['warna']['name'][$color_index]['thumbnail'],
                'type' => $_FILES['warna']['type'][$color_index]['thumbnail'],
                'tmp_name' => $_FILES['warna']['tmp_name'][$color_index]['thumbnail'],
                'size' => $_FILES['warna']['size'][$color_index]['thumbnail']
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
            throw new Exception("Thumbnail untuk warna $warna_nama harus diupload");
        }
        
        // Proses upload foto produk untuk warna
        $product_images = [];
        if (isset($_FILES['warna']['name'][$color_index]['product_images'])) {
            $images_count = count($_FILES['warna']['name'][$color_index]['product_images']);
            
            for ($i = 0; $i < $images_count; $i++) {
                if ($_FILES['warna']['error'][$color_index]['product_images'][$i] == 0) {
                    $image = [
                        'name' => $_FILES['warna']['name'][$color_index]['product_images'][$i],
                        'type' => $_FILES['warna']['type'][$color_index]['product_images'][$i],
                        'tmp_name' => $_FILES['warna']['tmp_name'][$color_index]['product_images'][$i],
                        'size' => $_FILES['warna']['size'][$color_index]['product_images'][$i]
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
        
        // Simpan data gambar untuk warna
        $product_images_json = json_encode($product_images);
        
        $query_gambar = "INSERT INTO admin_produk_mac_gambar 
                        (produk_id, warna, foto_thumbnail, foto_produk) 
                        VALUES ('$product_id', '$warna_nama', '$thumbnail_name', '$product_images_json')";
        
        if (!mysqli_query($db, $query_gambar)) {
            throw new Exception("Gagal menyimpan gambar untuk warna: $warna_nama");
        }
        
        $warna_gambar[$warna_nama] = true;
    }
    
    // Proses data processor
    $processor_data = $_POST['processor'] ?? [];
    $processor_list = [];
    
    foreach ($processor_data as $proc) {
        $proc_clean = mysqli_real_escape_string($db, $proc);
        if (!empty($proc_clean)) {
            $processor_list[] = $proc_clean;
        }
    }
    
    // Proses data penyimpanan
    $penyimpanan_data = $_POST['penyimpanan'] ?? [];
    $penyimpanan_harga = [];
    
    foreach ($penyimpanan_data as $storage_index => $storage_info) {
        $size = mysqli_real_escape_string($db, $storage_info['size'] ?? '');
        $harga = mysqli_real_escape_string($db, $storage_info['harga'] ?? 0);
        $harga_diskon = !empty($storage_info['harga_diskon']) ? 
                       mysqli_real_escape_string($db, $storage_info['harga_diskon']) : NULL;
        
        if (!empty($size) && $harga > 0) {
            $penyimpanan_harga[$size] = [
                'harga' => $harga,
                'harga_diskon' => $harga_diskon
            ];
        }
    }
    
    // Proses data RAM
    $ram_data = $_POST['ram'] ?? [];
    $ram_list = [];
    
    foreach ($ram_data as $ram) {
        $ram_clean = mysqli_real_escape_string($db, $ram);
        if (!empty($ram_clean)) {
            $ram_list[] = $ram_clean;
        }
    }
    
    // Proses kombinasi dan stok
    $combinations = $_POST['combinations'] ?? [];
    $combination_count = 0;
    
    foreach ($combinations as $combination_id => $combination_data) {
        $warna = mysqli_real_escape_string($db, $combination_data['warna'] ?? '');
        $processor = mysqli_real_escape_string($db, $combination_data['processor'] ?? '');
        $penyimpanan = mysqli_real_escape_string($db, $combination_data['penyimpanan'] ?? '');
        $ram = mysqli_real_escape_string($db, $combination_data['ram'] ?? '');
        $harga = mysqli_real_escape_string($db, $combination_data['harga'] ?? 0);
        $harga_diskon = !empty($combination_data['harga_diskon']) ? 
                       mysqli_real_escape_string($db, $combination_data['harga_diskon']) : NULL;
        $jumlah_stok = mysqli_real_escape_string($db, $combination_data['jumlah_stok'] ?? 0);
        
        // Tentukan status stok berdasarkan jumlah stok
        $status_stok = ($jumlah_stok > 0) ? 'tersedia' : 'habis';
        
        // Validasi data kombinasi
        if (empty($warna) || empty($processor) || empty($penyimpanan) || empty($ram)) {
            continue;
        }
        
        // Simpan kombinasi ke database
        $query_kombinasi = "INSERT INTO admin_produk_mac_kombinasi 
                           (produk_id, warna, processor, penyimpanan, ram, harga, harga_diskon, jumlah_stok, status_stok) 
                           VALUES ('$product_id', '$warna', '$processor', '$penyimpanan', '$ram', 
                                   '$harga', " . ($harga_diskon ? "'$harga_diskon'" : "NULL") . ", 
                                   '$jumlah_stok', '$status_stok')";
        
        if (mysqli_query($db, $query_kombinasi)) {
            $combination_count++;
        }
    }
    
    if ($combination_count === 0) {
        throw new Exception("Tidak ada kombinasi yang berhasil disimpan");
    }

    $response['success'] = true;
    $response['message'] = "Produk Mac berhasil ditambahkan dengan $combination_count kombinasi";
    $response['product_id'] = $product_id;

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

header('Content-Type: application/json');
echo json_encode($response);
?>