<?php
session_start();
include 'connect.php';

$cart = $_SESSION['cart'] ?? [];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Invalid request.");
}
// 
if(isset($_POST['proceed_to_payment'])){
    $cart = $_SESSION['cart'] ?? [];
$address = trim($_POST['address'] ?? '');
$phone = trim($_POST['phone'] ?? '');
 

if (empty($cart)) die("Cart is empty.");
if (!$address || !$phone) die("Missing required fields.");

$user_id = $_SESSION['id'] ?? null;

// Fetch user info
$stmt = $conn->prepare("SELECT name, email FROM customer WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$user) die("User not found.");

$user_name = $user['name'];
$email = $user['email'];

// Calculate totals
$subtotal = 0;
$item_ids = [];
foreach ($cart as $item) {
    $subtotal += $item['price'] * $item['quantity'];
    $item_ids[] = $item['item_name'];
}
$delivery_charge = 70;
$total = $subtotal + $delivery_charge;

// Insert order
$stmt = $conn->prepare("INSERT INTO orders (user_id, user_name, email, address, phone, total_amount) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("issssd", $user_id, $user_name, $email, $address, $phone, $total);
$stmt->execute();
$order_id = $stmt->insert_id;
$stmt->close();

// Fetch all item IDs
$placeholders = implode(',', array_fill(0, count($item_ids), '?'));
$types = str_repeat('s', count($item_ids));
$stmt = $conn->prepare("SELECT item_id, item_name FROM items WHERE item_name IN ($placeholders)");
$stmt->bind_param($types, ...$item_ids);
$stmt->execute();
$result = $stmt->get_result();
$item_map = [];
while ($row = $result->fetch_assoc()) {
    $item_map[$row['item_name']] = $row['item_id'];
}
$stmt->close();

// Insert order items
$stmt = $conn->prepare("INSERT INTO order_items (order_id, item_id, item_name, price, quantity, total) VALUES (?, ?, ?, ?, ?, ?)");
foreach ($cart as $item) {
    $item_name = $item['item_name'];
    $item_id = $item_map[$item_name] ?? null;
    if (!$item_id) die("Item $item_name not found in DB.");

    $price = $item['price'];
    $quantity = $item['quantity'];
    $total_item = $price * $quantity;

    $stmt->bind_param("iisdis", $order_id, $item_id, $item_name, $price, $quantity, $total_item);
    $stmt->execute();
}
$stmt->close();

// Clear cart
unset($_SESSION['cart']);
    header("Location: user_dashboard.php");
    exit;
}


?>
