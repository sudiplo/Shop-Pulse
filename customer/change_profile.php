<?php
include 'connect.php';
session_start();
$id = $_SESSION['id'];
$result = $conn->query("SELECT * FROM customer WHERE id = $id");
$customer = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $mobile = $_POST['mobile'];
    $address = $_POST['address'];
    $image = $_POST['image'];

    $stmt = $conn->prepare("UPDATE customer SET name=?, email=?, mobile=?, address=?, image=? WHERE id=?");
    $stmt->bind_param("ssissi", $name, $email, $mobile, $address, $image, $id);
    $stmt->execute();

    header("Location: user_dashboard.php");
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
    <link rel="stylesheet" href="../admin/table.css">
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
                    <a href="my_orders.php" class="icon-btn">
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

<!-- main content -->
    <div class="col-2">
    <form method="POST">
      <div class="page-title">
        <h3>Proile Change</h3>
      </div>
      <div class="flex justify-center items-center h-40 mb-4">
         <img src="../image/<?= htmlspecialchars(basename($customer['image'])) ?>" 
                     width="200" height="200" 
                     style="object-fit: cover; border-radius: 4px;">
      </div>
      <div class="form-group">
        <label>Name</label>
        <input type="text" name="name" value="<?= $customer['name'] ?>" required><br>
      </div>

      <div class="form-group">
        <label>Email</label>
        <input type="text" name="email" value="<?= $customer['email'] ?>"  required><br>
      </div>

      <div class="form-group">
        <label>Mobile</label>
        <input type="text" name="mobile" value="<?= $customer['mobile'] ?>" min="0" required><br>
      </div>

      <div class="form-group">
        <label>Address</label>
        <input type="text" name="address" value="<?= $customer['address'] ?>" required><br>
      </div>

      <div class="form-group">
        <label>Image</label>
        <input type="text" name="image" value="<?= $customer['image'] ?>" required><br>
      </div>
      <div class="form-group"></div>
        <button type="submit" class="edit">Update Item</button>
      </form>
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
