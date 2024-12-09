<?php

session_start();

// Check if the user is logged in and is an employee
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'employee') {
    header("Location: ../admin-login.php");
    exit();
}

include '../dbcon.php';

// Query to count total customers
$sqlTotalCustomers = "SELECT COUNT(*) AS total_customers FROM customers";
$resultTotalCustomers = mysqli_query($connection, $sqlTotalCustomers);
$rowTotalCustomers = mysqli_fetch_assoc($resultTotalCustomers);
$totalCustomers = $rowTotalCustomers['total_customers'];

// Query to count total products
$sqlTotalProducts = "SELECT COUNT(*) AS total_products FROM products";
$resultTotalProducts = mysqli_query($connection, $sqlTotalProducts);
$rowTotalProducts = mysqli_fetch_assoc($resultTotalProducts);
$totalProducts = $rowTotalProducts['total_products'];

// Query to fetch requested products
$sqlRequestedProducts = "SELECT r.*, p.name AS product_name, c.email AS customer_email
                        FROM requests r
                        INNER JOIN products p ON r.product_id = p.product_id
                        INNER JOIN customers c ON r.customer_id = c.customer_id WHERE status = 'pending'";
$resultRequestedProducts = mysqli_query($connection, $sqlRequestedProducts);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="../design.css">
</head>
<body>
    
    <!-- Navigation bar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="employee-page.php">
            <img src="../resources/logo1.webp" alt="Starbucks Logo" class="logo" style="width: 100px; height: 40px;">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link active bg-secondary rounded" aria-current="page" href="employee-page.php">Dashboard</a>
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
                    <li class="nav-item">
                        <a class="nav-link dropdown-toggle" href="#" role="button" id="navbarDropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
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
    
    <!-- Dashboard Content -->
    <div class="container mt-4">
        <div class="text-center" >
        <h2>Welcome, <?php echo $_SESSION['username']; ?></h2>
        </div>
        <h1>Dashboard</h1>
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Total Customers</h5>
                        <p class="card-text"><?php echo $totalCustomers; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Total Products</h5>
                        <p class="card-text"><?php echo $totalProducts; ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
     <!-- Dashboard Content -->
     <div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Requested Products</h5>
                    <table class="table text-center">
                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th>Customer Email</th>
                                <th>Quantity</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            while ($row = mysqli_fetch_assoc($resultRequestedProducts)) {
                                echo "<tr>";
                                echo "<td>".$row['product_name']."</td>";
                                echo "<td>".$row['customer_email']."</td>";
                                echo "<td>".$row['request_quantity']."</td>";
                                echo "<td>";
                                echo "<form action='./process-request/approve-request.php' method='post'>";
                                echo "<input type='hidden' name='request_id' value='".$row['request_id']."'>";
                                echo "<button type='submit' class='btn btn-success btn-sm'>Approve</button>";
                                echo "</form><br>";
                                echo "<form action='./process-request/decline-request.php' method='post'>";
                                echo "<input type='hidden' name='request_id' value='".$row['request_id']."'>";
                                echo "<button type='submit' class='btn btn-danger btn-sm'>Decline</button>";
                                echo "</form>";
                                echo "</td>";
                                echo "</tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
</body>
</html>
