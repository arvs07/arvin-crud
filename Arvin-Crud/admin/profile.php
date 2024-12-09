<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../admin-login.php");
    exit();
}

include '../dbcon.php';

// Query to count total customers
$sqlTotalCustomers = "SELECT COUNT(*) AS total_customers FROM customers";
$resultTotalCustomers = mysqli_query($connection, $sqlTotalCustomers);
$rowTotalCustomers = mysqli_fetch_assoc($resultTotalCustomers);
$totalCustomers = $rowTotalCustomers['total_customers'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link rel="stylesheet" href="../design.css">
</head>

<body>
    <!-- Navigation bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="admin-page.php">
                <img src="../resources/logo1.webp" alt="Starbucks Logo" class="logo" style="width: 100px; height: 40px;">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link active" href="admin-page.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="./customers/customers-list.php">Customers</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="./products/products-list.php">Products</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="./orders/orders-list.php">Orders</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="./messages/customer-messages.php">Customer Messages</a>
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person"></i> Settings
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownMenuLink">
                            <li><a class="dropdown-item" href="profile.php">Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="../logout.php">Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Profile Content -->
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <h2 class="mb-4">Admin Profile</h2>
                <div class="card">
                    <div class="card-body text-center">
                        <?php
                        // Fetch admin profile data from the database
                        $admin_id = $_SESSION['admin_id'];
                        $sql = "SELECT * FROM admins WHERE admin_id = '$admin_id'";
                        $result = mysqli_query($connection, $sql);

                        if ($result) {
                            $admin_data = mysqli_fetch_assoc($result);
                            // Display username
                            echo "<h5>" . htmlspecialchars($admin_data['username']) . "</h5>";
                            
                            // Display profile picture
                            echo '<img src="' . htmlspecialchars($admin_data['profile_picture']) . '" alt="Profile Picture" class="img-thumbnail rounded-circle mb-3" style="height: 200px; width: 200px;">';
                        } else {
                            echo "<p>Error fetching profile data.</p>";
                        }
                        ?>
                        
                        <!-- Form for updating username and profile picture -->
                        <form action="edit-profile.php" method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <input type="file" class="form-control" id="profile_picture" name="profile_picture">
                            </div>

                            <div class="mb-3">
                                <label for="username" class="form-label">Username:</label>
                                <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($admin_data['username']); ?>" required>
                            </div>
                            
                            <button type="submit" name="submit" class="btn btn-primary">Save Changes</button>
                        </form>
                        
                        <!-- Change Password Button -->
                        <button type="button" class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                            Change Password
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Change Password Modal -->
    <div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="changePasswordModalLabel">Change Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="change-password.php" method="POST">
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Current Password:</label>
                            <input type="password" class="form-control" id="current_password" name="current_password" required>
                        </div>
                        <div class="mb-3">
                            <label for="new_password" class="form-label">New Password:</label>
                            <input type="password" class="form-control" id="new_password" name="new_password" required>
                        </div>
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Confirm New Password:</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" name='submit' class="btn btn-primary">Change Password</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" crossorigin="anonymous"></script>
</body>

</html>
