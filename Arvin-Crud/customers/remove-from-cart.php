<?php
session_start();
include '../dbcon.php';

if(isset($_POST['selected_products']) && isset($_SESSION['customer_id'])) {
    $customerId = $_SESSION['customer_id'];
    $selectedProducts = json_decode($_POST['selected_products'], true);

    // Check if $selectedProducts is an array and not null
    if(is_array($selectedProducts) && count($selectedProducts) > 0) {
        // Construct the SQL query to delete selected products from the cart
        $deleteQuery = "DELETE FROM cart WHERE customer_id = ? AND product_id IN (";
        $placeholders = str_repeat('?, ', count($selectedProducts) - 1) . '?';
        $deleteQuery .= $placeholders . ")";
        
        // Prepare and execute the statement
        $stmt = mysqli_prepare($connection, $deleteQuery);
        
        // Construct the type definition string dynamically based on the number of selected products
        $typeString = str_repeat('i', count($selectedProducts) + 1); // One for customer_id, rest for product_ids
        
        // Create an array with customer_id as the first parameter
        $bindParams = array($customerId);
        
        // Append product_ids to the bindParams array
        foreach($selectedProducts as $productId) {
            $bindParams[] = $productId;
        }
        
        // Bind parameters and execute the statement
        mysqli_stmt_bind_param($stmt, $typeString, ...$bindParams);
        mysqli_stmt_execute($stmt);
        echo "
        <script>
            alert('Removed from the cart');
            window.location.href='customers-page.php';
        </script>
        ";
    } else {
        // Handle the case where no products were selected
        echo "No products selected to remove.";
        exit();
    }
} else {
    // Redirect to login page if the necessary data is not provided or the customer is not logged in
    header("Location: ../customers-login.php");
    exit();
}
?>
