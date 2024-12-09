<?php
session_start();
include '../../dbcon.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../../admin-login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['request_id'])) {
    $request_id = $_POST['request_id'];

    // Update the status of the request to 'declined' in the database
    $update_status_sql = "UPDATE requests SET status = 'declined' WHERE request_id = ?";
    $update_status_stmt = mysqli_prepare($connection, $update_status_sql);
    mysqli_stmt_bind_param($update_status_stmt, "i", $request_id);
    
    if (mysqli_stmt_execute($update_status_stmt)) {
        // Redirect to the dashboard with a success message
        echo "<script> alert('Request Declined!'); window.location.href='../admin-page.php' </script>";
    } else {
        // Redirect to the dashboard with an error message if the update fails
        $_SESSION['error_message'] = "Failed to decline request. Please try again.";
        header("Location: ../admin-page.php");
        exit();
    }
} else {
    // Redirect to the dashboard if the request ID is not set
    header("Location: ../admin-page.php");
    exit();
}
?>
