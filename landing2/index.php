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
        $websiteAddress = $data['address'];
        $websitePhone = $data['phone'];
        $accNum = $data['acc_num'];
        $websiteEmail = $data['email'];
        $websiteFbLink = $data['fb_link'];
        $websiteInstaLink = $data['insta_link'];
        $websiteTwitterLink = $data['twitter_link'];
        $websiteYtLink = $data['yt_link'];
        $inside_location = $data['inside_location'];
        $inside_delivery_charge = $data['inside_delivery_charge'];
        $outside_delivery_charge = $data['outside_delivery_charge'];
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
        $feature_img = $data['feature_img'];
        $youtube_url = $data['youtube_url'] ?? '';
    }
}

// Fetch Product Info
$sql = "SELECT * FROM product_info WHERE product_id = $product_id LIMIT 1";
$result = mysqli_query($conn, $sql);
$productData = mysqli_fetch_assoc($result);
$productTitle = $productData['product_title'];
$productPrice = $productData['product_price'];
$productImg = $productData['product_img1'];
$productSKU = 'PRD-' . $product_id;

// Fetch available sizes for this product
$sizes_sql = "SELECT size FROM product_size_list WHERE product_id = $product_id";
$sizes_result = mysqli_query($conn, $sizes_sql);
$sizes = [];
while ($size_row = mysqli_fetch_assoc($sizes_result)) {
    $sizes[] = $size_row['size'];
}

// Process Order
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullName = $_POST['name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'] ?? '';
    $address = $_POST['address'];
    $district = $_POST['district'];
    $size = $_POST['size'] ?? '';
    $quantity = $_POST['quantity'] ?? 1;
    $city = 'Free Shipping';
    $payment_method = 'Cash On Delivery';
    $user_id = 0;

    function generateInvoiceNo()
    {
        $timestamp = microtime(true) * 10000;
        $uniqueString = 'INV-' . strtoupper(base_convert($timestamp, 10, 36));
        return $uniqueString;
    }
    $invoice_no = generateInvoiceNo();
    $_SESSION['temporary_invoice_no'] = $invoice_no;

    $product_title_full = $productTitle;
    if (!empty($size)) {
        $product_title_full .= " (Size: $size)";
    }

    $total_price = $productPrice * $quantity;

    $sql = "INSERT INTO order_info (user_id, user_full_name, user_phone, user_email, user_address, city_address, invoice_no, product_id, product_title, product_quantity, product_size, total_price, payment_method)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $full_address = $address . ", " . $district;
    $stmt->bind_param(
        "issssssisisss",
        $user_id,
        $fullName,
        $phone,
        $email,
        $full_address,
        $city,
        $invoice_no,
        $product_id,
        $product_title_full,
        $quantity,
        $size,
        $total_price,
        $payment_method
    );

    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        echo "<script>window.location.href = 'index.php?slug=$product_slug&or_msg=successful';</script>";
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="bn">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $websiteName; ?> - <?php echo $productTitle; ?></title>
    <link href="../Admin/<?= $logo ?>" rel="icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Anek+Bangla:wght@100..800&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: "Anek Bangla", sans-serif;
            line-height: 1.6;
            color: #333;
            background: #fff;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 30px;
        }

        /* Success Message */
        #success-box {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: #D1E7DD;
            color: #0A3622;
            padding: 20px;
            text-align: center;
            font-size: 20px;
            font-weight: 500;
            z-index: 9999;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        /* Hero Banner */
        .hero-banner {
            background: linear-gradient(135deg, #6d4c41 0%, #5d4037 50%, #4e342e 100%);
            color: #fff;
            padding: 35px 20px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .hero-banner::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 300px;
            height: 300px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-20px);
            }
        }

        .hero-banner h1 {
            font-size: 2.5rem;
            font-weight: 700;
            line-height: 1.4;
            margin-bottom: 10px;
            position: relative;
            z-index: 1;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }

        .hero-banner p {
            font-size: 1.3rem;
            opacity: 0.95;
            position: relative;
            z-index: 1;
        }

        /* Quality Badge */
        .quality-badge {
            background: linear-gradient(to bottom, #fafafa, #f5f5f5);
            padding: 15px 20px;
            text-align: center;
            border-bottom: 2px solid #e0e0e0;
        }

        .quality-badge p {
            font-size: 1.1rem;
            color: #333;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .stars {
            color: #ffa726;
            font-size: 1.2rem;
        }

        /* CTA Button */
        .cta-section {
            text-align: center;
            padding: 30px 20px;
            background: linear-gradient(to bottom, #fff, #fafafa);
        }

        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #66bb6a 0%, #57a65a 100%);
            color: #fff;
            padding: 16px 50px;
            text-decoration: none;
            border-radius: 50px;
            font-size: 1.2rem;
            font-weight: 700;
            transition: all 0.4s ease;
            box-shadow: 0 4px 15px rgba(102, 187, 106, 0.4);
        }

        .cta-button:hover {
            background: linear-gradient(135deg, #57a65a 0%, #4caf50 100%);
            transform: translateY(-3px);
            box-shadow: 0 6px 25px rgba(102, 187, 106, 0.5);
        }

        /* Notice Box */
        .notice-box {
            max-width: 700px;
            margin: 25px auto;
            padding: 20px 30px;
            border: 2px solid #66bb6a;
            border-radius: 12px;
            text-align: center;
            background: linear-gradient(135deg, #ffffff 0%, #f1f8e9 100%);
            box-shadow: 0 4px 12px rgba(102, 187, 106, 0.15);
        }

        .notice-box p {
            color: #2e7d32;
            font-size: 1.05rem;
            font-weight: 600;
            line-height: 1.7;
        }

        /* Product Showcase */
        .product-showcase {
            padding: 0px 20px 60px 20px;
            background: linear-gradient(to bottom, #f5f5f5, #fafafa);
            text-align: center;
        }

        .product-image-wrapper {
            max-width: 700px;
            margin: 0 auto;
            background: #fff;
            padding: 50px 40px;
            border-radius: 20px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
            transition: transform 0.3s ease;
        }

        .product-image-wrapper:hover {
            transform: translateY(-5px);
        }

        .product-image-wrapper img {
            width: 100%;
            height: auto;
            display: block;
            border-radius: 10px;
        }

        /* Gallery */
        .gallery {
            background: #f8f9fa;
            padding: 60px 20px;
        }

        .section-title {
            text-align: center;
            font-size: 2.5rem;
            margin-bottom: 40px;
            color: #333;
            font-weight: 700;
        }

        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-top: 30px;
        }

        .gallery-item img {
            width: 100%;
            height: 150px;
            object-fit: contain;
            border-radius: 10px;
            transition: transform 0.3s ease;
            background: #fff;
            padding: 10px;
        }

        .gallery-item img:hover {
            transform: scale(1.05);
        }

        /* Reviews */
        .reviews-section {
            padding: 60px 20px 80px 20px;
            background: linear-gradient(to bottom, #f9f9f9, #f5f5f5);
        }

        .swiper {
            width: 100%;
            padding: 30px 0 80px 0;
        }

        .review-card {
            background: linear-gradient(135deg, #2a2a2a 0%, #1e1e1e 100%);
            color: #fff;
            padding: 50px 35px;
            border-radius: 25px;
            text-align: center;
            height: 550px;
            display: flex;
            flex-direction: column;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
        }

        .review-header {
            font-size: 2rem;
            margin-bottom: 35px;
            color: #b8b8b8;
            font-weight: 500;
        }

        .review-content {
            background: linear-gradient(to bottom, #fff, #fafafa);
            color: #333;
            padding: 35px 28px;
            border-radius: 18px;
            margin-bottom: 28px;
            flex: 1;
            display: flex;
            flex-direction: column;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            overflow-y: auto;
        }

        .reviewer-info {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 18px;
        }

        .reviewer-avatar {
            width: 55px;
            height: 55px;
            border-radius: 50%;
            background: linear-gradient(135deg, #e0e0e0, #d0d0d0);
            flex-shrink: 0;
            border: 2px solid #66bb6a;
        }

        .reviewer-name {
            font-weight: 700;
            font-size: 1.15rem;
            color: #333;
            text-align: left;
        }

        .review-text {
            text-align: left;
            line-height: 1.9;
            font-size: 1.1rem;
            color: #444;
        }

        .review-stars {
            color: #ffa726;
            font-size: 1.9rem;
            margin-top: 25px;
            letter-spacing: 4px;
        }

        .swiper-pagination-bullet {
            background: #666;
            opacity: 0.5;
            width: 12px;
            height: 12px;
        }

        .swiper-pagination-bullet-active {
            background: #66bb6a;
            opacity: 1;
            width: 35px;
            border-radius: 5px;
        }

        /* Order Form */
        .order-form-section {
            padding: 60px 20px 70px;
            background: linear-gradient(to bottom, #fff, #fafafa);
        }

        .order-form-section h2 {
            text-align: center;
            font-size: 1.7rem;
            margin-bottom: 8px;
            color: #333;
            font-weight: 700;
            line-height: 1.6;
        }

        .form-container {
            max-width: 850px;
            margin: 0 auto;
        }

        .product-info {
            display: grid;
            grid-template-columns: 250px 1fr;
            gap: 30px;
            margin-bottom: 35px;
            background: #fff;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        .product-info img {
            width: 100%;
            height: auto;
            border-radius: 12px;
            background: #fafafa;
            padding: 15px;
        }

        .product-details h3 {
            font-size: 1.3rem;
            margin-bottom: 10px;
            font-weight: 700;
        }

        .product-details .sku {
            color: #888;
            font-size: 0.9rem;
            margin-bottom: 15px;
        }

        .product-details .price-display {
            margin-bottom: 18px;
        }

        .offer-price {
            color: #e53935;
            font-size: 2rem;
            font-weight: 800;
            margin-right: 12px;
        }

        .regular-price {
            text-decoration: line-through;
            color: #999;
            font-size: 1.2rem;
        }

        .size-selector {
            margin: 18px 0;
        }

        .size-selector label {
            display: block;
            margin-bottom: 12px;
            font-weight: 700;
            color: #333;
        }

        .size-options {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }

        .size-option {
            padding: 12px 18px;
            border: 2px solid #ddd;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            background: #fff;
            text-align: center;
            font-weight: 500;
        }

        .size-option:hover {
            border-color: #66bb6a;
            background: linear-gradient(135deg, #f1f8e9, #e8f5e9);
            transform: translateY(-2px);
        }

        .size-option.selected {
            border-color: #66bb6a;
            background: linear-gradient(135deg, #e8f5e9, #c8e6c9);
            font-weight: 700;
        }

        .size-status {
            color: #e53935;
            margin-top: 10px;
            font-weight: 700;
            font-size: 0.9rem;
        }

        .quantity-selector {
            display: flex;
            align-items: center;
            gap: 15px;
            margin: 18px 0 0 0;
        }

        .quantity-selector label {
            font-weight: 700;
            font-size: 0.95rem;
        }

        .quantity-controls {
            display: flex;
            align-items: center;
            border: 2px solid #ddd;
            border-radius: 10px;
            overflow: hidden;
        }

        .quantity-selector button {
            width: 42px;
            height: 42px;
            border: none;
            background: #f8f8f8;
            cursor: pointer;
            font-size: 1.4rem;
            color: #333;
            transition: all 0.3s ease;
            font-weight: 700;
        }

        .quantity-selector button:hover {
            background: #66bb6a;
            color: #fff;
        }

        .quantity-selector input {
            width: 55px;
            text-align: center;
            border: none;
            border-left: 2px solid #ddd;
            border-right: 2px solid #ddd;
            padding: 11px 6px;
            font-size: 1.1rem;
            font-weight: 700;
        }

        .form-section {
            margin-top: 35px;
        }

        .form-section h3 {
            font-size: 1.2rem;
            margin-bottom: 22px;
            color: #333;
            font-weight: 700;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 10px;
            font-weight: 600;
            color: #333;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid #ddd;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: #fafafa;
        }

        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: #66bb6a;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(102, 187, 106, 0.1);
        }

        .order-summary {
            background: linear-gradient(135deg, #f9f9f9, #f5f5f5);
            padding: 25px;
            border-radius: 12px;
            margin-top: 30px;
            border: 2px solid #e0e0e0;
        }

        .order-summary h3 {
            font-size: 1.2rem;
            margin-bottom: 18px;
            color: #333;
            font-weight: 700;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 14px 0;
            font-size: 1.05rem;
        }

        .summary-total {
            font-size: 1.5rem;
            font-weight: 800;
            color: #333;
            border-top: 2px solid #ddd;
            padding-top: 14px;
            margin-top: 12px;
        }

        .submit-button {
            width: 100%;
            background: linear-gradient(135deg, #66bb6a 0%, #57a65a 100%);
            color: #fff;
            padding: 18px;
            border: none;
            border-radius: 50px;
            font-size: 1.3rem;
            font-weight: 800;
            cursor: pointer;
            margin-top: 25px;
            transition: all 0.4s ease;
            box-shadow: 0 4px 20px rgba(102, 187, 106, 0.4);
        }

        .submit-button:hover {
            background: linear-gradient(135deg, #57a65a 0%, #4caf50 100%);
            transform: translateY(-3px);
            box-shadow: 0 6px 30px rgba(102, 187, 106, 0.5);
        }

        /* Footer */
        footer {
            background: linear-gradient(135deg, #2c3e50 0%, #1a252f 100%);
            color: #fff;
            padding: 35px 20px;
            text-align: center;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero-banner h1 {
                font-size: 1.8rem;
            }

            .product-info {
                grid-template-columns: 1fr;
            }

            .size-options {
                grid-template-columns: 1fr;
            }

            .gallery-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media(max-width:575px) {
            .cta-button {
                padding: 16px 24px;
            }

            .cta-section {
                padding: 18px 26px;
            }

            .notice-box {
                margin: 0 auto;
            }

            .quality-badge p {
                font-size: 1rem;
            }

            .product-showcase {
                padding: 0px 15px 10px 15px;
            }

            .gallery {
                padding: 20px 20px;
            }

            .section-title {

                font-size: 2rem;
            }
            .gallery-grid {
    grid-template-columns: repeat(1, 1fr);
}
.reviews-section {
    padding: 20px 20px 10px 20px;
}
.reviews-section .section-title {
    font-size: 1.5rem;
}.container {
    padding: 0px 0px;
}
.order-form-section h2 {
    font-size: 1.3rem;
    margin-bottom: 0;
}
.product-details h3 {
    font-size: 1.5rem;
}
.offer-price {
    font-size: 1.6rem;
}
.regular-price {
    font-size: 1.5rem;
}
.order-summary {
    margin-top: 24px;
}
.summary-total {
    font-size: 1.2rem;
}
.submit-button {
    font-size: 1rem;
}
.order-form-section {
    padding: 20px 20px 20px;
}
footer {
    padding: 10px 20px;
}
        }
    </style>
</head>

<body>
    <?php if (isset($_GET['or_msg'])): ?>
        <div id="success-box">অর্ডার সফলভাবে সম্পন্ন হয়েছে!</div>
        <script>
            setTimeout(() => {
                document.getElementById('success-box').style.display = 'none';
            }, 3000);
        </script>
    <?php endif; ?>

    <!-- Hero Banner -->
    <section class="hero-banner">
        <div class="container">
            <h1><?= $home_title ?></h1>
            <p><?= $home_des ?></p>
        </div>
    </section>

    <!-- Quality Badge -->
    <section class="quality-badge">
        <div class="container">
            <p>১০০% সন্তুষ্ট গ্রাহক <span class="stars">⭐⭐⭐⭐⭐</span></p>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <a href="#order" class="cta-button">অর্ডার করতে ক্লিক করুন</a>
        </div>
    </section>

    <!-- Notice Box -->
    <section class="cta-section" style="padding-top: 0;">
        <div class="container">
            <div class="notice-box">
                <p>আমরা নিশ্চিত সম্পূর্ণ কোয়ালিটি প্রোডাক্ট ডেলিভারি করি।</p>
            </div>
        </div>
    </section>

    <!-- Product Showcase -->
    <section class="product-showcase">
        <div class="container">
            <div class="product-image-wrapper">
                <img src="../Admin/<?= $home_img ?>" alt="<?= $productTitle ?>">
            </div>
        </div>
    </section>

    <!-- Gallery -->
    <section class="gallery">
        <div class="container">
            <h2 class="section-title">Product Gallery</h2>
            <div class="gallery-grid">
                <?php
                $sql = "SELECT * FROM gallery WHERE product_id = $product_id";
                $result = mysqli_query($conn, $sql);
                if (mysqli_num_rows($result) > 0) {
                    while ($data = mysqli_fetch_assoc($result)) {
                        echo '<div class="gallery-item">
                                <img src="../Admin/' . $data['gallery_image'] . '" alt="Gallery">
                              </div>';
                    }
                }
                ?>
            </div>
        </div>
    </section>

    <!-- Reviews -->
    <section class="reviews-section">
        <div class="container">
            <h2 class="section-title">সম্মানিত গ্রাহকদের রিভিউ</h2>
            <div class="swiper reviewSwiper">
                <div class="swiper-wrapper">
                    <?php
                    $sql = "SELECT * FROM reviews WHERE product_id = $product_id";
                    $result = mysqli_query($conn, $sql);
                    if (mysqli_num_rows($result) > 0) {
                        while ($data = mysqli_fetch_assoc($result)) {
                            echo '<div class="swiper-slide">
                                    <div class="review-card">
                                        <div class="review-header">From Our<br>Happy Customer</div>
                                        <div class="review-content">
                                            <div class="reviewer-info">
                                                <div class="reviewer-avatar"></div>
                                                <div class="reviewer-name">সন্তুষ্ট গ্রাহক</div>
                                            </div>
                                            <div class="review-text">
                                                <img src="../Admin/' . $data['review_image'] . '" style="width: 100%; border-radius: 10px;" alt="Review">
                                            </div>
                                        </div>
                                        <div class="review-stars">⭐⭐⭐⭐⭐</div>
                                    </div>
                                  </div>';
                        }
                    }
                    ?>
                </div>
                <div class="swiper-pagination"></div>
            </div>
        </div>
    </section>

    <!-- Order Form -->
    <section class="order-form-section" id="order">
        <div class="container">
            <h2>অর্ডার টি সম্পূর্ণ করতে আপনার নাম,<br>মোবাইল নাম্বার ও ঠিকানা নিচে লিখুন</h2>

            <div class="form-container">
                <form id="orderForm" method="POST">
                    <div class="product-info">
                        <img src="<?= $productImg ?>" alt="<?= $productTitle ?>">
                        <div class="product-details">
                            <h3><?= $productTitle ?></h3>
                            <p class="sku">SKU: <?= $productSKU ?></p>
                            <div class="price-display">
                                <span class="offer-price">৳<?= $productPrice ?></span>
                                <span class="regular-price">৳<?= $productPrice * 1.5 ?></span>
                            </div>

                            <?php if (!empty($sizes)): ?>
                                <div class="size-selector">
                                    <label>সাইজ নির্বাচন করুন:</label>
                                    <div class="size-options">
                                        <?php foreach ($sizes as $size): ?>
                                            <div class="size-option" data-size="<?= $size ?>"><?= $size ?></div>
                                        <?php endforeach; ?>
                                    </div>
                                    <p class="size-status" id="sizeStatus">দয়া করে একটি সাইজ নির্বাচন করুন</p>
                                </div>
                                <input type="hidden" name="size" id="selectedSize">
                            <?php endif; ?>

                            <div class="quantity-selector">
                                <label>পরিমাণ</label>
                                <div class="quantity-controls">
                                    <button type="button" id="decreaseQty">−</button>
                                    <input type="text" id="quantity" name="quantity" value="1" readonly>
                                    <button type="button" id="increaseQty">+</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h3>বিলিং ডিটেইল</h3>

                        <div class="form-group">
                            <label for="name">আপনার নাম লিখুন *</label>
                            <input type="text" id="name" name="name" placeholder="আপনার পূর্ণ নাম লিখুন" required>
                        </div>

                        <div class="form-group">
                            <label for="phone">ফোন নাম্বার লিখুন *</label>
                            <input type="tel" id="phone" name="phone" placeholder="আপনার ১১ সংখ্যার নাম্বার লিখুন" required pattern="[0-9]{11}">
                        </div>

                        <div class="form-group">
                            <label for="address">আপনার সম্পূর্ণ ঠিকানা লিখুন *</label>
                            <textarea id="address" name="address" rows="3" placeholder="বাসা নাম্বর, রোড, থানা/ডাকঘর" required></textarea>
                        </div>

                        <div class="form-group">
                            <label for="district">জেলা নির্বাচন *</label>
                            <select id="district" name="district" required>
                                <option value="">জেলা নির্বাচন করুন</option>
                                <option value="ঢাকা">ঢাকা</option>
                                <option value="চট্টগ্রাম">চট্টগ্রাম</option>
                                <option value="রাজশাহী">রাজশাহী</option>
                                <option value="খুলনা">খুলনা</option>
                                <option value="বরিশাল">বরিশাল</option>
                                <option value="সিলেট">সিলেট</option>
                                <option value="রংপুর">রংপুর</option>
                                <option value="ময়মনসিংহ">ময়মনসিংহ</option>
                                <option value="নারায়ণগঞ্জ">নারায়ণগঞ্জ</option>
                                <option value="গাজীপুর">গাজীপুর</option>
                                <option value="কুমিল্লা">কুমিল্লা</option>
                                <option value="যশোর">যশোর</option>
                                <option value="নোয়াখালী">নোয়াখালী</option>
                                <option value="ফরিদপুর">ফরিদপুর</option>
                                <option value="টাঙ্গাইল">টাঙ্গাইল</option>
                                <option value="বগুড়া">বগুড়া</option>
                                <option value="দিনাজপুর">দিনাজপুর</option>
                                <option value="পাবনা">পাবনা</option>
                                <option value="কক্সবাজার">কক্সবাজার</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>ডেলিভারি চার্জ</label>
                            <div style="margin-top: 10px;">
                                <label style="display: flex; align-items: center; font-weight: normal;">
                                    <input type="radio" name="shipping" value="free" checked style="margin-right: 8px; width: auto;">
                                    ফ্রি ডেলিভারি
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="order-summary">
                        <h3>অর্ডার সারাংশ</h3>
                        <div class="summary-row summary-total">
                            <span>মোট</span>
                            <span id="totalPrice">৳<?= $productPrice ?></span>
                        </div>
                    </div>

                    <button type="submit" class="submit-button">
                        অর্ডার টি কনফার্ম করুন ৳<span id="finalTotal"><?= $productPrice ?></span>
                    </button>
                </form>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <p>&copy; <?= date('Y') ?> <?= $websiteName ?>. All Rights Reserved</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script>
        // Initialize Swiper
        const swiper = new Swiper('.reviewSwiper', {
            slidesPerView: 1,
            spaceBetween: 30,
            loop: true,
            autoplay: {
                delay: 5000,
                disableOnInteraction: false,
            },
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            breakpoints: {
                640: {
                    slidesPerView: 1,
                    spaceBetween: 20
                },
                768: {
                    slidesPerView: 2,
                    spaceBetween: 25
                },
                1024: {
                    slidesPerView: 3,
                    spaceBetween: 30
                },
            },
        });

        const productPrice = <?= $productPrice ?>;
        let selectedSize = null;

        // Size Selection
        <?php if (!empty($sizes)): ?>
            const sizeOptions = document.querySelectorAll('.size-option');
            const sizeStatus = document.getElementById('sizeStatus');
            const selectedSizeInput = document.getElementById('selectedSize');

            sizeOptions.forEach(option => {
                option.addEventListener('click', function() {
                    sizeOptions.forEach(opt => opt.classList.remove('selected'));
                    this.classList.add('selected');
                    selectedSize = this.getAttribute('data-size');
                    selectedSizeInput.value = selectedSize;
                    sizeStatus.style.color = '#66bb6a';
                    sizeStatus.textContent = 'সাইজ ' + selectedSize + ' নির্বাচিত';
                });
            });
        <?php else: ?>
            selectedSize = 'N/A'; // No size needed for this product
        <?php endif; ?>

        // Quantity Controls
        const quantityInput = document.getElementById('quantity');
        const decreaseBtn = document.getElementById('decreaseQty');
        const increaseBtn = document.getElementById('increaseQty');

        decreaseBtn.addEventListener('click', function() {
            let qty = parseInt(quantityInput.value);
            if (qty > 1) {
                quantityInput.value = qty - 1;
                updateTotal();
            }
        });

        increaseBtn.addEventListener('click', function() {
            let qty = parseInt(quantityInput.value);
            quantityInput.value = qty + 1;
            updateTotal();
        });

        // Update Total
        function updateTotal() {
            const quantity = parseInt(quantityInput.value);
            const total = productPrice * quantity;

            document.getElementById('totalPrice').textContent = '৳' + total;
            document.getElementById('finalTotal').textContent = total;
        }

        // Form Submission
        document.getElementById('orderForm').addEventListener('submit', function(e) {
            <?php if (!empty($sizes)): ?>
                if (!selectedSize) {
                    e.preventDefault();
                    alert('দয়া করে একটি সাইজ নির্বাচন করুন');
                    window.scrollTo({
                        top: document.querySelector('.size-selector').offsetTop - 100,
                        behavior: 'smooth'
                    });
                    return;
                }
            <?php endif; ?>

            const name = document.getElementById('name').value.trim();
            const phone = document.getElementById('phone').value.trim();
            const address = document.getElementById('address').value.trim();
            const district = document.getElementById('district').value;

            if (!name || !phone || !address || !district) {
                e.preventDefault();
                alert('দয়া করে সকল তথ্য পূরণ করুন');
                return;
            }

            if (phone.length !== 11 || !/^\d+$/.test(phone)) {
                e.preventDefault();
                alert('দয়া করে সঠিক ১১ ডিজিটের মোবাইল নম্বর দিন');
                return;
            }
        });

        // Smooth Scroll
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    const offsetTop = target.offsetTop - 20;
                    window.scrollTo({
                        top: offsetTop,
                        behavior: 'smooth'
                    });
                }
            });
        });

        // Initialize total
        updateTotal();
    </script>
</body>

</html>