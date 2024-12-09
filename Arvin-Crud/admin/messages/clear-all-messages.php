<?php
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: ../admin-login.php");
    exit();
}

include '../../dbcon.php';

// Query to delete all messages from the customer_messages table
$sqlClearAllMessages = "DELETE FROM customer_messages";

if (mysqli_query($connection, $sqlClearAllMessages)) {
    echo "<script>
    alert('All customer messages have been cleared successfully.');
    window.location.href = 'customer-messages.php';
    </script>";
} else {
    echo "Error: " . mysqli_error($connection);
}

// Close the database connection
mysqli_close($connection);
?>
