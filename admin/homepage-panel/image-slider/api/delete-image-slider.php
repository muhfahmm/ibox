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
    if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
        throw new Exception('Invalid request method');
    }

    if (!isset($_GET['id'])) {
        throw new Exception('ID slider tidak ditemukan');
    }

    $id = intval($_GET['id']);
    
    // Ambil data slider untuk mendapatkan nama file gambar
    $query = "SELECT * FROM admin_homepage_slider WHERE id = $id";
    $result = mysqli_query($db, $query);
    
    if (!$result || mysqli_num_rows($result) === 0) {
        throw new Exception('Slider tidak ditemukan');
    }
    
    $slider = mysqli_fetch_assoc($result);
    $image_file = $slider['gambar'];
    
    // Hapus file gambar jika ada
    if ($image_file) {
        $upload_dir = '../../uploads/slider/';
        $image_path = $upload_dir . $image_file;
        
        if (file_exists($image_path)) {
            unlink($image_path);
        }
    }
    
    // Hapus dari database
    $query = "DELETE FROM admin_homepage_slider WHERE id = $id";
    
    if (mysqli_query($db, $query)) {
        $response['success'] = true;
        $response['message'] = 'Slider berhasil dihapus';
    } else {
        throw new Exception('Gagal menghapus dari database: ' . mysqli_error($db));
    }

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
    error_log('Delete Slider Error: ' . $e->getMessage());
}

header('Content-Type: application/json');
echo json_encode($response);
?>