<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../admin-login.php");
    exit();
}

include '../dbcon.php';

// Check if the form is submitted
if (isset($_POST['submit'])) {
    // Get the form data
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Fetch admin_id from session
    $admin_id = $_SESSION['admin_id'];

    // Fetch the current password from the database
    $sql = "SELECT password FROM admins WHERE admin_id = ?";
    $stmt = mysqli_prepare($connection, $sql);
    mysqli_stmt_bind_param($stmt, "i", $admin_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $db_password);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    // Check if the current password is correct
    if ($current_password === $db_password) {
        // Check if the new passwords match
        if ($new_password === $confirm_password) {
            // Update the password in the database
            $update_sql = "UPDATE admins SET password = ? WHERE admin_id = ?";
            $update_stmt = mysqli_prepare($connection, $update_sql);
            mysqli_stmt_bind_param($update_stmt, "si", $new_password, $admin_id);
            mysqli_stmt_execute($update_stmt);
            
            // Check if the update was successful
            if (mysqli_stmt_affected_rows($update_stmt) > 0) {
                echo "<script>alert('Password changed successfully!'); window.location.href = 'profile.php';</script>";
            } else {
                echo "<script>alert('Failed to change password. Please try again.'); window.location.href = 'profile.php';</script>";
            }
            
            mysqli_stmt_close($update_stmt);
        } else {
            echo "<script>alert('New passwords do not match. Please try again.'); window.location.href = 'profile.php';</script>";
        }
    } else {
        echo "<script>alert('Current password is incorrect. Please try again.'); window.location.href = 'profile.php';</script>";
    }
} else {
    echo "<script>alert('Invalid request.'); window.location.href = 'profile.php';</script>";
}

// Close the database connection
mysqli_close($connection);
?>
