<?php
include '../../dbcon.php';

// Check if product ID is provided as a query parameter
if (!isset($_GET['id'])) {
    exit('Product ID not provided');
}

// Get the product ID from the query parameter
$product_id = $_GET['id'];

// Query to fetch product information by ID
$sql = "SELECT * FROM products WHERE product_id = ?";
$stmt = mysqli_prepare($connection, $sql);
mysqli_stmt_bind_param($stmt, "i", $product_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);

// Check if product exists
if (!$row) {
    exit('Product not found');
}

// Close the prepared statement
mysqli_stmt_close($stmt);

// Query to fetch sizes and quantities for the product
$sql_sizes = "SELECT size, quantity FROM product_sizes WHERE product_id = ?";
$stmt_sizes = mysqli_prepare($connection, $sql_sizes);
mysqli_stmt_bind_param($stmt_sizes, "i", $product_id);
mysqli_stmt_execute($stmt_sizes);
$result_sizes = mysqli_stmt_get_result($stmt_sizes);

// Store sizes and quantities in an array for easy access
$sizes_quantities = [];
while ($row_size = mysqli_fetch_assoc($result_sizes)) {
    $sizes_quantities[] = $row_size;
}

// Close the prepared statement
mysqli_stmt_close($stmt_sizes);

// Check if form is submitted for updating product
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $category = $_POST['category'];
    
    // Handle product image update
    if ($_FILES['image']['size'] > 0) {
        $image = $_FILES['image']['name'];
        $temp = $_FILES['image']['tmp_name'];
        move_uploaded_file($temp, "../../uploads/$image");
        $sql = "UPDATE products SET name=?, description=?, price=?, category=?, image=? WHERE product_id=?";
        $stmt = mysqli_prepare($connection, $sql);
        mysqli_stmt_bind_param($stmt, "sssisi", $name, $description, $price, $category, $image, $product_id);
    } else {
        // Update product information without updating the image
        $sql = "UPDATE products SET name=?, description=?, price=?, category=? WHERE product_id=?";
        $stmt = mysqli_prepare($connection, $sql);
        mysqli_stmt_bind_param($stmt, "ssdsi", $name, $description, $price, $category, $product_id);
    }

    if (mysqli_stmt_execute($stmt)) {
        // Update sizes and quantities
        // First, delete existing sizes and quantities for the product
        $sql_delete = "DELETE FROM product_sizes WHERE product_id = ?";
        $stmt_delete = mysqli_prepare($connection, $sql_delete);
        mysqli_stmt_bind_param($stmt_delete, "i", $product_id);
        mysqli_stmt_execute($stmt_delete);
        mysqli_stmt_close($stmt_delete);

        // Then, insert the new sizes and quantities from the form
        $sql_insert = "INSERT INTO product_sizes (product_id, size, quantity) VALUES (?, ?, ?)";
        $stmt_insert = mysqli_prepare($connection, $sql_insert);
        
        // Loop through the size and quantity inputs from the form
        foreach ($_POST['sizes'] as $index => $size) {
            $quantity = $_POST['quantities'][$index];
            mysqli_stmt_bind_param($stmt_insert, "isi", $product_id, $size, $quantity);
            mysqli_stmt_execute($stmt_insert);
        }

        mysqli_stmt_close($stmt_insert);

        // Redirect to products list after successful update
        header("Location: products-list.php");
        exit();
    } else {
        echo "Error updating product.";
    }

    // Close the statement
    mysqli_stmt_close($stmt);
}

mysqli_close($connection);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
    <div class="container mt-4">
        <h2>Edit Product</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?id=" . $product_id); ?>" method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label>Name</label>
                <input type="text" name="name" class="form-control" value="<?php echo $row['name']; ?>">
            </div>
            <div class="mb-3">
                <label>Description</label>
                <textarea name="description" class="form-control"><?php echo $row['description']; ?></textarea>
            </div>
            <div class="mb-3">
                <label>Price</label>
                <input type="text" name="price" class="form-control" value="<?php echo $row['price']; ?>">
            </div>
            <div class="mb-3">
                <label>Category</label>
                <select name="category" class="form-control">
                    <option value="T-Shirt" <?php if ($row['category'] === 'T-Shirt') echo 'selected'; ?>>T-Shirt</option>
                    <option value="Pants" <?php if ($row['category'] === 'Pants') echo 'selected'; ?>>Pants</option>
                    <option value="Short" <?php if ($row['category'] === 'Short') echo 'selected'; ?>>Short</option>
                </select>
            </div>
            <div class="mb-3">
                <label>Product Image</label>
                <input type="file" name="image" class="form-control">
            </div>
            
            <!-- Display size and quantity inputs -->
            <div class="mb-3">
                <label>Sizes and Quantities</label>
                <div id="sizes-quantities-container">
                    <?php
                    // Loop through each size and quantity to create input fields
                    foreach ($sizes_quantities as $index => $size_quantity) {
                        echo '<div class="mb-2">';
                        echo '<input type="text" name="sizes[]" class="form-control mb-2" value="' . htmlspecialchars($size_quantity['size']) . '" placeholder="Size">';
                        echo '<input type="number" name="quantities[]" class="form-control" value="' . htmlspecialchars($size_quantity['quantity']) . '" placeholder="Quantity">';
                        echo '</div>';
                    }
                    ?>
                </div>
                <!-- Button to add more sizes and quantities -->
                <button type="button" class="btn btn-success" id="add-size-quantity">Add Size and Quantity</button>
            </div>

            <div class="mb-3">
                <input type="submit" class="btn btn-primary" value="Update">
                <a href="products-list.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
    
    <!-- JavaScript to handle adding more size and quantity fields -->
    <script>
        document.getElementById('add-size-quantity').addEventListener('click', function() {
            // Create a new div for size and quantity inputs
            var newDiv = document.createElement('div');
            newDiv.className = 'mb-2';
            
            // Create input for size
            var sizeInput = document.createElement('input');
            sizeInput.type = 'text';
            sizeInput.name = 'sizes[]';
            sizeInput.className = 'form-control mb-2';
            sizeInput.placeholder = 'Size';
            newDiv.appendChild(sizeInput);
            
            // Create input for quantity
            var quantityInput = document.createElement('input');
            quantityInput.type = 'number';
            quantityInput.name = 'quantities[]';
            quantityInput.className = 'form-control';
            quantityInput.placeholder = 'Quantity';
            newDiv.appendChild(quantityInput);
            
            // Add the new div to the container
            document.getElementById('sizes-quantities-container').appendChild(newDiv);
        });
    </script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
</body>
</html>
