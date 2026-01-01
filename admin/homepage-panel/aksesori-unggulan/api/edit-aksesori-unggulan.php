<?php
header('Content-Type: application/json');
session_start();
require_once '../../../db.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $tipe_produk = mysqli_real_escape_string($db, $_POST['tipe_produk']);
    $produk_id = mysqli_real_escape_string($db, $_POST['produk_id']);
    $label = mysqli_real_escape_string($db, $_POST['label']);
    $urutan = isset($_POST['urutan']) ? (int)$_POST['urutan'] : 0;

    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'ID tidak valid']);
        exit();
    }

    // Cek apakah produk sudah ada di baris lain
    $check_query = "SELECT * FROM home_aksesori 
                    WHERE produk_id = '$produk_id' AND tipe_produk = '$tipe_produk' AND id != $id";
    $check_result = mysqli_query($db, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        echo json_encode(['success' => false, 'message' => 'Produk ini sudah ada dalam daftar aksesori unggulan']);
        exit();
    }

    $update_query = "UPDATE home_aksesori SET 
                    produk_id = '$produk_id', 
                    tipe_produk = '$tipe_produk', 
                    label = '$label', 
                    urutan = '$urutan' 
                    WHERE id = $id";
    
    if (mysqli_query($db, $update_query)) {
        echo json_encode(['success' => true, 'message' => 'Aksesori unggulan berhasil diperbarui']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Kesalahan database: ' . mysqli_error($db)]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
