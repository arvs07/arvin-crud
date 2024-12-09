<?php
session_start();
include 'dbcon.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="sign-in-up.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>

<body>
    <div class="container mt-5">
        <div class="card mx-auto" style="max-width: 500px;">
            <div class="card-body">
                <h2 class="text-center mb-4">Admin Login</h2>
                
                <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
                    <div class="mb-3">
                        <input type="text" class="form-control" id="username" name="username" placeholder="Username" required aria-label="Username">
                    </div>
                    <div class="mb-3">
                        <input type="password" class="form-control" id="password" name="password" placeholder="Password" required aria-label="Password">
                    </div>
                    <div class="mb-3">
                        <select class="form-control" id="userType" name="user_type" required aria-label="User Type">
                            <option value="admin">Admin</option>
                            <option value="employee">Employee</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 mb-2">Login</button>
                    
                    <!-- Error message -->
                    <?php if (isset($login_err)) { ?>
                        <div class="alert alert-danger mt-3"><?php echo $login_err; ?></div>
                    <?php } ?>
                </form>
                
                <div class="mt-3 text-center">
                    <a href="customers-login.php" class="btn btn-link">Switch to Customer Login</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = $_POST['username'];
        $password = $_POST['password'];

        // SQL query to check the provided credentials and the role
        $sql = "SELECT * FROM admins WHERE username = ? AND password = ? AND role = ?";
        $stmt = mysqli_prepare($connection, $sql);

        // Bind the username, password, and role to the query
        mysqli_stmt_bind_param($stmt, "sss", $username, $password, $role);

        // Check if the form contains a user type selection
        if (isset($_POST['user_type'])) {
            $role = $_POST['user_type'];
        } else {
            // Default to 'admin' if no user type is specified
            $role = 'admin';
        }

        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);

        if ($row) {
            // User found, set session variables and redirect to the appropriate dashboard
            $_SESSION['admin_id'] = $row['admin_id'];
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $role;
        
            if ($role == 'admin') {
                // Success message with SweetAlert
                echo '<script>
                        Swal.fire({
                            icon: "success",
                            title: "Login Successful!",
                            text: "Redirecting to admin dashboard...",
                            timer: 1000,
                            timerProgressBar: true,
                            showConfirmButton: false
                        }).then(function() {
                            window.location.href = "./admin/admin-page.php";
                        });
                      </script>';
            } else {
                // Success message with SweetAlert
                echo '<script>
                        Swal.fire({
                            icon: "success",
                            title: "Login Successful!",
                            text: "Redirecting to employee dashboard...",
                            timer: 1000,
                            timerProgressBar: true,
                            showConfirmButton: false
                        }).then(function() {
                            window.location.href = "./employees/employee-page.php";
                        });
                      </script>';
            }
            exit();
        } else {
            $login_err = "Invalid username or password";
        }

        mysqli_stmt_close($stmt);
        mysqli_close($connection);
    }
    ?>
</body>

</html>
