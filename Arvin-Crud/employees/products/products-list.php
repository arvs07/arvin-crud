<?php
session_start();

include '../../dbcon.php';

// Check if the user is logged in and is an employee
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'employee') {
    header("Location: ../../admin-login.php");
    exit();
}

// Query to fetch product information
$sql = "SELECT p.product_id, p.name, p.category, p.description, p.price, ps.size, ps.quantity, p.image
FROM products p
LEFT JOIN product_sizes ps ON p.product_id = ps.product_id
ORDER BY p.product_id, ps.size;
";
$result = mysqli_query($connection, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Products List</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="../../design.css">
</head>
<body>
    <!-- Navigation bar -->
    <nav class="navbar navbar-expand-lg navbar-dark ">
        <div class="container-fluid">
        <a class="navbar-brand" href="../admin-page.php">
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
                        <a class="nav-link active bg-secondary rounded" href="products-list.php">Products</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../orders/orders-list.php">Orders</a>
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

    <!-- Products Table -->
    <div class="container mt-4 table-responsive text-center">
        <h2>Products List</h2>
        <div class="d-flex">
                <a href="add-product.php" class="btn btn-success">Add Product</a>
            </div>
        <table class="table ">
            <thead>
                <tr>
                    <th>Product ID</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th>Clothing Size</th>
                    <th>Stock Quantity</th>
                    <th>Image</th>
                    <th>Controls</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Initialize variables to keep track of the current product
$current_product_id = null;
$size_quantity_list = [];

// Loop through each row in the result set
while ($row = mysqli_fetch_assoc($result)) {
    // Check if we have moved to a new product
    if ($current_product_id !== $row['product_id']) {
        // If we are at a new product and it's not the first iteration, display the previous product's sizes and quantities
        if ($current_product_id !== null) {
            // Display the current product information
            echo "<tr>";
            echo "<td>{$current_product_id}</td>";
            echo "<td>{$current_product['name']}</td>";
            echo "<td>{$current_product['category']}</td>";
            echo "<td>{$current_product['description']}</td>";
            echo "<td>{$current_product['price']}</td>";
            echo "<td>" . implode("<br>", array_column($size_quantity_list, 'size')) . "</td>";
            echo "<td>" . implode("<br>", array_column($size_quantity_list, 'quantity')) . "</td>";
            echo "<td><img src='../../uploads/{$current_product['image']}' style='height: 100px; width: 100px;'></td>";
            echo "<td>
                    <a href='edit-product.php?id={$current_product_id}' class='btn btn-primary btn-md'>Edit</a>
                    <a href='delete-product.php?id={$current_product_id}' class='btn btn-danger btn-md'>Delete</a>
                  </td>";
            echo "</tr>";

            // Reset the size and quantity list for the new product
            $size_quantity_list = [];
        }

        // Update the current product information
        $current_product_id = $row['product_id'];
        $current_product = $row;
    }

    // Add the current size and quantity to the list for the current product
    $size_quantity_list[] = [
        'size' => $row['size'],
        'quantity' => $row['quantity']
    ];
}

// Display the last product's sizes and quantities after the loop ends
if ($current_product_id !== null) {
    echo "<tr>";
    echo "<td>{$current_product_id}</td>";
    echo "<td>{$current_product['name']}</td>";
    echo "<td>{$current_product['category']}</td>";
    echo "<td>{$current_product['description']}</td>";
    echo "<td>{$current_product['price']}</td>";
    echo "<td>" . implode("<br>", array_column($size_quantity_list, 'size')) . "</td>";
    echo "<td>" . implode("<br>", array_column($size_quantity_list, 'quantity')) . "</td>";
    echo "<td><img src='../../uploads/{$current_product['image']}' style='height: 100px; width: 100px;'></td>";
    echo "<td>
            <a href='edit-product.php?id={$current_product_id}' class='btn btn-primary btn-md'>Edit</a>
            <a href='delete-product.php?id={$current_product_id}' class='btn btn-danger btn-md'>Delete</a>
          </td>";
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
