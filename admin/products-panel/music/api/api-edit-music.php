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
    $required_fields = ['product_id', 'nama_produk', 'kategori'];
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            throw new Exception("Field $field harus diisi");
        }
    }

    $product_id = mysqli_real_escape_string($db, $_POST['product_id']);
    
    // Update data produk utama (tanpa updated_at)
    $nama_produk = mysqli_real_escape_string($db, $_POST['nama_produk']);
    
    // Handle deskripsi produk - prevent '0' from being saved
    $deskripsi_produk = trim($_POST['deskripsi_produk'] ?? '');
    // If the value is '0' or empty, save as empty string instead of '0'
    if ($deskripsi_produk === '0' || $deskripsi_produk === 0) {
        $deskripsi_produk = '';
    }
    $deskripsi_produk = mysqli_real_escape_string($db, $deskripsi_produk);
    
    $kategori = mysqli_real_escape_string($db, $_POST['kategori']);

    $query = "UPDATE admin_produk_music SET 
              nama_produk = '$nama_produk', 
              deskripsi_produk = '$deskripsi_produk',
              kategori = '$kategori'
              WHERE id = '$product_id'";
    
    if (!mysqli_query($db, $query)) {
        throw new Exception("Gagal update data produk: " . mysqli_error($db));
    }

    // Proses data warna dengan gambar
    $warna_data = $_POST['warna'] ?? [];
    $updated_colors = [];
    
    foreach ($warna_data as $color_index => $color_info) {
        $warna_nama = mysqli_real_escape_string($db, $color_info['nama'] ?? '');
        $hex_code = mysqli_real_escape_string($db, $color_info['hex_code'] ?? '');
        
        if (empty($warna_nama)) {
            continue;
        }
        
        $updated_colors[] = $warna_nama;
        
        // Cek apakah warna ini sudah ada (existing)
        $is_existing = isset($color_info['existing']);
        $old_thumbnail = $color_info['old_thumbnail'] ?? null;
        
        // Proses upload thumbnail baru jika ada
        $thumbnail_name = $old_thumbnail;
        if (isset($_FILES['warna']['name'][$color_index]['thumbnail']) && 
            $_FILES['warna']['error'][$color_index]['thumbnail'] == 0) {
            
            $thumbnail = [
                'name' => $_FILES['warna']['name'][$color_index]['thumbnail'],
                'type' => $_FILES['warna']['type'][$color_index]['thumbnail'],
                'tmp_name' => $_FILES['warna']['tmp_name'][$color_index]['thumbnail'],
                'size' => $_FILES['warna']['size'][$color_index]['thumbnail']
            ];
            
            if (in_array($thumbnail['type'], $allowed_types) && $thumbnail['size'] <= $max_size) {
                // Hapus thumbnail lama jika ada
                if ($old_thumbnail && file_exists($upload_dir . $old_thumbnail)) {
                    unlink($upload_dir . $old_thumbnail);
                }
                
                $thumbnail_name = generateFileName($thumbnail['name']);
                $thumbnail_path = $upload_dir . $thumbnail_name;
                
                if (!move_uploaded_file($thumbnail['tmp_name'], $thumbnail_path)) {
                    throw new Exception("Gagal upload thumbnail untuk warna: $warna_nama");
                }
            } else {
                throw new Exception("Format atau ukuran thumbnail untuk warna $warna_nama tidak valid");
            }
        } elseif (!$is_existing && !$thumbnail_name) {
            throw new Exception("Thumbnail untuk warna baru $warna_nama harus diupload");
        }
        
        // Proses upload foto produk baru jika ada
        $product_images_json = '[]';
        if ($is_existing) {
            // Ambil foto produk yang ada
            $query_existing = "SELECT foto_produk FROM admin_produk_music_gambar 
                              WHERE produk_id = '$product_id' AND warna = '$warna_nama'";
            $result_existing = mysqli_query($db, $query_existing);
            if ($row = mysqli_fetch_assoc($result_existing)) {
                $product_images_json = $row['foto_produk'];
            }
        }
        
        if (isset($_FILES['warna']['name'][$color_index]['product_images'])) {
            $new_product_images = json_decode($product_images_json, true) ?: [];
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
                            $new_product_images[] = $image_name;
                        }
                    }
                }
            }
            
            $product_images_json = json_encode($new_product_images);
        }
        
        // Update atau insert data gambar untuk warna
        if ($is_existing) {
            // Update existing
            $query_gambar = "UPDATE admin_produk_music_gambar 
                            SET hex_code = '$hex_code',
                                foto_thumbnail = '$thumbnail_name', 
                                foto_produk = '$product_images_json'
                            WHERE produk_id = '$product_id' AND warna = '$warna_nama'";
        } else {
            // Insert new
            $query_gambar = "INSERT INTO admin_produk_music_gambar 
                            (produk_id, warna, hex_code, foto_thumbnail, foto_produk) 
                            VALUES ('$product_id', '$warna_nama', '$hex_code', '$thumbnail_name', '$product_images_json')";
        }
        
        if (!mysqli_query($db, $query_gambar)) {
            throw new Exception("Gagal menyimpan gambar untuk warna: $warna_nama");
        }
    }
    
    // Hapus warna yang tidak lagi digunakan
    $existing_colors_query = "SELECT warna FROM admin_produk_music_gambar WHERE produk_id = '$product_id'";
    $existing_colors_result = mysqli_query($db, $existing_colors_query);
    $existing_colors = [];
    while ($row = mysqli_fetch_assoc($existing_colors_result)) {
        $existing_colors[] = $row['warna'];
    }
    
    $colors_to_delete = array_diff($existing_colors, $updated_colors);
    foreach ($colors_to_delete as $color_to_delete) {
        // Hapus file gambar
        $color_images_query = "SELECT foto_thumbnail, foto_produk FROM admin_produk_music_gambar 
                               WHERE produk_id = '$product_id' AND warna = '$color_to_delete'";
        $color_images_result = mysqli_query($db, $color_images_query);
        if ($color_images = mysqli_fetch_assoc($color_images_result)) {
            // Hapus thumbnail
            if (!empty($color_images['foto_thumbnail']) && file_exists($upload_dir . $color_images['foto_thumbnail'])) {
                unlink($upload_dir . $color_images['foto_thumbnail']);
            }
            
            // Hapus foto produk
            if (!empty($color_images['foto_produk'])) {
                $product_images = json_decode($color_images['foto_produk'], true);
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
        
        // Hapus dari database
        $delete_color_query = "DELETE FROM admin_produk_music_gambar 
                               WHERE produk_id = '$product_id' AND warna = '$color_to_delete'";
        mysqli_query($db, $delete_color_query);
    }
    
    // Hapus semua kombinasi lama
    $delete_combinations = "DELETE FROM admin_produk_music_kombinasi WHERE produk_id = '$product_id'";
    mysqli_query($db, $delete_combinations);
    
    // Proses kombinasi dan stok baru
    $combinations = $_POST['combinations'] ?? [];
    $combination_count = 0;
    
    foreach ($combinations as $combination_id => $combination_data) {
        $warna = mysqli_real_escape_string($db, $combination_data['warna'] ?? '');
        $tipe = mysqli_real_escape_string($db, $combination_data['tipe'] ?? '');
        $konektivitas = mysqli_real_escape_string($db, $combination_data['konektivitas'] ?? '');
        $harga = mysqli_real_escape_string($db, $combination_data['harga'] ?? 0);
        $harga_diskon = !empty($combination_data['harga_diskon']) ? 
                       mysqli_real_escape_string($db, $combination_data['harga_diskon']) : NULL;
        $jumlah_stok = mysqli_real_escape_string($db, $combination_data['jumlah_stok'] ?? 0);
        
        // Tentukan status stok berdasarkan jumlah stok
        $status_stok = ($jumlah_stok > 0) ? 'tersedia' : 'habis';
        
        // Validasi data kombinasi
        if (empty($warna) || empty($tipe) || empty($konektivitas)) {
            continue;
        }
        
        // Validasi harga
        if ($harga <= 0) {
            continue;
        }
        
        // Validasi diskon
        if ($harga_diskon && $harga_diskon >= $harga) {
            throw new Exception("Harga diskon tidak boleh lebih besar atau sama dengan harga normal");
        }
        
        // Simpan kombinasi ke database
        $query_kombinasi = "INSERT INTO admin_produk_music_kombinasi 
                           (produk_id, warna, tipe, konektivitas, harga, harga_diskon, jumlah_stok, status_stok) 
                           VALUES ('$product_id', '$warna', '$tipe', '$konektivitas', 
                                   '$harga', " . ($harga_diskon ? "'$harga_diskon'" : "NULL") . ", 
                                   '$jumlah_stok', '$status_stok')";
        
        if (mysqli_query($db, $query_kombinasi)) {
            $combination_count++;
        } else {
            throw new Exception("Gagal menyimpan kombinasi: " . mysqli_error($db));
        }
    }
    
    if ($combination_count === 0) {
        throw new Exception("Tidak ada kombinasi yang berhasil disimpan");
    }

    $response['success'] = true;
    $response['message'] = "Produk Music berhasil diperbarui dengan $combination_count kombinasi";
    $response['product_id'] = $product_id;

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

header('Content-Type: application/json');
echo json_encode($response);
?>