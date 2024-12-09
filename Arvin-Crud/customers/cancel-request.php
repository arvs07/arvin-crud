<?php
session_start();
include '../dbcon.php';

if(isset($_POST['request_id']) && isset($_SESSION['customer_id'])) {
    $customerId = $_SESSION['customer_id'];
    $requestId = $_POST['request_id'];

    // Check if the request belongs to the current customer and has a status of "pending"
    $checkRequestQuery = "SELECT * FROM requests WHERE request_id = ? AND customer_id = ? AND status = 'pending'";
    $stmt = mysqli_prepare($connection, $checkRequestQuery);
    mysqli_stmt_bind_param($stmt, "ii", $requestId, $customerId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if(mysqli_num_rows($result) > 0) {
        // If the request is valid, delete it from the database
        $deleteRequestQuery = "DELETE FROM requests WHERE request_id = ?";
        $stmt = mysqli_prepare($connection, $deleteRequestQuery);
        mysqli_stmt_bind_param($stmt, "i", $requestId);
        mysqli_stmt_execute($stmt);

        // Redirect back to the requested products page after canceling
        header("Location: requested-products.php");
        exit();
    } else {
        // If the request is not valid (e.g., doesn't belong to the current customer or is not pending), redirect to an error page
        header("Location: error.php");
        exit();
    }
} else {
    // Redirect to login page if the necessary data is not provided or the customer is not logged in
    header("Location: ../customers-login.php");
    exit();
}
?>
