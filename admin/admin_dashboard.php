<?php
include 'connect.php';
require("functions.php");
session_start();
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
                    <a href="add_item.php">
                        <button class="btn-main bg-orange-500 text-white font-semibold py-3 px-8 rounded-full text-lg shadow-lg">
                        Add New Product
                    </button>
                    </a>
                </div>
                <div class="md:w-1/2 flex justify-center md:justify-end">
                    <img src="../image/logo.png" alt="Tech Gadgets" class="w-full max-w-md rounded-lg shadow-xl">
                </div>
            </section>

        <!-- New Products Section -->
        <section class="mb-12">
            <!-- total products -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-6">
            <div class="card-product bg-white rounded-xl shadow-md p-4 text-center relative">
                <h4 class="font-semibold text-gray-800 mb-1">Total Products</h4>
                <h2 class="text-3xl font-bold text-gray-900"><?php echo get_product_count();?></h2>
                <a href="item.php">
                        <button class="btn-add-cart">View</button>
                    </a>
            </div>
            <!-- total customer -->
             <div class="card-product bg-white rounded-xl shadow-md p-4 text-center relative">
                <h4 class="font-semibold text-gray-800 mb-1">Total Customer</h4>
                <h2 class="text-3xl font-bold text-gray-900"><?php echo get_customer_count();?></h2>
                <a href="customer.php">
                        <button class="btn-add-cart">View</button>
                    </a>
            </div>
        </div><br>
            <!-- new products -->
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-3xl font-bold text-gray-900">New Products</h2>
            </div>
            <!-- list of products -->
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
                    <a href="item.php">
                        <button class="btn-add-cart">Edit</button>
                    </a>
                </div>
            <?php
                }
            ?>
            </div>
        </section>
            </main>
</div>
    
</body>
</html>
