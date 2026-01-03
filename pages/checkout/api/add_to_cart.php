<?php
require '../../../db/db.php';
session_start();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1; // Default to 1 for dev
$product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
$jumlah = isset($_POST['jumlah']) ? intval($_POST['jumlah']) : 1;
$tipe = isset($_POST['tipe']) ? trim($_POST['tipe']) : '';
$thumbnail = isset($_POST['thumbnail']) ? trim($_POST['thumbnail']) : '';

if ($product_id <= 0 || empty($tipe)) {
    echo json_encode(['success' => false, 'message' => 'Invalid product data']);
    exit;
}

// Map types to enum if necessary
// 'airtag' is not in enum, neither is 'aksesoris' (enum is 'aksesori')
// Enum: 'iphone','ipad','mac','music','watch','aksesori'
$allowed_types = ['iphone', 'ipad', 'mac', 'music', 'watch', 'aksesori'];

$db_tipe = strtolower($tipe);
if ($db_tipe === 'aksesoris') {
    $db_tipe = 'aksesori';
}

if (!in_array($db_tipe, $allowed_types)) {
    // If type is not in allowed list (e.g. airtag), we might have an issue.
    // However, for now, let's try to insert it or default to 'aksesori' if it's airtag?
    // User request showed enum('iphone','ipad','mac','music','watch','aksesori').
    // Let's assume AirTag should be mapped to 'aksesori' or maybe just fail. 
    // Given iBox structure, AirTag is often categorized as accessory.
    if ($db_tipe === 'airtag') {
        $db_tipe = 'aksesori'; // Fallback for AirTag
    } else {
        // If still not found, return error
         echo json_encode(['success' => false, 'message' => 'Tipe produk tidak valid untuk keranjang: ' . $tipe]);
         exit;
    }
}

// Insert into user_keranjang
$query = "INSERT INTO user_keranjang (user_id, product_id, foto_thumbnail, jumlah, tipe_produk) VALUES (?, ?, ?, ?, ?)";
$stmt = $db->prepare($query);

if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Prepare failed: ' . $db->error]);
    exit;
}

$stmt->bind_param("iisss", $user_id, $product_id, $thumbnail, $jumlah, $db_tipe);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Produk berhasil ditambahkan ke keranjang']);
} else {
    echo json_encode(['success' => false, 'message' => 'Gagal menambahkan ke keranjang: ' . $stmt->error]);
}

$stmt->close();
?>
