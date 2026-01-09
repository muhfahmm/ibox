<?php
// Fix path: db.php is in pages/ directory, and we are in pages/checkout/
require_once '../db.php';

// Fix ENUM for user_histori_transaksi to include 'airtag' and 'aksesoris'
$sql1 = "ALTER TABLE user_histori_transaksi MODIFY COLUMN tipe_produk ENUM('iphone','ipad','mac','music','watch','aksesori','aksesoris','airtag') NOT NULL";
$sql2 = "ALTER TABLE user_keranjang MODIFY COLUMN tipe_produk ENUM('iphone','ipad','mac','music','watch','aksesori','aksesoris','airtag') NOT NULL";

if ($db->query($sql1) === TRUE) {
    echo "user_histori_transaksi updated successfully.\n";
} else {
    echo "Error updating user_histori_transaksi: " . $db->error . "\n";
}

if ($db->query($sql2) === TRUE) {
    echo "user_keranjang updated successfully.\n";
} else {
    echo "Error updating user_keranjang: " . $db->error . "\n";
}
?>
