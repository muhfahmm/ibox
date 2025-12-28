<?php
session_start();
require_once '../../../db.php';

// Cek login
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

$response = ['success' => false, 'message' => ''];

try {
    // GET request untuk mengambil data slider
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
        $id = intval($_GET['id']);
        
        $query = "SELECT * FROM admin_homepage_slider WHERE id = $id";
        $result = mysqli_query($db, $query);
        
        if ($result && mysqli_num_rows($result) > 0) {
            $slider = mysqli_fetch_assoc($result); // PERBAIKAN: $result bukan $slider
            
            // Decode HTML entities jika ada
            if ($slider['judul']) {
                $slider['judul'] = htmlspecialchars_decode($slider['judul'], ENT_QUOTES);
            }
            if ($slider['deskripsi']) {
                $slider['deskripsi'] = htmlspecialchars_decode($slider['deskripsi'], ENT_QUOTES);
            }
            if ($slider['link']) {
                $slider['link'] = htmlspecialchars_decode($slider['link'], ENT_QUOTES);
            }
            
            $response['success'] = true;
            $response['slider'] = $slider;
        } else {
            throw new Exception('Slider tidak ditemukan');
        }
    }
    // POST request untuk update data slider
    elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!isset($_POST['id'])) {
            throw new Exception('ID slider tidak ditemukan');
        }

        $id = intval($_POST['id']);
        
        // Ambil data slider saat ini
        $query = "SELECT * FROM admin_homepage_slider WHERE id = $id";
        $result = mysqli_query($db, $query);
        
        if (!$result || mysqli_num_rows($result) === 0) {
            throw new Exception('Slider tidak ditemukan');
        }
        
        $current_slider = mysqli_fetch_assoc($result);
        $current_image = $current_slider['gambar'];

        // Handle image upload
        $new_image = $current_image;
        if (isset($_FILES['gambar']) && $_FILES['gambar']['size'] > 0) {
            $upload_dir = '../../uploads/slider/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $max_size = 5 * 1024 * 1024; // 5MB

            $file = $_FILES['gambar'];
            
            // Validasi file
            if (!in_array($file['type'], $allowed_types)) {
                throw new Exception('Format file tidak didukung. Gunakan JPG, PNG, GIF, atau WebP');
            }

            if ($file['size'] > $max_size) {
                throw new Exception('Ukuran file terlalu besar. Maksimal 5MB');
            }

            // Generate nama file unik
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = uniqid() . '_' . time() . '.' . $extension;

            // Upload file baru
            if (move_uploaded_file($file['tmp_name'], $upload_dir . $filename)) {
                // Hapus file lama jika ada
                if ($current_image && file_exists($upload_dir . $current_image)) {
                    unlink($upload_dir . $current_image);
                }
                $new_image = $filename;
            } else {
                throw new Exception('Gagal mengupload gambar baru');
            }
        }

        // Prepare data untuk update
        $judul = mysqli_real_escape_string($db, $_POST['judul'] ?? '');
        $deskripsi = mysqli_real_escape_string($db, $_POST['deskripsi'] ?? '');
        $link = mysqli_real_escape_string($db, $_POST['link'] ?? '');
        $urutan = intval($_POST['urutan'] ?? 1);
        $status = mysqli_real_escape_string($db, $_POST['status'] ?? 'active');

        // Update database
        $query = "UPDATE admin_homepage_slider SET 
                  judul = '$judul',
                  deskripsi = '$deskripsi',
                  gambar = '$new_image',
                  link = '$link',
                  urutan = $urutan,
                  status = '$status',
                  updated_at = NOW()
                  WHERE id = $id";

        if (mysqli_query($db, $query)) {
            $response['success'] = true;
            $response['message'] = 'Slider berhasil diperbarui';
        } else {
            throw new Exception('Gagal update database: ' . mysqli_error($db));
        }
    } else {
        throw new Exception('Invalid request method');
    }

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
    error_log('Edit Slider Error: ' . $e->getMessage());
}

header('Content-Type: application/json');
echo json_encode($response);
?>