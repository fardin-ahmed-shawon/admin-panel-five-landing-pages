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
$regularPrice = $productData['product_regular_price'];
$discount = round((($regularPrice - $productPrice) / $regularPrice) * 100);

// Fetch available sizes for this product
$sizes_sql = "SELECT size FROM product_size_list WHERE product_id = $product_id";
$sizes_result = mysqli_query($conn, $sizes_sql);
$sizes = [];
while ($size_row = mysqli_fetch_assoc($sizes_result)) {
    $sizes[] = $size_row['size'];
}

// Fetch gallery images for slider
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
    $email = $_POST['email'] ?? '';
    $address = $_POST['address'];
    $district = $_POST['district'];
    $order_data = json_decode($_POST['order_data'], true);
    $city = 'Free Shipping';
    $payment_method = 'Cash On Delivery';
    $user_id = 0;

    function generateInvoiceNo() {
        $timestamp = microtime(true) * 10000;
        $uniqueString = 'INV-' . strtoupper(base_convert($timestamp, 10, 36));
        return $uniqueString;
    }
    $invoice_no = generateInvoiceNo();
    $_SESSION['temporary_invoice_no'] = $invoice_no;

    if (!empty($order_data)) {
        foreach ($order_data as $item) {
            $product_name = $item['product'];
            $size = $item['size'];
            $quantity = $item['quantity'];
            $price = $item['price'];
            
            $product_title_full = $product_name;
            if (!empty($size) && $size !== 'standard') {
                $product_title_full .= " (Size: $size)";
            }
            
            $total_price = $price * $quantity;

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
            $stmt->execute();
        }
        
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
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Noto Sans Bengali', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            overflow-x: hidden;
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

        .header-banner {
            background: #FF0000;
            color: white;
            text-align: center;
            padding: 25px 20px;
            font-size: 24px;
            font-weight: bold;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            animation: slideDown 0.6s ease-out;
        }

        @keyframes slideDown {
            from {
                transform: translateY(-100%);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .hero-section {
            position: relative;
            min-height: 600px;
            background: white;
            overflow: hidden;
        }

        .hero-slider {
            position: relative;
            width: 100%;
            height: 600px;
            overflow: hidden;
        }

        .hero-slides {
            display: flex;
            transition: transform 0.5s ease-in-out;
            height: 100%;
        }

        .hero-slide {
            min-width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .hero-slide-content {
            max-width: 1400px;
            margin: 0 auto;
            padding: 60px 40px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
            align-items: center;
            width: 100%;
        }

        .hero-text h1 {
            font-size: 48px;
            margin-bottom: 20px;
            line-height: 1.2;
            color: #000;
            animation: fadeInUp 0.8s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .hero-text p {
            font-size: 20px;
            margin-bottom: 30px;
            color: #555;
        }

        .hero-image {
            position: relative;
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }

        .hero-image img {
            width: 100%;
            height: auto;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }

        .hero-btn {
            background: #FF0000;
            color: white;
            border: none;
            padding: 18px 45px;
            font-size: 20px;
            font-weight: bold;
            border-radius: 50px;
            cursor: pointer;
            box-shadow: 0 8px 25px rgba(255,0,0,0.3);
            transition: all 0.3s ease;
        }

        .hero-btn:hover {
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 12px 35px rgba(255,0,0,0.4);
            background: #CC0000;
        }

        .slider-controls {
            position: absolute;
            bottom: 20px;
            left: 0;
            right: 0;
            text-align: center;
            z-index: 10;
        }

        .slider-dots {
            display: inline-block;
            background: rgba(255, 255, 255, 0.7);
            padding: 10px 15px;
            border-radius: 20px;
        }

        .slider-dot {
            display: inline-block;
            width: 12px;
            height: 12px;
            background: rgba(0, 0, 0, 0.3);
            border-radius: 50%;
            margin: 0 5px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .slider-dot.active {
            background: #FF0000;
        }

        .slider-nav {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(255, 255, 255, 0.7);
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 10;
            transition: all 0.3s ease;
            font-size: 24px;
            font-weight: bold;
            color: #FF0000;
        }

        .slider-nav:hover {
            background: rgba(255, 255, 255, 0.9);
            transform: translateY(-50%) scale(1.1);
        }

        .slider-prev {
            left: 20px;
        }

        .slider-next {
            right: 20px;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }

        .price-section {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            padding: 50px 20px;
            text-align: center;
            margin: 40px 0;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        }

        .discount-badge {
            display: inline-block;
            background: #e74c3c;
            color: white;
            padding: 8px 20px;
            border-radius: 25px;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .price-original {
            text-decoration: line-through;
            color: #999;
            font-size: 24px;
            margin-bottom: 10px;
        }

        .price-current {
            color: #e74c3c;
            font-size: 48px;
            font-weight: bold;
            margin: 15px 0;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
        }

        .free-delivery {
            color: #27ae60;
            font-size: 20px;
            margin: 20px 0;
            font-weight: 600;
        }

        .order-button {
            background: #FF0000;
            color: white;
            border: none;
            padding: 18px 50px;
            font-size: 20px;
            border-radius: 50px;
            cursor: pointer;
            margin: 25px 0;
            box-shadow: 0 8px 25px rgba(255, 0, 0, 0.3);
            transition: all 0.3s ease;
        }

        .order-button:hover {
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 12px 35px rgba(255, 0, 0, 0.4);
            background: #CC0000;
        }

        .details-section {
            margin: 50px 0;
        }

        .details-header {
            background: #FF0000;
            color: white;
            padding: 20px;
            font-size: 24px;
            font-weight: bold;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        .details-list {
            list-style: none;
            padding: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }

        .details-list li {
            padding: 15px 0;
            border-bottom: 1px solid #eee;
            position: relative;
            padding-left: 35px;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .details-list li:hover {
            padding-left: 40px;
            background: #f9f9f9;
        }

        .details-list li:before {
            content: "✓";
            color: #27ae60;
            position: absolute;
            left: 0;
            font-weight: bold;
            font-size: 20px;
        }

        .testimonials-section {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            padding: 60px 20px;
            margin: 60px auto;
            border-radius: 20px;
        }

        .testimonials-section h2 {
            text-align: center;
            margin-bottom: 40px;
            font-size: 32px;
            color: #333;
        }

        .testimonials-slider {
            display: flex;
            justify-content: center;
            gap: 25px;
            overflow-x: auto;
            padding: 20px 0;
            scroll-behavior: smooth;
        }

        .testimonial-card {
            background-color: white;
            padding: 25px;
            border-radius: 15px;
            min-width: 320px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }

        .testimonial-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.15);
        }

        .testimonial-image {
            width: 100%;
            border-radius: 12px;
            margin-bottom: 15px;
        }

        .order-form-section {
            background: white;
            padding: 50px;
            margin: 60px auto;
            max-width: 1000px;
            border-radius: 20px;
            box-shadow: 0 10px 50px rgba(0,0,0,0.1);
        }

        .form-title {
            text-align: center;
            font-size: 32px;
            margin-bottom: 15px;
            color: #FF0000;
            font-weight: bold;
        }

        .form-subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 40px;
            font-size: 16px;
        }

        .product-selection {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }

        .product-card {
            border: 2px solid #e0e0e0;
            padding: 20px;
            border-radius: 10px;
            transition: all 0.3s ease;
            background: white;
        }

        .product-card:hover {
            border-color: #FF0000;
            box-shadow: 0 8px 25px rgba(255, 0, 0, 0.2);
            transform: translateY(-5px);
        }

        .product-card.selected {
            border-color: #FF0000;
            background: linear-gradient(135deg, #fff5f5 0%, #ffe8e8 100%);
            box-shadow: 0 8px 25px rgba(255, 0, 0, 0.3);
        }

        .product-header {
            display: flex;
            align-items: flex-start;
            gap: 15px;
            margin-bottom: 15px;
        }

        .product-checkbox {
            margin-right: 0;
            margin-top: 5px;
            width: 20px;
            height: 20px;
            cursor: pointer;
            accent-color: #FF0000;
            flex-shrink: 0;
        }

        .product-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            flex-shrink: 0;
        }

        .product-title {
            flex: 1;
            font-size: 16px;
            font-weight: 600;
            margin: 0;
            color: #333;
            line-height: 1.4;
            cursor: pointer;
        }

        .product-price {
            font-weight: bold;
            color: #FF0000;
            font-size: 18px;
        }

        .quantity-control {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 15px 0 0 0;
            padding-left: 95px;
        }

        .quantity-btn {
            background-color: #f0f0f0;
            color: #333;
            border: 2px solid #ddd;
            padding: 8px 16px;
            cursor: pointer;
            border-radius: 8px;
            font-size: 18px;
            transition: all 0.3s ease;
            font-weight: bold;
            min-width: 40px;
        }

        .quantity-btn:hover {
            background-color: #FF0000;
            color: white;
            border-color: #FF0000;
        }

        .quantity-input {
            width: 60px;
            text-align: center;
            border: 2px solid #ddd;
            padding: 8px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            margin-bottom: 10px;
            font-weight: 600;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 15px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #FF0000;
            box-shadow: 0 0 0 3px rgba(255, 0, 0, 0.1);
        }

        .order-summary {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            padding: 30px;
            margin: 30px 0;
            border-radius: 15px;
        }

        .order-summary-item {
            display: flex;
            justify-content: space-between;
            padding: 15px 0;
            border-bottom: 1px solid rgba(255,255,255,0.5);
        }

        .order-total {
            font-size: 24px;
            font-weight: bold;
            color: #FF0000;
        }

        .submit-button {
            width: 100%;
            background: #28a745;
            color: white;
            border: none;
            padding: 20px;
            font-size: 22px;
            font-weight: bold;
            border-radius: 10px;
            cursor: pointer;
            box-shadow: 0 8px 25px rgba(40, 167, 69, 0.3);
            transition: all 0.3s ease;
        }

        .submit-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(40, 167, 69, 0.4);
            background: #218838;
        }

        .contact-box {
            border: 3px solid #FF0000;
            padding: 30px;
            margin: 30px auto;
            max-width: 700px;
            text-align: center;
            border-radius: 15px;
            background: white;
        }

        .contact-number {
            background: #FF0000;
            color: white;
            padding: 18px 40px;
            font-size: 28px;
            font-weight: bold;
            display: inline-block;
            margin: 20px 0;
            border-radius: 10px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .header-banner {
                font-size: 16px;
                padding: 20px 15px;
            }

            .hero-slide-content {
                grid-template-columns: 1fr;
                gap: 40px;
                padding: 40px 20px;
            }

            .hero-text h1 {
                font-size: 28px;
            }

            .product-selection {
                grid-template-columns: 1fr;
            }

            .product-card {
                padding: 15px;
            }

            .product-header {
                flex-wrap: wrap;
            }

            .quantity-control {
                padding-left: 0;
                justify-content: center;
            }

            .size-options {
                grid-template-columns: 1fr;
            }

            .order-form-section {
                padding: 30px 20px;
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

    <div class="header-banner">
        <?= $home_title ?>
    </div>

    <!-- Hero Slider -->
    <div class="hero-section">
        <div class="hero-slider">
            <div class="hero-slides">
                <?php 
                // Use gallery images or fall back to home image
                $slider_images = !empty($gallery_images) ? $gallery_images : [$home_img];
                foreach($slider_images as $index => $image): 
                    $img_path = strpos($image, 'Admin/') === 0 ? '../' . $image : '../Admin/' . $image;
                ?>
                <div class="hero-slide">
                    <div class="hero-slide-content">
                        <div class="hero-text">
                            <h1><?= $productTitle ?></h1>
                            <p><?= $home_des ?></p>
                            <button class="hero-btn" onclick="scrollToOrder()">এখনই অর্ডার করুন</button>
                        </div>
                        <div class="hero-image">
                            <img src="<?= $img_path ?>" alt="<?= $productTitle ?>">
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <?php if (count($slider_images) > 1): ?>
            <div class="slider-nav slider-prev">❮</div>
            <div class="slider-nav slider-next">❯</div>
            
            <div class="slider-controls">
                <div class="slider-dots">
                    <?php for($i = 0; $i < count($slider_images); $i++): ?>
                    <span class="slider-dot <?= $i === 0 ? 'active' : '' ?>" data-index="<?= $i ?>"></span>
                    <?php endfor; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="container">
        <div class="price-section">
            <div class="discount-badge"><?= $discount ?>% ছাড়</div>
            <p class="price-original">রেগুলার মূল্য ৳<?= $regularPrice ?></p>
            <p class="price-current">অফার মূল্য ৳<?= $productPrice ?></p>
            <p class="free-delivery">✓ ফ্রি হোম ডেলিভারি সারা বাংলাদেশ</p>
            <button class="order-button" onclick="scrollToOrder()">অর্ডার করুন 🛒</button>
        </div>

        <?php if (!empty($features)): ?>
        <div class="details-section">
            <div class="details-header">প্রোডাক্টের বিবরণ</div>
            <ul class="details-list">
                <?php foreach($features as $feature): ?>
                <li><strong><?= $feature['feature_title'] ?>:</strong> <?= $feature['feature_description'] ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>
    </div>

    <?php
    // Fetch reviews
    $reviews_sql = "SELECT review_image FROM reviews WHERE product_id = $product_id";
    $reviews_result = mysqli_query($conn, $reviews_sql);
    if (mysqli_num_rows($reviews_result) > 0):
    ?>
    <div class="testimonials-section">
        <h2>গ্রাহকদের মতামত</h2>
        <div class="testimonials-slider">
            <?php while($review = mysqli_fetch_assoc($reviews_result)): ?>
            <div class="testimonial-card">
                <img src="../Admin/<?= $review['review_image'] ?>" alt="Review" class="testimonial-image">
            </div>
            <?php endwhile; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Order Form -->
    <div class="order-form-section" id="order">
        <h2 class="form-title">অর্ডার ফর্ম</h2>
        <p class="form-subtitle">নিচের ফর্মটি পূরণ করে অর্ডার সম্পন্ন করুন</p>
        
        <form method="POST" id="orderForm">
            <!-- Product Selection with Sizes -->
            <div class="product-selection">
                <?php
                // Fetch all available sizes for this product
                $sizes_sql = "SELECT size FROM product_size_list WHERE product_id = $product_id";
                $sizes_result = mysqli_query($conn, $sizes_sql);
                $available_sizes = [];
                while ($size_row = mysqli_fetch_assoc($sizes_result)) {
                    $available_sizes[] = $size_row['size'];
                }

                // If sizes exist, create product variants
                if (!empty($available_sizes)) {
                    foreach ($available_sizes as $index => $size) {
                        $variant_id = "product_" . $index;
                        ?>
                        <div class="product-card" data-price="<?= $productPrice ?>">
                            <div class="product-header">
                                <input type="checkbox" class="product-checkbox" id="<?= $variant_id ?>" name="product_variant[]" value="<?= $size ?>" data-product-name="<?= $productTitle ?>" data-size="<?= $size ?>">
                                <img src="<?= $productImg ?>" alt="<?= $productTitle ?>" class="product-image">
                                <label for="<?= $variant_id ?>" class="product-title">
                                    <?= $productTitle ?> - সাইজ: <?= $size ?>
                                </label>
                                <span class="product-price">৳ <?= $productPrice ?></span>
                            </div>
                            <div class="quantity-control">
                                <button type="button" class="quantity-btn minus">-</button>
                                <input type="number" class="quantity-input" value="1" min="1" data-variant="<?= $variant_id ?>">
                                <button type="button" class="quantity-btn plus">+</button>
                            </div>
                        </div>
                        <?php
                    }
                } else {
                    // If no sizes, show single product
                    ?>
                    <div class="product-card" data-price="<?= $productPrice ?>">
                        <div class="product-header">
                            <input type="checkbox" class="product-checkbox" id="product_single" name="product_variant[]" value="standard" data-product-name="<?= $productTitle ?>" data-size="">
                            <img src="<?= $productImg ?>" alt="<?= $productTitle ?>" class="product-image">
                            <label for="product_single" class="product-title">
                                <?= $productTitle ?>
                            </label>
                            <span class="product-price">৳ <?= $productPrice ?></span>
                        </div>
                        <div class="quantity-control">
                            <button type="button" class="quantity-btn minus">-</button>
                            <input type="number" class="quantity-input" value="1" min="1" data-variant="product_single">
                            <button type="button" class="quantity-btn plus">+</button>
                        </div>
                    </div>
                    <?php
                }
                ?>
            </div>

            <div class="form-group">
                <label for="name">পুরো নাম *</label>
                <input type="text" id="name" name="name" placeholder="আপনার পূর্ণ নাম লিখুন" required>
            </div>

            <div class="form-group">
                <label for="phone">মোবাইল নম্বর *</label>
                <input type="tel" id="phone" name="phone" placeholder="আপনার ১১ ডিজিটের নম্বর লিখুন" required pattern="[0-9]{11}">
            </div>

            <div class="form-group">
                <label for="address">ঠিকানা *</label>
                <textarea id="address" name="address" rows="3" placeholder="বাসা নাম্বর, রোড, থানা/ডাকঘর" required></textarea>
            </div>

            <div class="form-group">
                <label for="district">জেলা *</label>
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

            <div class="order-summary">
                <h3>অর্ডার সারাংশ</h3>
                <div class="order-summary-item">
                    <span>পণ্যের মূল্য:</span>
                    <span id="productTotal">৳ 0</span>
                </div>
                <div class="order-summary-item">
                    <span>ডেলিভারি চার্জ:</span>
                    <span>৳ 0 (ফ্রি)</span>
                </div>
                <div class="order-summary-item order-total">
                    <span>মোট:</span>
                    <span id="grandTotal">৳ 0</span>
                </div>
            </div>

            <input type="hidden" name="order_data" id="orderData">
            <button type="submit" class="submit-button">অর্ডার নিশ্চিত করুন</button>
        </form>
    </div>

    <div class="contact-box">
        <h2>সরাসরি অর্ডার করতে কল করুন</h2>
        <a href="tel:<?= $websitePhone ?>" class="contact-number"><?= $websitePhone ?></a>
        <p>সকাল ৯টা থেকে রাত ১০টা পর্যন্ত</p>
    </div>

    <script>
        const productPrice = <?= $productPrice ?>;

        // Hero Slider
        let currentSlide = 0;
        const slides = document.querySelectorAll('.hero-slide');
        const dots = document.querySelectorAll('.slider-dot');
        const totalSlides = slides.length;
        const slidesContainer = document.querySelector('.hero-slides');
        
        if (totalSlides > 1) {
            function updateSlider() {
                slidesContainer.style.transform = `translateX(-${currentSlide * 100}%)`;
                dots.forEach((dot, index) => {
                    dot.classList.toggle('active', index === currentSlide);
                });
            }
            
            function nextSlide() {
                currentSlide = (currentSlide + 1) % totalSlides;
                updateSlider();
            }
            
            function prevSlide() {
                currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
                updateSlider();
            }
            
            function goToSlide(index) {
                currentSlide = index;
                updateSlider();
            }
            
            document.querySelector('.slider-next')?.addEventListener('click', nextSlide);
            document.querySelector('.slider-prev')?.addEventListener('click', prevSlide);
            
            dots.forEach(dot => {
                dot.addEventListener('click', function() {
                    goToSlide(parseInt(this.getAttribute('data-index')));
                });
            });
            
            // Auto slide
            setInterval(nextSlide, 5000);
        }

        // Product Selection
        document.querySelectorAll('.product-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const card = this.closest('.product-card');
                if (this.checked) {
                    card.classList.add('selected');
                } else {
                    card.classList.remove('selected');
                }
                updateOrderSummary();
            });
        });

        // Quantity Controls
        document.querySelectorAll('.quantity-btn').forEach(button => {
            button.addEventListener('click', function() {
                const input = this.parentElement.querySelector('.quantity-input');
                let value = parseInt(input.value);
                
                if (this.classList.contains('plus')) {
                    value++;
                } else if (this.classList.contains('minus') && value > 1) {
                    value--;
                }
                
                input.value = value;
                updateOrderSummary();
            });
        });

        // Update Order Summary
        function updateOrderSummary() {
            let total = 0;
            
            document.querySelectorAll('.product-checkbox:checked').forEach(checkbox => {
                const card = checkbox.closest('.product-card');
                const quantity = parseInt(card.querySelector('.quantity-input').value);
                const price = parseInt(card.getAttribute('data-price'));
                
                total += price * quantity;
            });
            
            document.getElementById('productTotal').textContent = '৳ ' + total;
            document.getElementById('grandTotal').textContent = '৳ ' + total;
        }

        // Scroll to Order
        function scrollToOrder() {
            document.getElementById('order').scrollIntoView({ 
                behavior: 'smooth',
                block: 'start'
            });
        }

        // Form Submission
        document.getElementById('orderForm').addEventListener('submit', function(e) {
            e.preventDefault();

            // Check if at least one product is selected
            const selectedProducts = document.querySelectorAll('.product-checkbox:checked');
            if (selectedProducts.length === 0) {
                alert('দয়া করে অন্তত একটি পণ্য নির্বাচন করুন');
                return;
            }

            const name = document.getElementById('name').value.trim();
            const phone = document.getElementById('phone').value.trim();
            const address = document.getElementById('address').value.trim();
            const district = document.getElementById('district').value;

            if (!name || !phone || !address || !district) {
                alert('দয়া করে সকল তথ্য পূরণ করুন');
                return;
            }

            if (phone.length !== 11 || !/^\d+$/.test(phone)) {
                alert('দয়া করে সঠিক ১১ ডিজিটের মোবাইল নম্বর দিন');
                return;
            }

            // Prepare order data
            const orderData = [];
            selectedProducts.forEach(checkbox => {
                const card = checkbox.closest('.product-card');
                const productName = checkbox.getAttribute('data-product-name');
                const size = checkbox.getAttribute('data-size');
                const quantity = parseInt(card.querySelector('.quantity-input').value);
                const price = parseInt(card.getAttribute('data-price'));
                
                orderData.push({
                    product: productName,
                    size: size,
                    quantity: quantity,
                    price: price
                });
            });

            // Set order data in hidden field
            document.getElementById('orderData').value = JSON.stringify(orderData);

            // Submit form
            this.submit();
        });

        // Initialize
        updateOrderSummary();
    </script>
</body>
</html>