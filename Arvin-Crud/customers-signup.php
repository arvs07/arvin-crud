<?php
session_start();
include 'dbcon.php'; // Include your database connection file

// Initialize an empty error message
$signup_error = "";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Signup</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="sign-in-up.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>

<body>
    <div class="container mt-5">
        <div class="card mx-auto" style="max-width: 500px;">
            <div class="card-body">
                <h2 class="text-center mb-4">Customer Signup</h2>
                
                <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
                    <div class="mb-3">
                        <input type="text" class="form-control" id="first_name" name="first_name" placeholder="First Name" required aria-label="First Name">
                    </div>
                    <div class="mb-3">
                        <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Last Name" required aria-label="Last Name">
                    </div>
                    <div class="mb-3">
                        <input type="email" class="form-control" id="email" name="email" placeholder="Email" required aria-label="Email">
                    </div>
                    <div class="mb-3">
                        <input type="password" class="form-control" id="password" name="password" placeholder="Password" required aria-label="Password">
                    </div>
                    <div class="mb-3">
                        <input type="tel" class="form-control" id="phone" name="phone" placeholder="Phone Number" required aria-label="Phone Number">
                    </div>
                    <div class="mb-3">
                        <input type="text" class="form-control" id="province" name="province" placeholder="Province" required aria-label="Province">
                    </div>
                    <div class="mb-3">
                        <input type="text" class="form-control" id="city" name="city" placeholder="City" required aria-label="City">
                    </div>
                    <div class="mb-3">
                        <input type="text" class="form-control" id="barangay" name="barangay" placeholder="Barangay" required aria-label="Barangay">
                    </div>
                    <button type="submit" class="btn btn-primary w-100 mb-2">Sign Up</button>
                    <button type="button" class="btn btn-link w-100" onclick="window.location.href='customers-login.php'">
                        Already have an account? Log In
                    </button>
                    
                    <!-- Error message -->
                    <?php if (isset($signup_error) && !empty($signup_error)) { ?>
                        <div class="alert alert-danger mt-3"><?php echo $signup_error; ?></div>
                    <?php } ?>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
    <?php
    // Check if the form has been submitted
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        // Get form data and sanitize it
        $first_name = $connection->real_escape_string($_POST["first_name"]);
        $last_name = $connection->real_escape_string($_POST["last_name"]);
        $email = $connection->real_escape_string($_POST["email"]);
        $password = $connection->real_escape_string($_POST["password"]);
        $phone = $connection->real_escape_string($_POST["phone"]);
        $province = $connection->real_escape_string($_POST["province"]);
        $city = $connection->real_escape_string($_POST["city"]);
        $barangay = $connection->real_escape_string($_POST["barangay"]);

        // Check if the email already exists in the database
        $check_email_query = "SELECT customer_id FROM customers WHERE email = '$email'";
        $result = $connection->query($check_email_query);

        if ($result->num_rows > 0) {
            $signup_error = "Email already exists. Please use a different email.";
        } else {
            // Hash the password before inserting it into the database
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert the user's data into the database
            $insert_query = "INSERT INTO customers (first_name, last_name, email, password, phone, province, city, barangay)
                            VALUES ('$first_name', '$last_name', '$email', '$hashed_password', '$phone', '$province', '$city', '$barangay')";

            if ($connection->query($insert_query) === TRUE) {
                // Registration successful, you can redirect the user to a success page or show a success message
                echo "
                <script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Account Created!',
                        text: 'Welcome to Starbucks.',
                        showConfirmButton: false,
                        timer: 1000
                    }).then(function() {
                        window.location.href='customers-login.php';
                    });
                </script>
                ";
            } else {
                $signup_error = "Error: " . $insert_query . "<br>" . $connection->error;
            }
        }
    }
    ?>
</body>

</html>
