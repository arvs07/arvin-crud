<?php
session_start();
include '../dbcon.php';

// Fetch distinct categories
$sqlCategories = "SELECT DISTINCT category FROM products";
$resultCategories = mysqli_query($connection, $sqlCategories);
$categories = [];
while ($row = mysqli_fetch_assoc($resultCategories)) {
    $categories[] = $row['category'];
}

// Get the selected category from the request, if any
$selectedCategory = isset($_GET['category']) ? mysqli_real_escape_string($connection, $_GET['category']) : null;
function getProductSizesAndQuantities($productId, $connection) {
    // Prepare the SQL statement
    $sql = "SELECT size, quantity FROM product_sizes WHERE product_id = ?";
    $stmt = mysqli_prepare($connection, $sql);
    
    // Bind the product ID parameter to the SQL statement
    mysqli_stmt_bind_param($stmt, 'i', $productId);
    
    // Execute the SQL statement
    mysqli_stmt_execute($stmt);
    
    // Get the result of the statement
    $result = mysqli_stmt_get_result($stmt);
    
    // Initialize an associative array to store sizes and quantities
    $sizesAndQuantities = [];
    
    // Fetch the rows from the result
    while ($row = mysqli_fetch_assoc($result)) {
        // Store each size and quantity in the associative array
        $sizesAndQuantities[$row['size']] = $row['quantity'];
    }
    
    // Close the statement
    mysqli_stmt_close($stmt);
    
    // Return the associative array with sizes and quantities
    return $sizesAndQuantities;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../design.css">
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar sticky-top navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="customers-page.php">
                <img src="../resources/logo1.webp" alt="Starbucks Logo" class="logo" style="width: 100px; height: 40px;">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link bg-secondary rounded" href="customers-page.php">
                            <i class="bi bi-house-door"></i> Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="about-us.php">
                            <i class="bi bi-info-circle"></i> About Us
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contact-us.php">
                            <i class="bi bi-envelope"></i> Contact Us
                        </a>
                    </li>
                    
                    </ul>
                    <ul class="navbar-nav me-auto">
                        <!-- Category Dropdown -->
                    <li class="nav-item dropdown bg-warning rounded">
                            <a class="nav-link dropdown-toggle" href="#" id="categoryDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-funnel"></i>
                                <?php
                                // Display the currently selected category name, or default to 'All Categories'
                                echo $selectedCategory ? htmlspecialchars($selectedCategory) : 'Categories';
                                ?>
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="categoryDropdown">
                                <!-- All Categories -->
                                <li><a class="dropdown-item <?php echo !$selectedCategory?>" href="#allProduct">All Products</a></li>
                                <hr>
                                <!-- Categories from the database -->
                                <?php foreach ($categories as $category) {
                                    // Check if the category is currently selected and add the 'active' class if so
                                    $isActive = ($category === $selectedCategory) ? 'active' : '';
                                    echo "<li><a class='dropdown-item $isActive' href='#" . urlencode($category) . "'>" . htmlspecialchars($category) . "</a></li>";
                                } ?>
                            </ul>
                        </li>
                    </ul>
                <!-- Right-aligned links -->
                <ul class="navbar-nav ms-auto">
                    
                    <?php if (isset($_SESSION['customer_id']) && !empty($_SESSION['customer_id'])) : ?>
                        <!-- If customer is logged in -->
                        <li class="nav-item">
                            <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#cartModal"><i class="bi bi-cart4"></i></a>
                        </li>
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
                    <?php else : ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownAuthLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Authenticate
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownAuthLink">
                                <li><a class="dropdown-item" href="../customers-login.php">Login</a></li>
                                <li><a class="dropdown-item" href="../customers-signup.php">Sign Up</a></li>
                            </ul>
                        </li>
                    <?php endif; ?>
                    
                </ul>
            </div>
        </div>
    </nav>

    <!-- Dashboard Content -->
    <div class="container mt-4">
        <!-- Carousel -->
        <div id="carouselExampleAutoplaying" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active">
            <img src="../resources/crs-1.webp" class="d-block w-100" alt="...">
            </div>
            <div class="carousel-item">
            <img src="../resources/crs-2.webp" class="d-block w-100" alt="...">
            </div>
            <div class="../resources/crs-3.webp">
            <img src="..." class="d-block w-100" alt="...">
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleAutoplaying" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleAutoplaying" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button><br>
        </div>
                        
        <h2>Products</h2>

        <?php
        

        // Determine the SQL query based on the selected category
        if ($selectedCategory) {
            // If a category is selected, fetch products from that category
            $sqlProducts = "SELECT * FROM products WHERE category = '" . mysqli_real_escape_string($connection, $selectedCategory) . "'";
        } else {
            // Otherwise, fetch all products
            $sqlProducts = "SELECT * FROM products";
        }

        $resultProducts = mysqli_query($connection, $sqlProducts);

// Display products within the selected category or all products
echo "<div class='row'>";
while ($productRow = mysqli_fetch_assoc($resultProducts)) { 
    echo "<div class='col-md-3 mb-4' id='allProduct'>
        <div class='card'>
            <img src='../uploads/" . htmlspecialchars($productRow['image']) . "' class='card-img-top' alt='Product Image' style='height: 200px; object-fit: cover;'>
            <div class='card-body'>
                <h5 class='card-title'>" . htmlspecialchars($productRow['name']) . "</h5>
                <p class='card-text'>" . htmlspecialchars($productRow['description']) . "</p>
                <p class='card-text'><strong>Price:</strong> $" . htmlspecialchars($productRow['price']) . "</p>";
                if (isset($_SESSION['customer_id']) && !empty($_SESSION['customer_id'])) {
                    // Get available sizes and quantities for the current product
                    $sizesAndQuantities = getProductSizesAndQuantities($productRow['product_id'], $connection);

                    // Calculate total quantity for the product
                    $totalQuantity = array_sum($sizesAndQuantities);

                    echo "<form action='request.php' method='POST' class='mb-3'>
                            <input type='hidden' name='product_id' value='" . htmlspecialchars($productRow['product_id']) . "'>";

                    // Check if total quantity is greater than zero
                    if ($totalQuantity > 0) {
                        echo "<div class='input-group mb-2'>
                                <!-- Dropdown for selecting size -->
                                <select name='size' class='form-control' required>
                                    <option value=''>Select Size</option>";
                                    
                        // Loop through sizes and quantities and create option elements
                        foreach ($sizesAndQuantities as $size => $quantity) {
                            echo "<option value='" . htmlspecialchars($size) . "'>" . htmlspecialchars($size) . " (Available: " . htmlspecialchars($quantity) . ")</option>";
                        }

                        // Add the max attribute to the quantity input field
                        echo "</select>
                                <!-- Quantity input -->
                                <input type='number' class='form-control' name='quantity' min='1' max='" . htmlspecialchars($totalQuantity) . "' value='1' required>
                                <button type='submit' class='btn btn-primary'><i class='bi bi-send-check-fill'></i></button>
                            </div>";
                    }

                    echo "</form>

                        <form action='add-to-cart.php' method='POST'>";

                    // Check if total quantity is greater than zero
                    if ($totalQuantity > 0) {
                        echo "<div class='input-group'>
                                <!-- Dropdown for selecting size -->
                                <select name='size' class='form-control' required>
                                    <option value=''>Select Size</option>";

                        // Loop through sizes and quantities and create option elements
                        foreach ($sizesAndQuantities as $size => $quantity) {
                            echo "<option value='" . htmlspecialchars($size) . "'>" . htmlspecialchars($size) . " (Available: " . htmlspecialchars($quantity) . ")</option>";
                        }

                        // Add the max attribute to the quantity input field
                        echo "</select>
                                <!-- Quantity input -->
                                <input type='number' class='form-control' name='quantity' min='1' max='" . htmlspecialchars($totalQuantity) . "' value='1' required>
                                <button type='submit' class='btn btn-success'><i class='bi bi-cart-check-fill'></i></button>
                            </div>";
                    }else{
                        echo '<p class="text-danger" > Out of stock</p>';
                    }

                    echo "</form>";
                }

                echo "</div></div></div>";
            ?>
         <?php                   
        }
        echo "</div>";
        
        ?>



        
    <?php
    foreach ($categories as $category) {
        // Fetch products for each category
        $sqlProducts = "SELECT * FROM products WHERE category = '" . mysqli_real_escape_string($connection, $category) . "'";
        $resultProducts = mysqli_query($connection, $sqlProducts);

        echo "<hr>";
        echo "<h3 id='" . urlencode($category) . "'>" . htmlspecialchars($category) . "</h3>";

        // Display products in the category
        echo "<div class='row'>";
        while ($productRow = mysqli_fetch_assoc($resultProducts)) {
            echo "<div class='col-md-3 mb-4'>
                    <div class='card'>
                        <img src='../uploads/" . htmlspecialchars($productRow['image']) . "' class='card-img-top' alt='Product Image' style='height: 200px; object-fit: cover;'>
                        <div class='card-body'>
                            <h5 class='card-title'>" . htmlspecialchars($productRow['name']) . "</h5>
                            <p class='card-text'>" . htmlspecialchars($productRow['description']) . "</p>
                            <p class='card-text'><strong>Price:</strong> $" . htmlspecialchars($productRow['price']) . "</p>";

            if (isset($_SESSION['customer_id']) && !empty($_SESSION['customer_id'])) {
                // Get available sizes and quantities for the current product
                $sizesAndQuantities = getProductSizesAndQuantities($productRow['product_id'], $connection);

                // Calculate total quantity for the product
                $totalQuantity = array_sum($sizesAndQuantities);

                // Check if total quantity is greater than zero
                if ($totalQuantity > 0) {
                    echo "<form action='request.php' method='POST' class='mb-3'>
                            <input type='hidden' name='product_id' value='" . htmlspecialchars($productRow['product_id']) . "'>
                            <div class='input-group mb-2'>
                                <!-- Dropdown for selecting size -->
                                <select name='size' class='form-control' required>
                                    <option value=''>Select Size</option>";

                    // Loop through sizes and quantities and create option elements
                    foreach ($sizesAndQuantities as $size => $quantity) {
                        echo "<option value='" . htmlspecialchars($size) . "'>" . htmlspecialchars($size) . " (Available: " . htmlspecialchars($quantity) . ")</option>";
                    }

                    // Add the max attribute to the quantity input field
                    echo "</select>
                        <!-- Quantity input -->
                        <input type='number' class='form-control' name='quantity' min='1' max='" . htmlspecialchars($totalQuantity) . "' value='1' required>
                        <button type='submit' class='btn btn-primary'><i class='bi bi-send-check-fill'></i></button>
                    </div>
                    </form>

                        <form action='add-to-cart.php' method='POST'>
                            <input type='hidden' name='product_id' value='" . htmlspecialchars($productRow['product_id']) . "'>
                            <div class='input-group'>
                                <!-- Dropdown for selecting size -->
                                <select name='size' class='form-control' required>
                                    <option value=''>Select Size</option>";

                    // Loop through sizes and quantities and create option elements
                    foreach ($sizesAndQuantities as $size => $quantity) {
                        echo "<option value='" . htmlspecialchars($size) . "'>" . htmlspecialchars($size) . " (Available: " . htmlspecialchars($quantity) . ")</option>";
                    }

                    // Add the max attribute to the quantity input field
                    echo "</select>
                        <!-- Quantity input -->
                        <input type='number' class='form-control' name='quantity' min='1' max='" . htmlspecialchars($totalQuantity) . "' value='1' required>
                        <button type='submit' class='btn btn-success'><i class='bi bi-cart-check-fill'></i></button>
                    </div>
                    </form>";
                }else{
                    echo '<p class="text-danger" > Out of stock</p>';
                }
            }

            echo "</div>
                </div>
            </div>";
        }
        echo "</div>";
    }
    ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
    
    <?php include 'cart.php'; ?>
</body>
</html>
