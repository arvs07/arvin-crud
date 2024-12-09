<!-- Modal -->
<div class="modal fade" id="cartModal" tabindex="-1" aria-labelledby="cartModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cartModalLabel">Shopping Cart</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Cart content will be displayed here -->
                <?php
                // Query to fetch cart information
                $customerId = $_SESSION['customer_id'];
                $cartSql = "SELECT products.*, cart.quantity, cart.size FROM cart 
                INNER JOIN products ON cart.product_id = products.product_id 
                WHERE cart.customer_id = $customerId";
                $cartResult = mysqli_query($connection, $cartSql);

                if (mysqli_num_rows($cartResult) > 0) {
                    // Display cart items if there are items in the cart
                ?>
                    <div class="mb-3">
                        <input type="checkbox" id="selectAll" class="form-check-input">
                        <label for="selectAll" class="form-check-label">Select All</label>
                    </div>
                    <?php
                    while ($cartRow = mysqli_fetch_assoc($cartResult)) {
                    ?>
                        <div class="card mb-3">
                            <div class="row g-0">
                                <div class="col-md-3">
                                    <img src="../uploads/<?php echo htmlspecialchars($cartRow['image']); ?>" class="img-fluid rounded-start" alt="Product Image">
                                </div>
                                <div class="col-md-8">
                                    <div class="card-body">
                                        <h5 class="card-title"><?php echo htmlspecialchars($cartRow['name']); ?></h5>
                                        <p class="card-text"><?php echo htmlspecialchars($cartRow['description']); ?></p>
                                        <p class="card-text">Price: $<?php echo htmlspecialchars($cartRow['price']); ?></p>
                                        <p class="card-text">Size: <?php echo htmlspecialchars($cartRow['size']); ?></p> <!-- Add this line -->
                                        <p class="card-text">Quantity: <?php echo htmlspecialchars($cartRow['quantity']); ?></p>
                                        <p class="card-text">Total: $<?php echo htmlspecialchars($cartRow['price'] * $cartRow['quantity']); ?></p>
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <input type="checkbox" name="selected_product[]" value="<?php echo htmlspecialchars($cartRow['product_id']); ?>" class="form-check-input select-product">Select
                                </div>
                            </div>
                        </div>
                <?php
                    }
                    // Display buttons to process selected products
                ?>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <form action="cart-request.php" method="POST" id="cartRequestForm">
                                    <input type="hidden" name="selected_products" id="selectedProducts" value="">
                                    <button type="submit" class="btn btn-primary me-md-2 mb-2 mb-md-0">Request Selected</button>
                                </form>
                                <form action="remove-from-cart.php" method="POST" id="removeFromCartForm">
                                    <input type="hidden" name="selected_products" id="selectedProductsToRemove" value="">
                                    <button type="submit" class="btn btn-danger">Remove Selected</button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php
                } else {
                    // Display a message if the cart is empty
                    echo "<p>Your cart is empty.</p>";
                }
                ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    // JavaScript to handle select all checkbox functionality
    document.getElementById('selectAll').addEventListener('change', function() {
        let checkboxes = document.querySelectorAll('.select-product');
        checkboxes.forEach((checkbox) => {
            checkbox.checked = this.checked;
        });
    });

    // JavaScript to update hidden input field with selected product IDs
    document.getElementById('cartRequestForm').addEventListener('submit', function() {
        let selectedProducts = [];
        let checkboxes = document.querySelectorAll('.select-product:checked');
        checkboxes.forEach((checkbox) => {
            selectedProducts.push(checkbox.value);
        });
        document.getElementById('selectedProducts').value = JSON.stringify(selectedProducts);
    });
    // JavaScript to update hidden input field with selected product IDs for removing from cart
    document.getElementById('removeFromCartForm').addEventListener('submit', function() {
        let selectedProductsToRemove = [];
        let checkboxes = document.querySelectorAll('.select-product:checked');
        checkboxes.forEach((checkbox) => {
            selectedProductsToRemove.push(checkbox.value);
        });
        document.getElementById('selectedProductsToRemove').value = JSON.stringify(selectedProductsToRemove);
    });
    
</script>
