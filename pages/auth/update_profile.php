<?php
session_start();
require '../../db/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    
    // Get POST data
    $firstname = trim($_POST['firstname']);
    $lastname = trim($_POST['lastname']);
    $email = trim($_POST['email']);
    $no_hp = trim($_POST['no_hp']);
    $new_password = $_POST['new_password']; // Used for new password

    // Basic Validation
    if (empty($firstname) || empty($lastname) || empty($email) || empty($no_hp)) {
         echo "<script>alert('Semua field data diri wajib diisi.'); window.location='profile.php';</script>";
         exit;
    }
    
    // Check if new password is provided
    if (!empty($new_password)) {
        // Hash the new password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        
        $query = "UPDATE user_autentikasi SET firstname=?, lastname=?, email=?, no_hp=?, password=? WHERE id=?";
        $stmt = $db->prepare($query);
        $stmt->bind_param("sssssi", $firstname, $lastname, $email, $no_hp, $hashed_password, $user_id);
    } else {
        // Update without changing password
        $query = "UPDATE user_autentikasi SET firstname=?, lastname=?, email=?, no_hp=? WHERE id=?";
        $stmt = $db->prepare($query);
        $stmt->bind_param("ssssi", $firstname, $lastname, $email, $no_hp, $user_id);
    }
    
    if ($stmt->execute()) {
        // Update session variables if name changed
        $_SESSION['user_firstname'] = $firstname;
        $_SESSION['user_lastname'] = $lastname;

        echo "<script>alert('Profil berhasil diperbarui!'); window.location='profile.php';</script>";
    } else {
        echo "<script>alert('Gagal memperbarui profil: " . $stmt->error . "'); window.location='profile.php';</script>";
    }
    
    $stmt->close();
} else {
    // If not POST, redirect back
    header("Location: profile.php");
}
?>
