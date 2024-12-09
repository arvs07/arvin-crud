<?php
session_start();

include '../../dbcon.php';

// Check if the user is logged in and is an employee
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">

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
                        <a class="nav-link" href="../admin-page.php"><i class="bi bi-house"></i> Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../customers/customers-list.php"><i class="bi bi-people"></i> Customers</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active bg-secondary rounded" href="products-list.php"><i class="bi bi-box-seam"></i> Products</a>
                    </li>
                    <li class="nav-item dropdown-center">
                        <a class="nav-link dropdown-toggle" href="#" role="button" id="orderDropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-cart"></i> Orders
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="orderDropdownMenuLink">
                            <li><a class="dropdown-item" href="../orders/orders-list.php">Approved</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="../orders/declined-list.php">Declined</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../messages/customer-messages.php"><i class="bi bi-envelope"></i> Customer Messages</a>
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
        <div class="d-flex justify-content-center">
                <a href="add-product.php" class="btn btn-success">Add Product</a>
            </div><br>
    <table id="Table" class="table">
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

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>

    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
<style>
    .dt-buttons button {
        padding: 0.2rem 0.5rem;
        font-size: 0.7rem;
    }
    </style>

<script>
$(document).ready(function() {
            $('#Table').DataTable({
                dom: '<"d-flex justify-content-between align-items-center"Bfl>r<"table-responsive"t>p',
                buttons: [ 
                    {
                        extend: 'collection',
                        text: 'Download',
                        buttons: [
                            'copy', 'csv', 'excel', 'pdf', 'print'
                        ]
                    }
                ],
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search...",
                    lengthMenu: "_MENU_",
                    info: "Showing _START_ to _END_ of _TOTAL_ entries",
                    infoEmpty: "Showing 0 to 0 of 0 entries",
                    infoFiltered: "(filtered from _MAX_ total entries)",
                    paginate: {
                        first: '<i class="bi bi-chevron-double-left"></i>',
                        last: '<i class="bi bi-chevron-double-right"></i>',
                        next: '<i class="bi bi-chevron-right"></i>',
                        previous: '<i class="bi bi-chevron-left"></i>'
                    }
                }
            });
        });
</script>
</body>
</html>
