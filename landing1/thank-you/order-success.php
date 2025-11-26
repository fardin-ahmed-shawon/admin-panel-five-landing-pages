<?php
session_start();
require '../../database/dbConnection.php';

// Get invoice number from URL
$invoice_no = $_GET['invoice'] ?? '';

if (empty($invoice_no)) {
    header('Location: ../index.php');
    exit;
}

// Fetch order information
$sql = "SELECT * FROM order_info WHERE invoice_no = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $invoice_no);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header('Location: ../index.php');
    exit;
}

$order = $result->fetch_assoc();

// Customer Information
$customerInfo = [
    'fullName' => $order['user_full_name'],
    'phone' => $order['user_phone'],
    'email' => $order['user_email'] ?? '',
    'address' => $order['user_address'],
    'city' => $order['city_address'],
    'orderNote' => ''
];

// Fetch product details
$product_id = $order['product_id'];
$sql = "SELECT * FROM product_info WHERE product_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$product_result = $stmt->get_result();
$product = $product_result->fetch_assoc();

// Order Items
$items = [
    [
        'name' => $order['product_title'],
        'price' => $order['total_price'] / $order['product_quantity'],
        'quantity' => $order['product_quantity'],
        'img' => $product['product_img1'] ?? '',
        'slug' => $product['product_slug'] ?? ''
    ]
];

// Calculate shipping cost
$shipping_cost = 0;
if ($order['city_address'] == 'Inside Dhaka') {
    $sql = "SELECT inside_delivery_charge FROM website_info LIMIT 1";
    $result = mysqli_query($conn, $sql);
    if ($row = mysqli_fetch_assoc($result)) {
        $shipping_cost = $row['inside_delivery_charge'];
    }
} elseif ($order['city_address'] == 'Outside Dhaka') {
    $sql = "SELECT outside_delivery_charge FROM website_info LIMIT 1";
    $result = mysqli_query($conn, $sql);
    if ($row = mysqli_fetch_assoc($result)) {
        $shipping_cost = $row['outside_delivery_charge'];
    }
}

// Calculate discount (if any)
$subtotal = $order['total_price'];
$discount = 0;
$total = $subtotal + $shipping_cost;

// Pricing
$pricing = [
    'subtotal' => $subtotal,
    'discount' => $discount,
    'shipping' => $shipping_cost,
    'total' => $total
];

// Payment Information
$paymentInfo = [
    'method' => $order['payment_method']
];

// Fetch website settings
$sql = "SELECT * FROM website_info LIMIT 1";
$result = mysqli_query($conn, $sql);
if ($row = mysqli_fetch_assoc($result)) {
    $websiteName = $row['name'];
    $websitePhone = $row['phone'];
    $logo = $row['logo'];
}

// Calculate item totals
function calculateItemTotal($price, $quantity) {
    return number_format($price * $quantity, 2);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Success - Invoice #<?php echo $invoice_no; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
        }
        .success-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #6da538 0%, #004d1f 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 48px;
            color: white;
            font-weight: bold;
            margin: 0 auto 1rem;
            animation: scaleIn 0.5s ease-out;
        }
        @keyframes scaleIn {
            0% {
                transform: scale(0);
            }
            50% {
                transform: scale(1.1);
            }
            100% {
                transform: scale(1);
            }
        }
        .order-card {
            max-width: 900px;
            border-radius: 20px;
            background: white;
        }
        .btn-primary-custom {
            background: #6da538;
            border: none;
        }
        .btn-primary-custom:hover {
            background: #5a8c2e;
        }
        .product-img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
        }
        header {
            background: #fff;
            padding: 20px 0;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }
        .logo {
            display: flex;
            justify-content: center;
        }
        .logo img {
            max-width: 150px;
            height: auto;
        }
    </style>
</head>
<body>

    <div class="container py-5">
        <div class="card shadow-lg p-4 mx-auto order-card">

            <!-- Success Header -->
            <div class="text-center mb-4">
                <div class="success-icon">✓</div>
                <h2 class="fw-bold text-dark">Order Placed Successfully!</h2>
                <p class="text-muted">
                    Thank you for your purchase. Your order has been confirmed and is being processed.
                </p>
            </div>

            <!-- Invoice Number -->
            <div class="p-3 bg-light rounded mb-4 text-center">
                <p class="fw-semibold mb-1">Invoice Number</p>
                <h4 class="text-success fw-bold"><?php echo $invoice_no; ?></h4>
            </div>

            <!-- Customer Information -->
            <div class="mb-4">
                <h5 class="fw-bold mb-3 pb-2 border-bottom">Customer Information</h5>
                <div class="row">
                    <div class="col-md-6 mb-2">
                        <p class="mb-1"><strong>Name:</strong> <?php echo $customerInfo['fullName']; ?></p>
                        <p class="mb-1"><strong>Phone:</strong> <?php echo $customerInfo['phone']; ?></p>
                        <p class="mb-1"><strong>Address:</strong> <?php echo $customerInfo['address']; ?></p>
                    </div>
                    <div class="col-md-6 mb-2">
                        <?php if (!empty($customerInfo['email'])): ?>
                            <p class="mb-1"><strong>Email:</strong> <?php echo $customerInfo['email']; ?></p>
                        <?php endif; ?>
                        <p class="mb-1"><strong>City:</strong> <?php echo $customerInfo['city']; ?></p>
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <div class="mb-4">
                <h5 class="fw-bold mb-3 pb-2 border-bottom">Order Items</h5>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $item): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <span><?php echo $item['name']; ?></span>
                                        </div>
                                    </td>
                                    <td>৳ <?php echo number_format($item['price'], 2); ?></td>
                                    <td><?php echo $item['quantity']; ?></td>
                                    <td>৳ <?php echo calculateItemTotal($item['price'], $item['quantity']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pricing Summary -->
            <div class="mb-4">
                <h5 class="fw-bold mb-3 pb-2 border-bottom">Order Summary</h5>
                <div class="row">
                    <div class="col-md-6 ms-auto">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <span class="fw-semibold">৳ <?php echo number_format($pricing['subtotal'], 2); ?></span>
                        </div>
                        <?php if ($pricing['discount'] > 0): ?>
                            <div class="d-flex justify-content-between mb-2 text-success">
                                <span>Discount:</span>
                                <span class="fw-semibold">- ৳ <?php echo number_format($pricing['discount'], 2); ?></span>
                            </div>
                        <?php endif; ?>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Shipping Cost:</span>
                            <span class="fw-semibold">
                                <?php echo $pricing['shipping'] === 0 ? 'Free' : '৳ ' . number_format($pricing['shipping'], 2); ?>
                            </span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <span class="fw-bold fs-5">Total Cost:</span>
                            <span class="fw-bold fs-5 text-success">৳ <?php echo number_format($pricing['total'], 2); ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Information -->
            <div class="mb-4">
                <h5 class="fw-bold mb-3 pb-2 border-bottom">Payment Information</h5>
                <p class="mb-1"><strong>Payment Method:</strong> <?php echo $paymentInfo['method']; ?></p>
                <p class="text-muted small">You will pay when you receive your order.</p>
            </div>

            <!-- Action Buttons -->
            <div class="d-flex flex-column flex-sm-row gap-2 justify-content-center mt-4">
                <a href="../<?=  $item['slug'] ?>" class="btn btn-primary-custom py-3 px-4 text-white">
                    ← Back to Home
                </a>
            </div>

            <!-- Contact Info -->
            <div class="text-center mt-4 pt-3 border-top">
                <p class="text-muted mb-1">Need help with your order?</p>
                <p class="fw-semibold">Call us: <a href="tel:<?php echo $websitePhone; ?>"><?php echo $websitePhone; ?></a></p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Print Styles -->
    <style media="print">
        .btn, .alert {
            display: none !important;
        }
        body {
            background: white;
        }
        .order-card {
            box-shadow: none !important;
        }
    </style>
</body>
</html>