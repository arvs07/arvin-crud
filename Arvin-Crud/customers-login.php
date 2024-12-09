<?php
session_start();
include 'dbcon.php'; // Include your database connection file

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Login</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="sign-in-up.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>

<body>
<div class="top-right-button position-fixed top-0 end-0 m-3">
        <a href="admin-login.php" class="btn btn-secondary">Admin Login</a>
    </div>

    <div class="container mt-5">
        <div class="card mx-auto" style="max-width: 400px; padding: 20px;">
            <div class="card-body text-center">
                <h2 class="mb-4">Customer Login</h2>
                
                <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
                    <div class="mb-3">
                        <input type="email" class="form-control" id="email" name="email" placeholder="Email" required>
                    </div>
                    <div class="mb-3">
                        <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Login</button>
                </form>
                
                <!-- Error message -->
                <?php if (isset($login_err)) { ?>
                    <div class="alert alert-danger mt-3"><?php echo $login_err; ?></div>
                <?php } ?>

                <div class="mt-4">
                    <a href="customers-signup.php" class="btn btn-secondary w-100 mb-2">Customer Registration</a>
                    <a href="./customers/customers-page.php" class="btn btn-link">View Products</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $email = $_POST['email'];
        $password = $_POST['password'];

        // SQL query to fetch the hashed password for the given email
        $sql = "SELECT customer_id, password FROM customers WHERE email = ?";
        $stmt = mysqli_prepare($connection, $sql);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        // Check if the email exists in the database and fetch the row
        if ($row = mysqli_fetch_assoc($result)) {
            // Verify the password using password_verify
            if (password_verify($password, $row['password'])) {
                // Passwords match, login the user and set session variables
                $_SESSION['customer_id'] = $row['customer_id'];
                $_SESSION['email'] = $email;

                echo "
                <script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Login Successful!',
                        text: 'Redirecting to customer dashboard...',
                        timer: 1000,
                        timerProgressBar: true,
                        showConfirmButton: false
                    }).then(function() {
                        window.location.href = './customers/customers-page.php';
                    });
                </script>
                ";
            } else {
                // Password does not match
                $login_err = "Invalid email or password";
            }
        } else {
            // Email does not exist
            $login_err = "Invalid email or password";
        }

        // Close the prepared statement and database connection
        mysqli_stmt_close($stmt);
        mysqli_close($connection);
    }
    ?>
</body>

</html>
