<?php
session_start();
require_once '../../db.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: ../../auth/login.php'); exit;
}

if (!isset($_GET['id'])) {
    header('Location: airtag.php'); exit;
}

$id = mysqli_real_escape_string($db, $_GET['id']);

// Get images
$q = mysqli_query($db, "SELECT * FROM admin_produk_airtag_gambar WHERE produk_id='$id'");
$path = '../../uploads/';

while($row = mysqli_fetch_assoc($q)) {
    if(!empty($row['foto_thumbnail']) && file_exists($path.$row['foto_thumbnail'])) {
        unlink($path.$row['foto_thumbnail']);
    }
    $imgs = json_decode($row['foto_produk'], true);
    if(is_array($imgs)) {
        foreach($imgs as $f) {
            if(file_exists($path.$f)) unlink($path.$f);
        }
    }
}

// Delete record
if (mysqli_query($db, "DELETE FROM admin_produk_airtag WHERE id='$id'")) {
    header('Location: airtag.php?success=deleted');
} else {
    header('Location: airtag.php?error=failed');
}
?>
