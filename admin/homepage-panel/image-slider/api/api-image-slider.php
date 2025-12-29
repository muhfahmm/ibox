<?php
session_start();
require_once '../../db.php';
header('Content-Type: application/json');

// Cek apakah request dari AJAX
if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
    http_response_code(403);
    echo json_encode(['error' => 'Access denied']);
    exit();
}

// Cek apakah admin sudah login
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

// Get action parameter
$action = isset($_GET['action']) ? $_GET['action'] : '';

switch ($action) {
    case 'get_sliders':
        getSliders();
        break;
    
    case 'get_slider':
        getSlider();
        break;
    
    case 'add_slider':
        addSlider();
        break;
    
    case 'update_slider':
        updateSlider();
        break;
    
    case 'delete_slider':
        deleteSlider();
        break;
    
    case 'upload_image':
        uploadImage();
        break;
    
    default:
        http_response_code(400);
        echo json_encode(['error' => 'Invalid action']);
        break;
}

function getSliders() {
    global $db;
    
    $query = "SELECT * FROM home_image_slider ORDER BY id DESC";
    $result = mysqli_query($db, $query);
    
    $sliders = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $row['image_url'] = '../../../uploads/slider/' . $row['gambar_produk'];
        $row['image_exists'] = file_exists('../../../uploads/slider/' . $row['gambar_produk']);
        $sliders[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'data' => $sliders,
        'total' => count($sliders)
    ]);
}

function getSlider() {
    global $db;
    
    if (!isset($_GET['id'])) {
        http_response_code(400);
        echo json_encode(['error' => 'ID is required']);
        return;
    }
    
    $id = intval($_GET['id']);
    $query = "SELECT * FROM home_image_slider WHERE id = $id";
    $result = mysqli_query($db, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $slider = mysqli_fetch_assoc($result);
        $slider['image_url'] = '../../../uploads/slider/' . $slider['gambar_produk'];
        $slider['image_exists'] = file_exists('../../../uploads/slider/' . $slider['gambar_produk']);
        
        echo json_encode([
            'success' => true,
            'data' => $slider
        ]);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Slider not found']);
    }
}

function addSlider() {
    global $db;
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        return;
    }
    
    // Get data from request
    $nama_produk = mysqli_real_escape_string($db, $_POST['nama_produk'] ?? '');
    $deskripsi_produk = mysqli_real_escape_string($db, $_POST['deskripsi_produk'] ?? '');
    
    if (empty($nama_produk)) {
        http_response_code(400);
        echo json_encode(['error' => 'Nama produk is required']);
        return;
    }
    
    // Handle file upload
    $gambar_produk = '';
    if (isset($_FILES['gambar_produk']) && $_FILES['gambar_produk']['error'] === 0) {
        $file_name = basename($_FILES["gambar_produk"]["name"]);
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        if (in_array($file_ext, $allowed_ext)) {
            $new_filename = uniqid() . '_' . time() . '.' . $file_ext;
            $target_dir = '../../../uploads/slider/';
            
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            
            $target_file = $target_dir . $new_filename;
            
            if (move_uploaded_file($_FILES["gambar_produk"]["tmp_name"], $target_file)) {
                $gambar_produk = $new_filename;
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to upload image']);
                return;
            }
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid file format']);
            return;
        }
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Image is required']);
        return;
    }
    
    // Insert into database
    $query = "INSERT INTO home_image_slider (gambar_produk, nama_produk, deskripsi_produk) 
              VALUES ('$gambar_produk', '$nama_produk', '$deskripsi_produk')";
    
    if (mysqli_query($db, $query)) {
        $id = mysqli_insert_id($db);
        
        echo json_encode([
            'success' => true,
            'message' => 'Slider added successfully',
            'id' => $id
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Database error: ' . mysqli_error($db)]);
    }
}

function updateSlider() {
    global $db;
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        return;
    }
    
    if (!isset($_POST['id'])) {
        http_response_code(400);
        echo json_encode(['error' => 'ID is required']);
        return;
    }
    
    $id = intval($_POST['id']);
    $nama_produk = mysqli_real_escape_string($db, $_POST['nama_produk'] ?? '');
    $deskripsi_produk = mysqli_real_escape_string($db, $_POST['deskripsi_produk'] ?? '');
    
    if (empty($nama_produk)) {
        http_response_code(400);
        echo json_encode(['error' => 'Nama produk is required']);
        return;
    }
    
    // Get current image
    $query = "SELECT gambar_produk FROM home_image_slider WHERE id = $id";
    $result = mysqli_query($db, $query);
    
    if (!$result || mysqli_num_rows($result) === 0) {
        http_response_code(404);
        echo json_encode(['error' => 'Slider not found']);
        return;
    }
    
    $current = mysqli_fetch_assoc($result);
    $gambar_produk = $current['gambar_produk'];
    
    // Handle new file upload if provided
    if (isset($_FILES['gambar_produk']) && $_FILES['gambar_produk']['error'] === 0) {
        $file_name = basename($_FILES["gambar_produk"]["name"]);
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        if (in_array($file_ext, $allowed_ext)) {
            $new_filename = uniqid() . '_' . time() . '.' . $file_ext;
            $target_dir = '../../../uploads/slider/';
            $target_file = $target_dir . $new_filename;
            
            if (move_uploaded_file($_FILES["gambar_produk"]["tmp_name"], $target_file)) {
                // Delete old image
                if ($gambar_produk && file_exists($target_dir . $gambar_produk)) {
                    unlink($target_dir . $gambar_produk);
                }
                $gambar_produk = $new_filename;
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to upload new image']);
                return;
            }
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid file format']);
            return;
        }
    }
    
    // Update database
    $query = "UPDATE home_image_slider 
              SET gambar_produk = '$gambar_produk', 
                  nama_produk = '$nama_produk', 
                  deskripsi_produk = '$deskripsi_produk'
              WHERE id = $id";
    
    if (mysqli_query($db, $query)) {
        echo json_encode([
            'success' => true,
            'message' => 'Slider updated successfully'
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Database error: ' . mysqli_error($db)]);
    }
}

function deleteSlider() {
    global $db;
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'DELETE') {
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        return;
    }
    
    $data = json_decode(file_get_contents('php://input'), true);
    $id = isset($data['id']) ? intval($data['id']) : (isset($_GET['id']) ? intval($_GET['id']) : 0);
    
    if ($id <= 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Valid ID is required']);
        return;
    }
    
    // Get image filename
    $query = "SELECT gambar_produk FROM home_image_slider WHERE id = $id";
    $result = mysqli_query($db, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $slider = mysqli_fetch_assoc($result);
        $image_file = $slider['gambar_produk'];
        
        // Delete image file
        if ($image_file) {
            $target_dir = '../../../uploads/slider/';
            $image_path = $target_dir . $image_file;
            
            if (file_exists($image_path)) {
                unlink($image_path);
            }
        }
        
        // Delete from database
        $delete_query = "DELETE FROM home_image_slider WHERE id = $id";
        
        if (mysqli_query($db, $delete_query)) {
            echo json_encode([
                'success' => true,
                'message' => 'Slider deleted successfully'
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Database error: ' . mysqli_error($db)]);
        }
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Slider not found']);
    }
}

function uploadImage() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        return;
    }
    
    if (!isset($_FILES['image']) || $_FILES['image']['error'] !== 0) {
        http_response_code(400);
        echo json_encode(['error' => 'No image uploaded']);
        return;
    }
    
    $file = $_FILES['image'];
    $file_name = basename($file["name"]);
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    $allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    
    if (!in_array($file_ext, $allowed_ext)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid file format']);
        return;
    }
    
    // Check file size (max 5MB)
    if ($file['size'] > 5 * 1024 * 1024) {
        http_response_code(400);
        echo json_encode(['error' => 'File too large. Max 5MB']);
        return;
    }
    
    $new_filename = uniqid() . '_' . time() . '.' . $file_ext;
    $target_dir = '../../../uploads/slider/';
    
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $target_file = $target_dir . $new_filename;
    
    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        echo json_encode([
            'success' => true,
            'filename' => $new_filename,
            'url' => '../../../uploads/slider/' . $new_filename,
            'message' => 'Image uploaded successfully'
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to upload image']);
    }
}
?>