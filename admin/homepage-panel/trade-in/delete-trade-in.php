<?php
session_start();
require_once '../../db.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ../../auth/login.php?error=not_logged_in');
    exit();
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $query = "DELETE FROM home_trade_in WHERE id = '$id'";
    
    if (mysqli_query($db, $query)) {
        header('Location: trade-in.php?success=deleted');
    } else {
        header('Location: trade-in.php?error=db_error');
    }
} else {
    header('Location: trade-in.php');
}
exit();
?>
