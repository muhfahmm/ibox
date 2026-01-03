<?php
require '../../../db/db.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $_SESSION['notification'] = [
            'type' => 'error',
            'message' => 'Email dan Password wajib diisi!'
        ];
        header("Location: ../login.php");
        exit;
    }

    $query = "SELECT id, firstname, lastname, password FROM user_autentikasi WHERE email = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $firstname, $lastname, $hashed_password);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            // Set Session
            $_SESSION['user_id'] = $id;
            $_SESSION['user_firstname'] = $firstname;
            $_SESSION['user_lastname'] = $lastname;
            $_SESSION['user_name'] = $firstname . ' ' . $lastname;
            $_SESSION['user_email'] = $email;

            // Redirect to home/dashboard
            header("Location: ../../index.php");
            exit;
        } else {
            $_SESSION['notification'] = [
                'type' => 'error',
                'message' => 'Password salah!'
            ];
            header("Location: ../login.php");
            exit;
        }
    } else {
        $_SESSION['notification'] = [
            'type' => 'error',
            'message' => 'Email tidak ditemukan!'
        ];
        header("Location: ../login.php");
        exit;
    }
    $stmt->close();
}
?>
