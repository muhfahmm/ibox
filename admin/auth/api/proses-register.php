<?php
session_start();
require_once '../../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Validasi input
    if (empty($username) || empty($password) || empty($confirm_password)) {
        header('Location: ../register.php?error=empty');
        exit();
    }
    
    // Validasi panjang password
    if (strlen($password) < 6) {
        header('Location: ../register.php?error=short');
        exit();
    }
    
    // Validasi kesamaan password
    if ($password !== $confirm_password) {
        header('Location: ../register.php?error=password_mismatch');
        exit();
    }
    
    // Escape input
    $username = escape($username);
    
    // Cek apakah username sudah ada
    $check_query = "SELECT id FROM admin_autentikasi WHERE username = '$username'";
    $check_result = mysqli_query($db, $check_query);
    
    if ($check_result && mysqli_num_rows($check_result) > 0) {
        header('Location: ../register.php?error=username_exists');
        exit();
    }
    
    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert admin baru
    $insert_query = "INSERT INTO admin_autentikasi (username, password) VALUES ('$username', '$hashed_password')";
    
    if (mysqli_query($db, $insert_query)) {
        header('Location: ../register.php?success=true');
        exit();
    } else {
        header('Location: ../register.php?error=unknown');
        exit();
    }
} else {
    header('Location: ../register.php');
    exit();
}
?>