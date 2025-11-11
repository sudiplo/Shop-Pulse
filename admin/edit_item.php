<?php
include 'connect.php';

$id = $_GET['id'];
$result = $conn->query("SELECT * FROM items WHERE item_id = $id");
$item = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['item_name'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $detail = $_POST['item_detail'];
    $image = $_POST['image'];

    $stmt = $conn->prepare("UPDATE items SET item_name=?, price=?, stock=?, item_detail=?, image=? WHERE item_id=?");
    $stmt->bind_param("ssissi", $name, $price, $stock, $detail, $image, $id);
    $stmt->execute();

    header("Location: admin_dashboard.php");
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

<!-- main content -->
    <div class="col-2">
    <form method="POST">
      <div class="page-title">
        <h3>Edit Item</h3>
      </div>

      <div class="form-group">
        <label>Item Name</label>
        <input type="text" name="item_name" value="<?= $item['item_name'] ?>" required><br>
      </div>

      <div class="form-group">
        <label>Price</label>
        <input type="text" name="price" value="<?= $item['price'] ?>" min="0" required><br>
      </div>

      <div class="form-group">
        <label>Stock</label>
        <input type="number" name="stock" value="<?= $item['stock'] ?>" min="0" required><br>
      </div>

      <div class="form-group">
        <label>Item Detail</label>
        <input type="text" name="item_detail" value="<?= $item['item_detail'] ?>" required><br>
      </div>

      <div class="form-group">
        <label>Image</label>
        <input type="text" name="image" value="<?= $item['image'] ?>" required><br>
      </div>
      <div class="form-group"></div>
        <button type="submit" class="edit">Update Item</button>
      </form>
    </form>
  </div>
    </div>
    </div>
</body>
</html>
