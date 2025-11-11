<?php
include 'connect.php';
session_start();
$id = $_SESSION['id'];
$result = $conn->query("SELECT * FROM customer WHERE id = $id");
$customer = $result->fetch_assoc();


// 
$connection = mysqli_connect("localhost", "root", "");
$db = mysqli_select_db($connection, "shoppulse");

// Handle search input
$search = '';
if (isset($_GET['search'])) {
  $search = mysqli_real_escape_string($connection, $_GET['search']);
  $query = "SELECT * FROM items WHERE LOWER(item_name) LIKE LOWER('%$search%')";
} else {
  $query = "SELECT * FROM items";
}

$query_run = mysqli_query($connection, $query);
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

                <!-- Search Bar -->
              
                     <form method="GET" action="" class="flex-grow mx-4 flex items-center search-group max-w-lg">
                        <input type="text" name="search" placeholder="Enter your product here..." class="flex-grow py-2 px-4 text-sm" value="<?php echo htmlspecialchars($search); ?>" />
                        <button class="btn-search text-white py-2 px-4 rounded-r-full">
                            <i class="fas fa-search"></i>
                        </button> 
                    </form>

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

        <!-- Main Content Area (Remaining sections from previous version) -->
        <main class="flex-grow w-full max-w-7xl px-4 md:px-8 py-8">
            <!-- Hero Section -->
            <section class="bg-white rounded-xl shadow-lg p-6 md:p-10 flex flex-col md:flex-row items-center justify-between mb-12 overflow-hidden relative">
                <div class="md:w-1/2 text-center md:text-left mb-6 md:mb-0">
                    <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 leading-tight mb-4">
                        Smart Tech<br> Smarter Living
                    </h1>
                    <p class="text-gray-600 text-lg mb-6 max-w-md mx-auto md:mx-0">
                        Upgrade your lifestyle with tech that fits your world.
                    </p>
                    <a href="shop.php">
                        <button class="btn-main bg-orange-500 text-white font-semibold py-3 px-8 rounded-full text-lg shadow-lg">
                        Shop Now
                    </button>
                    </a>
                </div>
                <div class="md:w-1/2 flex justify-center md:justify-end">
                    <img src="../image/logo.png" alt="Tech Gadgets" class="w-full max-w-md rounded-lg shadow-xl">
                </div>
            </section>
            
           
 <!-- New Products Section -->
        <section class="mb-12">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-6">
               
            <?php
                while ($row = mysqli_fetch_assoc($query_run)) {
            ?>
                <div class="card-product bg-white rounded-xl shadow-md p-4 text-center relative">
                    <div class="flex justify-center items-center h-40 mb-4">
                        <img src="../image/<?php echo htmlspecialchars($row['image']); ?>" alt="Image" class="max-h-full max-w-full object-contain">
                    </div>
                    <h4 class="font-semibold text-gray-800 mb-1"><?php echo htmlspecialchars($row['item_name']); ?></h4>
                    <div class="flex justify-center items-center text-yellow-400 text-sm mb-2">
                        <?php echo nl2br(htmlspecialchars($row['item_detail'])); ?>
                    </div>
                    <p class="text-lg font-bold text-orange-500"><?php echo nl2br(htmlspecialchars('RS.' . $row['price'])); ?> </p>
                    <!-- <button class="btn-add-cart">Add to Cart</button> -->
                     <form method="POST" action="add_to_cart.php">
                        <input type="hidden" name="item_id" value="<?php echo $row['item_id']; ?>">
                        <button type="submit" class="btn-add-cart">Add to Cart</button>
                    </form>
                </div>
            <?php
                }
            ?>
            </div>
        </section>  
    </main>
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
