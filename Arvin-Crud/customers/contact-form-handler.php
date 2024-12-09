<?php
session_start();
include '../dbcon.php';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $customerName = $_POST['name'];
    $customerEmail = $_POST['email'];
    $message = $_POST['message'];
    $messageDate = date('Y-m-d H:i:s'); // Current date and time

    // Prepare the SQL statement to insert the customer's message into the database
    $sql = "INSERT INTO customer_messages (customer_name, customer_email, message, message_date) 
            VALUES (?, ?, ?, ?)";
    
    // Prepare and execute the statement
    $stmt = mysqli_prepare($connection, $sql);
    mysqli_stmt_bind_param($stmt, 'ssss', $customerName, $customerEmail, $message, $messageDate);
    
    if (mysqli_stmt_execute($stmt)) {
        // If the query executes successfully
        echo "
        <script>
        alert('Your message has been sent successfully. Thank you!');
        window.location.href = 'contact-us.php';
        </script>";
    } else {
        // If there is an error executing the query
        echo "Error: " . mysqli_error($connection);
    }
    
    // Close the statement
    mysqli_stmt_close($stmt);
}

// Close the database connection
mysqli_close($connection);
?>
