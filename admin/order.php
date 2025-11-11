<?php
include 'connect.php';
session_start();
$connection = mysqli_connect("localhost", "root", "");
$db = mysqli_select_db($connection, "shoppulse");

// Handle search input
$search = '';
if (isset($_GET['search'])) {
  $search = mysqli_real_escape_string($connection, $_GET['search']);
  $query = "SELECT * FROM customer WHERE LOWER(name) LIKE LOWER('%$search%')";
} else {
  $query = "SELECT * FROM customer";
}

$query_run = mysqli_query($connection, $query);


// Fetch orders along with customer details
$query = "SELECT o.id, o.user_id, o.user_name, o.total_amount, o.order_date, o.payment_status, o.status, o.address 
          FROM orders o
          JOIN customer c ON o.user_id = c.id";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    $orders = $result->fetch_all(MYSQLI_ASSOC);
} else {
    $orders = [];
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop Pulse</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../customer/style.css">
    <link rel="stylesheet" href="table.css">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex flex-col items-center">
        <!-- Top Bar (Updated based on new screenshot) -->
        <header class="w-full header-top py-3 px-4 md:px-8">
            <div class="max-w-7xl mx-auto flex justify-between items-center">
                <!-- Logo -->
                <a href="#" class="text-2xl font-bold text-gray-800">🛍️ ShopPulse</a>

                <!-- Search Bar -->
              
                     <form method="GET" action="" class="flex-grow mx-4 flex items-center search-group max-w-lg">
                        <input type="text" name="search" placeholder="Enter your product here..." class="flex-grow py-2 px-4 text-sm" value="<?php echo htmlspecialchars($search); ?>" />
                        <button class="btn-search text-white py-2 px-4 rounded-r-full">
                            <i class="fas fa-search"></i>
                        </button> 
                    </form>

                <!-- Icons -->
                <div class="flex items-center space-x-6">
                    <a href="#" class="icon-btn">
                        <i class="fas fa-heart text-xl"></i>
                    </a>
                    <a href="#" class="icon-btn">
                        <i class="fas fa-user-circle text-xl"></i>
                    </a>
                    <a href="../customer/logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> Log out</a>
                </div>
            </div>
        </header>

        <!-- Navigation Bar (Updated based on new screenshot) -->
        <nav class="w-full navbar py-3">
            <div class="max-w-7xl mx-auto flex flex-wrap justify-center md:justify-center items-center px-4 md:px-8 space-x-4 md:space-x-8">
                <a href="admin_dashboard.php" class="navbar-item">HOME</a>
                <a href="item.php" class="navbar-item">PRODUCTS</a>
                <a href="order.php" class="navbar-item">ORDERS</a>
                <a href="customer.php" class="navbar-item">CUSTOMER</a>
            </div>
        </nav>
<h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 leading-tight mb-4"></h1>
        <!-- Admin Orders Table -->
        <main class="main-content">
            <header><h1 class="text-2xl font-bold text-gray-800">Admin - Manage Orders</h1></header>
            <section class="product-table">
                <!-- Bootstrap Table with Green Header -->
                <table class="table table-striped table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Product Image</th>
                            <th>Product Name</th>
                            <th>Quantity</th>
                            <th>Total Amount</th>
                            <th>Order Date</th>
                            <th>User Name</th>
                            <th>Order Address</th>
                            <th>Status</th>
                            <th>Payment Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <!-- Fetch and display the ordered product details (image, name, quantity, etc.) -->
                                <?php
                                    $order_id = $order['id'];
                                    $items_query = "SELECT i.item_name, i.image, oi.quantity FROM order_items oi 
                                                    JOIN items i ON oi.item_id = i.item_id WHERE oi.order_id = ?";
                                    $stmt = $conn->prepare($items_query);
                                    $stmt->bind_param("i", $order_id);
                                    $stmt->execute();
                                    $items_result = $stmt->get_result();

                                    while ($item = $items_result->fetch_assoc()):
                                ?>
                                    <td><img src="../image/<?php echo $item['image']; ?>" alt="<?php echo $item['item_name']; ?>" width="50" height="50"></td>
                                    <td><?php echo $item['item_name']; ?></td>
                                    <td><?php echo $item['quantity']; ?></td>
                                    <td><?php echo $order['total_amount']; ?></td>
                                    <td><?php echo $order['order_date']; ?></td>
                                    <td><?php echo $order['user_name']; ?></td>
                                    <td><?php echo $order['address']; ?></td>
                                    <td>
                                        <?php if ($order['status'] != 'Accept'): ?>
                                            <form method="POST" action="update_order.php">
                                                <select name="status"  class="edit" onchange="this.form.submit()">
                                                    <option value="In Process" <?php echo $order['status'] == 'In Process' ? 'selected' : ''; ?>>In Process</option>
                                                    <option value="Accept" <?php echo $order['status'] == 'Accept' ? 'selected' : ''; ?>>Accept</option>
                                                </select>
                                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                            </form>
                                        <?php else: ?>
                                            <span class="text-success">Accepted</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($order['status'] == 'Accept'): ?>
                                            <span class="text-success">Paid</span>
                                        <?php else: ?>
                                            <span class="text-danger"><?php echo $order['payment_status']; ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="order_details.php?order_id=<?php echo $order['id']; ?>" class="edit">View</a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </section>
        </main>
    </div>
</body>
</html>
