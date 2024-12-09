<?php
include '../../dbcon.php';

// Check if product ID is provided as a query parameter
if (!isset($_GET['id'])) {
    exit('Product ID not provided');
}

// Get the product ID from the query parameter
$product_id = $_GET['id'];

// Query to fetch product information and available sizes from product_sizes table by ID
$sql = "SELECT p.*, ps.size, ps.quantity as size_quantity
        FROM products p
        INNER JOIN product_sizes ps ON p.product_id = ps.product_id
        WHERE p.product_id = ?";

$stmt = mysqli_prepare($connection, $sql);
mysqli_stmt_bind_param($stmt, "i", $product_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Store product and sizes information
$productInfo = [];
$sizesInfo = [];

while ($row = mysqli_fetch_assoc($result)) {
    $productInfo = [
        'name' => $row['name'],
        'description' => $row['description'],
        'price' => $row['price'],
        'image' => $row['image']
    ];
    $sizesInfo[] = [
        'size' => $row['size'],
        'quantity' => $row['size_quantity']
    ];
}

// Check if product exists
if (empty($productInfo)) {
    exit('Product not found');
}
mysqli_stmt_close($stmt);

// Check if form is submitted for deleting product or size
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['delete_size'])) {
        $selected_size = $_POST['delete_size'];
        $sql = "DELETE FROM product_sizes WHERE product_id = ? AND size = ?";
        $stmt = mysqli_prepare($connection, $sql);
        mysqli_stmt_bind_param($stmt, "is", $product_id, $selected_size);
    } else {
        $sql = "DELETE FROM product_sizes WHERE product_id = ?";
        $stmt = mysqli_prepare($connection, $sql);
        mysqli_stmt_bind_param($stmt, "i", $product_id);
        mysqli_stmt_execute($stmt);
        
        $sql = "DELETE FROM products WHERE product_id = ?";
        $stmt = mysqli_prepare($connection, $sql);
        mysqli_stmt_bind_param($stmt, "i", $product_id);
    }

    if (mysqli_stmt_execute($stmt)) {
        header("Location: products-list.php");
        exit();
    } else {
        echo "Error deleting product or size.";
    }
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
    <title>Delete Product</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>
    <div class="container mt-4">
        <h2>Delete Product</h2>
        <div class="alert alert-danger">Are you sure you want to delete this product or specific sizes?</div>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?id=" . $product_id); ?>" method="post">
            <div class="mb-3">
                <label>Name</label>
                <input type="text" class="form-control" value="<?php echo htmlspecialchars($productInfo['name']); ?>" readonly>
            </div>
            <div class="mb-3">
                <label>Description</label>
                <textarea class="form-control" readonly><?php echo htmlspecialchars($productInfo['description']); ?></textarea>
            </div>
            <div class="mb-3">
                <label>Price</label>
                <input type="text" class="form-control" value="<?php echo htmlspecialchars($productInfo['price']); ?>" readonly>
            </div>

            <!-- Display available sizes -->
            <div class="mb-3">
                <label>Available Sizes</label>
                <ul>
                    <?php foreach ($sizesInfo as $sizeInfo): ?>
                        <li class="d-flex justify-content-between align-items-center my-4">
                            <span>
                                Size: <?php echo htmlspecialchars($sizeInfo['size']); ?> (Quantity: <?php echo htmlspecialchars($sizeInfo['quantity']); ?>)
                            </span>
                            <button type="submit" name="delete_size" value="<?php echo htmlspecialchars($sizeInfo['size']); ?>" class="btn btn-danger btn-sm ms-2">Delete Size</button>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="mb-3">
                <div class="d-flex justify-content-end">
                    <input type="submit" class="btn btn-danger me-2" value="Delete Entire Product">
                    <a href="products-list.php" class="btn btn-secondary">Cancel</a>
                </div>
            </div>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
</body>

</html>
