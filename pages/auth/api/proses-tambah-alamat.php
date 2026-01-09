<?php
session_start();
require '../../../db/db.php';

// Check user authentication
if (!isset($_SESSION['user_id']) || $_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$label = trim($_POST['label_alamat']);
$username = trim($_POST['username']);
$no_hp = trim($_POST['no_hp']);
$alamat_lengkap = trim($_POST['alamat_lengkap']);
$kota = trim($_POST['kota']);
$kecamatan = trim($_POST['kecamatan']);
$provinsi = trim($_POST['provinsi']);
$kode_post = trim($_POST['kode_post']);

// Basic validation
if (empty($username) || empty($no_hp) || empty($alamat_lengkap) || empty($kota) || empty($provinsi)) {
    $_SESSION['flash_status'] = 'error';
    $_SESSION['flash_message'] = 'Mohon lengkapi data alamat.';
    header("Location: ../add_address.php"); // Redirect back to form
    exit;
}

// Optional: Fetch email from user profile if needed, or leave null in db if user doesn't update
// For simplicity, we are not asking email in the address form explicitly, as it's less critical for physical address.
// If needed, we can get it from session or DB.
$email = ""; 

$query = "INSERT INTO user_alamat (user_id, label_alamat, username, no_hp, alamat_lengkap, kota, kecamatan, provinsi, kode_post, email) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $db->prepare($query);
$stmt->bind_param("isssssssss", $user_id, $label, $username, $no_hp, $alamat_lengkap, $kota, $kecamatan, $provinsi, $kode_post, $email);

if ($stmt->execute()) {
    $_SESSION['flash_status'] = 'success';
    $_SESSION['flash_message'] = 'Alamat berhasil ditambahkan!';
    header("Location: ../profile.php");
} else {
    $_SESSION['flash_status'] = 'error';
    $_SESSION['flash_message'] = 'Gagal menambahkan alamat: ' . $stmt->error;
    header("Location: ../add_address.php");
}

$stmt->close();
?>
