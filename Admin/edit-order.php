<?php
$current_page = basename($_SERVER['PHP_SELF']);
$page_title = 'Edit Order';
require 'header.php';

$invoice_no = $_GET['invoice_no'] ?? '';
if (!$invoice_no) {
    echo "<h4>Invalid invoice number.</h4>";
    exit;
}

// Fetch order + products
$order_sql = "SELECT * FROM order_info WHERE invoice_no = ?";
$stmt = $conn->prepare($order_sql);
$stmt->bind_param("s", $invoice_no);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    echo "<h4>No order found.</h4>";
    exit;
}
$orders = $result->fetch_all(MYSQLI_ASSOC);
$order = $orders[0]; // First row for customer/order info

// Fetch discount info
$discount_sql = "SELECT * FROM order_discount_list WHERE invoice_no = ?";
$stmt2 = $conn->prepare($discount_sql);
$stmt2->bind_param("s", $invoice_no);
$stmt2->execute();
$discount_row = $stmt2->get_result()->fetch_assoc();
$discount_amount = $discount_row['total_discount_amount'] ?? 0;

// Fetch all products
$products = [];
$product_result = mysqli_query($conn, "SELECT product_id, product_title FROM product_info");
while ($row = mysqli_fetch_assoc($product_result)) {
    $products[] = $row;
}

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    mysqli_query($conn, "DELETE FROM order_info WHERE invoice_no = '$invoice_no'");

    $user_full_name = mysqli_real_escape_string($conn, $_POST['user_full_name']);
    $user_phone     = mysqli_real_escape_string($conn, $_POST['user_phone']);
    $user_email     = mysqli_real_escape_string($conn, $_POST['user_email']);
    $user_address   = mysqli_real_escape_string($conn, $_POST['user_address']);
    $city_address   = mysqli_real_escape_string($conn, $_POST['city_address']);
    $payment_method = mysqli_real_escape_string($conn, $_POST['payment_method']);
    $order_status   = mysqli_real_escape_string($conn, $_POST['order_status']);
    $discount_amount = intval($_POST['discount_amount']);
    $user_id = 0;
    $order_visibility = "Show";
    $total_order_amount = 0;

    foreach ($_POST['products'] as $item) {
        $product_id = intval($item['product_id']);
        $product_size = mysqli_real_escape_string($conn, $item['product_size']);
        $product_quantity = intval($item['product_quantity']);
        $total_price = intval($item['total_price']);

        $product_title = '';
        $query = mysqli_query($conn, "SELECT product_title FROM product_info WHERE product_id=$product_id");
        if ($row = mysqli_fetch_assoc($query)) {
            $product_title = mysqli_real_escape_string($conn, $row['product_title']);
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

    mysqli_query($conn, "DELETE FROM order_discount_list WHERE invoice_no = '$invoice_no'");
    mysqli_query($conn, "INSERT INTO order_discount_list (invoice_no, total_order_amount, total_discount_amount) 
                VALUES ('$invoice_no', '$total_order_amount', '$discount_amount')");

    // echo "<script>window.location.href='order_details.php?invoice_no=" . $invoice_no . "';</script>";
    echo "<script>window.location.href='makeInvoice.php'</script>";

    exit;
}
?>

<div class="content-wrapper">
<div class="container py-5">
<div class="card shadow-lg border-0">
    <div class="card-header bg-dark text-white">
        <h1 class="mb-0 p-3">Edit Order (<?= htmlspecialchars($invoice_no) ?>)</h1>
    </div>
    <div class="card-body p-4">
        <form method="POST">
            <div class="row">
                <!-- Left: Products -->
                <div class="col-lg-7 mb-4">
                    <div class="border-0 h-100">
                        <div class="bg-primary text-white"><h5 class="p-3">Products</h5></div>
                        <div id="products-container">
                            <?php foreach ($orders as $i => $row): ?>
                            <div class="product-row mb-3 p-2 border rounded">
                                <div class="row g-3 align-items-end">
                                    <div class="col-md-6">
                                        <label class="form-label">Product</label>
                                        <select name="products[<?= $i ?>][product_id]" class="form-select product-select" required>
                                            <option value="">Select Product</option>
                                            <?php foreach ($products as $p): ?>
                                                <option value="<?= $p['product_id'] ?>" <?= ($p['product_id']==$row['product_id'])?'selected':'' ?>>
                                                    <?= htmlspecialchars($p['product_title']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Size</label>
                                        <select name="products[<?= $i ?>][product_size]" class="form-select size-select">
                                            <option value="<?= htmlspecialchars($row['product_size']) ?>"><?= htmlspecialchars($row['product_size']) ?></option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Quantity</label>
                                        <input type="number" name="products[<?= $i ?>][product_quantity]" class="form-control qty-input" value="<?= $row['product_quantity'] ?>" required>
                                    </div>
                                    <div class="col-md-6 mt-2">
                                        <label class="form-label">Total Price</label>
                                        <input type="number" name="products[<?= $i ?>][total_price]" class="form-control price-input" value="<?= $row['total_price'] ?>" data-base-price="<?= $row['total_price']/$row['product_quantity'] ?>" required>
                                    </div>
                                    <div class="col-md-6 mt-2 d-flex justify-content-end">
                                        <button type="button" class="btn btn-danger remove-product">Remove</button>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <button type="button" class="btn btn-outline-primary w-100 mt-3" id="add-product">
                            + Add Another Product
                        </button>
                    </div>
                </div>

                <!-- Right: Customer & Order Info -->
                <div class="col-lg-5">
                    <div class="bg-success text-white"><h5 class="p-3">Customer & Order Info</h5></div>
                    <div>
                        <div class="mb-3">
                            <label class="form-label">Discount Amount</label>
                            <input type="number" name="discount_amount" class="form-control" value="<?= $discount_amount ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Payment Method</label>
                            <input type="text" name="payment_method" class="form-control" value="<?= htmlspecialchars($order['payment_method']) ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="user_full_name" class="form-control" value="<?= htmlspecialchars($order['user_full_name']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Phone</label>
                            <input type="text" name="user_phone" class="form-control" value="<?= htmlspecialchars($order['user_phone']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="user_email" class="form-control" value="<?= htmlspecialchars($order['user_email']) ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Address</label>
                            <textarea name="user_address" class="form-control"><?= htmlspecialchars($order['user_address']) ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Delivery Location</label>
                            <select name="city_address" class="form-select">
                                <option value="Inside Dhaka" <?= ($order['city_address']=='Inside Dhaka')?'selected':'' ?>>Inside Dhaka</option>
                                <option value="Outside Dhaka" <?= ($order['city_address']=='Outside Dhaka')?'selected':'' ?>>Outside Dhaka</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="order_status" class="form-select">
                                <option value="Pending" <?= ($order['order_status']=='Pending')?'selected':'' ?>>Pending</option>
                                <option value="Processing" <?= ($order['order_status']=='Processing')?'selected':'' ?>>Processing</option>
                                <option value="Shipped" <?= ($order['order_status']=='Shipped')?'selected':'' ?>>Shipped</option>
                                <option value="Completed" <?= ($order['order_status']=='Completed')?'selected':'' ?>>Completed</option>
                                <option value="Canceled" <?= ($order['order_status']=='Canceled')?'selected':'' ?>>Canceled</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-dark w-100"><b>Update Order</b></button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
</div>

<script>
// Add/Remove product rows
document.getElementById('add-product').addEventListener('click', function() {
    const container = document.getElementById('products-container');
    const index = container.querySelectorAll('.product-row').length;
    const newRow = container.firstElementChild.cloneNode(true);

    newRow.querySelectorAll('select, input').forEach(el => {
        if(el.name) el.name = el.name.replace(/\[\d+\]/, `[${index}]`);
        el.value = '';
    });

    newRow.querySelector('.size-select').innerHTML = '<option value="">Select Size</option>';
    newRow.querySelector('.price-input').removeAttribute('data-base-price');
    container.appendChild(newRow);
});

// Remove product row
document.addEventListener('click', function(e){
    if(e.target.classList.contains('remove-product')){
        const rows = document.querySelectorAll('.product-row');
        if(rows.length>1) e.target.closest('.product-row').remove();
    }
});

// Fetch sizes and base price dynamically
document.addEventListener('change', function(e){
    if(e.target.classList.contains('product-select')){
        const productId = e.target.value;
        const row = e.target.closest('.product-row');
        const sizeSelect = row.querySelector('.size-select');
        const priceInput = row.querySelector('.price-input');
        const qtyInput = row.querySelector('.qty-input');

        fetch('fetch-product-info.php?product_id='+productId)
            .then(res => res.json())
            .then(data => {
                // Update sizes
                sizeSelect.innerHTML = '<option value="">Select Size</option>';
                if(Array.isArray(data.sizes)){
                    data.sizes.forEach(size => {
                        sizeSelect.innerHTML += `<option value="${size}">${size}</option>`;
                    });
                }
                // Update base price
                priceInput.value = '';
                qtyInput.value = '';
                priceInput.setAttribute('data-base-price', data.price || 0);
            });
    }
});

// Auto-calc total price
document.addEventListener('input', function(e){
    if(e.target.classList.contains('qty-input')){
        const row = e.target.closest('.product-row');
        const qty = parseInt(e.target.value) || 0;
        const priceInput = row.querySelector('.price-input');
        const basePrice = parseInt(priceInput.getAttribute('data-base-price')) || 0;
        priceInput.value = basePrice * qty;
    }
});
</script>

<?php require 'footer.php'; ?>