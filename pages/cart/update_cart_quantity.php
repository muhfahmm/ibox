<?php
session_start();
require '../../db/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit;
}

$cart_id = isset($input['cart_id']) ? intval($input['cart_id']) : 0;
$action = isset($input['action']) ? $input['action'] : ''; // 'increase', 'decrease', 'set'
$quantity = isset($input['quantity']) ? intval($input['quantity']) : 1;

if ($cart_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid cart ID']);
    exit;
}

// Verify cart item belongs to user
$stmt = $db->prepare("SELECT jumlah FROM user_keranjang WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $cart_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Cart item not found']);
    exit;
}

$row = $result->fetch_assoc();
$current_qty = $row['jumlah'];

// Calculate new quantity based on action
$new_qty = $current_qty;

if ($action === 'increase') {
    $new_qty = $current_qty + 1;
} elseif ($action === 'decrease') {
    $new_qty = $current_qty - 1;
    if ($new_qty < 1) {
        // Delete item if quantity becomes 0
        $delete_stmt = $db->prepare("DELETE FROM user_keranjang WHERE id = ? AND user_id = ?");
        $delete_stmt->bind_param("ii", $cart_id, $user_id);
        if ($delete_stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Item removed from cart', 'deleted' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to remove item']);
        }
        exit;
    }
} elseif ($action === 'set') {
    if ($quantity < 1) {
        // Delete item if quantity is set to 0 or less
        $delete_stmt = $db->prepare("DELETE FROM user_keranjang WHERE id = ? AND user_id = ?");
        $delete_stmt->bind_param("ii", $cart_id, $user_id);
        if ($delete_stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Item removed from cart', 'deleted' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to remove item']);
        }
        exit;
    }
    $new_qty = $quantity;
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
    exit;
}

// Update quantity
$update_stmt = $db->prepare("UPDATE user_keranjang SET jumlah = ? WHERE id = ? AND user_id = ?");
$update_stmt->bind_param("iii", $new_qty, $cart_id, $user_id);

if ($update_stmt->execute()) {
    echo json_encode([
        'success' => true, 
        'message' => 'Quantity updated',
        'new_quantity' => $new_qty
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update quantity']);
}
?>
