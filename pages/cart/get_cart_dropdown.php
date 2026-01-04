<?php
session_start();
require '../../db/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];
$response = [
    'success' => true,
    'count' => 0,
    'items' => []
];

// Get cart items
$stmt = $db->prepare("SELECT * FROM user_keranjang WHERE user_id = ? ORDER BY id DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$total_qty = 0;

while ($row = $result->fetch_assoc()) {
    $total_qty += $row['jumlah'];
    
    $product_id = $row['product_id'];
    $tipe = strtolower($row['tipe_produk']);
    
    // Map tipe_produk (enum) to actual table name
    // Enum: 'iphone','ipad','mac','music','watch','aksesori'
    $table = "";
    
    // Handle specific mappings
    if ($tipe == 'aksesori' || $tipe == 'aksesoris') {
        // Check which one exists. Usually one does. Common to have plurals in table.
        // Assuming admin_produk_aksesoris (plural) based on previous patterns.
        $table = "admin_produk_aksesoris";
    } elseif (in_array($tipe, ['mac', 'iphone', 'ipad', 'watch', 'music', 'airtag'])) {
        $table = "admin_produk_" . $tipe;
    } else {
        // Fallback or skip
        continue;
    }

    // Use image from cart if available
    $cart_image = $row['foto_thumbnail'] ?? '';

    // Fetch product details (Name, Price)
    // We assume ID is consistent
    $p_query = "SELECT * FROM $table WHERE id = $product_id";
    $p_res = $db->query($p_query);
    
    if ($p_res && $p_row = $p_res->fetch_assoc()) {
        // Try multiple price columns or logic
        $price = $p_row['harga'] ?? $p_row['harga_produk'] ?? 0;
        
        // If price is 0, check combination table for min price
        if ($price <= 0) {
            $table_comb = $table . "_kombinasi";
            // Check if combination table exists (simple check by query suppress)
            $q_comb = $db->query("SELECT MIN(harga) as min_price FROM $table_comb WHERE produk_id = $product_id AND status_stok = 'tersedia'");
            if ($q_comb && $row_comb = $q_comb->fetch_assoc()) {
                $price = $row_comb['min_price'] ?? 0;
            }
        }

        $item = [
            'id' => $row['id'],
            'product_id' => $product_id,
            'name' => $p_row['nama_produk'],
            'qty' => $row['jumlah'],
            'price' => $price,
            'formatted_price' => "Rp " . number_format($price, 0, ',', '.'),
            'type' => $tipe,
            'image' => $cart_image // Prioritize the thumbnail stored in cart
        ];
        
        $response['items'][] = $item;
    }
}

$response['count'] = $total_qty;

echo json_encode($response);
?>
