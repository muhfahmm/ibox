<?php
session_start();
require_once '../../../db.php';

header('Content-Type: application/json');

// Cek login
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

$response = ['success' => false, 'message' => ''];

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'DELETE') {
        // Allow GET for testing if needed, but preferably POST/DELETE
        if (!isset($_GET['id'])) {
           throw new Exception('Invalid request method'); 
        }
        $product_id = $_GET['id'];
    } else {
        // Get ID from body or POST
        $data = json_decode(file_get_contents('php://input'), true);
        $product_id = $data['id'] ?? $_POST['id'] ?? null;
    }

    if (!$product_id) {
        throw new Exception('Product ID is required');
    }

    $product_id = mysqli_real_escape_string($db, $product_id);

    // Ambil data gambar untuk dihapus filenya
    $query_images = "SELECT * FROM admin_produk_iphone_gambar WHERE produk_id = '$product_id'";
    $result_images = mysqli_query($db, $query_images);

    $upload_dir = '../../../uploads/';
    $deleted_files = 0;

    while ($image = mysqli_fetch_assoc($result_images)) {
        // Hapus thumbnail
        if (!empty($image['foto_thumbnail'])) {
            $thumb_path = $upload_dir . $image['foto_thumbnail'];
            if (file_exists($thumb_path)) {
                unlink($thumb_path);
                $deleted_files++;
            }
        }
        
        // Hapus foto produk (array JSON)
        if (!empty($image['foto_produk'])) {
            $product_images = json_decode($image['foto_produk'], true);
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

    // Hapus data produk (Cascade handle tables)
    $delete_product = "DELETE FROM admin_produk_iphone WHERE id = '$product_id'";
    
    if (mysqli_query($db, $delete_product)) {
        $response['success'] = true;
        $response['message'] = "Produk berhasil dihapus.";
    } else {
        throw new Exception("Database error: " . mysqli_error($db));
    }

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
?>
