<?php
header('Content-Type: application/json');
session_start();
require_once '../../../db.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tipe_produk = mysqli_real_escape_string($db, $_POST['tipe_produk']);
    $produk_id = mysqli_real_escape_string($db, $_POST['produk_id']);
    $label_promo = mysqli_real_escape_string($db, $_POST['label_promo']);
    $urutan = isset($_POST['urutan']) ? (int)$_POST['urutan'] : 0;

    // Cek apakah sudah ada
    $check_query = "SELECT * FROM home_trade_in 
                    WHERE produk_id = '$produk_id' AND tipe_produk = '$tipe_produk'";
    $check_result = mysqli_query($db, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        echo json_encode(['success' => false, 'message' => 'Produk sudah ada dalam daftar trade-in']);
        exit();
    }

    $insert_query = "INSERT INTO home_trade_in 
                    (produk_id, tipe_produk, label_promo, urutan) 
                    VALUES ('$produk_id', '$tipe_produk', '$label_promo', '$urutan')";
    
    if (mysqli_query($db, $insert_query)) {
        echo json_encode(['success' => true, 'message' => 'Produk trade-in berhasil ditambahkan']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Kesalahan database: ' . mysqli_error($db)]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
