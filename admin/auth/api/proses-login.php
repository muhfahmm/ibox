<?php
session_start();
require_once '../../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // Validasi input
    if (empty($username) || empty($password)) {
        header('Location: ../login.php?error=empty');
        exit();
    }
    
    // Escape input
    $username = escape($username);
    
    // Cari admin di database
    $query = "SELECT * FROM admin_autentikasi WHERE username = '$username' LIMIT 1";
    $result = mysqli_query($db, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $admin = mysqli_fetch_assoc($result);
        
        // Verifikasi password
        if (password_verify($password, $admin['password'])) {
            // Set session
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            
            // Redirect ke dashboard
            header('Location: ../../index.php');
            exit();
        } else {
            header('Location: ../login.php?error=invalid');
            exit();
        }
    } else {
        header('Location: ../login.php?error=invalid');
        exit();
    }
} else {
    header('Location: ../login.php');
    exit();
}
?>