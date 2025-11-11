<?php
session_start();
include 'connect.php';

$order_id = $_GET['id'] ?? null;
if (!$order_id) {
    header("Location: user_dashboard.php");
    exit;
}

// Delete the order items first to maintain referential integrity
$stmt = $conn->prepare("DELETE FROM order_items WHERE order_id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();

// Now, delete the order
$stmt = $conn->prepare("DELETE FROM orders WHERE id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();

$_SESSION['message'] = 'Order deleted successfully!';

header("Location: my_orders.php");  
exit;
?>
