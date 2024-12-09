<?php
session_start();
include '../dbcon.php';

// Ensure the user is logged in and the selected products are provided
if (isset($_POST['selected_products']) && isset($_SESSION['customer_id'])) {
    $customerId = $_SESSION['customer_id'];
    $selectedProducts = json_decode($_POST['selected_products'], true);

    // Retrieve the customer's available balance
    $balanceQuery = "SELECT amount_of_money FROM customers WHERE customer_id = ?";
    $stmtBalance = mysqli_prepare($connection, $balanceQuery);
    mysqli_stmt_bind_param($stmtBalance, "i", $customerId);
    mysqli_stmt_execute($stmtBalance);
    mysqli_stmt_bind_result($stmtBalance, $customerBalance);
    mysqli_stmt_fetch($stmtBalance);
    mysqli_stmt_close($stmtBalance);

    $totalCost = 0;

    // Calculate the total cost of the selected products
    foreach ($selectedProducts as $productId) {
        // Retrieve the quantity of the selected product from the cart
        $quantityQuery = "SELECT quantity FROM cart WHERE customer_id = ? AND product_id = ?";
        $stmtQuantity = mysqli_prepare($connection, $quantityQuery);
        mysqli_stmt_bind_param($stmtQuantity, "ii", $customerId, $productId);
        mysqli_stmt_execute($stmtQuantity);
        mysqli_stmt_bind_result($stmtQuantity, $quantity);
        mysqli_stmt_fetch($stmtQuantity);
        mysqli_stmt_close($stmtQuantity);

        // Retrieve the product price from the database
        $priceQuery = "SELECT price FROM products WHERE product_id = ?";
        $stmtPrice = mysqli_prepare($connection, $priceQuery);
        mysqli_stmt_bind_param($stmtPrice, "i", $productId);
        mysqli_stmt_execute($stmtPrice);
        mysqli_stmt_bind_result($stmtPrice, $productPrice);
        mysqli_stmt_fetch($stmtPrice);
        mysqli_stmt_close($stmtPrice);

        // Calculate the cost for the product and add it to the total cost
        $totalCost += $productPrice * $quantity;
    }

    // Check if the customer has sufficient balance
    if ($customerBalance >= $totalCost) {
        // Insert each selected product into the request table
        foreach ($selectedProducts as $productId) {
            // Retrieve the quantity and size of the selected product from the cart
            $quantityAndSizeQuery = "SELECT quantity, size FROM cart WHERE customer_id = ? AND product_id = ?";
            $stmtQuantityAndSize = mysqli_prepare($connection, $quantityAndSizeQuery);
            mysqli_stmt_bind_param($stmtQuantityAndSize, "ii", $customerId, $productId);
            mysqli_stmt_execute($stmtQuantityAndSize);
            mysqli_stmt_bind_result($stmtQuantityAndSize, $quantity, $size);
            mysqli_stmt_fetch($stmtQuantityAndSize);
            mysqli_stmt_close($stmtQuantityAndSize);
        
            // Insert the selected product, quantity, and size into the request table
            $insertRequestQuery = "INSERT INTO requests (customer_id, product_id, request_quantity, size, request_date, status) VALUES (?, ?, ?, ?, NOW(), 'pending')";
            $stmtInsert = mysqli_prepare($connection, $insertRequestQuery);
            mysqli_stmt_bind_param($stmtInsert, "iiis", $customerId, $productId, $quantity, $size);
            mysqli_stmt_execute($stmtInsert);
            mysqli_stmt_close($stmtInsert);
        }

        // Clear the cart after adding products to the request
        $clearCartQuery = "DELETE FROM cart WHERE customer_id = ? AND product_id IN (" . implode(',', $selectedProducts) . ")";
        $stmtClearCart = mysqli_prepare($connection, $clearCartQuery);
        mysqli_stmt_bind_param($stmtClearCart, "i", $customerId);
        mysqli_stmt_execute($stmtClearCart);
        mysqli_stmt_close($stmtClearCart);

        echo "<script>
            alert('Products requested successfully.');
            window.location.href='customers-page.php';
        </script>";
    } else {
        // If the customer's balance is insufficient, display an error message
        echo "<script>
            alert('Insufficient balance to complete the request.');
            window.location.href='customers-page.php';
        </script>";
    }
} else {
    // Redirect to login page if the necessary data is not provided or the customer is not logged in
    header("Location: ../customers-login.php");
    exit();
}
?>
