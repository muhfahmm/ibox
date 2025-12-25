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
    $id = intval($_POST['id'] ?? 0);
    $nama_kategori = trim($_POST['nama_kategori'] ?? '');
    
    // Validasi input
    if (empty($nama_kategori)) {
        header('Location: ../edit-category.php?id=' . $id . '&error=empty');
        exit();
    }
    
    if ($id <= 0) {
        header('Location: ../kategori.php?error=not_found');
        exit();
    }
    
    // Cek apakah kategori dengan nama yang sama sudah ada (kecuali kategori yang sedang diedit)
    $check_query = "SELECT id FROM admin_kategori_model WHERE nama_kategori_model = ? AND id != ?";
    $stmt = mysqli_prepare($db, $check_query);
    mysqli_stmt_bind_param($stmt, "si", $nama_kategori, $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    
    if (mysqli_stmt_num_rows($stmt) > 0) {
        mysqli_stmt_close($stmt);
        header('Location: ../edit-category.php?id=' . $id . '&error=exists');
        exit();
    }
    mysqli_stmt_close($stmt);
    
    // Update kategori
    $update_query = "UPDATE admin_kategori_model SET nama_kategori_model = ? WHERE id = ?";
    $stmt = mysqli_prepare($db, $update_query);
    mysqli_stmt_bind_param($stmt, "si", $nama_kategori, $id);
    
    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        header('Location: ../kategori.php?success=updated');
        exit();
    } else {
        mysqli_stmt_close($stmt);
        header('Location: ../edit-category.php?id=' . $id . '&error=failed');
        exit();
    }
} else {
    // Jika bukan POST request, redirect ke halaman kategori
    header('Location: ../kategori.php');
    exit();
}
?>
