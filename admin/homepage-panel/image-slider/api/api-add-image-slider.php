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
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    // Validasi input
    if (empty($_FILES['gambar']['name'])) {
        throw new Exception('Gambar slider harus diupload');
    }

    // Konfigurasi upload
    $upload_dir = '../../uploads/slider/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $max_size = 5 * 1024 * 1024; // 5MB

    // Validasi file
    $file = $_FILES['gambar'];
    if (!in_array($file['type'], $allowed_types)) {
        throw new Exception('Format file tidak didukung. Gunakan JPG, PNG, GIF, atau WebP');
    }

    if ($file['size'] > $max_size) {
        throw new Exception('Ukuran file terlalu besar. Maksimal 5MB');
    }

    // Generate nama file unik
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '_' . time() . '.' . $extension;

    // Upload file
    if (!move_uploaded_file($file['tmp_name'], $upload_dir . $filename)) {
        throw new Exception('Gagal mengupload gambar');
    }

    // Prepare data untuk database
    $judul = mysqli_real_escape_string($db, $_POST['judul'] ?? '');
    $deskripsi = mysqli_real_escape_string($db, $_POST['deskripsi'] ?? '');
    $link = mysqli_real_escape_string($db, $_POST['link'] ?? '');
    $urutan = intval($_POST['urutan'] ?? 1);
    $status = mysqli_real_escape_string($db, $_POST['status'] ?? 'active');

    // Insert ke database
    $query = "INSERT INTO admin_homepage_slider 
              (judul, deskripsi, gambar, link, urutan, status, created_at, updated_at) 
              VALUES ('$judul', '$deskripsi', '$filename', '$link', $urutan, '$status', NOW(), NOW())";

    if (mysqli_query($db, $query)) {
        $response['success'] = true;
        $response['message'] = 'Slider berhasil ditambahkan';
    } else {
        // Hapus file yang sudah diupload jika gagal insert ke database
        unlink($upload_dir . $filename);
        throw new Exception('Gagal menyimpan data ke database: ' . mysqli_error($db));
    }

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
    error_log('Add Slider Error: ' . $e->getMessage());
}

header('Content-Type: application/json');
echo json_encode($response);
?>