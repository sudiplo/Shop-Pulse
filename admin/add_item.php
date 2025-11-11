<?php
include 'connect.php';
require 'functions.php';
$item_name = $price = $stock = $item_detail = $image_name = "";
$updateMode = false;

if (isset($_GET['id'])) {
    $updateMode = true;
    $item_id = $_GET['id'];

    $stmt = $conn->prepare("SELECT * FROM items WHERE item_id = ?");
    $stmt->bind_param("i", $item_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $item_name = $row['item_name'];
        $price = $row['price'];
        $stock = $row['stock'];
        $item_detail = $row['item_detail'];
        $image_name = $row['image'];
    }
}

if (isset($_POST['btnSubmit'])) {
    $item_name = $_POST['item_name'];
    $price = preg_replace("/[^0-9.]/", "", $_POST['price']);
    $stock = $_POST['stock'];
    $item_detail = $_POST['item_detail'];

    $image = $_FILES['image']['name'];
    $img_tmp = $_FILES['image']['tmp_name'];
    $type = $_FILES['image']['type'];
    $size = $_FILES['image']['size'];

    // Image validation
    $allowed_types = ["image/jpeg", "image/jpg", "image/png", "image/gif"];
    if (!empty($image)) {
        if (!in_array($type, $allowed_types)) {
            die("<script>alert('Invalid image type'); window.history.back();</script>");
        }
        if (($size / (1024 * 1024)) > 2) {
            die("<script>alert('Image too large. Max size 2MB'); window.history.back();</script>");
        }
        $image_name = "item_" . time() . "." . pathinfo($image, PATHINFO_EXTENSION);
        move_uploaded_file($img_tmp, "../updateImg/" . $image_name);
    }

    if ($updateMode) {
        if (!empty($image)) {
            if (!empty($image_name)) {
                @unlink("../updateImg/" . $image_name);
            }
            $stmt = $conn->prepare("UPDATE items SET item_name=?, price=?, stock=?, item_detail=?, image=? WHERE item_id=?");
            $stmt->bind_param("sdissi", $item_name, $price, $stock, $item_detail, $image_name, $item_id);
        } else {
            $stmt = $conn->prepare("UPDATE items SET item_name=?, price=?, stock=?, item_detail=? WHERE item_id=?");
            $stmt->bind_param("sdisi", $item_name, $price, $stock, $item_detail, $item_id);
        }
    } else {
        $stmt = $conn->prepare("INSERT INTO items (item_name, price, stock, item_detail, image) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sdiss", $item_name, $price, $stock, $item_detail, $image_name);
    }

    if (!$stmt->execute()) {
        die("<script>alert('Error: " . $stmt->error . "'); window.history.back();</script>");
    }

    echo "<script>alert('Item " . ($updateMode ? "updated" : "added") . " successfully.'); window.location.href='admin_dashboard.php';</script>";
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
                <div class="page-title">
                    <h3><?php echo $updateMode ? "Edit" : "Add"; ?> Item</h3>
                </div>

                <form method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label>Item Name</label>
                        <input type="text" name="item_name" value="<?= htmlspecialchars($item_name); ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Image</label>
                        <input type="file" name="image">
                        <?php if ($updateMode && !empty($image_name)): ?>
                            <img src="../updateImg/<?= htmlspecialchars($image_name); ?>" style="height:100px;width:100px;">
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label>Price</label>
                        <input type="text" name="price" value="<?= $price ? 'RS ' . htmlspecialchars($price) : ''; ?>" min="0" required oninput="formatPrice(this)" >
                    </div>

                    <div class="form-group">
                        <label>Stock</label>
                        <input type="number" name="stock" value="<?= htmlspecialchars($stock); ?>" min="0" required>
                    </div>

                    <div class="form-group">
                        <label>Item Detail</label>
                        <textarea name="item_detail" required><?= htmlspecialchars($item_detail); ?></textarea>
                    </div>

                    <div class="form-group">
                        <input type="submit" name="btnSubmit" value="<?= $updateMode ? "Update" : "Add"; ?>">
                    </div>
                </form>
            </div>

    </div>

    <script>
    function formatPrice(input) {
        let value = input.value.replace(/[^\d.]/g, '');
        if (value) {
            input.value = "RS " + value;
        }
    }
    </script>

    </div>

</body>
</html>
