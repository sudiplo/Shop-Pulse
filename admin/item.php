<?php
include 'connect.php';
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
<h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 leading-tight mb-4"></h1>
<!-- main content -->
    <main class="main-content">
        <header><h1 class="text-2xl font-bold text-gray-800">Items List</h1></header>
        <section class="product-table">
        <table>
          <thead>
            <tr>
              <!-- <th>ID</th> -->
              <th>Image</th>
              <th>Name</th>
              <th>Price</th>
              <th>Stock</th>
              <th>Details</th>
              <th>Actions</th>
              
            </tr>
          </thead>
          <tbody>
            
          

    <!-- New Products Section -->
        <section class="mb-12">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-6">
               
            <?php
                while ($row = mysqli_fetch_assoc($query_run)) {
            ?>
                <tr>
              <!-- <td><?= $row['item_id'] ?></td> -->

              
              <td>
                <img src="../image/<?= htmlspecialchars(basename($row['image'])) ?>" 
                     width="40" height="40" 
                     style="object-fit: cover; border-radius: 4px;">
              </td>

              <td><?= htmlspecialchars($row['item_name']) ?></td>
              <td>Rs. <?= htmlspecialchars($row['price']) ?></td>
              <td><?= htmlspecialchars($row['stock']) ?></td>
              <td><?= htmlspecialchars($row['item_detail']) ?></td>
              <td>
                <a href="edit_item.php?id=<?= $row['item_id'] ?>" class="edit">Edit</a>
               <a href="delete_item.php?id=<?= $row['item_id'] ?>" onclick="return confirm('Are you sure you want to delete this item?');" class="delete">Delete</a>
</td>
            <?php
                }
            ?>
            </div>
        </section>
    </div>
</tbody>
        </table>
      </section>
    </main>
    
</body>
</html>
