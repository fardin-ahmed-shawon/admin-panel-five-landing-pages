<?php
$current_page = basename($_SERVER['PHP_SELF']);
$page_title = 'Create Order';
require 'header.php';

// Generate a unique invoice number
function generateInvoiceNo() {
    $timestamp = microtime(true) * 10000;
    $uniqueString = 'INV-' . strtoupper(base_convert($timestamp, 10, 36));
    return $uniqueString;
}

// Fetch all products for dropdowns
$products = [];
$product_result = mysqli_query($conn, "SELECT product_id, product_title FROM product_info");
while ($row = mysqli_fetch_assoc($product_result)) {
    $products[] = $row;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_full_name'])) {
    $user_full_name = mysqli_real_escape_string($conn, $_POST['user_full_name']);
    $user_phone = mysqli_real_escape_string($conn, $_POST['user_phone']);
    $user_email = mysqli_real_escape_string($conn, $_POST['user_email']);
    $user_address = mysqli_real_escape_string($conn, $_POST['user_address']);
    $city_address = mysqli_real_escape_string($conn, $_POST['city_address']);
    $invoice_no = mysqli_real_escape_string($conn, $_POST['invoice_no']);
    $payment_method = mysqli_real_escape_string($conn, $_POST['payment_method']);
    $order_status = mysqli_real_escape_string($conn, $_POST['order_status']);
    $user_id = 0;
    $order_visibility = "Show"; // default

    // Discount amount
    $discount_amount = isset($_POST['discount_amount']) ? intval($_POST['discount_amount']) : 0;
    $total_order_amount = 0;

    // Insert each product as a separate row in order_info
    if (isset($_POST['products']) && is_array($_POST['products'])) {
        foreach ($_POST['products'] as $item) {
            $product_id = intval($item['product_id']);
            $product_size = mysqli_real_escape_string($conn, $item['product_size']);
            $product_quantity = intval($item['product_quantity']);
            $total_price = intval($item['total_price']);

            // Get product title
            $product_title = '';
            $product_query = mysqli_query($conn, "SELECT product_title FROM product_info WHERE product_id = $product_id");
            if ($product_row = mysqli_fetch_assoc($product_query)) {
                $product_title = mysqli_real_escape_string($conn, $product_row['product_title']);
            }

            $sql = "INSERT INTO order_info (
                user_id, user_full_name, user_phone, user_email, user_address, city_address, invoice_no,
                product_id, product_title, product_quantity, product_size, total_price,
                payment_method, order_status, order_visibility
            ) VALUES (
                $user_id, '$user_full_name', '$user_phone', '$user_email', '$user_address', '$city_address', '$invoice_no',
                $product_id, '$product_title', $product_quantity, '$product_size', $total_price,
                '$payment_method', '$order_status', '$order_visibility'
            )";
            mysqli_query($conn, $sql);

            $total_order_amount += $total_price;
        }

        // Insert discount info into order_discount_list
        $sql_discount = "INSERT INTO order_discount_list (invoice_no, total_order_amount, total_discount_amount)
            VALUES ('$invoice_no', '$total_order_amount', '$discount_amount')";
        mysqli_query($conn, $sql_discount);

        echo "<script>window.location.href='invoice.php?inv=" . $invoice_no . "';</script>";
        exit;
    } else {
        echo "<div class='alert alert-danger mt-3'>No products selected.</div>";
    }
}
?>

<div class="content-wrapper">
    <div class="container py-5">
    <div class="card shadow-lg border-0">
        <div class="card-header bg-dark text-white">
            <h1 class="mb-0 p-3">Create New Order</h1>
        </div>
        <div class="card-body p-4">
            <form method="POST" id="orderForm">
                <div class="row">
                    <!-- Left Side: Products -->
                    <div class="col-lg-7 mb-4">
                        <div class="border-0 h-100">
                            <div class=" bg-primary text-white">
                                <h5 class="p-3">Products</h5>
                            </div>
                            <div id="products-container">
                                <div class="product-row mb-3 p-2 border rounded">
                                    <div class="row g-3 align-items-end">
                                        <div class="col-md-6">
                                            <label class="form-label">Product</label>
                                            <select name="products[0][product_id]" class="form-select product-select" required>
                                                <option value="">Select Product</option>
                                                <?php foreach ($products as $row): ?>
                                                    <option value="<?php echo $row['product_id']; ?>"><?php echo htmlspecialchars($row['product_title']); ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Size</label>
                                            <select name="products[0][product_size]" class="form-select size-select">
                                                <option value="">Select Size</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Quantity</label>
                                            <input type="number" name="products[0][product_quantity]" class="form-control qty-input" min="1" required>
                                        </div>
                                        <div class="col-md-6 mt-2">
                                            <label class="form-label">Total Price</label>
                                            <input type="number" name="products[0][total_price]" class="form-control price-input" required>
                                        </div>
                                        <div class="col-md-6 mt-2 d-flex justify-content-end">
                                            <button type="button" class="btn btn-danger remove-product">Remove</button>
                                        </div>
                                    </div>
                                </div>
                                
                            </div>
                            <button type="button" class="btn btn-outline-primary w-100 mt-3" id="add-product">
                                <b>+ Add Another Product</b>
                            </button>
                        </div>
                    </div>

                    <!-- Right Side: Customer & Order Info -->
                    <div class="col-lg-5">
                        <div class="h-100">
                            <div class="bg-success text-white">
                                <h5 class="p-3">Customer & Order Info</h5>
                            </div>
                            <div>
                                <div class="mb-3">
                                    <label class="form-label">Discount Amount</label>
                                    <input type="number" name="discount_amount" class="form-control" min="0" value="<?php echo isset($_POST['discount_amount']) ? htmlspecialchars($_POST['discount_amount']) : '0'; ?>">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Payment Method</label>
                                    <select name="payment_method" class="form-select" required>
                                        <option value="Cash On Delivery" <?php echo (isset($_POST['payment_method']) && $_POST['payment_method'] == 'Cash On Delivery') ? 'selected' : ''; ?>>Cash On Delivery</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Invoice No</label>
                                    <input type="text" name="invoice_no" id="invoice_no" class="form-control" readonly value="<?php echo isset($_POST['invoice_no']) ? htmlspecialchars($_POST['invoice_no']) : generateInvoiceNo(); ?>">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Full Name</label>
                                    <input type="text" name="user_full_name" class="form-control" required value="<?php echo isset($_POST['user_full_name']) ? htmlspecialchars($_POST['user_full_name']) : ''; ?>">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Phone</label>
                                    <input type="text" name="user_phone" class="form-control" required value="<?php echo isset($_POST['user_phone']) ? htmlspecialchars($_POST['user_phone']) : ''; ?>">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="user_email" class="form-control" value="<?php echo isset($_POST['user_email']) ? htmlspecialchars($_POST['user_email']) : ''; ?>">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Address</label>
                                    <textarea name="user_address" class="form-control" required><?php echo isset($_POST['user_address']) ? htmlspecialchars($_POST['user_address']) : ''; ?></textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Delivery Location</label>
                                    <select name="city_address" class="form-select">
                                        <option value="">Select Delivery Location</option>
                                        <option value="Inside Dhaka" <?php echo (isset($_POST['city_address']) && $_POST['city_address']=='Inside Dhaka') ? 'selected' : ''; ?>>Inside Dhaka</option>
                                        <option value="Outside Dhaka" <?php echo (isset($_POST['city_address']) && $_POST['city_address']=='Outside Dhaka') ? 'selected' : ''; ?>>Outside Dhaka</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Status</label>
                                    <select name="order_status" class="form-select">
                                        <option value="Pending" <?php echo (isset($_POST['order_status']) && $_POST['order_status'] == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                                        <option value="Shipped" <?php echo (isset($_POST['order_status']) && $_POST['order_status'] == 'Shipped') ? 'selected' : ''; ?>>Shipped</option>
                                        <option value="Completed" <?php echo (isset($_POST['order_status']) && $_POST['order_status'] == 'Completed') ? 'selected' : ''; ?>>Completed</option>
                                        <option value="Canceled" <?php echo (isset($_POST['order_status']) && $_POST['order_status'] == 'Canceled') ? 'selected' : ''; ?>>Cancelled</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-dark w-100"><b>Create Order</b></button><br>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

</div>

<script>
// Add/Remove product rows
document.getElementById('add-product').addEventListener('click', function() {
    const container = document.getElementById('products-container');
    const index = container.querySelectorAll('.product-row').length;
    const newRow = container.firstElementChild.cloneNode(true);

    // Update name attributes for new index
    newRow.querySelectorAll('select, input').forEach(el => {
        if (el.name) el.name = el.name.replace(/\[\d+\]/, `[${index}]`);
        el.value = '';
    });

    // Reset size dropdown
    newRow.querySelector('.size-select').innerHTML = '<option value="">Select Size</option>';

    container.appendChild(newRow);
});

// Remove product row
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('remove-product')) {
        const rows = document.querySelectorAll('.product-row');
        if (rows.length > 1) {
            e.target.closest('.product-row').remove();
        }
    }
});

// Fetch sizes and price for selected product via AJAX
document.addEventListener('change', function(e) {
    if (e.target.classList.contains('product-select')) {
        const productId = e.target.value;
        const row = e.target.closest('.product-row');
        const sizeSelect = row.querySelector('.size-select');
        const qtyInput = row.querySelector('.qty-input');
        const priceInput = row.querySelector('.price-input');

        // Fetch sizes
        fetch('fetch-product-info.php?product_id=' + productId)
            .then(res => res.json())
            .then(data => {
                // Populate sizes
                sizeSelect.innerHTML = '<option value="">Select Size</option>';
                if (Array.isArray(data.sizes)) {
                    data.sizes.forEach(function(size) {
                        sizeSelect.innerHTML += `<option value="${size}">${size}</option>`;
                    });
                }
                // Set price
                priceInput.value = '';
                qtyInput.value = '';
                priceInput.setAttribute('data-base-price', data.price || 0);
            });
    }
});

// Auto-calc total price based on quantity × product_price
document.addEventListener('input', function(e) {
    if (e.target.classList.contains('qty-input')) {
        const row = e.target.closest('.product-row');
        const qty = parseInt(e.target.value) || 0;
        const priceInput = row.querySelector('.price-input');
        const basePrice = parseInt(priceInput.getAttribute('data-base-price')) || 0;
        priceInput.value = basePrice * qty;
    }
});
</script>

<?php require 'footer.php'; ?>