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
    $kombinasi_id = $row['kombinasi_id'] ?? null;
    
    // Map tipe_produk (enum) to actual table name
    $table = "";
    
    // Handle specific mappings
    if ($tipe == 'aksesori' || $tipe == 'aksesoris') {
        $table = "admin_produk_aksesoris";
    } elseif (in_array($tipe, ['mac', 'iphone', 'ipad', 'watch', 'music', 'airtag'])) {
        $table = "admin_produk_" . $tipe;
    } else {
        continue;
    }

    // Use image from cart if available
    $cart_image = $row['foto_thumbnail'] ?? '';

    // Fetch product details (Name, Price)
    $p_query = "SELECT * FROM $table WHERE id = $product_id";
    $p_res = $db->query($p_query);
    
    if ($p_res && $p_row = $p_res->fetch_assoc()) {
        $price = $p_row['harga'] ?? $p_row['harga_produk'] ?? 0;
        $variant_info = [];
        $variant_text = '';
        
        // If kombinasi_id exists, fetch variant details
        if ($kombinasi_id) {
            $table_comb = $table . "_kombinasi";
            $q_variant = "SELECT * FROM $table_comb WHERE id = $kombinasi_id";
            $r_variant = $db->query($q_variant);
            
            if ($r_variant && $variant_row = $r_variant->fetch_assoc()) {
                // Get price from variant if available
                if (isset($variant_row['harga']) && $variant_row['harga'] > 0) {
                    $price = $variant_row['harga'];
                }
                
                // Build variant info array based on product type
                if ($tipe == 'iphone' || $tipe == 'ipad' || $tipe == 'mac') {
                    if (isset($variant_row['warna'])) {
                        $variant_info[] = $variant_row['warna'];
                    }
                    // Check both 'storage' and 'penyimpanan' for capacity
                    if (isset($variant_row['storage']) && !empty($variant_row['storage'])) {
                        $variant_info[] = $variant_row['storage'];
                    } elseif (isset($variant_row['penyimpanan']) && !empty($variant_row['penyimpanan'])) {
                        $variant_info[] = $variant_row['penyimpanan'];
                    }
                    if (isset($variant_row['konektivitas']) && !empty($variant_row['konektivitas'])) {
                        $variant_info[] = $variant_row['konektivitas'];
                    }
                } elseif ($tipe == 'watch') {
                    if (isset($variant_row['warna_case'])) {
                        $variant_info[] = 'Case: ' . $variant_row['warna_case'];
                    }
                    if (isset($variant_row['warna_strap'])) {
                        $variant_info[] = 'Strap: ' . $variant_row['warna_strap'];
                    }
                    if (isset($variant_row['ukuran'])) {
                        $variant_info[] = $variant_row['ukuran'];
                    }
                    if (isset($variant_row['konektivitas']) && !empty($variant_row['konektivitas'])) {
                        $variant_info[] = $variant_row['konektivitas'];
                    }
                } elseif ($tipe == 'airtag') {
                    if (isset($variant_row['warna'])) {
                        $variant_info[] = $variant_row['warna'];
                    }
                    if (isset($variant_row['jumlah_pack'])) {
                        $variant_info[] = $variant_row['jumlah_pack'] . ' Pack';
                    }
                }
                
                $variant_text = implode(' • ', $variant_info);
            }
        }
        
        // If no variant found but price is 0, get min price from combinations
        if ($price <= 0) {
            $table_comb = $table . "_kombinasi";
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
            'image' => $cart_image,
            'variant' => $variant_text,
            'variant_array' => $variant_info,
            'kombinasi_id' => $kombinasi_id,
            'checkout_url' => "/ibox/pages/checkout/checkout.php?id=" . $product_id . "&tipe=" . $tipe
        ];
        
        $response['items'][] = $item;
    }
}

$response['count'] = $total_qty;

echo json_encode($response);
?>
