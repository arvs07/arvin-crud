<?php
include '../../dbcon.php';

// Define variables and initialize with empty values
$name = $description = $price = $stock_quantity = $image = $category = '';
$name_err = $description_err = $price_err = $stock_quantity_err = $image_err = $category_err = '';

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validate name
    if (empty(trim($_POST["name"]))) {
        $name_err = "Please enter the product name.";
    } else {
        $name = trim($_POST["name"]);
    }
    
    // Validate description
    if (empty(trim($_POST["description"]))) {
        $description_err = "Please enter a description.";
    } else {
        $description = trim($_POST["description"]);
    }
    
    // Validate price
    if (empty(trim($_POST["price"])) || !is_numeric($_POST["price"])) {
        $price_err = "Please enter a valid price.";
    } else {
        $price = trim($_POST["price"]);
    }
    
    // Validate category
    if (empty(trim($_POST["category"]))) {
        $category_err = "Please select a category.";
    } else {
        $category = trim($_POST["category"]);
    }
    
    // Check if a file was uploaded
    if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {
        // Get file name
        $image = basename($_FILES["image"]["name"]);
        // Validate file size and type
        $allowed_types = array("image/jpeg", "image/png", "image/gif");
        if (!in_array($_FILES["image"]["type"], $allowed_types)) {
            $image_err = "Only JPG, PNG, and GIF files are allowed.";
        }
    } else {
        $image_err = "Please select an image file.";
    }

    // Check input errors before inserting into the database
    if (empty($name_err) && empty($description_err) && empty($price_err) && empty($image_err) && empty($category_err)) {
        // Upload image to server
        $target_dir = "../../uploads/";
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            // Image uploaded successfully, proceed with database insertion
            // Prepare an insert statement
            $sql = "INSERT INTO products (name, description, price, image, category) VALUES (?, ?, ?, ?, ?)";
             
            if ($stmt = mysqli_prepare($connection, $sql)) {
                // Bind variables to the prepared statement as parameters
                mysqli_stmt_bind_param($stmt, "ssdss", $param_name, $param_description, $param_price, $param_image, $param_category);
                
                // Set parameters
                $param_name = $name;
                $param_description = $description;
                $param_price = $price;
                $param_image = $image;
                $param_category = $category;

                
                // Attempt to execute the prepared statement
                if (mysqli_stmt_execute($stmt)) {
                    $last_product_id = mysqli_insert_id($connection);
                    
                    // Insert size and quantity information into product_sizes table
                    $sizes = $_POST['sizes'];
                    $quantities = $_POST['quantities'];
                    
                    $size_quantity_stmt = mysqli_prepare($connection, "INSERT INTO product_sizes (product_id, size, quantity) VALUES (?, ?, ?)");
                    mysqli_stmt_bind_param($size_quantity_stmt, "isi", $last_product_id, $size, $quantity);

                    // Loop through sizes and quantities and insert each into the database
                    for ($i = 0; $i < count($sizes); $i++) {
                        $size = $sizes[$i];
                        $quantity = $quantities[$i];

                        // Check if the combination of product_id and size already exists
                        $check_stmt = mysqli_prepare($connection, "SELECT * FROM product_sizes WHERE product_id = ? AND size = ?");
                        mysqli_stmt_bind_param($check_stmt, "is", $last_product_id, $size);
                        mysqli_stmt_execute($check_stmt);
                        mysqli_stmt_store_result($check_stmt);

                        if (mysqli_stmt_num_rows($check_stmt) == 0) {
                            // If the combination does not exist, insert the record
                            mysqli_stmt_execute($size_quantity_stmt);
                        } else {
                            // If the combination already exists, you can choose to handle it in some way, such as updating the existing record or skipping it
                            echo "Combination of product_id $last_product_id and size $size already exists. Skipping...";
                        }

                        mysqli_stmt_close($check_stmt);
                    }


                    // Close the size_quantity_stmt
                    mysqli_stmt_close($size_quantity_stmt);

                    // Redirect to products-list.php after successful insertion
                    header("location: products-list.php");
                    exit();
                } else {
                    echo "Something went wrong. Please try again later.";
                }
    
                // Close statement
                mysqli_stmt_close($stmt);
            }
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
    
    // Close connection
    mysqli_close($connection);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h2>Add Product</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label>Name</label>
                <input type="text" name="name" class="form-control <?php echo (!empty($name_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $name; ?>">
                <span class="invalid-feedback"><?php echo $name_err; ?></span>
            </div>
            <div class="mb-3">
                <label>Category</label>
                <select name="category" class="form-select <?php echo (!empty($category_err)) ? 'is-invalid' : ''; ?>" id="category-select">
                    <option value="">Select a category</option>
                    <option value="T-Shirt">T-Shirt</option>
                    <option value="Pants">Pants</option>
                    <option value="Short">Short</option>
                    <!-- Add more categories as needed -->
                </select>
                <span class="invalid-feedback"><?php echo $category_err; ?></span>
            </div>
            <div id="size-quantity-section" style="display: none;">
                <h4>Sizes and Quantities</h4>
                <!-- This container will hold the size and quantity inputs -->
                <div id="size-quantity-inputs"></div>
                <button type="button" id="add-size-quantity" class="btn btn-primary mb-3">Add Size and Quantity</button>
            </div>
            <div class="mb-3">
                <label>Description</label>
                <textarea name="description" class="form-control <?php echo (!empty($description_err)) ? 'is-invalid' : ''; ?>"><?php echo $description; ?></textarea>
                <span class="invalid-feedback"><?php echo $description_err; ?></span>
            </div>
            <div class="mb-3">
                <label>Price</label>
                <input type="number" name="price" class="form-control <?php echo (!empty($price_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $price; ?>" step="0.01">
                <span class="invalid-feedback"><?php echo $price_err; ?></span>
            </div>
            <div class="mb-3">
                <label>Image</label>
                <input type="file" name="image" class="form-control <?php echo (!empty($image_err)) ? 'is-invalid' : ''; ?>">
                <span class="invalid-feedback"><?php echo $image_err; ?></span>
            </div>
            <div class="mb-3">
                <input type="submit" class="btn btn-primary" value="Submit">
                <a href="products-list.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>

    <!-- JavaScript to handle adding size and quantity inputs dynamically -->
    <script>
        // Get references to DOM elements
        const categorySelect = document.getElementById("category-select");
        const sizeQuantitySection = document.getElementById("size-quantity-section");
        const sizeQuantityInputs = document.getElementById("size-quantity-inputs");
        const addSizeQuantityButton = document.getElementById("add-size-quantity");

        // Function to add a new size and quantity input set
        function addSizeQuantityInputs() {
            const newDiv = document.createElement("div");
            newDiv.innerHTML = `
                <div class="mb-2">
                    <label>Size</label>
                    <select name="sizes[]" class="form-select">
                        <option value="S">S</option>
                        <option value="M">M</option>
                        <option value="L">L</option>
                        <option value="XL">XL</option>
                        <option value="XXL">XXL</option>
                        <!-- Add more sizes as needed -->
                    </select>
                </div>
                <div class="mb-2">
                    <label>Quantity</label>
                    <input type="number" name="quantities[]" class="form-control" value="0">
                </div>
            `;
            sizeQuantityInputs.appendChild(newDiv);
        }

        // Add an event listener to the category select to show/hide size and quantity inputs
        categorySelect.addEventListener("change", function() {
            if (categorySelect.value === "T-Shirt" || categorySelect.value === "Pants" || categorySelect.value === "Short") {
                // Show size and quantity inputs for relevant categories
                sizeQuantitySection.style.display = "block";
                // Add initial size and quantity input set
                addSizeQuantityInputs();
            } else {
                // Hide size and quantity inputs for other categories
                sizeQuantitySection.style.display = "none";
                sizeQuantityInputs.innerHTML = "";
            }
        });

        // Add event listener to the button for adding more size and quantity inputs
        addSizeQuantityButton.addEventListener("click", addSizeQuantityInputs);
    </script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
</body>
</html>
