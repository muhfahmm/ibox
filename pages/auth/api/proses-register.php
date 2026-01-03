<?php
require '../../../db/db.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstname = trim($_POST['firstname']);
    $lastname = trim($_POST['lastname']);
    $no_hp = trim($_POST['no_hp']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Basic Validation
    if (empty($firstname) || empty($no_hp) || empty($email) || empty($password)) {
        $_SESSION['notification'] = [
            'type' => 'error',
            'message' => 'Semua field wajib diisi!'
        ];
        header("Location: ../register.php");
        exit;
    }

    // Check if email already exists
    $checkQuery = "SELECT id FROM user_autentikasi WHERE email = ?";
    $stmt = $db->prepare($checkQuery);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $_SESSION['notification'] = [
            'type' => 'error',
            'message' => 'Email sudah terdaftar!'
        ];
        $stmt->close();
        header("Location: ../register.php");
        exit;
    }
    $stmt->close();

    // Hash Password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert User
    $insertQuery = "INSERT INTO user_autentikasi (firstname, lastname, no_hp, email, password) VALUES (?, ?, ?, ?, ?)";
    $stmt = $db->prepare($insertQuery);
    $stmt->bind_param("sssss", $firstname, $lastname, $no_hp, $email, $hashed_password);

    if ($stmt->execute()) {
        $_SESSION['notification'] = [
            'type' => 'success',
            'message' => 'Registrasi berhasil! Silakan login.'
        ];
        header("Location: ../login.php");
        exit;
    } else {
        $_SESSION['notification'] = [
            'type' => 'error',
            'message' => 'Terjadi kesalahan: ' . $stmt->error
        ];
        header("Location: ../register.php");
        exit;
    }
    $stmt->close();
}
?>
