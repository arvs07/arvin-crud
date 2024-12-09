<?php
session_start();

// Include database connection
include '../../dbcon.php';

// Check if the user is logged in and is an employee
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'employee') {
    header("Location: ../admin-login.php");
    exit();
}
// Query to fetch order information with customer details
$sql = "SELECT orders.order_id, 
               CONCAT(customers.first_name, ' ', customers.last_name) AS fullname, 
               customers.phone, 
               CONCAT(province, ', ', city, ', ', barangay) As 'address',
               orders.order_date, 
               orders.total_amount
        FROM orders
        INNER JOIN customers ON orders.customer_id = customers.customer_id";
$result = mysqli_query($connection, $sql);

// Query to fetch requests information with related product and customer details
$requestSql = "SELECT requests.request_id, 
                      products.name,
                      products.image,
                      CONCAT(customers.first_name, ' ', customers.last_name) AS customer_name,
                      requests.request_date
               FROM requests
               INNER JOIN products ON requests.product_id = products.product_id
               INNER JOIN customers ON requests.customer_id = customers.customer_id WHERE status = 'declined'";
$requestResult = mysqli_query($connection, $requestSql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Orders List</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="../../design.css">
</head>
<body>
    <!-- Navigation bar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
        <a class="navbar-brand" href="../employee-page.php">
        <img src="../../resources/logo1.webp" alt="Starbucks Logo" class="logo" style="width: 100px; height: 40px;">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="../employee-page.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../products/products-list.php">Products</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active bg-secondary rounded" href="orders-list.php">Orders</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../messages/customer-messages.php">Customer Messages</a>
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link dropdown-toggle" href="#" role="button" id="navbarDropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person"></i> Settings
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownMenuLink">
                            <li><a class="dropdown-item" href="../profile.php">Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="../../logout.php">Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Orders Table -->
    <div class="container mt-4 text-center">
        <h2>Orders List</h2>
        <table class="table">
            <thead>
                <tr>
                    <!-- <th>Order ID</th> -->
                    <th>Customer Name</th>
                    <th>Phone</th>
                    <th>Address</th>
                    <th>Order Date</th>
                    <th>Total Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Loop through each row in the result set
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    // echo "<td>".$row['order_id']."</td>";
                    echo "<td>".$row['fullname']."</td>";
                    echo "<td>".$row['phone']."</td>";
                    echo "<td>".$row['address']."</td>";
                    echo "<td>".$row['order_date']."</td>";
                    echo "<td>".$row['total_amount']."</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>


        <h2>Declined List</h2>
        <table class="table">
            <thead>
                <tr>
                    <!-- <th>Request ID</th> -->
                    <th>Product Name</th>
                    <th>Product Image</th>
                    <th>Customer Name</th>
                    <th>Request Date</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Loop through each row in the requests result set
                while ($row = mysqli_fetch_assoc($requestResult)) {
                    echo "<tr>";
                    // echo "<td>".$row['request_id']."</td>";
                    echo "<td>".$row['name']."</td>";
                    echo "<td><img src='../../uploads/".$row['image']."' alt='Product Image' style='width: 50px; height: auto;'></td>";
                    echo "<td>".$row['customer_name']."</td>";
                    echo "<td>".$row['request_date']."</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
</body>
</html>
