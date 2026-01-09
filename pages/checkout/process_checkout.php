<?php
ob_start(); // Start output buffering immediately
session_start();
require_once '../../db/db.php';

// Clear any previous output (like PHP warnings from includes)
ob_clean();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Silakan login terlebih dahulu.']);
    exit;
}

$user_id = $_SESSION['user_id'];
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Invalid data.']);
    exit;
}

// Extract data
$product_id = isset($data['product_id']) ? intval($data['product_id']) : 0;
$product_type = isset($data['product_type']) ? $data['product_type'] : '';
$quantity = isset($data['quantity']) ? intval($data['quantity']) : 1;
$combination_id = isset($data['combination_id']) ? intval($data['combination_id']) : null;
$payment_method = isset($data['payment_method']) ? $data['payment_method'] : 'Transfer Bank';
$address_id = isset($data['address_id']) ? intval($data['address_id']) : 0;
$total_price_client = isset($data['total_price']) ? floatval($data['total_price']) : 0;

if ($product_id <= 0 || empty($product_type)) {
    echo json_encode(['success' => false, 'message' => 'Produk tidak valid.']);
    exit;
}

// 1. Fetch Product Price & Details (Server-side validation)
$price = 0;
$thumbnail = '';
$product_name = '';

// Determine table
$table_prod = "";
$table_comb = "";
$table_img  = "";

$type_key = strtolower($product_type);
if (in_array($type_key, ['mac', 'iphone', 'ipad', 'watch', 'music', 'airtag'])) {
    $table_prod = "admin_produk_" . $type_key;
    $table_comb = "admin_produk_" . $type_key . "_kombinasi";
    $table_img  = "admin_produk_" . $type_key . "_gambar";
} elseif ($type_key == 'aksesori' || $type_key == 'aksesoris') {
    $table_prod = "admin_produk_aksesoris";
    $table_comb = "admin_produk_aksesoris_kombinasi";
    $table_img  = "admin_produk_aksesoris_gambar";
} else {
    echo json_encode(['success' => false, 'message' => 'Tipe produk tidak valid.']);
    exit;
}

// Fetch Product Base Info
$stmt = $db->prepare("SELECT * FROM $table_prod WHERE id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows == 0) {
    echo json_encode(['success' => false, 'message' => 'Produk tidak ditemukan.']);
    exit;
}
$product_data = $res->fetch_assoc();
$product_name = $product_data['nama_produk'];

// Determine Pricing
if ($type_key === 'airtag' && empty($combination_id)) {
    // Airtag base price if no combination? user_histori_transaksi logic requires specific price.
    // Assuming combination is always required for checkout if it exists in DB.
    // Error if combination required but missing.
}

if ($combination_id) {
    $stmt_comb = $db->prepare("SELECT * FROM $table_comb WHERE id = ?");
    $stmt_comb->bind_param("i", $combination_id);
    $stmt_comb->execute();
    $res_comb = $stmt_comb->get_result();
    if ($res_comb->num_rows > 0) {
        $comb_data = $res_comb->fetch_assoc();
        
        // Check Price (Discount or Normal)
        if ($comb_data['harga_diskon'] > 0 && $comb_data['harga_diskon'] < $comb_data['harga']) {
            $price = $comb_data['harga_diskon'];
        } else {
            $price = $comb_data['harga'];
        }
        
    } else {
        echo json_encode(['success' => false, 'message' => 'Varian tidak ditemukan.']);
        exit;
    }
} else {
    // Should fallback to base price if the product table has price (e.g. simple products)
    // Most tables provided have 0.00 price in base table and use combination.
    // Let's assume passed client price is correct IF we can't verify, or fail.
    // Better: Fail if price is 0.
    if (isset($product_data['harga']) && $product_data['harga'] > 0) {
        $price = $product_data['harga'];
    } else {
         echo json_encode(['success' => false, 'message' => 'Harga produk tidak valid (Varian diperlukan).']);
         exit;
    }
}

// Calculate Total
$tax = $price * $quantity * 0.11;
$total_transaction = ($price * $quantity) + $tax;

// Fetch Thumbnail from cart if possible, or DB
// Try to get from user_keranjang to ensure consistency
$stmt_cart = $db->prepare("SELECT foto_thumbnail FROM user_keranjang WHERE user_id = ? AND product_id = ? AND tipe_produk = ? LIMIT 1");
$stmt_cart->bind_param("iis", $user_id, $product_id, $product_type);
$stmt_cart->execute();
$res_cart = $stmt_cart->get_result();
if ($res_cart->num_rows > 0) {
    $row_cart = $res_cart->fetch_assoc();
    $thumbnail = $row_cart['foto_thumbnail'];
} else {
    // If not in cart (direct buy?), fetch from image table
    // Try to find image associated with combination color (?)
    // Fallback:
    $thumbnail = 'default.png'; 
    if (isset($data['thumbnail']) && !empty($data['thumbnail'])) {
        $thumbnail = $data['thumbnail'];
    }
}

// 2. Insert into History (Transaction)
// Schema: id, user_id, product_id, foto_thumbnail, harga, jumlah, total_harga, pay_method, date, tipe_produk
// Truncate payment method to fit DB
$payment_method = substr($payment_method, 0, 50);

$stmt_insert = $db->prepare("INSERT INTO user_histori_transaksi (user_id, product_id, foto_thumbnail, harga, jumlah, total_harga, pay_method, tipe_produk) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
// Use $type_key instead of $product_type to ensure it matches the lowercase ENUM
$stmt_insert->bind_param("iisdidss", $user_id, $product_id, $thumbnail, $price, $quantity, $total_transaction, $payment_method, $type_key);

if ($stmt_insert->execute()) {
    // 3. Remove from Cart
    if ($combination_id) {
        $stmt_del = $db->prepare("DELETE FROM user_keranjang WHERE user_id = ? AND product_id = ? AND tipe_produk = ? AND kombinasi_id = ?");
        $stmt_del->bind_param("iisi", $user_id, $product_id, $product_type, $combination_id);
    } else {
        $stmt_del = $db->prepare("DELETE FROM user_keranjang WHERE user_id = ? AND product_id = ? AND tipe_produk = ?");
        $stmt_del->bind_param("iis", $user_id, $product_id, $product_type);
    }
    $stmt_del->execute();
    
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Gagal menyimpan transaksi: ' . $db->error]);
}
?>
