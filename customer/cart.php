<?php
session_start();
$cart = $_SESSION['cart'] ?? [];
$delivery_charge = 75;
$subtotal = 0;
$order_id = rand(1000, 9999); 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_qty'])) {
        $id = (int) $_POST['item_id'];
        $qty = max(1, (int) $_POST['quantity']);
        if (isset($cart[$id])) {
            $cart[$id]['quantity'] = $qty;
            $_SESSION['cart'] = $cart;
        }
        header("Location: cart.php");
        exit;
    }
    if (isset($_POST['remove_item'])) {
        $id = (int) $_POST['item_id'];
        if (isset($cart[$id])) {
            unset($cart[$id]);
            $_SESSION['cart'] = $cart;
        }
        header("Location: cart.php");
        exit;
    }
    if (isset($_POST['proceed_to_payment'])) {
        // Handle the checkout process (simulate order creation)
        $_SESSION['order_id'] = $order_id; 
        header("Location: user_dashboard.php"); 
        exit;
    }
}

foreach ($cart as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Cart</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        function showOrderConfirmationPopup() {
            const orderId = "<?php echo $order_id; ?>"; 
            alert("🎉 Order Placed Successfully!\nOrder ID: #" + orderId); 
            setTimeout(function() {
                window.location.href = "user_dashboard.php"; 
            }, 2000);
        }
    </script>
</head>
<body class="bg-gray-100">
    <div class="max-w-6xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
        <h1 class="text-4xl font-bold text-gray-800 mb-8 text-center">🛒 Your Shopping Cart</h1>

        <div class="bg-white p-6 rounded-lg shadow-custom">
            <?php if (empty($cart)): ?>
                <p class="text-gray-600 text-lg text-center">Your cart is currently empty. <a href="shop.php" class="text-blue-500 hover:underline mt-2 inline-block"> Shop Now</a></p>
                
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="w-full table-auto mb-8 text-sm sm:text-base">
                        <thead>
                            <tr class="bg-gray-100 text-gray-700 uppercase tracking-wide">
                                <th class="py-3 px-4 text-left">Product</th>
                                <th class="py-3 px-4 text-left">Price</th>
                                <th class="py-3 px-4 text-left">Quantity</th>
                                <th class="py-3 px-4 text-left">Total</th>
                                <th class="py-3 px-4 text-left">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            <?php foreach ($cart as $id => $item): 
                                $total = $item['price'] * $item['quantity'];
                            ?>
                            <tr class="hover:bg-gray-50">
                                <td class="py-4 px-4 flex items-center space-x-4">
                                    <img src="../image/<?php echo htmlspecialchars($item['image']); ?>" alt="Image" class="w-12 h-12 object-cover rounded">
                                    <span class="text-gray-800 font-medium"><?php echo htmlspecialchars($item['item_name']); ?></span>
                                </td>
                                <td class="py-4 px-4 text-orange-600 font-semibold">Rs <?php echo number_format($item['price'], 2); ?></td>
                                <td class="py-4 px-4">
                                    <form method="POST" class="flex items-center gap-2">
                                        <input type="hidden" name="item_id" value="<?php echo $id; ?>">
                                        <input type="number" name="quantity" min="1" value="<?php echo $item['quantity']; ?>" class="w-20 px-2 py-1 border rounded-md text-center">
                                        <button name="update_qty" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded transition">Update</button>
                                    </form>
                                </td>
                                <td class="py-4 px-4 font-semibold text-gray-700">Rs <?php echo number_format($total, 2); ?></td>
                                <td class="py-4 px-4">
                                    <form method="POST">
                                        <input type="hidden" name="item_id" value="<?php echo $id; ?>">
                                        <button name="remove_item" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded transition">Remove</button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Summary -->
                <div class="flex flex-col sm:flex-row sm:justify-end gap-4 mb-6">
                    <div class="bg-gray-50 p-4 rounded shadow-sm w-full sm:max-w-xs text-right text-gray-700">
                        <p class="mb-1">Subtotal: <span class="font-semibold">Rs <?php echo number_format($subtotal, 2); ?></span></p>
                        <p class="mb-1">Delivery: <span class="font-semibold">Rs <?php echo number_format($delivery_charge, 2); ?></span></p>
                        <p class="text-lg font-bold text-gray-800 border-t pt-2 mt-2">Total: Rs <?php echo number_format($subtotal + $delivery_charge, 2); ?></p>
                    </div>
                </div>

                <!-- Checkout Form -->
                <div class="bg-gray-50 p-6 rounded-lg shadow-custom">
                    <h2 class="text-2xl font-semibold mb-4 text-gray-800">Checkout</h2>
                    <form method="POST" action="payment.php" class="space-y-4">
                        <div>
                            <label for="address" class="block font-medium text-gray-700 mb-1">Delivery Address</label>
                            <input id="address" name="address" required class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 p-2" value="<?php echo $_SESSION['address'];?>"></textarea>
                        </div>
                        <div>
                            <label for="phone" class="block font-medium text-gray-700 mb-1">Phone Number</label>
                            <input id="phone" type="text" name="phone" required class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 p-2" value="<?php echo $_SESSION['mobile'];?>">
                        </div>
                        <div class="pt-4">
                            <!-- Proceed to Payment Button -->
                            <button type="submit" name="proceed_to_payment" onclick="showOrderConfirmationPopup()" class="w-full bg-green-600 hover:bg-green-700 text-white text-lg font-semibold py-3 rounded-md transition">Proceed to Payment</button>
                        </div>
                        <div>
                            <!-- <button type="submit" name="epay" onclick="showOrderConfirmationPopup()" class="w-full bg-green-600 hover:bg-green-700 text-white text-lg font-semibold py-3 rounded-md transition">Payment Online</button> -->
                            <a href="epay.php?address=<?php echo urlencode($_SESSION['address']); ?>&phone=<?php echo urlencode($_SESSION['mobile']); ?>">
                                <img src="../image/khalti_nobg.png" alt="" style="width: 20%;">
                            </a>
                        </div>
                       
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </div>
    

</body>
</html>
