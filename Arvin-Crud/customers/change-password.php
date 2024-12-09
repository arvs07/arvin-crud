<?php
session_start();
include '../dbcon.php';

// Check if the user is logged in
if (!isset($_SESSION['customer_id']) || empty($_SESSION['customer_id'])) {
    header("Location: ../customers-login.php");
    exit();
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $customer_id = $_SESSION['customer_id'];
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_new_password = $_POST['confirm_new_password'];

    // Check if the new password and confirm password match
    if ($new_password !== $confirm_new_password) {
        echo "
        <script>
        alert('New password and confirm password do not match.');
        window.location.href='profile.php';
        </script>
        ";
        exit();
    }

    // Fetch the current hashed password from the database
    $sql = "SELECT password FROM customers WHERE customer_id = ?";
    $stmt = mysqli_prepare($connection, $sql);
    mysqli_stmt_bind_param($stmt, "i", $customer_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);

    // Close the statement
    mysqli_stmt_close($stmt);

    // Check if the current password matches the stored hashed password
    if ($user && password_verify($current_password, $user['password'])) {
        // Hash the new password before updating
        $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);

        // Update the password in the database
        $update_sql = "UPDATE customers SET password = ? WHERE customer_id = ?";
        $update_stmt = mysqli_prepare($connection, $update_sql);
        mysqli_stmt_bind_param($update_stmt, "si", $hashed_new_password, $customer_id);
        mysqli_stmt_execute($update_stmt);
        
        // Close the statement
        mysqli_stmt_close($update_stmt);

        // Redirect back to the profile page with a success message
        echo "
        <script>
        alert('Password Updated.'); 
        window.location.href='profile.php';
        </script>
        ";
    } else {
        // If current password does not match, show an error message
        echo "
        <script>
        alert('Current password is incorrect.');
        window.location.href='profile.php';
        </script>
        ";
        exit();
    }
}