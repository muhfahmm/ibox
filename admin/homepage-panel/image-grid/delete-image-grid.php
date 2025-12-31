<?php
session_start();
require_once '../../db.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ../../auth/login.php?error=not_logged_in');
    exit();
}

if (!isset($_GET['id'])) {
    header('Location: image-grid.php?error=no_id');
    exit();
}

$id = $_GET['id'];

// Hapus data dari home_grid
$query = "DELETE FROM home_grid WHERE id = '$id'";

if (mysqli_query($db, $query)) {
    header('Location: image-grid.php?success=deleted');
} else {
    header('Location: image-grid.php?error=db_error');
}
exit();
?>
