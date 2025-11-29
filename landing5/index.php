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
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $selectedSize = $_POST['product_size'];
    $quantity = intval($_POST['quantity']);
    $shippingCost = intval($_POST['shipping']);
    
    $fullName = $_POST['full_name'] ?? 'Customer';
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
        $product_title_full .= " - Size: $selectedSize";
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
    
    <!-- Hind Siliguri & Noto Sans Bengali font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@300;400;500;600;700&family=Noto+Sans+Bengali:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Hind Siliguri', 'Noto Sans Bengali', 'Segoe UI', sans-serif;
            line-height: 1.6;
            color: #333;
            background: #f5f5f5;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
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

        /* Header */
        header {
            background: #fff;
            padding: 20px 0;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .logo h1 {
            font-size: 1.5rem;
            color: #2d5016;
            font-weight: 700;
        }

        /* Hero Section */
        .hero {
            background: linear-gradient(to bottom, #f5f5f5 0%, #e8e8e8 100%);
            padding: 30px 20px;
            text-align: center;
        }

        .hero h1 {
            font-size: 1.8rem;
            color: #2d5016;
            margin-bottom: 15px;
            line-height: 1.4;
        }

        .hero h1 .product-name {
            color: #ff4500;
            font-weight: bold;
        }

        .hero p {
            font-size: 1rem;
            color: #555;
            margin-bottom: 20px;
            max-width: 900px;
            margin-left: auto;
            margin-right: auto;
        }

        /* Countdown Timer */
        .countdown-section {
            background: #dc3545;
            color: #fff;
            padding: 15px;
            text-align: center;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
        }

        .countdown-section h3 {
            font-size: 1rem;
            margin-bottom: 10px;
        }

        .countdown {
            display: flex;
            justify-content: center;
            gap: 20px;
            flex-wrap: wrap;
        }

        .countdown-item {
            background: rgba(255, 255, 255, 0.15);
            padding: 10px 20px;
            border-radius: 10px;
            min-width: 70px;
        }

        .countdown-item span {
            display: block;
            font-size: 2rem;
            font-weight: bold;
            line-height: 1;
        }

        .countdown-item label {
            font-size: 0.85rem;
            opacity: 0.9;
            margin-top: 5px;
            display: block;
        }

        /* Product Hero */
        .product-hero {
            background: #fff;
            padding: 40px 20px;
        }

        .product-image-container {
            max-width: 800px;
            margin: 0 auto;
        }

        .product-image-container img {
            width: 100%;
            height: auto;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        }

        /* Section Title */
        .section-title {
            text-align: center;
            font-size: 1.8rem;
            margin: 40px 0 30px;
            padding: 20px;
            background: #6d4c28;
            color: #fff;
        }

        .section-title .highlight {
            color: #ffd700;
        }

        /* Features List */
        .features-list {
            background: #6d4c28;
            color: #fff;
            padding: 40px 20px;
        }

        .features-container {
            max-width: 1000px;
            margin: 0 auto;
        }

        .feature-item {
            display: flex;
            align-items: flex-start;
            gap: 15px;
            margin-bottom: 20px;
            padding: 15px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 8px;
        }

        .feature-item::before {
            content: '✓';
            background: #28a745;
            color: white;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            flex-shrink: 0;
            font-weight: bold;
            font-size: 1.2rem;
        }

        /* Gallery Section */
        .gallery-section {
            background: #fff;
            padding: 40px 20px;
        }

        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            max-width: 1000px;
            margin: 0 auto;
        }

        .gallery-grid img {
            width: 100%;
            height: auto;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        /* CTA Button */
        .cta-button {
            display: inline-block;
            background: #28a745;
            color: #fff;
            padding: 15px 40px;
            text-decoration: none;
            border-radius: 50px;
            font-size: 1.2rem;
            font-weight: bold;
            margin: 20px 0;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.4);
            border: none;
            cursor: pointer;
        }

        .cta-button:hover {
            background: #218838;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(40, 167, 69, 0.5);
        }

        /* Reviews Section */
        .reviews {
            background: #f8f9fa;
            padding: 60px 20px;
        }

        .swiper {
            width: 100%;
            padding: 20px 0 40px;
        }

        .swiper-slide {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .review-card {
            width: 350px;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            margin: 0 10px;
        }

        .review-card img {
            width: 100%;
            border-radius: 10px;
            margin-bottom: 15px;
        }

        .swiper-button-next,
        .swiper-button-prev {
            color: #28a745;
            background: rgba(255, 255, 255, 0.8);
            width: 40px;
            height: 40px;
            border-radius: 50%;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .swiper-button-next:after,
        .swiper-button-prev:after {
            font-size: 1.2rem;
            font-weight: bold;
        }

        .swiper-pagination-bullet {
            background: #28a745;
            opacity: 0.5;
            width: 12px;
            height: 12px;
        }

        .swiper-pagination-bullet-active {
            opacity: 1;
        }

        /* Order Form */
        .order-form {
            background: #f5f5f5;
            padding: 40px 20px;
        }

        .order-form .section-title {
            background: #28a745;
            color: #fff;
            font-size: 1.3rem;
            padding: 15px;
            margin-bottom: 30px;
            border-radius: 5px;
        }

        .form-container {
            max-width: 900px;
            margin: 0 auto;
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #28a745;
        }

        .product-variants-section h3 {
            margin: 20px 0 15px;
            font-size: 1.1rem;
            color: #333;
        }

        .product-option {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 15px;
            cursor: pointer;
            border: 2px solid #e0e0e0;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .product-option:hover,
        .product-option.selected {
            border-color: #28a745;
            background: #e8f5e9;
        }

        .product-option input[type="radio"] {
            width: 20px;
            height: 20px;
            cursor: pointer;
            accent-color: #28a745;
        }

        .product-option-content {
            flex: 1;
        }

        .product-option h4 {
            font-size: 1rem;
            color: #000;
            margin-bottom: 5px;
        }

        .product-option p {
            font-size: 0.9rem;
            color: #666;
            margin: 0;
        }

        .price-display {
            font-size: 1.1rem;
            font-weight: 700;
            color: #dc3545;
        }

        .price-display del {
            color: #999;
            font-size: 0.9rem;
            margin-right: 5px;
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

        .order-summary {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
        }

        .order-summary h3 {
            margin-bottom: 15px;
            font-size: 1.2rem;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #ddd;
        }

        .summary-total {
            font-size: 1.4rem;
            font-weight: bold;
            color: #dc3545;
            border-top: 2px solid #333;
            margin-top: 10px;
            padding-top: 10px;
        }

        .submit-btn {
            width: 100%;
            background: #ff6b35;
            color: #fff;
            padding: 18px;
            border: none;
            border-radius: 8px;
            font-size: 1.3rem;
            font-weight: bold;
            cursor: pointer;
            margin-top: 20px;
            transition: all 0.3s;
        }

        .submit-btn:hover {
            background: #ff5722;
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(255, 107, 53, 0.4);
        }

        /* Footer */
        footer {
            background: #2d5016;
            color: #fff;
            padding: 40px 20px;
            text-align: center;
        }

        .footer-contact {
            font-size: 1.5rem;
            margin-bottom: 10px;
        }

        /* Sticky Button */
        .sticky-cta {
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1000;
            display: none;
        }

        .sticky-cta.show {
            display: block;
            animation: slideUp 0.3s ease;
        }

        @keyframes slideUp {
            from {
                transform: translate(-50%, 100px);
                opacity: 0;
            }
            to {
                transform: translate(-50%, 0);
                opacity: 1;
            }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero h1 {
                font-size: 1.4rem;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            .countdown {
                gap: 10px;
            }

            .countdown-item {
                padding: 8px 15px;
                min-width: 60px;
            }

            .countdown-item span {
                font-size: 1.5rem;
            }

            .review-card {
                width: 300px;
            }
            
            .swiper-button-next,
            .swiper-button-prev {
                display: none;
            }

            .product-option {
                flex-direction: column;
                align-items: flex-start;
            }

            .quantity-control {
                width: 100%;
                justify-content: center;
            }
        }

        @media (max-width: 575px) {
            header {
    background: #ffd9a84d;
    padding: 10px 0;
}
.section-title {
    font-size: 1.4rem;
    padding: 15px;
    margin: 0px 0 -2px;
}
.countdown-item span {
    font-size: 1.1rem;
}
.hero {
    padding: 10px 20px;
}      
.product-hero {
    background: #fff;
    padding: 20px 20px;
}
.features-list {
    padding: 20px 20px;
}
.feature-item {
    align-items: center;
    gap: 12px;
    margin-bottom: 20px;
    padding: 10px;
    font-size: 14px;
}           
.gallery-section {
    padding: 10px 20px;
}
.review-card {
                width: 250px;
            }
.reviews {
    padding: 20px 20px;
}
.cta-button {
    padding: 13px 15px;
    font-size: 0.93rem;
    margin: 20px 0;
}
.order-form {
    padding: 10px 15px;
}
.form-container {
    padding: 20px; 
}
.quantity-control {
    justify-content: start;
}
.form-grid {
    gap: 10px;
}
.submit-btn {
    font-size: 1rem;
    margin-top: 10px;
}
footer {
    padding: 10px 10px;
}
.footer-contact {
    font-size: 1.5rem;
    margin-bottom: 0;
}
element.style {
    margin-top: 0;
    opacity: 0.8;
}
        }
    </style>
</head>
<body>
    <?php if (isset($_GET['or_msg'])): ?>
    <div class="success-message" id="successMsg">
        আপনার অর্ডারটি সফলভাবে গ্রহণ করা হয়েছে! আমরা শীঘ্রই আপনার সাথে যোগাযোগ করব। ✅
    </div>
    <script>
        setTimeout(() => {
            document.getElementById('successMsg').style.display = 'none';
        }, 5000);
    </script>
    <?php endif; ?>

    <!-- Header -->
    <header>
        <div class="container">
            <div class="logo">
                <h1><?= $websiteName ?></h1>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <h1><?= $home_title ?> <span class="product-name"><?= $productTitle ?></span></h1>
            <p><?= $home_des ?></p>
        </div>
    </section>

    <!-- Countdown Timer -->
    <section class="countdown-section">
        <div class="container">
            <h3>⚠️ বিশেষ অফারটি সীমিত সময়ের জন্য</h3>
            <div class="countdown">
                <div class="countdown-item">
                    <span id="days">12</span>
                    <label>দিন</label>
                </div>
                <div class="countdown-item">
                    <span id="hours">46</span>
                    <label>ঘন্টা</label>
                </div>
                <div class="countdown-item">
                    <span id="minutes">57</span>
                    <label>মিনিট</label>
                </div>
            </div>
        </div>
    </section>

    <!-- Product Hero -->
    <section class="product-hero">
        <div class="container">
            <div class="product-image-container">
                <?php 
                $hero_img = strpos($home_img, 'Admin/') === 0 ? '../' . $home_img : '../Admin/' . $home_img;
                ?>
                <img src="<?= $hero_img ?>" alt="<?= $productTitle ?>">
            </div>
        </div>
    </section>

    <?php if (!empty($features)): ?>
    <h2 class="section-title"><span class="highlight">প্রোডাক্টের বিশেষত্ব</span></h2>

    <!-- Features List -->
    <section class="features-list">
        <div class="features-container">
            <?php foreach($features as $feature): ?>
            <div class="feature-item">
                <strong><?= $feature['feature_title'] ?>:</strong> <?= $feature['feature_description'] ?>
            </div>
            <?php endforeach; ?>
            <div style="text-align: center; margin-top: 30px;">
                <button class="cta-button" onclick="document.getElementById('order').scrollIntoView({behavior: 'smooth'})">এখনই অর্ডার করুন</button>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <?php if (!empty($gallery_images)): ?>
    <!-- Gallery Section -->
    <section class="gallery-section">
        <div class="container">
            <h2 style="text-align: center; font-size: 2rem; margin-bottom: 30px; color: #2d5016;">প্রোডাক্ট গ্যালারি</h2>
            <div class="gallery-grid">
                <?php foreach($gallery_images as $img): 
                    $img_path = strpos($img, 'Admin/') === 0 ? '../' . $img : '../Admin/' . $img;
                ?>
                <img src="<?= $img_path ?>" alt="Product Image">
                <?php endforeach; ?>
            </div>
            <div style="text-align: center; margin-top: 30px;">
                <button class="cta-button" onclick="document.getElementById('order').scrollIntoView({behavior: 'smooth'})">এখনই অর্ডার করুন</button>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <?php if (!empty($reviews)): ?>
    <h2 class="section-title">আমাদের কাস্টমার ফিডব্যাক</h2>

    <!-- Reviews -->
    <section class="reviews">
        <div class="container">
            <div class="swiper mySwiper">
                <div class="swiper-wrapper">
                    <?php foreach($reviews as $review_img): ?>
                    <div class="swiper-slide">
                        <div class="review-card">
                            <img src="../Admin/<?= $review_img ?>" alt="Review">
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="swiper-button-next"></div>
                <div class="swiper-button-prev"></div>
                <div class="swiper-pagination"></div>
            </div>
            <div style="text-align: center; margin-top: 30px;">
                <button class="cta-button" onclick="document.getElementById('order').scrollIntoView({behavior: 'smooth'})">এখনই অর্ডার করুন</button>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Order Form -->
    <section class="order-form" id="order">
        <div class="container">
            <h2 class="section-title">অর্ডার করতে এখানে নিচের তথ্য পূরণ করুন</h2>

            <div class="form-container">
                <form method="POST" id="orderForm">
                    <div class="form-grid">
                        <div>
                            <div class="form-group">
                                <label>আপনার পুরো নাম *</label>
                                <input type="text" name="full_name" placeholder="আপনার পুরো নাম লিখুন" required>
                            </div>
                            <div class="form-group">
                                <label>আপনার ১১ ডিজিটের মোবাইল নম্বর *</label>
                                <input type="tel" name="phone" required pattern="[0-9]{11}" placeholder="01XXXXXXXXX">
                            </div>

                            <div class="form-group">
                                <label>সম্পূর্ণ ঠিকানা: বাসা, রোড, থানা *</label>
                                <textarea name="address" rows="3" required placeholder="আপনার সম্পূর্ণ ঠিকানা লিখুন"></textarea>
                            </div>

                            <div class="form-group">
                                <label>Country / Region *</label>
                                <input type="text" value="Bangladesh" readonly style="background: #f0f0f0;">
                            </div>

                            <div class="product-variants-section">
                                <h3>পণ্য নির্বাচন করুন</h3>

                                <?php if (!empty($sizes)): ?>
                                    <?php foreach($sizes as $index => $size): ?>
                                    <div class="product-option <?= $index === 0 ? 'selected' : '' ?>" data-index="<?= $index ?>" onclick="selectVariant(<?= $index ?>)">
                                        <input type="radio" name="product_variant" value="<?= $size ?>" id="variant_<?= $index ?>" <?= $index === 0 ? 'checked' : '' ?>>
                                        <div class="product-option-content">
                                            <h4><?= $productTitle ?> - সাইজ: <?= $size ?></h4>
                                            <p class="price-display">
                                                <del>৳<?= $regularPrice ?></del>
                                                <strong>৳ <?= number_format($productPrice, 2) ?></strong>
                                            </p>
                                        </div>
                                        <div class="quantity-control">
                                            <button type="button" class="quantity-btn" onclick="updateVariantQuantity(<?= $index ?>, -1); event.stopPropagation();">−</button>
                                            <input type="text" class="quantity-input" value="1" readonly data-variant="<?= $index ?>">
                                            <button type="button" class="quantity-btn" onclick="updateVariantQuantity(<?= $index ?>, 1); event.stopPropagation();">+</button>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                    <input type="hidden" name="product_size" id="selectedSize" value="<?= $sizes[0] ?>">
                                    <input type="hidden" name="quantity" id="selectedQuantity" value="1">
                                <?php else: ?>
                                    <div class="product-option selected" data-index="0" onclick="selectVariant(0)">
                                        <input type="radio" name="product_variant" value="standard" id="variant_0" checked>
                                        <div class="product-option-content">
                                            <h4><?= $productTitle ?></h4>
                                            <p class="price-display">
                                                <del>৳<?= $regularPrice ?></del>
                                                <strong>৳ <?= number_format($productPrice, 2) ?></strong>
                                            </p>
                                        </div>
                                        <div class="quantity-control">
                                            <button type="button" class="quantity-btn" onclick="updateVariantQuantity(0, -1); event.stopPropagation();">−</button>
                                            <input type="text" class="quantity-input" value="1" readonly data-variant="0">
                                            <button type="button" class="quantity-btn" onclick="updateVariantQuantity(0, 1); event.stopPropagation();">+</button>
                                        </div>
                                    </div>
                                    <input type="hidden" name="product_size" value="standard">
                                    <input type="hidden" name="quantity" id="selectedQuantity" value="1">
                                <?php endif; ?>
                            </div>
                        </div>

                        <div>
                            <div class="order-summary">
                                <h3>আপনার অর্ডার</h3>

                                <div class="summary-row">
                                    <span><strong>PRODUCT</strong></span>
                                    <span><strong>Subtotal</strong></span>
                                </div>

                                <div class="summary-row">
                                    <span id="productName"><?= $productTitle ?> × 1</span>
                                    <span id="productPrice">৳ <?= number_format($productPrice, 2) ?></span>
                                </div>

                                <div class="summary-row">
                                    <span>Subtotal</span>
                                    <span id="subtotal">৳ <?= number_format($productPrice, 2) ?></span>
                                </div>

                                <div class="summary-row">
                                    <span>Shipping</span>
                                    <div>
                                        <div style="margin-bottom: 8px;">
                                            <input type="radio" name="shipping" value="150" id="shipping-outside" checked onchange="updateTotal()">
                                            <label for="shipping-outside">ঢাকার বাইরে: ৳ 150.00</label>
                                        </div>
                                        <div>
                                            <input type="radio" name="shipping" value="80" id="shipping-inside" onchange="updateTotal()">
                                            <label for="shipping-inside">ঢাকার ভিতরে: ৳ 80.00</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="summary-row summary-total">
                                    <span>Total</span>
                                    <span id="total">৳ <?= number_format($productPrice + 130, 2) ?></span>
                                </div>
                            </div>

                            <div style="margin-top: 20px; padding: 15px; background: #fff3cd; border-radius: 5px; border-left: 4px solid #ffc107;">
                                <p style="margin: 0; font-size: 0.9rem; color: #856404;">
                                    <strong> ক্যাশ অন ডেলিভারি</strong><br>
                                    পণ্য হাতে পেয়ে টাকা পরিশোধ করুন।
                                </p>
                            </div>

                            <p style="margin-top: 15px; font-size: 0.85rem; color: #666; line-height: 1.5;">
                                আপনার ব্যক্তিগত তথ্য আপনার অর্ডার প্রসেস করতে, এই ওয়েবসাইট জুড়ে আপনার অভিজ্ঞতা সমর্থন করতে এবং আমাদের গোপনীয়তা নীতিতে বর্ণিত অন্যান্য উদ্দেশ্যে ব্যবহার করা হবে।
                            </p>

                            <button type="submit" class="submit-btn">
                                🔒 অর্ডার করুন <span id="orderTotal">৳ <?= number_format($productPrice + 130, 2) ?></span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <h2 class="footer-contact">আরো জানতে কল করুন!</h2>
            <p style="font-size: 1.6rem; font-weight: bold; margin: 0px 0;"><?= $websitePhone ?></p>
            <p style="margin-top: 0px; opacity: 0.8;">© 2025 <?= $websiteName ?>. সর্বস্বত্ব সংরক্ষিত</p>
        </div>
    </footer>

    <!-- Sticky CTA -->
    <div class="sticky-cta" id="stickyCta">
        <button class="cta-button" onclick="document.getElementById('order').scrollIntoView({behavior: 'smooth'})">
            🛒 অর্ডার করতে চাই
        </button>
    </div>

    <!-- Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script>
        const productPrice = <?= $productPrice ?>;
        const productTitle = '<?= $productTitle ?>';
        let selectedVariantIndex = 0;
        let variantQuantities = [1<?php if (!empty($sizes)): ?><?php for($i = 1; $i < count($sizes); $i++): ?>, 1<?php endfor; ?><?php endif; ?>];
        const variantSizes = [<?php if (!empty($sizes)): ?><?php foreach($sizes as $i => $s): ?>'<?= $s ?>'<?= $i < count($sizes) - 1 ? ',' : '' ?><?php endforeach; ?><?php else: ?>'standard'<?php endif; ?>];

        // Initialize Swiper
        <?php if (!empty($reviews)): ?>
        var swiper = new Swiper(".mySwiper", {
            slidesPerView: 1,
            spaceBetween: 20,
            loop: true,
            autoplay: {
                delay: 4000,
                disableOnInteraction: false,
            },
            pagination: {
                el: ".swiper-pagination",
                clickable: true,
            },
            navigation: {
                nextEl: ".swiper-button-next",
                prevEl: ".swiper-button-prev",
            },
            breakpoints: {
                640: {
                    slidesPerView: 1,
                },
                768: {
                    slidesPerView: 2,
                },
                1024: {
                    slidesPerView: 3,
                },
            },
        });
        <?php endif; ?>

        // Countdown Timer
        function startCountdown() {
            let days = 12;
            let hours = 46;
            let minutes = 57;

            function updateCountdown() {
                minutes--;
                if (minutes < 0) {
                    minutes = 59;
                    hours--;
                    if (hours < 0) {
                        hours = 23;
                        days--;
                        if (days < 0) {
                            days = 0;
                            hours = 0;
                            minutes = 0;
                        }
                    }
                }

                document.getElementById('days').textContent = days.toString().padStart(2, '0');
                document.getElementById('hours').textContent = hours.toString().padStart(2, '0');
                document.getElementById('minutes').textContent = minutes.toString().padStart(2, '0');
            }

            updateCountdown();
            setInterval(updateCountdown, 60000);
        }

        // Select Variant
        function selectVariant(index) {
            selectedVariantIndex = index;
            
            // Update card selection
            document.querySelectorAll('.product-option').forEach((card, i) => {
                if (i === index) {
                    card.classList.add('selected');
                    card.querySelector('input[type="radio"]').checked = true;
                } else {
                    card.classList.remove('selected');
                }
            });

            // Update hidden inputs
            document.getElementById('selectedSize').value = variantSizes[index];
            document.getElementById('selectedQuantity').value = variantQuantities[index];

            updateTotal();
        }

        // Update Variant Quantity
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

        // Update Order Summary
        function updateTotal() {
            const quantity = variantQuantities[selectedVariantIndex];
            const subtotal = productPrice * quantity;
            const shippingCost = parseInt(document.querySelector('input[name="shipping"]:checked').value);
            const total = subtotal + shippingCost;

            const variantSize = variantSizes[selectedVariantIndex];
            const productNameText = variantSize === 'standard' 
                ? `${productTitle} × ${quantity}`
                : `${productTitle} - সাইজ: ${variantSize} × ${quantity}`;

            // Update summary
            document.getElementById('productName').textContent = productNameText;
            document.getElementById('productPrice').textContent = '৳ ' + subtotal.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
            document.getElementById('subtotal').textContent = '৳ ' + subtotal.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
            document.getElementById('total').textContent = '৳ ' + total.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
            document.getElementById('orderTotal').textContent = '৳ ' + total.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        // Sticky CTA Button
        window.addEventListener('scroll', function() {
            const stickyCta = document.getElementById('stickyCta');
            const orderSection = document.getElementById('order');
            const orderPosition = orderSection.getBoundingClientRect().top;

            if (window.scrollY > 600 && orderPosition > window.innerHeight) {
                stickyCta.classList.add('show');
            } else {
                stickyCta.classList.remove('show');
            }
        });

        // Form Submission
        document.getElementById('orderForm').addEventListener('submit', function(e) {
            const phone = document.querySelector('input[name="phone"]').value;
            if (phone.length !== 11 || !/^\d+$/.test(phone)) {
                e.preventDefault();
                alert('দয়া করে সঠিক ১১ ডিজিটের মোবাইল নম্বর দিন');
                return false;
            }
        });

        // Initialize
        startCountdown();
        updateTotal();
    </script>
</body>
</html>