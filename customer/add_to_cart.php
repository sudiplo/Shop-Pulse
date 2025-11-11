<?php
session_start();
include 'connect.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['item_id'])) {
    $item_id = (int) $_POST['item_id'];

    // Fetch item details
    $result = $conn->query("SELECT * FROM items WHERE item_id = $item_id");

    if ($result && $result->num_rows > 0) {
        $item = $result->fetch_assoc();

        // Check if cart exists
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        // If item already in cart, increase quantity
        if (isset($_SESSION['cart'][$item_id])) {
            $_SESSION['cart'][$item_id]['quantity'] += 1;
        } else {
            $_SESSION['cart'][$item_id] = [
                'item_name' => $item['item_name'],
                'price' => (float)$item['price'],
                'image' => $item['image'],
                'quantity' => 1
            ];
        }
    }
}
header("Location: shop.php");
exit;
