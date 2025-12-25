<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$dbnm = 'db_ibox';

$db = mysqli_connect($host, $user, $pass, $dbnm);

if (!$db) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Fungsi sederhana untuk escape string
function escape($string) {
    global $db;
    return mysqli_real_escape_string($db, trim($string));
}
?>