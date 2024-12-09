<?php 
session_start();
include '../dbcon.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <!-- Include SweetAlert2 library -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<?php

// Check if the form is submitted
// Add a new field for the size in the form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['size'])) {
    // Retrieve data from the form, including the size
    $customerId = $_SESSION['customer_id'];
    $productId = $_POST['product_id'];
    $size = $_POST['size'];
    $requestDate = date('Y-m-d H:i:s'); 
    $requestQuantity = $_POST['quantity'];
    
    // Calculate the total cost of the request
    $productSql = "SELECT price FROM products WHERE product_id = ?";
    $stmtProduct = mysqli_prepare($connection, $productSql);
    mysqli_stmt_bind_param($stmtProduct, "i", $productId);
    mysqli_stmt_execute($stmtProduct);
    mysqli_stmt_bind_result($stmtProduct, $productPrice);
    mysqli_stmt_fetch($stmtProduct);
    mysqli_stmt_close($stmtProduct);
    
    $totalCost = $productPrice * $requestQuantity;

    // Fetch the customer's available balance from the database
    $balanceSql = "SELECT amount_of_money FROM customers WHERE customer_id = ?";
    $stmtBalance = mysqli_prepare($connection, $balanceSql);
    mysqli_stmt_bind_param($stmtBalance, "i", $customerId);
    mysqli_stmt_execute($stmtBalance);
    mysqli_stmt_bind_result($stmtBalance, $customerBalance);
    mysqli_stmt_fetch($stmtBalance);
    mysqli_stmt_close($stmtBalance);

    // Check if the customer has sufficient balance
    if ($customerBalance >= $totalCost) {
        // Insert the request with the specified size
        $insertSql = "INSERT INTO requests (customer_id, product_id, size, request_date, request_quantity, status) 
                      VALUES (?, ?, ?, ?, ?, 'pending')";
        $stmt = mysqli_prepare($connection, $insertSql);
        mysqli_stmt_bind_param($stmt, "iisii", $customerId, $productId, $size, $requestDate, $requestQuantity);
        
        // Attempt to execute the prepared statement
        if (mysqli_stmt_execute($stmt)) {
            // Display success message with SweetAlert2
            echo "<script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Product Requested',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(function() {
                        window.location.href='customers-page.php';
                    });
                </script>";
        } else {
            // Handle errors
            echo "Error: " . mysqli_error($connection);
        }
        mysqli_stmt_close($stmt);
    } else {
        // If insufficient balance, display an error message with SweetAlert2
        echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Insufficient balance',
                    text: 'You do not have enough balance to complete the request.',
                    showConfirmButton: false,
                    timer: 2500
                }).then(function() {
                    window.location.href='customers-page.php';
                });
            </script>";
    }
}

?>

</body>
</html>