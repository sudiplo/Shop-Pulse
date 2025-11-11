<?php 
include 'connect.php';
session_start(); 

// Assuming $conn is your database connection
$cart = $_SESSION['cart'] ?? []; 
$delivery_charge = 70; 
$subtotal = 0;
$order_id = rand(1000, 9999); 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['proceed_to_payment'])) {
        
        $_SESSION['order_id'] = $order_id; 

        // Fetch user info from session (ensure these are set during login)
        $user_id = $_SESSION['id']; 
        $user_name = $_SESSION['name']; 
        $email = $_SESSION['email']; 
        $address = $_SESSION['del_add']; 
        $phone = $_SESSION['del_ph'] ; 


        // Calculate the subtotal from the cart
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        $total = $subtotal + $delivery_charge;

        // Insert the order into the `orders` table
        $stmt = $conn->prepare("INSERT INTO orders (user_id, user_name, email, address, phone, total_amount, payment_status, status) 
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssdsss", $user_id, $user_name, $email, $address, $phone, $total, $payment_status, $status);
        $payment_status = 'Paid'; 
        $status = 'In Process'; 
        $stmt->execute();
        $order_id_db = $stmt->insert_id; 
        $stmt->close();

        // Insert the order items into `order_items` table
        $stmt = $conn->prepare("INSERT INTO order_items (order_id, item_id, item_name, price, quantity, total) 
                                VALUES (?, ?, ?, ?, ?, ?)");
        foreach ($cart as $item) {
            $item_name = $item['item_name'];
            $price = $item['price'];
            $quantity = $item['quantity'];
            $total_item = $price * $quantity; 

            // Get item ID from the `items` table
            $stmt_item = $conn->prepare("SELECT item_id FROM items WHERE item_name = ?");
            $stmt_item->bind_param("s", $item_name);
            $stmt_item->execute();
            $result = $stmt_item->get_result();
            $item_id = $result->fetch_assoc()['item_id'];
            $stmt_item->close();

            // Insert item into `order_items` table
            $stmt->bind_param("iisdis", $order_id_db, $item_id, $item_name, $price, $quantity, $total_item);
            $stmt->execute();
        }
        $stmt->close();
        unset($_SESSION['cart']);

        $_SESSION['transaction_msg'] = "<script>Swal.fire('Success!', 'Your payment has been processed successfully.', 'success');</script>";

        header("Location: user_dashboard.php");
        exit;
    }
}

?>


<!--  -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment successful</title>

    <!-- bootstrap css -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<body>
    <?php
    if (isset($_SESSION['transaction_msg'])) {
        echo $_SESSION['transaction_msg'];
        unset($_SESSION['transaction_msg']);
    }
    ?>

    <div class="mt-5 d-flex justify-content-center">
        <div class="mb-3">
            <img src="payment-success.jpg" class="img-flud" alt="">
            <div class="card">
                <div class="card-body text-white bg-success">
                    <h5 class="card-title">Dear Customer,</h5>
                    <p class="card-text">
                        Your payment has been successfully processed. Thank you for shopping with us.
                    </p>
                </div>
                <div class="card-footer">
                    <form method="POST" action="">
                        <button type="submit" name="proceed_to_payment" class="btn btn-primary">Back to Checkout</button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</body>

</html>