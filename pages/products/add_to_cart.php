<?php
ini_set('display_errors', 0);
error_reporting(E_ALL);

session_start();
require_once '../../db/db.php';

header('Content-Type: application/json');

function sendError($msg, $details = null) {
    http_response_code(200); // Always return 200 for JS to handle structure
    echo json_encode(['status' => 'error', 'message' => $msg, 'debug' => $details]);
    exit();
}

// Check login
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Silakan login terlebih dahulu', 'code' => 'auth_required']);
    exit();
}

$user_id = $_SESSION['user_id'];
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    sendError('Invalid JSON input', json_last_error_msg());
}

$product_id = isset($input['product_id']) ? intval($input['product_id']) : 0;
$product_type = isset($input['product_type']) ? strtolower(trim($input['product_type'])) : '';
$quantity = isset($input['quantity']) ? intval($input['quantity']) : 1;
// selected_variant is an ID of the combination table, if user already picked one
$selected_combination_id = isset($input['combination_id']) ? intval($input['combination_id']) : null;

if ($product_id <= 0 || empty($product_type)) {
    sendError('Data produk tidak valid', ['id' => $product_id, 'type' => $product_type]);
}

// Map frontend type to DB table suffix
// types: iphone, ipad, mac, watch, aksesoris, music, airtag
// DB tables: admin_produk_iphone, admin_produk_aksesoris, admin_produk_airtag, etc.
$type_map = [
    'iphone' => 'iphone',
    'ipad' => 'ipad',
    'mac' => 'mac',
    'watch' => 'watch',
    'aksesoris' => 'aksesoris',
    'music' => 'music',
    'airtag' => 'airtag'
];

if (!array_key_exists($product_type, $type_map)) {
    sendError('Tipe produk tidak dikenal: ' . $product_type);
}

$db_suffix = $type_map[$product_type];
$table_main = "admin_produk_" . $db_suffix;
$table_comb = "admin_produk_" . $db_suffix . "_kombinasi";
$table_img  = "admin_produk_" . $db_suffix . "_gambar";

// Ensure DB connection
if (!$db) {
    sendError('Database connection failed');
}

// Check if combination table exists (simple check by query)
$result = $db->query("SHOW TABLES LIKE '$table_comb'");
$has_variants_table = $result && $result->num_rows > 0;

if (!$has_variants_table) {
    // If no variant table, just add the main product
    $thumbnail = 'default.jpg';
    
    // Check main table for details
    $q_main = $db->query("SELECT * FROM $table_main WHERE id = $product_id");
    if (!$q_main || $q_main->num_rows === 0) {
        sendError('Produk tidak ditemukan di database');
    }
    
    // Try to get thumbnail from image table if exists
    $q_img = $db->query("SHOW TABLES LIKE '$table_img'");
    if ($q_img && $q_img->num_rows > 0) {
        $img_res = $db->query("SELECT foto_thumbnail FROM $table_img WHERE produk_id = $product_id LIMIT 1");
        if ($img_res && $row = $img_res->fetch_assoc()) {
            $thumbnail = $row['foto_thumbnail'];
        }
    }
    
    addToCart($db, $user_id, $product_id, $product_type, $quantity, $thumbnail);
    exit();
}

// Handle Variants
// Fetch all combinations for this product ID
$query_comb = "SELECT * FROM $table_comb WHERE produk_id = $product_id AND status_stok = 'tersedia'";
$res_comb = $db->query($query_comb);

if (!$res_comb) {
    sendError('Gagal mengambil data varian: ' . $db->error);
}

$variants = [];
while ($row = $res_comb->fetch_assoc()) {
    $variants[] = $row;
}

if (count($variants) === 0) {
    sendError('Stok produk habis atau varian tidak tersedia');
}

// If user selected a specific combination
if ($selected_combination_id) {
    // Find the variant
    $found = false;
    $selected_variant = null;
    foreach ($variants as $v) {
        if (isset($v['id']) && $v['id'] == $selected_combination_id) {
            $found = true;
            $selected_variant = $v;
            break;
        }
    }
    
    if (!$found) {
        sendError('Varian yang dipilih tidak valid atau stok habis');
    }
    
    // Success, add to cart
    // Get thumbnail based on color?
    $color_field = '';
    if (isset($selected_variant['warna'])) $color_field = 'warna';
    elseif (isset($selected_variant['warna_case'])) $color_field = 'warna_case'; // Watch
    
    $thumbnail = 'default.jpg';
    if ($color_field && isset($selected_variant[$color_field])) {
        $color_val = $db->real_escape_string($selected_variant[$color_field]);
        $col_name_in_img = ($product_type === 'watch') ? 'warna_case' : 'warna';
        
        $q_thumb = "SELECT foto_thumbnail FROM $table_img WHERE produk_id = $product_id AND $col_name_in_img = '$color_val' LIMIT 1";
        $r_thumb = $db->query($q_thumb);
        if ($r_thumb && $row = $r_thumb->fetch_assoc()) {
            $thumbnail = $row['foto_thumbnail'];
        }
    } else {
        // Fallback to any image
        $q_thumb = "SELECT foto_thumbnail FROM $table_img WHERE produk_id = $product_id LIMIT 1";
        $r_thumb = $db->query($q_thumb);
        if ($r_thumb && $row = $r_thumb->fetch_assoc()) {
            $thumbnail = $row['foto_thumbnail'];
        }
    }
    
    ensureColumnExists($db);
    
    addToCart($db, $user_id, $product_id, $product_type, $quantity, $thumbnail, $selected_combination_id);
    exit();

} else {
    // Multiple variants exist OR single variant exists but not selected
    // Return Data for Modal
    
    // Fetch images mappings
    $images_map = [];
    $q_img = $db->query("SELECT * FROM $table_img WHERE produk_id = $product_id");
    if ($q_img) {
        while ($row = $q_img->fetch_assoc()) {
            // key by color
            $c_key = $row['warna'] ?? ($row['warna_case'] ?? 'default');
            $images_map[$c_key] = $row['foto_thumbnail'];
        }
    }
    
    echo json_encode([
        'status' => 'variant_required',
        'message' => 'Pilih varian',
        'product_id' => $product_id,
        'product_type' => $product_type,
        'variants' => $variants,
        'images' => $images_map
    ]);
    exit();
}

function ensureColumnExists($db) {
    $check_col = $db->query("SHOW COLUMNS FROM user_keranjang LIKE 'kombinasi_id'");
    if ($check_col && $check_col->num_rows == 0) {
        $db->query("ALTER TABLE user_keranjang ADD COLUMN kombinasi_id INT NULL DEFAULT NULL");
    }
}

function addToCart($db, $user_id, $product_id, $type, $qty, $thumbnail, $kombinasi_id = null) {
    // Check if already exists? Update quantity
    // Using kombinsi_id to distinguish
    
    $kombinasi_sql = $kombinasi_id ? "AND kombinasi_id = $kombinasi_id" : "AND (kombinasi_id IS NULL OR kombinasi_id = 0)";
    
    $check = $db->query("SELECT id, jumlah FROM user_keranjang WHERE user_id = $user_id AND product_id = $product_id AND tipe_produk = '$type' $kombinasi_sql");
    
    if ($check && $check->num_rows > 0) {
        // Update
        $row = $check->fetch_assoc();
        $new_qty = $row['jumlah'] + $qty;
        $cart_id = $row['id'];
        $update = $db->query("UPDATE user_keranjang SET jumlah = $new_qty WHERE id = $cart_id");
        if ($update) {
            echo json_encode(['status' => 'success', 'message' => 'Jumlah produk di keranjang diperbarui']);
        } else {
            sendError('Gagal memperbarui keranjang: ' . $db->error);
        }
    } else {
        // Insert
        $k_id_val = $kombinasi_id ? $kombinasi_id : "NULL";
        
        $sql = "INSERT INTO user_keranjang (user_id, product_id, tipe_produk, jumlah, foto_thumbnail, kombinasi_id) 
                VALUES ($user_id, $product_id, '$type', $qty, '$thumbnail', $k_id_val)";
        
        if ($db->query($sql)) {
            echo json_encode(['status' => 'success', 'message' => 'Produk ditambahkan ke keranjang']);
        } else {
             // Fallback if column doesn't exist (if alter failed)
             $sql_fallback = "INSERT INTO user_keranjang (user_id, product_id, tipe_produk, jumlah, foto_thumbnail) 
                VALUES ($user_id, $product_id, '$type', $qty, '$thumbnail')";
             if ($db->query($sql_fallback)) {
                 echo json_encode(['status' => 'success', 'message' => 'Produk (umum) ditambahkan ke keranjang']);
             } else {
                 sendError('Gagal menyimpan ke keranjang: ' . $db->error);
             }
        }
    }
}
?>
