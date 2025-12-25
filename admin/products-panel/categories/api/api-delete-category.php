<?php
session_start();

// Jika belum login, redirect ke login
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ../../../auth/login.php?error=not_logged_in');
    exit();
}

// Koneksi database
require_once '../../../db.php';

// Cek apakah ada parameter id
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // Validasi ID
    if ($id <= 0) {
        header('Location: ../kategori.php?error=not_found');
        exit();
    }
    
    // Cek apakah kategori ada
    $check_query = "SELECT id FROM admin_kategori_model WHERE id = ?";
    $stmt = mysqli_prepare($db, $check_query);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    
    if (mysqli_stmt_num_rows($stmt) === 0) {
        mysqli_stmt_close($stmt);
        header('Location: ../kategori.php?error=not_found');
        exit();
    }
    mysqli_stmt_close($stmt);
    
    // Hapus kategori
    $delete_query = "DELETE FROM admin_kategori_model WHERE id = ?";
    $stmt = mysqli_prepare($db, $delete_query);
    mysqli_stmt_bind_param($stmt, "i", $id);
    
    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        header('Location: ../kategori.php?success=deleted');
        exit();
    } else {
        mysqli_stmt_close($stmt);
        header('Location: ../kategori.php?error=failed');
        exit();
    }
} else {
    // Jika tidak ada parameter id, redirect ke halaman kategori
    header('Location: ../kategori.php');
    exit();
}
?>
