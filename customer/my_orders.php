<?php
session_start();
include 'connect.php';

$user_id = $_SESSION['id'] ?? null;
if (!$user_id) {
    header("Location: login.php");
    exit;
}

// Fetch the user's orders along with item details (including item image and payment status) from order_items and items tables
$stmt = $conn->prepare("
    SELECT o.id AS order_id, o.order_date, o.address, o.total_amount, o.status, o.payment_status, 
           oi.quantity, oi.total AS item_total, 
           i.item_name, i.image AS item_image
    FROM orders o
    JOIN order_items oi ON o.id = oi.order_id
    JOIN items i ON oi.item_id = i.item_id
    WHERE o.user_id = ?
    ORDER BY o.order_date DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$orders = $stmt->get_result();

// Fetch the customer's details
$id = $_SESSION['id'];
$result = $conn->query("SELECT * FROM customer WHERE id = $id");
$customer = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop Pulse</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex flex-col items-center">
        <!-- Top Bar (Updated based on new screenshot) -->
        <header class="w-full header-top py-3 px-4 md:px-8">
            <div class="max-w-7xl mx-auto flex justify-between items-center">
                <!-- Logo -->
                <a href="#" class="text-2xl font-bold text-gray-800">🛍️ ShopPulse</a>

                <!-- Icons -->
                <div class="flex items-center space-x-6">
                    <a href="#" class="icon-btn">
                        <i class="fas fa-heart text-xl"></i>
                    </a>
                    <a href="cart.php" class="relative icon-btn">
                        <i class="fas fa-shopping-cart text-xl"></i>
                    </a>
                    <a href="change_profile.php" class="icon-btn">
                        <img src="../image/<?= htmlspecialchars(basename($customer['image'])) ?>" 
                            width="30" 
                            style="object-fit: cover; border-radius: 50%;">
                    </a>
                    <a href="logout.php" class="logout">
                        <i class="fas fa-sign-out-alt"></i> Log out 
                    </a>
                </div>
            </div>
        </header>

        <!-- Navigation Bar (Updated based on new screenshot) -->
        <nav class="w-full navbar py-3">
            <div class="max-w-7xl mx-auto flex flex-wrap justify-center md:justify-center items-center px-4 md:px-8 space-x-4 md:space-x-8">
                <a href="user_dashboard.php" class="navbar-item">HOME</a>
                <a href="shop.php" class="navbar-item">SHOP</a>
            </div>
        </nav>
  
        <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 leading-tight mb-4"></h1>

        <div class="max-w-6xl mx-auto py-10 px-4">
            <h1 class="text-4xl font-bold text-gray-800 mb-8 text-center">📦 My Orders</h1>

            <?php if ($orders->num_rows > 0): ?>
                <div class="overflow-x-auto bg-white p-6 rounded shadow">
                    <table class="w-full table-auto">
                        <thead>
                            <tr class="bg-gray-100 text-gray-700 uppercase text-sm">
                                <th class="p-3 text-left">Product</th>
                                <th class="p-3 text-left"></th>
                                <th class="p-3 text-left">Quantity</th>
                                <th class="p-3 text-left">Order Date</th>
                                <th class="p-3 text-left">Order ID</th>
                                <th class="p-3 text-left">Total Price</th>
                                <th class="p-3 text-left">Status</th>
                                <th class="p-3 text-left">Payment Status</th>
                                <th class="p-3 text-left">Actions</th> <!-- Add actions column -->
                            </tr>
                        </thead>
                        <tbody class="divide-y text-gray-700">
                            <?php while ($order = $orders->fetch_assoc()): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="p-3">
                                        <img src="../image/<?= htmlspecialchars($order['item_image']) ?>" 
                                             alt="<?= htmlspecialchars($order['item_name']) ?>" 
                                             class="w-16 h-16 object-cover rounded">
                                    </td>
                                    <td class="p-3"><?= htmlspecialchars($order['item_name']) ?></td>
                                    <td class="p-3"><?= htmlspecialchars($order['quantity']) ?></td>
                                    <td class="p-3"><?= date('Y-m-d H:i', strtotime($order['order_date'])) ?></td>
                                    <td class="p-3 font-medium text-blue-600">#<?= $order['order_id'] ?></td>
                                    <td class="p-3 font-semibold text-orange-600">Rs <?= number_format($order['item_total'], 2) ?></td>
                                    <td class="p-3">
                                        <span class="inline-block px-3 py-1 text-sm rounded-full 
                                            <?= $order['status'] == 'Delivered' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' ?>">
                                            <?= $order['status'] ?>
                                        </span>
                                    </td>
                                    <td class="p-3">
                                        <span class="inline-block px-3 py-1 text-sm rounded-full 
                                            <?= $order['payment_status'] == 'Not Paid' ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' ?>">
                                            <?= $order['payment_status'] ?>
                                        </span>
                                    </td>
                                    <td class="p-3">
                                        <?php if ($order['payment_status'] == 'Not Paid'): ?>
                                            
                                            <a href="delete_order.php?id=<?= $order['order_id'] ?>" 
                                               class="text-red-600 hover:underline">Delete</a>
                                        <?php else: ?>
                                            <span class="text-gray-400">N/A</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center text-gray-600 text-lg">
                    <p>You haven’t placed any orders yet.</p>
                    <a href="shop.php" class="text-blue-500 hover:underline mt-2 inline-block">Go to Shop</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    
    <!-- Footer Section -->
    <footer class="w-full bg-gray-800 text-white py-6 mt-12">
        <div class="max-w-7xl mx-auto text-center">
            <div class="text-lg font-semibold mb-4">ShopPulse</div>
            <p class="mb-4">Upgrade your lifestyle with tech that fits your world.</p>
            
            <!-- Payment Methods Section -->
            <div class="mb-4">
                <p class="font-semibold">We Accept Online Payments via:</p>
                <img src="../image/khalti.png" alt="Khalti" class="inline-block w-24 mt-2">
            </div>

            <!-- Contact Info Section -->
            <div class="space-x-6 mb-4">
                <a href="mailto:support@shoppulse.com" class="text-gray-400 hover:text-white">Email: support@shoppulse.com</a>
                <a href="tel:+977123456789" class="text-gray-400 hover:text-white">Phone: +977-9865992479 </a>
            </div>

            <!-- Social Media Links Section -->
            <div class="space-x-6 mb-4">
                <a href="https://www.facebook.com/profile.php?id=61573619773650" target="_blank" class="text-gray-400 hover:text-white">
                    <i class="fab fa-facebook-f"></i> Facebook
                </a>
                <a href="https://www.tiktok.com/@ghising.shop" target="_blank" class="text-gray-400 hover:text-white">
                    <i class="fab fa-tiktok"></i> TikTok
                </a>
                <a href="https://www.instagram.com/ghising.shop/?hl=en" target="_blank" class="text-gray-400 hover:text-white">
                    <i class="fab fa-instagram"></i> Instagram
                </a>
            </div>

            <!-- Footer Copyright -->
            <div class="text-sm">
                <p>&copy; 2025 ShopPulse. All Rights Reserved.</p>
            </div>
        </div>
    </footer>

</body>
</html>
