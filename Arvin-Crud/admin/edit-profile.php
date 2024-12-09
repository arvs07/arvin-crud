<?php
session_start();
// Check if the user is logged in and is an employee
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: ../admin-login.php");
    exit();
}

include '../dbcon.php';

// Fetch the admin_id from the session
$admin_id = $_SESSION['admin_id'];

// Get the form data
$username = $_POST['username'];
$profile_picture = $_FILES['profile_picture']['tmp_name'];

$update_query = "UPDATE admins SET username = ?";

if (!empty($profile_picture)) {
    // If a profile picture is uploaded, add the file path to the query
    $profile_picture_path = '../profile-picture/' . basename($_FILES['profile_picture']['name']);
    move_uploaded_file($profile_picture, $profile_picture_path);
    $update_query .= ", profile_picture = ?";
}

// Add the WHERE clause to target the specific admin record
$update_query .= " WHERE admin_id = ?";

// Prepare and bind
$stmt = mysqli_prepare($connection, $update_query);

if (!empty($profile_picture)) {
    mysqli_stmt_bind_param($stmt, "ssi", $username, $profile_picture_path, $admin_id);
} else {
    mysqli_stmt_bind_param($stmt, "si", $username, $admin_id);
}

// Execute the update query
mysqli_stmt_execute($stmt);

// Check for errors
if (mysqli_stmt_error($stmt)) {
    echo "Error updating profile: " . mysqli_stmt_error($stmt);
} else {
    echo "Profile updated successfully!";
}

// Close statement and connection
mysqli_stmt_close($stmt);
mysqli_close($connection);

// Redirect back to the profile page
header("Location: profile.php");
exit(); // It's important to call exit after the header redirect