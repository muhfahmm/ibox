<?php
session_start();
require_once '../../db.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ../../auth/login.php?error=not_logged_in');
    exit();
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    $delete_query = "DELETE FROM home_produk_terbaru WHERE id = '$id'";
    if (mysqli_query($db, $delete_query)) {
        header('Location: produk-terbaru.php?success=deleted');
    } else {
        header('Location: produk-terbaru.php?error=delete_failed');
    }
} else {
    header('Location: produk-terbaru.php');
}
exit();
?>