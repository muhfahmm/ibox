<?php
session_start();
require_once '../../../db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['admin_logged_in'])) {
    echo json_encode(['success'=>false, 'message'=>'Not logged in']); exit;
}

try {
    $data = json_decode(file_get_contents('php://input'), true);
    $id = $data['id'] ?? $_POST['id'] ?? $_GET['id'] ?? null;
    
    if (!$id) throw new Exception("ID Required");
    $id = mysqli_real_escape_string($db, $id);

    // Delete files
    $q = mysqli_query($db, "SELECT * FROM admin_produk_airtag_gambar WHERE produk_id='$id'");
    $up = '../../../uploads/';
    
    while($row = mysqli_fetch_assoc($q)) {
        if($row['foto_thumbnail'] && file_exists($up.$row['foto_thumbnail'])) unlink($up.$row['foto_thumbnail']);
        $imgs = json_decode($row['foto_produk'], true);
        if(is_array($imgs)) {
            foreach($imgs as $i) if(file_exists($up.$i)) unlink($up.$i);
        }
    }

    if(mysqli_query($db, "DELETE FROM admin_produk_airtag WHERE id='$id'")) {
        echo json_encode(['success'=>true, 'message'=>'Deleted']);
    } else {
        throw new Exception(mysqli_error($db));
    }
} catch (Exception $e) {
    echo json_encode(['success'=>false, 'message'=>$e->getMessage()]);
}
?>
