<?php
session_start();
require_once '../../../db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tipe_produk = mysqli_real_escape_string($db, $_POST['tipe_produk']);
    $produk_id = mysqli_real_escape_string($db, $_POST['produk_id']);
    $label = mysqli_real_escape_string($db, $_POST['label']);
    $urutan = isset($_POST['urutan']) ? (int)$_POST['urutan'] : 0;

    // Validasi input
    if (empty($tipe_produk) || empty($produk_id) || empty($label)) {
        echo json_encode(['success' => false, 'message' => 'Semua field harus diisi!']);
        exit();
    }

    // Cek apakah sudah ada
    $check_query = "SELECT * FROM home_produk_populer 
                    WHERE produk_id = '$produk_id' AND tipe_produk = '$tipe_produk'";
    $check_result = mysqli_query($db, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        echo json_encode(['success' => false, 'message' => 'Produk ini sudah ada di daftar Produk Populer!']);
        exit();
    }

    // Insert data
    $insert_query = "INSERT INTO home_produk_populer 
                    (produk_id, tipe_produk, label, urutan) 
                    VALUES ('$produk_id', '$tipe_produk', '$label', '$urutan')";
    
    if (mysqli_query($db, $insert_query)) {
        echo json_encode(['success' => true, 'message' => 'Produk Populer berhasil ditambahkan!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal menambahkan produk: ' . mysqli_error($db)]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
