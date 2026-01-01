<?php
session_start();
require_once '../../db.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ../../auth/login.php?error=not_logged_in');
    exit();
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    $stmt = $db->prepare("DELETE FROM home_aksesori WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        header('Location: aksesori-unggulan.php?success=deleted');
    } else {
        header('Location: aksesori-unggulan.php?error=db_error');
    }
} else {
    header('Location: aksesori-unggulan.php');
}
exit();
?>
