<?php
session_start();
include '../dbcon.php';

if (isset($_POST['product_id']) && isset($_POST['quantity']) && isset($_POST['size']) && isset($_SESSION['customer_id'])) {
    $customerId = $_SESSION['customer_id'];
    $productId = $_POST['product_id'];
    $quantity = $_POST['quantity'];
    $size = $_POST['size'];
    
    // Check if the product and size already exist in the cart for the customer
    $checkCartQuery = "SELECT * FROM cart WHERE customer_id = $customerId AND product_id = $productId AND size = '$size'";
    $checkCartResult = mysqli_query($connection, $checkCartQuery);
    
    if (mysqli_num_rows($checkCartResult) > 0) {
        // If the product and size already exist in the cart, update the quantity
        $updateCartQuery = "UPDATE cart SET quantity = quantity + $quantity WHERE customer_id = $customerId AND product_id = $productId AND size = '$size'";
        mysqli_query($connection, $updateCartQuery);
    } else {
        // If the product and size do not exist in the cart, insert them
        $insertCartQuery = "INSERT INTO cart (customer_id, product_id, quantity, size) VALUES ($customerId, $productId, $quantity, '$size')";
        mysqli_query($connection, $insertCartQuery);
    }
    
    // Redirect back to the products page after adding to cart
    echo "
    <script>
    alert('Product added to cart');
    window.location.href='customers-page.php';
    </script>
    ";
}

?>
