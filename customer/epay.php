<?php
include 'a.php';
session_start();
$cart = $_SESSION['cart'] ?? [];

$address = $_GET['address'];
$mobile = $_GET['phone'];
// 
$_SESSION['del_add'] =$_GET['address'];
$_SESSION['del_ph']  =$_GET['phone'];
// 

$delivery_charge = 70;
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




// khalti 
    
    $amount = $subtotal+$delivery_charge; 
    $purchase_order_id = $order_id;
    $purchase_order_name = htmlspecialchars($item['item_name']);
    $name = $_SESSION['name'];
    $email = $_SESSION['email'];
    $phone = $_SESSION['mobile'];

    //here validate the data
    if(empty($amount) || empty($purchase_order_id) || empty($purchase_order_name) || empty($name) || empty($email) || empty($phone)){
        $_SESSION["validate_msg"] = '<script>
        Swal.fire({
            icon: "error",
            title: "All fields are required",
            showConfirmButton: false,
            timer: 1500
        });
    </script>';
        header("Location: payment.php");
        exit();
    }
    //check if the amount is a number
    if(!is_numeric($amount)){
        $_SESSION["validate_msg"] = '<script>
        Swal.fire({
            icon: "error",
            title: "Amount must be a number",
            showConfirmButton: false,
            timer: 1500
        });
    </script>';
        header("Location: payment.php");
        exit();
    }

    //check if the phone number is a number
    if(!is_numeric($phone)){
        $_SESSION["validate_msg"] = '<script>
        Swal.fire({
            icon: "error",
            title: "Phone must be a number",
            showConfirmButton: false,
            timer: 1500
        });
    </script>';
        header("Location: payment.php");
        exit();
    }

    //check if the email is valid
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $_SESSION["validate_msg"] = '<script>
        Swal.fire({
            icon: "error",
            title: "Email is not valid",
            showConfirmButton: false,
            timer: 1500
        });
    </script>';
        header("Location: payment.php");
        exit();
    }

    $postFields = array(
        "return_url" => "http://localhost/abc/customer/payment-response.php",
        "website_url" => "http://localhost/abc/customer/",
        "amount" => $amount*100,
        "purchase_order_id" => $purchase_order_id,
        "purchase_order_name" => $purchase_order_name,
        "customer_info" => array(
            "name" => $name,
            "email" => $email,
            "phone" => $phone
        )
    );


$jsonData = json_encode($postFields);

$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://a.khalti.com/api/v2/epayment/initiate/',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => $jsonData,
    CURLOPT_HTTPHEADER => array(
        'Authorization: key live_secret_key_68791341fdd94846a146f0457ff7b455',
        'Content-Type: application/json',
    ),
));

$response = curl_exec($curl);


if (curl_errno($curl)) {
    echo 'Error:' . curl_error($curl);
} else {
    $responseArray = json_decode($response, true);

    if (isset($responseArray['error'])) {
        echo 'Error: ' . $responseArray['error'];
    } elseif (isset($responseArray['payment_url'])) {
        // Redirect the user to the payment page
        header('Location: ' . $responseArray['payment_url']);
    } else {
        echo 'Unexpected response: ' . $response;
    }
}

curl_close($curl);

?>
