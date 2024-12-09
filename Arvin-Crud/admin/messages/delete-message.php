<?php
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: ../admin-login.php");
    exit();
}

include '../../dbcon.php';

// Check if a message ID is provided in the POST request
if (isset($_POST['message_id'])) {
    $messageId = $_POST['message_id'];

    // Query to delete the specific message based on the message ID
    $sqlDeleteMessage = "DELETE FROM customer_messages WHERE message_id = ?";

    // Prepare the SQL statement
    $stmt = mysqli_prepare($connection, $sqlDeleteMessage);
    if ($stmt) {
        // Bind the message ID parameter
        mysqli_stmt_bind_param($stmt, "i", $messageId);

        // Execute the statement
        if (mysqli_stmt_execute($stmt)) {
            // If successful, show an alert and redirect back to view messages page
            echo "<script>
            alert('Customer message deleted successfully.');
            window.location.href = 'customer-messages.php';
            </script>";
        } else {
            // Handle any errors during execution
            echo "Error: " . mysqli_stmt_error($stmt);
        }

        // Close the statement
        mysqli_stmt_close($stmt);
    } else {
        echo "Error: Could not prepare the SQL statement.";
    }
} else {
    echo "Error: No message ID provided.";
}

// Close the database connection
mysqli_close($connection);
?>
