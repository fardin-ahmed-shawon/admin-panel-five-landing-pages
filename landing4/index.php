<?php
session_start();
require '../database/dbConnection.php';

$product_slug = $_GET['slug'] ?? '';

// Fetch product id based on slug
if ($product_slug != '') {
    $sql = "SELECT product_id FROM landing_pages WHERE product_slug='$product_slug'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_num_rows($result);

    if ($row > 0) {
        $data = mysqli_fetch_assoc($result);
        $product_id = $data['product_id'];
    } else {
        exit;
    }
} else {
    exit;
}

// Fetch website settings
$sql = "SELECT * FROM website_info";
$result = mysqli_query($conn, $sql);
$row = mysqli_num_rows($result);
if ($row > 0) {
    while ($data = mysqli_fetch_assoc($result)) {
        $websiteName = $data['name'];
        $websitePhone = $data['phone'];
        $logo = $data['logo'];
    }
}

// Fetch Landing Page Info
$sql = "SELECT * FROM landing_pages WHERE product_slug='$product_slug'";
$result = mysqli_query($conn, $sql);
$row = mysqli_num_rows($result);
if ($row > 0) {
    while ($data = mysqli_fetch_assoc($result)) {
        $home_title = $data['home_title'];
        $home_des = $data['home_description'];
        $home_img = $data['home_img'];
    }
}

// Fetch Product Info
$sql = "SELECT * FROM product_info WHERE product_id = $product_id LIMIT 1";
$result = mysqli_query($conn, $sql);
$productData = mysqli_fetch_assoc($result);
$productTitle = $productData['product_title'];
$productPrice = $productData['product_price'];
$productImg = $productData['product_img1'];
$regularPrice = $productData['product_regular_price'];
$discount = round((($regularPrice - $productPrice) / $regularPrice) * 100);

// Fetch available sizes
$sizes_sql = "SELECT size FROM product_size_list WHERE product_id = $product_id";
$sizes_result = mysqli_query($conn, $sizes_sql);
$sizes = [];
while ($size_row = mysqli_fetch_assoc($sizes_result)) {
    $sizes[] = $size_row['size'];
}

// Fetch gallery images
$gallery_sql = "SELECT gallery_image FROM gallery WHERE product_id = $product_id";
$gallery_result = mysqli_query($conn, $gallery_sql);
$gallery_images = [];
while ($gallery_row = mysqli_fetch_assoc($gallery_result)) {
    $gallery_images[] = $gallery_row['gallery_image'];
}

// Fetch features
$features_sql = "SELECT feature_title, feature_description FROM features WHERE product_id = $product_id";
$features_result = mysqli_query($conn, $features_sql);
$features = [];
while ($feature_row = mysqli_fetch_assoc($features_result)) {
    $features[] = $feature_row;
}

// Process Order
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullName = $_POST['name'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $selectedSize = $_POST['product_size'];
    $quantity = intval($_POST['quantity']);
    $shippingCost = intval($_POST['shipping']);
    
    $city = $shippingCost == 80 ? 'Inside Dhaka' : 'Outside Dhaka';
    $payment_method = 'Cash On Delivery';
    $user_id = 0;

    function generateInvoiceNo() {
        $timestamp = microtime(true) * 10000;
        $uniqueString = 'INV-' . strtoupper(base_convert($timestamp, 10, 36));
        return $uniqueString;
    }
    $invoice_no = generateInvoiceNo();
    $_SESSION['temporary_invoice_no'] = $invoice_no;

    $product_title_full = $productTitle;
    if (!empty($selectedSize) && $selectedSize !== 'standard') {
        $product_title_full .= " (Size: $selectedSize)";
    }
    
    $total_price = ($productPrice * $quantity);

    $sql = "INSERT INTO order_info (user_id, user_full_name, user_phone, user_email, user_address, city_address, invoice_no, product_id, product_title, product_quantity, product_size, total_price, payment_method)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $email = '';
    $stmt->bind_param(
        "issssssisisss",
        $user_id,
        $fullName,
        $phone,
        $email,
        $address,
        $city,
        $invoice_no,
        $product_id,
        $product_title_full,
        $quantity,
        $selectedSize,
        $total_price,
        $payment_method
    );
    $stmt->execute();
    
    $stmt->close();
    $conn->close();
    echo "<script>window.location.href = 'index.php?slug=$product_slug&or_msg=successful';</script>";
    exit;
}

// Fetch reviews
$reviews_sql = "SELECT review_image FROM reviews WHERE product_id = $product_id";
$reviews_result = mysqli_query($conn, $reviews_sql);
$reviews = [];
while($review = mysqli_fetch_assoc($reviews_result)) {
    $reviews[] = $review['review_image'];
}
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $websiteName; ?> - <?php echo $productTitle; ?></title>
    <link href="../Admin/<?= $logo ?>" rel="icon">
    
    <!-- Hind Siliguri font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Hind Siliguri', sans-serif;
        }

        body {
            font-family: 'Hind Siliguri', sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #fff;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .success-message {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: #d4edda;
            color: #155724;
            padding: 20px;
            text-align: center;
            z-index: 9999;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            font-size: 18px;
            font-weight: bold;
        }

        .hero-section {
            text-align: center;
            padding: 40px 20px;
        }

        .hero-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 30px;
            color: #2c3e50;
            line-height: 1.3;
        }

        .hero-image {
            max-width: 100%;
            height: auto;
            border-radius: 15px;
            margin: 20px 0;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .price-box {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 30px;
            border-radius: 15px;
            margin: 30px 0;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .price-item {
            margin-bottom: 20px;
            padding: 15px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .old-price {
            text-decoration: line-through;
            color: #e74c3c;
            font-weight: 500;
        }

        .new-price {
            color: #27ae60;
            font-weight: 700;
            font-size: 1.2rem;
        }

        .offer-notice {
            background: #e74c3c;
            color: white;
            padding: 15px;
            border-radius: 10px;
            margin: 20px 0;
            font-weight: 600;
            font-size: 1.1rem;
        }

        .order-button {
            background: linear-gradient(135deg, #27ae60 0%, #2ecc71 100%);
            color: white;
            border: none;
            padding: 18px 40px;
            font-size: 1.2rem;
            font-weight: 600;
            border-radius: 50px;
            cursor: pointer;
            box-shadow: 0 8px 25px rgba(39, 174, 96, 0.3);
            transition: all 0.3s ease;
            margin: 20px 0;
        }

        .order-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(39, 174, 96, 0.4);
        }

        .details-section {
            margin: 60px 0;
            padding: 40px 20px;
            background: #f8f9fa;
            border-radius: 15px;
        }

        .detail-images {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .detail-image {
            width: 100%;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .benefits-list {
            list-style: none;
            padding: 0;
        }

        .benefits-list li {
            padding: 15px 20px;
            margin-bottom: 10px;
            background: white;
            border-radius: 8px;
            border-left: 4px solid #27ae60;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        .section-title {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 30px;
            color: #2c3e50;
            text-align: center;
        }

        .reviews-section {
            margin: 60px 0;
            padding: 40px 20px;
        }

        .review-card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .review-image {
            width: 100%;
            height: auto;
            display: block;
        }

        .contact-section {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            color: white;
            padding: 40px 20px;
            text-align: center;
            border-radius: 15px;
            margin: 40px 0;
        }

        .contact-button {
            display: inline-block;
            background: #27ae60;
            color: white;
            padding: 15px 30px;
            font-size: 1.3rem;
            font-weight: 600;
            border-radius: 50px;
            text-decoration: none;
            margin-top: 20px;
            box-shadow: 0 5px 15px rgba(39, 174, 96, 0.3);
            transition: all 0.3s ease;
        }

        .contact-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(39, 174, 96, 0.4);
        }

        .form-section {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            margin: 40px 0;
        }

        .form-title {
            text-align: center;
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 30px;
            color: #2c3e50;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #2c3e50;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #27ae60;
        }

        .required {
            color: #e74c3c;
        }

        .product-variants {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-bottom: 30px;
        }

        .product-variant-card {
            display: grid;
            grid-template-columns: auto 80px 1fr auto;
            align-items: center;
            padding: 20px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            gap: 15px;
            background: white;
        }

        .product-variant-card:hover {
            border-color: #27ae60;
            box-shadow: 0 4px 15px rgba(39, 174, 96, 0.1);
        }

        .product-variant-card.selected {
            border-color: #27ae60;
            background: #f8fff9;
            box-shadow: 0 4px 15px rgba(39, 174, 96, 0.2);
        }

        .variant-radio {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .variant-radio input[type="radio"] {
            width: 20px;
            height: 20px;
            cursor: pointer;
            accent-color: #27ae60;
        }

        .variant-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .variant-info {
            flex: 1;
        }

        .variant-name {
            font-weight: 600;
            margin-bottom: 5px;
            color: #2c3e50;
            font-size: 1.05rem;
        }

        .variant-price {
            font-weight: 700;
            color: #27ae60;
            font-size: 1.2rem;
        }

        .variant-quantity {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .product-display {
            display: flex;
            align-items: center;
            padding: 20px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            margin-bottom: 20px;
            gap: 15px;
        }

        .product-image-small {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
        }

        .product-info {
            flex: 1;
        }

        .product-name {
            font-weight: 600;
            margin-bottom: 5px;
            color: #2c3e50;
            font-size: 1.1rem;
        }

        .product-price {
            font-weight: 700;
            color: #27ae60;
            font-size: 1.2rem;
        }

        .quantity-control {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .quantity-btn {
            background: #f8f9fa;
            border: 1px solid #ddd;
            width: 40px;
            height: 40px;
            border-radius: 5px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            transition: all 0.3s ease;
        }

        .quantity-btn:hover {
            background: #e9ecef;
        }

        .quantity-input {
            width: 60px;
            text-align: center;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 8px;
            font-weight: 600;
        }

        .size-selector {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
            gap: 10px;
            margin-bottom: 20px;
        }

        .size-option {
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 600;
        }

        .size-option:hover {
            border-color: #27ae60;
        }

        .size-option.selected {
            border-color: #27ae60;
            background: #f8fff9;
            color: #27ae60;
        }

        .order-summary {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 10px;
            margin: 30px 0;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #e0e0e0;
        }

        .summary-row:last-child {
            border-bottom: none;
            font-weight: 700;
            font-size: 1.2rem;
            color: #2c3e50;
        }

        .submit-button {
            width: 100%;
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
            color: white;
            border: none;
            padding: 20px;
            font-size: 1.3rem;
            font-weight: 600;
            border-radius: 10px;
            cursor: pointer;
            box-shadow: 0 8px 25px rgba(231, 76, 60, 0.3);
            transition: all 0.3s ease;
        }

        .submit-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(231, 76, 60, 0.4);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2rem;
            }
            
            .product-display {
                flex-direction: column;
                text-align: center;
            }
            
            .form-section {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <?php if (isset($_GET['or_msg'])): ?>
    <div class="success-message" id="successMsg">
        আপনার অর্ডারটি সফলভাবে গ্রহণ করা হয়েছে! আমরা শীঘ্রই আপনার সাথে যোগাযোগ করব।
    </div>
    <script>
        setTimeout(() => {
            document.getElementById('successMsg').style.display = 'none';
        }, 5000);
    </script>
    <?php endif; ?>

    <div class="container">
        <!-- Hero Section -->
        <div class="hero-section">
            <h1 class="hero-title"><?= $home_title ?></h1>
            <?php 
            $hero_img = strpos($home_img, 'Admin/') === 0 ? '../' . $home_img : '../Admin/' . $home_img;
            ?>
            <img src="<?= $hero_img ?>" alt="<?= $productTitle ?>" class="hero-image">
            
            <div class="price-box">
                <div class="price-item">
                    <div>পূর্বের মূল্য <span class="old-price">৳<?= $regularPrice ?></span></div>
                    <div>বর্তমান মূল্য <span class="new-price">৳<?= $productPrice ?></span></div>
                </div>
            </div>

            <div class="offer-notice">
                🔥 বিঃদ্রঃ অফারটি সীমিত সময়ের জন্য থাকবে 🔥
            </div>

            <button class="order-button" onclick="scrollToForm()">
                🛒 অর্ডার করতে ক্লিক করুন
            </button>
        </div>

        <!-- Product Details -->
        <?php if (!empty($gallery_images) || !empty($features)): ?>
        <div class="details-section">
            <?php if (!empty($gallery_images)): ?>
            <div class="detail-images">
                <?php foreach($gallery_images as $img): 
                    $img_path = strpos($img, 'Admin/') === 0 ? '../' . $img : '../Admin/' . $img;
                ?>
                <img src="<?= $img_path ?>" alt="Product Image" class="detail-image">
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <?php if (!empty($features)): ?>
            <ul class="benefits-list">
                <?php foreach($features as $feature): ?>
                <li><strong><?= $feature['feature_title'] ?>:</strong> <?= $feature['feature_description'] ?></li>
                <?php endforeach; ?>
            </ul>
            <?php endif; ?>

            <div style="text-align: center; margin: 30px 0;">
                <button class="order-button" onclick="scrollToForm()">
                    🛒 অর্ডার করতে ক্লিক করুন
                </button>
            </div>
        </div>
        <?php endif; ?>

        <!-- Customer Reviews -->
        <?php if (!empty($reviews)): ?>
        <div class="reviews-section">
            <h2 class="section-title">আমাদের কাস্টমার ফিডব্যাক</h2>
            
            <div class="swiper reviews-swiper">
                <div class="swiper-wrapper">
                    <?php foreach($reviews as $review_img): ?>
                    <div class="swiper-slide">
                        <div class="review-card">
                            <img src="../Admin/<?= $review_img ?>" alt="Review" class="review-image">
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="swiper-button-next"></div>
                <div class="swiper-button-prev"></div>
                <div class="swiper-pagination"></div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Contact Section -->
        <div class="contact-section">
            <h2 class="section-title" style="color: white;">প্রয়োজনে কল করুন:</h2>
            <a href="tel:<?= $websitePhone ?>" class="contact-button">
                📞 <?= $websitePhone ?>
            </a>
        </div>

        <!-- Order Form -->
        <div class="form-section" id="orderForm">
            <h2 class="form-title">অর্ডার করতে সঠিক তথ্য দিয়ে নিচের ফর্মটি পূরণ করুন</h2>

            <form method="POST" id="checkoutForm">
                <div class="form-group">
                    <label>নাম <span class="required">*</span></label>
                    <input type="text" name="name" placeholder="আপনার নাম" required>
                </div>

                <div class="form-group">
                    <label>ঠিকানা <span class="required">*</span></label>
                    <input type="text" name="address" placeholder="সম্পূর্ণ ঠিকানা, থানা, জেলা" required>
                </div>

                <div class="form-group">
                    <label>নাম্বার <span class="required">*</span></label>
                    <input type="tel" name="phone" placeholder="আপনার ১১ ডিজিটের মোবাইল নাম্বার" pattern="[0-9]{11}" required>
                </div>

                <h3 style="margin: 30px 0 20px 0; text-align: center;">পণ্য নির্বাচন করুন</h3>

                <div class="product-variants">
                    <?php if (!empty($sizes)): ?>
                        <?php foreach($sizes as $index => $size): ?>
                        <div class="product-variant-card <?= $index === 0 ? 'selected' : '' ?>" onclick="selectVariant(<?= $index ?>)">
                            <div class="variant-radio">
                                <input type="radio" name="product_variant" value="<?= $size ?>" <?= $index === 0 ? 'checked' : '' ?> id="variant_<?= $index ?>">
                            </div>
                            <img src="<?= $productImg ?>" alt="<?= $productTitle ?>" class="variant-image">
                            <div class="variant-info">
                                <div class="variant-name"><?= $productTitle ?> - সাইজ: <?= $size ?></div>
                                <div class="variant-price">৳ <?= $productPrice ?></div>
                            </div>
                            <div class="variant-quantity">
                                <button type="button" class="quantity-btn" onclick="updateVariantQuantity(<?= $index ?>, -1); event.stopPropagation();">−</button>
                                <input type="text" class="quantity-input" value="1" readonly data-variant="<?= $index ?>">
                                <button type="button" class="quantity-btn" onclick="updateVariantQuantity(<?= $index ?>, 1); event.stopPropagation();">+</button>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        <input type="hidden" name="product_size" id="selectedSize" value="<?= $sizes[0] ?? 'standard' ?>">
                        <input type="hidden" name="quantity" id="selectedQuantity" value="1">
                    <?php else: ?>
                        <div class="product-variant-card selected" onclick="selectVariant(0)">
                            <div class="variant-radio">
                                <input type="radio" name="product_variant" value="standard" checked id="variant_0">
                            </div>
                            <img src="<?= $productImg ?>" alt="<?= $productTitle ?>" class="variant-image">
                            <div class="variant-info">
                                <div class="variant-name"><?= $productTitle ?></div>
                                <div class="variant-price">৳ <?= $productPrice ?></div>
                            </div>
                            <div class="variant-quantity">
                                <button type="button" class="quantity-btn" onclick="updateVariantQuantity(0, -1); event.stopPropagation();">−</button>
                                <input type="text" class="quantity-input" value="1" readonly data-variant="0">
                                <button type="button" class="quantity-btn" onclick="updateVariantQuantity(0, 1); event.stopPropagation();">+</button>
                            </div>
                        </div>
                        <input type="hidden" name="product_size" value="standard">
                        <input type="hidden" name="quantity" id="selectedQuantity" value="1">
                    <?php endif; ?>
                </div>

                <div class="order-summary">
                    <h3 style="margin-bottom: 20px;">আপনার অর্ডার</h3>
                    <div class="summary-row">
                        <span>Subtotal</span>
                        <span id="subtotal">৳ <?= $productPrice ?></span>
                    </div>
                    <div class="summary-row">
                        <span>Shipping</span>
                        <div>
                            <div style="margin-bottom: 10px;">
                                <input type="radio" name="shipping" value="150" id="shipping-outside" checked onchange="updateTotal()">
                                <label for="shipping-outside">ঢাকার বাইরে: ৳ 150</label>
                            </div>
                            <div>
                                <input type="radio" name="shipping" value="80" id="shipping-inside" onchange="updateTotal()">
                                <label for="shipping-inside">ঢাকার ভিতরে: ৳ 80</label>
                            </div>
                        </div>
                    </div>
                    <div class="summary-row">
                        <span>Total</span>
                        <span id="total">৳ <?= $productPrice + 130 ?></span>
                    </div>
                </div>

                <input type="submit" class="submit-button" value="অর্ডারটি কনফার্ম করুন"/>
            </form>
        </div>
    </div>

    <!-- Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

    <script>
        const productPrice = <?= $productPrice ?>;
        let selectedVariantIndex = 0;
        let variantQuantities = [1<?php if (!empty($sizes)): ?><?php for($i = 1; $i < count($sizes); $i++): ?>, 1<?php endfor; ?><?php endif; ?>];

        // Initialize Swiper for Reviews
        <?php if (!empty($reviews)): ?>
        const reviewsSwiper = new Swiper('.reviews-swiper', {
            slidesPerView: 1,
            spaceBetween: 20,
            loop: true,
            autoplay: {
                delay: 3000,
                disableOnInteraction: false,
            },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            breakpoints: {
                640: {
                    slidesPerView: 2,
                },
                1024: {
                    slidesPerView: 3,
                }
            }
        });
        <?php endif; ?>

        function selectVariant(index) {
            selectedVariantIndex = index;
            
            // Update card selection
            document.querySelectorAll('.product-variant-card').forEach((card, i) => {
                if (i === index) {
                    card.classList.add('selected');
                    card.querySelector('input[type="radio"]').checked = true;
                } else {
                    card.classList.remove('selected');
                }
            });

            // Update hidden inputs
            const selectedRadio = document.querySelector(`#variant_${index}`);
            document.getElementById('selectedSize').value = selectedRadio.value;
            document.getElementById('selectedQuantity').value = variantQuantities[index];

            updateTotal();
        }

        function updateVariantQuantity(index, change) {
            variantQuantities[index] = Math.max(1, variantQuantities[index] + change);
            
            const quantityInput = document.querySelector(`input.quantity-input[data-variant="${index}"]`);
            quantityInput.value = variantQuantities[index];

            // If this is the selected variant, update the hidden quantity
            if (index === selectedVariantIndex) {
                document.getElementById('selectedQuantity').value = variantQuantities[index];
            }

            updateTotal();
        }

        function updateTotal() {
            const quantity = variantQuantities[selectedVariantIndex];
            const subtotal = productPrice * quantity;
            const shippingCost = parseInt(document.querySelector('input[name="shipping"]:checked').value);
            const total = subtotal + shippingCost;

            document.getElementById('subtotal').textContent = '৳ ' + subtotal;
            document.getElementById('total').textContent = '৳ ' + total;
        }

        function scrollToForm() {
            document.getElementById('orderForm').scrollIntoView({ behavior: 'smooth', block: 'start' });
        }

        // Form validation
        document.getElementById('checkoutForm').addEventListener('submit', function(e) {
            const phone = document.querySelector('input[name="phone"]').value;
            if (phone.length !== 11 || !/^\d+$/.test(phone)) {
                e.preventDefault();
                alert('দয়া করে সঠিক ১১ ডিজিটের মোবাইল নম্বর দিন');
                return false;
            }
        });

        // Initialize
        updateTotal();
    </script>
</body>
</html>