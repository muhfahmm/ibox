<?php
session_start();

// Jika belum login, redirect ke login
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ../../../auth/login.php?error=not_logged_in');
    exit();
}

// Koneksi database
require_once '../../../db.php';

// Cek apakah form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari form
    $nama_kategori = trim($_POST['nama_kategori'] ?? '');
    
    // Validasi input
    if (empty($nama_kategori)) {
        header('Location: ../kategori.php?error=empty');
        exit();
    }
    
    // Cek apakah kategori sudah ada
    $check_query = "SELECT id FROM admin_kategori_model WHERE nama_kategori_model = ?";
    $stmt = mysqli_prepare($db, $check_query);
    mysqli_stmt_bind_param($stmt, "s", $nama_kategori);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    
    if (mysqli_stmt_num_rows($stmt) > 0) {
        mysqli_stmt_close($stmt);
        header('Location: ../kategori.php?error=exists');
        exit();
    }
    mysqli_stmt_close($stmt);
    
    // Insert kategori baru
    $insert_query = "INSERT INTO admin_kategori_model (nama_kategori_model) VALUES (?)";
    $stmt = mysqli_prepare($db, $insert_query);
    mysqli_stmt_bind_param($stmt, "s", $nama_kategori);
    
    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        header('Location: ../kategori.php?success=added');
        exit();
    } else {
        mysqli_stmt_close($stmt);
        header('Location: ../kategori.php?error=failed');
        exit();
    }
} else {
    // Jika bukan POST request, redirect ke halaman kategori
    header('Location: ../kategori.php');
    exit();
}
?>
