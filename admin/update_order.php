<?php
session_start();
include 'connect.php'; // Your database connection

// Check if the user is admin
// if ($_SESSION['role'] != 'admin') {
//     header("Location: login.php");
//     exit();
// }

if (isset($_POST['status'], $_POST['order_id'])) {
    $status = $_POST['status'];
    $order_id = $_POST['order_id'];

    // Prevent status update if already accepted
    if ($status == 'Accept') {
        // Fetch the current status of the order
        $stmt = $conn->prepare("SELECT status, payment_status FROM orders WHERE id = ?");
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $order = $result->fetch_assoc();

        // Check if order is already accepted
        if ($order['status'] != 'Accept') {
            // Update the order status to 'Accept'
            $stmt_update = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
            $stmt_update->bind_param("si", $status, $order_id);
            $stmt_update->execute();
            $stmt_update->close();

            // If the payment status is 'Not Paid', update it to 'Paid'
            if ($order['payment_status'] == 'Not Paid') {
                $stmt_payment = $conn->prepare("UPDATE orders SET payment_status = 'Paid' WHERE id = ?");
                $stmt_payment->bind_param("i", $order_id);
                $stmt_payment->execute();
                $stmt_payment->close();
            }

            // Fetch order items and reduce stock accordingly
            $stmt_items = $conn->prepare("SELECT item_id, quantity FROM order_items WHERE order_id = ?");
            $stmt_items->bind_param("i", $order_id);
            $stmt_items->execute();
            $result_items = $stmt_items->get_result();

            while ($row = $result_items->fetch_assoc()) {
                $item_id = $row['item_id'];
                $quantity = $row['quantity'];

                // Reduce stock of each item
                $stmt2 = $conn->prepare("UPDATE items SET stock = stock - ? WHERE item_id = ?");
                $stmt2->bind_param("ii", $quantity, $item_id);
                $stmt2->execute();
                $stmt2->close();
            }

            // Close the result set
            $stmt_items->close();
        }
    }

    // Redirect back to the admin order list page
    header("Location: order.php");
    exit();
}
?>
