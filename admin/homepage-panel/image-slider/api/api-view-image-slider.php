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
    if (!isset($_GET['id'])) {
        throw new Exception('ID slider tidak ditemukan');
    }

    $id = intval($_GET['id']);
    
    $query = "SELECT * FROM admin_homepage_slider WHERE id = $id";
    $result = mysqli_query($db, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $slider = mysqli_fetch_assoc($result);
        
        // Decode HTML entities
        if ($slider['judul']) {
            $slider['judul'] = htmlspecialchars_decode($slider['judul'], ENT_QUOTES);
        }
        if ($slider['deskripsi']) {
            $slider['deskripsi'] = htmlspecialchars_decode($slider['deskripsi'], ENT_QUOTES);
        }
        if ($slider['link']) {
            $slider['link'] = htmlspecialchars_decode($slider['link'], ENT_QUOTES);
        }
        
        // Format tanggal untuk tampilan
        $slider['created_at'] = date('d/m/Y H:i', strtotime($slider['created_at']));
        $slider['updated_at'] = date('d/m/Y H:i', strtotime($slider['updated_at']));
        
        $response['success'] = true;
        $response['slider'] = $slider;
    } else {
        throw new Exception('Slider tidak ditemukan');
    }

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
    error_log('View Slider Error: ' . $e->getMessage());
}

header('Content-Type: application/json');
echo json_encode($response);
?>