<?php
session_start();

// Check if the user is logged in and is an employee
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
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
$sqlRequestedProducts = "SELECT r.*, p.name AS product_name, c.email AS customer_email, p.image AS product_image, c.image AS customer_image, r.size
                        FROM requests r
                        INNER JOIN products p ON r.product_id = p.product_id
                        INNER JOIN customers c ON r.customer_id = c.customer_id
                        WHERE r.status = 'pending'";
$resultRequestedProducts = mysqli_query($connection, $sqlRequestedProducts);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../design.css">
</head>
<body>
    
    <!-- Navigation bar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="admin-page.php">
            <img src="../resources/logo1.webp" alt="Starbucks Logo" class="logo" style="width: 100px; height: 40px;">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link active bg-secondary rounded" aria-current="page" href="admin-page.php">
                        <i class="bi bi-house"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="./customers/customers-list.php">
                            <i class="bi bi-people"></i> Customers
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="./products/products-list.php">
                            <i class="bi bi-box-seam"></i> Products
                        </a>
                    </li>
                    <li class="nav-item dropdown-center">
                        <a class="nav-link dropdown-toggle" href="#" role="button" id="orderDropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-cart"></i> Orders
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="orderDropdownMenuLink">
                            <li><a class="dropdown-item" href="./orders/orders-list.php">Approved</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="./orders/declined-list.php">Declined</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="./messages/customer-messages.php">
                            <i class="bi bi-envelope"></i> Customer Messages
                        </a>
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
        <div class="container mt-4">
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">
                                <i class="bi bi-person-fill"></i> Total Customers
                            </h5>
                            <p class="card-text"><?php echo $totalCustomers; ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">
                                <i class="bi bi-box"></i> Total Products
                            </h5>
                            <p class="card-text"><?php echo $totalProducts; ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Requested Products</h5><br>
                    <div class="row">
                        <?php
                        // Loop through requested products
                        while ($row = mysqli_fetch_assoc($resultRequestedProducts)) {
                        ?>
                            <div class="col-md-3 mb-4">
                                <div class="card">
                                    <img src="../uploads/<?php echo $row['product_image']; ?>" class="card-img-top" alt="Product Image" style="height: 250px;" >
                                    <div class="card-body">
                                        <h5 class="card-title"><?php echo $row['product_name']; ?></h5>
                                        <p class="card-text">Customer Email: <?php echo $row['customer_email']; ?></p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <p class="mb-0">Quantity: <?php echo $row['request_quantity']; ?></p>
                                                <p class="mb-0">Size: <?php echo $row['size']; ?></p>
                                            </div>
                                        </div>
                                        <div class="mt-3">
                                            <!-- Approve form -->
                                            <form action="./process-request/approve-request.php" method="post" class="d-inline">
                                                <input type="hidden" name="request_id" value="<?php echo $row['request_id']; ?>">
                                                <button type="submit" class="btn btn-success btn-sm">
                                                    <i class="bi bi-check-circle"></i> Approve
                                                </button>
                                            </form>
                                            <!-- Decline form -->
                                            <form action="./process-request/decline-request.php" method="post" class="d-inline">
                                                <input type="hidden" name="request_id" value="<?php echo $row['request_id']; ?>">
                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    <i class="bi bi-x-circle"></i> Decline
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php
                        }
                        ?>
                    </div>
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
