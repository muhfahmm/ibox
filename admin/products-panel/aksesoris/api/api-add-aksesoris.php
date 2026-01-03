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
    error_log("=== DEBUG DATA DITERIMA (Aksesoris) ===");
    error_log("Nama Produk: " . ($_POST['nama_produk'] ?? ''));
    error_log("Kategori: " . ($_POST['kategori'] ?? ''));
    error_log("Kompatibel Dengan: " . ($_POST['kompatibel_dengan'] ?? ''));
    error_log("Jumlah Warna: " . (is_array($_POST['warna'] ?? []) ? count($_POST['warna']) : 0));
    error_log("Jumlah Tipe: " . (is_array($_POST['tipe'] ?? []) ? count($_POST['tipe']) : 0));
    error_log("Jumlah Ukuran: " . (is_array($_POST['ukuran'] ?? []) ? count($_POST['ukuran']) : 0));
    error_log("Jumlah Kombinasi: " . (is_array($_POST['combinations'] ?? []) ? count($_POST['combinations']) : 0));

    // Validasi data
    $required_fields = ['nama_produk', 'kategori'];
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            throw new Exception("Field $field harus diisi");
        }
    }

    // Validasi dan sanitasi kategori
    $kategori_input = mysqli_real_escape_string($db, $_POST['kategori'] ?? '');
    $allowed_categories = ['case', 'charger', 'headphone', 'keyboard', 'mouse', 'trackpad', 'adapter', 'other'];
    
    // Jika kategori tidak ada dalam daftar yang diizinkan, gunakan default 'case'
    if (!in_array($kategori_input, $allowed_categories)) {
        $kategori = 'case';
        error_log("Kategori tidak valid: $kategori_input, menggunakan default 'case'");
    } else {
        $kategori = $kategori_input;
    }

    // Validasi minimal data
    if (empty($_POST['warna']) || !is_array($_POST['warna'])) {
        throw new Exception("Minimal satu warna harus diisi");
    }
    
    if (empty($_POST['tipe']) || !is_array($_POST['tipe'])) {
        throw new Exception("Minimal satu tipe harus diisi");
    }

    // Insert data produk utama
    $nama_produk = mysqli_real_escape_string($db, $_POST['nama_produk']);
    $deskripsi_produk = mysqli_real_escape_string($db, $_POST['deskripsi_produk'] ?? '');
    $kompatibel_dengan = mysqli_real_escape_string($db, $_POST['kompatibel_dengan'] ?? '');
    
    // Format kompatibel_dengan sebagai array JSON jika ada
    $kompatibel_json = '[]';
    if (!empty($kompatibel_dengan)) {
        $devices = array_map('trim', explode(',', $kompatibel_dengan));
        $kompatibel_json = json_encode($devices);
    }

    $query = "INSERT INTO admin_produk_aksesoris 
              (nama_produk, deskripsi_produk, kategori, kompatibel_dengan) 
              VALUES ('$nama_produk', '$deskripsi_produk', '$kategori', '$kompatibel_json')";
    
    error_log("Query INSERT: " . $query);
    
    if (!mysqli_query($db, $query)) {
        throw new Exception("Gagal menyimpan data produk: " . mysqli_error($db));
    }

    $product_id = mysqli_insert_id($db);
    error_log("Product ID created: " . $product_id);

    // Proses data warna dengan gambar
    $warna_data = $_POST['warna'];
    
    foreach ($warna_data as $color_index => $color_info) {
        $warna_nama = mysqli_real_escape_string($db, $color_info['nama'] ?? '');
        
        if (empty($warna_nama)) {
            error_log("Warna nama kosong pada index: " . $color_index);
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
        
        $query_gambar = "INSERT INTO admin_produk_aksesoris_gambar 
                        (produk_id, warna, foto_thumbnail, foto_produk) 
                        VALUES ('$product_id', '$warna_nama', '$thumbnail_name', '$product_images_json')";
        
        if (!mysqli_query($db, $query_gambar)) {
            throw new Exception("Gagal menyimpan gambar untuk warna: $warna_nama - " . mysqli_error($db));
        }
        
        error_log("Warna berhasil disimpan: " . $warna_nama);
    }
    
    // Proses kombinasi dan stok
    $combinations = $_POST['combinations'] ?? [];
    $combination_count = 0;
    
    error_log("Memproses " . count($combinations) . " kombinasi...");
    
    foreach ($combinations as $combination_id => $combination_data) {
        $warna = mysqli_real_escape_string($db, $combination_data['warna'] ?? '');
        $tipe = mysqli_real_escape_string($db, $combination_data['tipe'] ?? '');
        $ukuran = mysqli_real_escape_string($db, $combination_data['ukuran'] ?? '');
        $harga = mysqli_real_escape_string($db, $combination_data['harga'] ?? 0);
        
        $diskon_persen = !empty($combination_data['diskon_persen']) ? floatval($combination_data['diskon_persen']) : 0;
        $harga_diskon = NULL;

        if ($diskon_persen > 0 && $harga > 0) {
            $calc_diskon = $harga - ($harga * ($diskon_persen / 100));
            $harga_diskon = round($calc_diskon);
        }

        $jumlah_stok = mysqli_real_escape_string($db, $combination_data['jumlah_stok'] ?? 0);
        
        $status_stok = ($jumlah_stok > 0) ? 'tersedia' : 'habis';
        
        if (empty($warna) || empty($tipe)) {
            error_log("Kombinasi tidak valid - Warna: $warna, Tipe: $tipe");
            continue;
        }
        
        if ($harga <= 0) {
            error_log("Harga tidak valid untuk kombinasi: " . $combination_id);
            continue;
        }
        
        $query_kombinasi = "INSERT INTO admin_produk_aksesoris_kombinasi 
                           (produk_id, warna, tipe, ukuran, harga, harga_diskon, jumlah_stok, status_stok) 
                           VALUES ('$product_id', '$warna', '$tipe', '$ukuran', 
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
        $warna_list = $warna_data;
        $tipe_list = $_POST['tipe'] ?? [];
        $ukuran_list = $_POST['ukuran'] ?? [];
        
        foreach ($warna_list as $warna_item) {
            $warna_nama = mysqli_real_escape_string($db, $warna_item['nama'] ?? '');
            if (empty($warna_nama)) continue;
            
            foreach ($tipe_list as $tipe_item) {
                $tipe_nama = mysqli_real_escape_string($db, $tipe_item['nama'] ?? '');
                $tipe_harga = mysqli_real_escape_string($db, $tipe_item['harga'] ?? 0);
                
                if (empty($tipe_nama) || $tipe_harga <= 0) continue;
                
                // Jika tidak ada ukuran, gunakan array dengan satu elemen kosong
                if (empty($ukuran_list)) {
                    $ukuran_list = [['size' => '']];
                }
                
                foreach ($ukuran_list as $ukuran_item) {
                    $ukuran_size = mysqli_real_escape_string($db, $ukuran_item['size'] ?? '');
                    
                    $jumlah_stok = 0;
                    $status_stok = 'habis';
                    
                    $query_kombinasi = "INSERT INTO admin_produk_aksesoris_kombinasi 
                                       (produk_id, warna, tipe, ukuran, harga, harga_diskon, jumlah_stok, status_stok) 
                                       VALUES ('$product_id', '$warna_nama', '$tipe_nama', '$ukuran_size', 
                                               '$tipe_harga', NULL, '$jumlah_stok', '$status_stok')";
                    
                    if (mysqli_query($db, $query_kombinasi)) {
                        $combination_count++;
                    }
                }
            }
        }
    }
    
    if ($combination_count === 0) {
        mysqli_query($db, "DELETE FROM admin_produk_aksesoris WHERE id = '$product_id'");
        throw new Exception("Tidak ada kombinasi yang berhasil disimpan. Periksa data yang dikirim.");
    }

    $response['success'] = true;
    $response['message'] = "Produk Aksesoris berhasil ditambahkan dengan $combination_count kombinasi";
    $response['product_id'] = $product_id;

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
    error_log("ERROR (Aksesoris): " . $e->getMessage());
}

header('Content-Type: application/json');
echo json_encode($response);
?>