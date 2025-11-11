<?php
session_start();
include 'connect.php'; // Your database connection

// Ensure only admin can access this page
// if ($_SESSION['role'] != 'admin') {
//     header("Location: login.php");
//     exit();
// }

// Check if an order ID is passed in the URL
// if (!isset($_GET['order_id']) || empty($_GET['order_id'])) {
//     header("Location: admin_orders.php");
//     exit();
// }

$order_id = $_GET['order_id']; // Get the order ID from the URL

// Fetch order details from the database
$query = "SELECT o.id, o.user_id, o.user_name, o.email, o.total_amount, o.order_date, o.status, o.payment_status, c.image 
          FROM orders o
          JOIN customer c ON o.user_id = c.id
          WHERE o.id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order_result = $stmt->get_result();

if ($order_result->num_rows == 0) {
    echo "Order not found.";
    exit();
}

$order = $order_result->fetch_assoc();

// Fetch all orders of the user
$query_all_orders = "SELECT o.id, o.order_date, o.total_amount, o.status, o.payment_status
                     FROM orders o
                     WHERE o.user_id = ?";
$stmt_all_orders = $conn->prepare($query_all_orders);
$stmt_all_orders->bind_param("i", $order['user_id']);
$stmt_all_orders->execute();
$all_orders_result = $stmt_all_orders->get_result();

// Fetch order items for the current order
$query_items = "SELECT oi.item_id, i.item_name, oi.quantity, i.price, (oi.quantity * i.price) AS total_price
                FROM order_items oi
                JOIN items i ON oi.item_id = i.item_id
                WHERE oi.order_id = ?";
$stmt_items = $conn->prepare($query_items);
$stmt_items->bind_param("i", $order_id);
$stmt_items->execute();
$order_items_result = $stmt_items->get_result();

// Extract order details
$user_name = $order['user_name'];
$user_email = $order['email'];
$user_image = $order['image'];
$order_status = $order['status'];
$payment_status = $order['payment_status'];
$total_amount = $order['total_amount'];
$order_date = $order['order_date'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details - Admin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css"> <!-- Link to Custom CSS -->
    <style>
        .billing-info {
            display: none; /* Hide the billing section by default */
            margin-top: 30px;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 5px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .show-bill-btn {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    
    <div class="container mt-5">
        <h2 class="order-title">Order Details - Order #<?php echo $order_id; ?></h2>
        
        <!-- Order Info Section -->
        <div class="row">
            <div class="col-md-6 user-info">
                <h4>User Information</h4>
                <p><strong>Name:</strong> <?php echo $user_name; ?></p>
                <p><strong>Email:</strong> <?php echo $user_email; ?></p>
                <div class="profile-img">
                    <img src="../image/<?php echo $user_image; ?>" alt="Profile Image" class="img-fluid rounded-circle" width="120">
                </div>
            </div>
            <div class="col-md-6 order-info">
                <h4>Order Information</h4>
                <p><strong>Order ID:</strong> <?php echo $order_id; ?> </p>
                <p><strong>Order Date:</strong> <?php echo $order_date; ?></p>
                <p><strong>Status:</strong> <span class="status <?php echo strtolower($order_status); ?>"><?php echo $order_status; ?></span></p>
                <p><strong>Payment Status:</strong> <span class="status <?php echo strtolower($payment_status); ?>"><?php echo $payment_status; ?></span></p>
                <p><strong>Total Amount:</strong> Rs <?php echo number_format($total_amount, 2); ?></p>
            </div>
            
        </div>
 
        <!-- Order Items Section -->
        <h4 class="mt-4">Order Items</h4>
        <table class="table table-bordered order-items-table">
            <thead>
                <tr>
                    <th>Item Name</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Total Price</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($item = $order_items_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $item['item_name']; ?></td>
                        <td><?php echo $item['quantity']; ?></td>
                        <td>Rs <?php echo number_format($item['price'], 2); ?></td>
                        <td>Rs <?php echo number_format($item['total_price'], 2); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        
        <!-- All Orders of the User Section -->
        <h4 class="mt-4">All Orders from <?php echo $user_name; ?></h4>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Order Date</th>
                    <th>Total Amount</th>
                    <th>Status</th>
                    <th>Payment Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($user_order = $all_orders_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $user_order['id']; ?></td>
                        <td><?php echo $user_order['order_date']; ?></td>
                        <td>Rs <?php echo number_format($user_order['total_amount'], 2); ?></td>
                        <td><span class="status <?php echo strtolower($user_order['status']); ?>"><?php echo $user_order['status']; ?></span></td>
                        <td><span class="status <?php echo strtolower($user_order['payment_status']); ?>"><?php echo $user_order['payment_status']; ?></span></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>


        <!-- Back Button -->
        <a href="order.php" class="btn btn-secondary mt-3">Back to Orders</a>
    </div>

    <script>
        // Function to toggle the visibility of the billing section
        function toggleBilling() {
            var billingInfo = document.getElementById("billing-info");
            var button = document.querySelector(".show-bill-btn");

            // Toggle visibility
            if (billingInfo.style.display === "none" || billingInfo.style.display === "") {
                billingInfo.style.display = "block";
                button.textContent = "Hide Bill"; // Change button text to Hide Bill
            } else {
                billingInfo.style.display = "none";
                button.textContent = "Show Bill"; // Change button text to Show Bill
            }
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
