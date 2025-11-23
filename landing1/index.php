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
        $youtube_url = $data['yt_link'] ?? '';

        $features_main_title = $data['features_main_title'];
        $why_choose_main_title = $data['why_choose_main_title'] ?? '';
        $why_choose_bottom_title = $data['why_choose_bottom_title'] ?? '';
        $review_main_title = $data['review_main_title'] ?? '';
        $checkout_main_title = $data['checkout_main_title'] ?? '';

    }
}

// Process Order
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullName = $_POST['name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'] ?? '';
    $address = $_POST['address'];
    $product_selection = $_POST['product'];
    
    // Determine city based on product selection
    if ($product_selection == '2-box') {
        $city = 'Free Shipping';
    } else {
        $city = $_POST['shipping'] == 'inside-dhaka' ? 'Inside Dhaka' : 'Outside Dhaka';
    }
    
    $payment_method = 'Cash On Delivery';
    $user_id = 0;

    function generateInvoiceNo() {
        $timestamp = microtime(true) * 10000;
        $uniqueString = 'INV-' . strtoupper(base_convert($timestamp, 10, 36));
        return $uniqueString;
    }
    $invoice_no = generateInvoiceNo();
    $_SESSION['temporary_invoice_no'] = $invoice_no;

    // Get product data from POST
    $quantity = $_POST['quantity'] ?? 1;
    
    // Fetch product details
    $sql = "SELECT * FROM product_info WHERE product_id = $product_id";
    $result = mysqli_query($conn, $sql);
    if ($row = mysqli_fetch_assoc($result)) {
        $product_title = $row['product_title'];
        $product_price = $row['product_price'];
        $product_regular_price = $row['product_regular_price'];
        
        // Adjust based on selection
        if ($product_selection == '2-box') {
            $quantity = 2;
            $product_title = "2 Box " . $product_title;
        }
        
        $total_price = $product_price * $quantity;

        $sql = "INSERT INTO order_info (user_id, user_full_name, user_phone, user_email, user_address, city_address, invoice_no, product_id, product_title, product_quantity, total_price, payment_method)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "issssssisiss",
            $user_id,
            $fullName,
            $phone,
            $email,
            $address,
            $city,
            $invoice_no,
            $product_id,
            $product_title,
            $quantity,
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
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $websiteName; ?> - <?php echo $home_title; ?></title>
    <link href="../Admin/<?= $logo ?>" rel="icon">
    
    <!-- Slick Slider CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css"/>
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: "Poppins", sans-serif;
            line-height: 1.6;
            color: #333;
            background: #f5f5f5;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Header */
        header {
            background: #fff;
            padding: 20px 0;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .logo {
            display: flex;
            justify-content: center;
        }

        .logo img {
            max-width: 200px;
            height: auto;
        }

        /* Success Message */
        #success-box {
            margin: auto;
            text-align: center;
            font-size: 20px;
            font-weight: 500;
            padding: 20px;
            color: #0A3622;
            background: #D1E7DD;
            position: fixed;
            width: 100%;
            z-index: 9999;
            top: 0;
        }

        /* Hero Section */
        .hero {
            padding: 40px 20px;
            text-align: center;
        }

        .hero h1 {
            background: linear-gradient(135deg, #017739 0%, #04aa3e 100%);
            border-radius: 10px;
            color: #fff;
            font-size: 2rem;
            margin-bottom: 10px;
        }

        .hero p {
            color: #7a7a7a;
            font-size: 1.3rem;
            font-weight: 700;
            opacity: 0.9;
        }

        /* Video Section */
        .video-section {
            /* background: #fff; */
            padding: 40px 20px;
            text-align: center;
            border-radius: 10px;
        }

        .video-wrapper {
            max-width: 100%;
            margin: 0 auto;
            position: relative;
            padding-bottom: 56.25%;
            height: 0;
            overflow: hidden;
        }

        .video-wrapper iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: none;
        }

        /* CTA Button */
        .cta-button {
            display: inline-block;
            background: #0030FF;
            color: #fff;
            padding: 15px 40px;
            text-decoration: none;
            border-radius: 50px;
            font-size: 1.2rem;
            font-weight: bold;
            margin: 20px 0;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 48, 255, 0.3);
        }

        .cta-button:hover {
            background: #4764e5ff;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 48, 255, 0.4);
        }

        /* Countdown Timer */
        .countdown-section {
            background: #000000;
            color: #fff;
            padding: 6px;
            text-align: center;
            position: sticky;
            top: 0;
            z-index: 100;
            margin-bottom: 30px;
        }

        .countdown-section h3 {
            color: #f62222ff;
        }

        .countdown {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 10px;
        }

        .countdown-item {
            background: rgba(255, 255, 255, 0.1);
            padding: 10px 20px;
            border-radius: 10px;
        }

        .countdown-item span {
            display: block;
            font-size: 2rem;
            font-weight: bold;
        }

        .countdown-item label {
            font-size: 0.9rem;
            opacity: 0.8;
        }

        /* Section Title */
        .section-title {
            text-align: center;
            font-size: 1.8rem;
            margin-bottom: 20px;
            color: #ffffffff;
            background: #000000ff;
            padding: 5px 0;
            border-top: 1px solid #e0e0e0;
            border-bottom: 1px solid #e0e0e0;
        }

        /* Features Section */
        .features {
            background: #fff;
            padding: 40px 20px;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 0;
            margin-top: 0;
            background: #fff;
        }

        .feature-card {
            background: #fff;
            padding: 30px 20px;
            text-align: center;
            border-bottom: 1px solid #e0e0e0;
        }

        .feature-icon {
            width: 50px;
            height: 50px;
            margin: 0 auto 15px;
            fill: #4caf50;
        }

        .feature-card h3 {
            margin-bottom: 10px;
            color: #000;
            font-size: 1.1rem;
            font-weight: 600;
        }

        .feature-card p {
            color: #666;
            font-size: 0.9rem;
            line-height: 1.5;
        }

        /* Gallery */
        .gallery {
            background: #f8f9fa;
            /* padding: 60px 20px; */
        }

        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-top: 30px;
            padding-bottom: 30px;
        }

        .gallery-item img {
            width: 100%;
            
            object-fit: contain;
            border-radius: 10px;
            transition: transform 0.3s ease;
        }

        .gallery-item img:hover {
            transform: scale(1.05);
        }

        /* Products Section */
        .products {
            background: #fff;
            /* padding: 40px 20px; */
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 30px;
            padding-bottom: 30px;
        }

        .product-card {
            background: #fff;
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            transition: all 0.3s ease;
        }

        .product-card:hover {
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .product-card img {
            width: 60%;
            object-fit: contain;
            margin-bottom: 15px;
        }

        .product-card h3 {
            font-size: 1.1rem;
            margin-bottom: 10px;
        }

        .product-price {
            display: flex;
            gap: 10px;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
        }

        .product-price h4 {
            color: #4caf50;
            font-size: 1.3rem;
        }

        .product-price .old-price {
            color: gray;
            text-decoration: line-through;
            font-size: 1rem;
        }

        /* Reviews */
        .reviews {
            background: #f8f9fa;
            /* padding: 40px 20px; */
        }

        .reviews-slider .review-card {
            padding: 10px;
        }

        .reviews-slider .review-card img {
            width: 100%;
            border-radius: 10px;
        }

        /* Order Form */
        .order-form {
            background: #ffffffff;
            padding: 40px 20px;
        }

        .order-form .section-title {
            background: #0030FF;
            color: #fff;
            font-size: 1.3rem;
            padding: 12px 20px;
            margin-bottom: 30px;
            border: none;
        }

        .form-container {
            max-width: 900px;
            margin: 0 auto;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }

        .product-selection {
            background: transparent;
            padding: 0;
            margin-top: 20px;
        }

        .product-option {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 15px;
            cursor: pointer;
            border: 2px solid #e0e0e0;
            transition: all 0.3s ease;
        }

        .product-option:hover,
        .product-option.active {
            border-color: #0030FF;
        }

        .product-option input[type="radio"] {
            margin-right: 10px;
        }

        .form-section-title {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 20px;
            color: #000;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #000;
            font-size: 0.95rem;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 0.95rem;
            transition: border-color 0.3s ease;
            background: #fff;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #0030FF;
        }

        .shipping-options label {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            cursor: pointer;
        }

        .shipping-options input[type="radio"] {
            margin-right: 8px;
            width: auto;
        }

        .order-summary {
            background: #fff;
            padding: 25px;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #e0e0e0;
            font-size: 0.95rem;
        }

        .summary-total {
            font-size: 1.3rem;
            font-weight: bold;
            color: #000;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 2px solid #000;
        }

        .submit-btn {
            width: 100%;
            background: #0030FF;
            color: #fff;
            padding: 15px;
            border: none;
            border-radius: 5px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 20px;
        }

        .submit-btn:hover {
            background: #0028dd;
            transform: translateY(-2px);
        }

        /* Footer */
        footer {
            background: #e3effa;
            color: #000000ff;
            padding: 40px 20px;
            text-align: center;
        }

        .footer-links {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .footer-links a {
            color: #000000ff;
            text-decoration: none;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero h1 { font-size: 1.5rem; }
            .features-grid { grid-template-columns: 1fr; }
            .form-grid { grid-template-columns: 1fr; }
            .countdown { gap: 10px; }
            .countdown-item { padding: 8px 15px; }
            .countdown-item span { font-size: 1.5rem; }
        }
    </style>
</head>
<body>
    <?php if (isset($_GET['or_msg'])): ?>
    <div id="success-box">Order Successfully Placed...</div>
    <script>
        setTimeout(() => {
            document.getElementById('success-box').style.display = 'none';
        }, 3000);
    </script>
    <?php endif; ?>

    <!-- Header -->
    <header>
        <div class="container">
            <div class="logo">
                <img src="../Admin/<?= $logo ?>" alt="<?= $websiteName ?>">
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <h1><?= $home_title ?></h1>
            <p><?= $home_des ?></p>
        </div>
    </section>

    <!-- Video Section -->
    <?php
    if (!empty($youtube_url)) {

        // Extract ID from shorts link
        if (preg_match('/shorts\/([a-zA-Z0-9_-]+)/', $youtube_url, $match)) {
            $video_id = $match[1];
            $embed_url = "https://www.youtube.com/embed/$video_id";
        } else {
            $embed_url = $youtube_url; // fallback
        }
    ?>
    <section class="video-section">
        <div class="container">
            <div class="video-wrapper">
                <iframe width="100%" height="400" src="<?= $embed_url ?>" allowfullscreen></iframe>
            </div>
            <a href="#order" class="cta-button">অর্ডার করতে চাই</a>
            <p style="font-size: 22px; color: #fc0202ff;"><b>Limited Time OFFER!</b></p>
        </div>
    </section>
    <?php } ?>

    

    <!-- Countdown Timer -->
    <section class="countdown-section">
        <div class="container">
            <h3>Limited Time OFFER!</h3>
            <div class="countdown" id="countdown">
                <div class="countdown-item">
                    <span id="hours">00</span>
                    <label>Hours</label>
                </div>
                <div class="countdown-item">
                    <span id="minutes">00</span>
                    <label>Minutes</label>
                </div>
                <div class="countdown-item">
                    <span id="seconds">00</span>
                    <label>Seconds</label>
                </div>
            </div>
        </div><br>
    </section>

    <!-- Features Section -->
    <h2 class="section-title" style="margin-bottom: 0"><?= $features_main_title; ?></h2>
    <section class="features">
        <div class="container">
            <div class="features-grid">
                <?php
                $sql = "SELECT * FROM features WHERE product_id = $product_id";
                $result = mysqli_query($conn, $sql);
                if (mysqli_num_rows($result) > 0) {
                    while ($data = mysqli_fetch_assoc($result)) {
                        echo '
                        <div class="feature-card">
                            <svg class="feature-icon" viewBox="0 0 512 512">
                                <path d="M504 256c0 136.967-111.033 248-248 248S8 392.967 8 256 119.033 8 256 8s248 111.033 248 248zM227.314 387.314l184-184c6.248-6.248 6.248-16.379 0-22.627l-22.627-22.627c-6.248-6.249-16.379-6.249-22.628 0L216 308.118l-70.059-70.059c-6.248-6.248-16.379-6.248-22.628 0l-22.627 22.627c-6.248 6.248-6.248 16.379 0 22.627l104 104c6.249 6.249 16.379 6.249 22.628.001z"></path>
                            </svg>
                            <h3>'.$data['feature_title'].'</h3>
                            <p>'.$data['feature_description'].'</p>
                        </div>';
                    }
                }
                ?>
            </div>
            <div style="text-align: center; margin-top: 40px;">
                <a href="#order" class="cta-button">এখনই অর্ডার করুন</a>
            </div>
        </div>
    </section>

    <!-- Gallery Section -->
    <section class="gallery">
        <h2 class="section-title"><?= $why_choose_main_title; ?></h2>
        <div class="container">
            <div class="gallery-grid">
                <?php
                $sql = "SELECT * FROM gallery WHERE product_id = $product_id";
                $result = mysqli_query($conn, $sql);
                if (mysqli_num_rows($result) > 0) {
                    while ($data = mysqli_fetch_assoc($result)) {
                        echo '<div class="gallery-item">
                                <img src="../Admin/'.$data['gallery_image'].'" alt="Gallery Image">
                              </div>';
                    }
                }
                ?>
            </div>
        </div>
    </section>

    <!-- Products Section -->
    <section class="products">
        <h2 class="section-title"><?= $why_choose_bottom_title; ?></h2>
        <div class="container">
            <div class="products-grid">
                <?php
                $sql = "SELECT * FROM product_info WHERE product_id = $product_id";
                $result = mysqli_query($conn, $sql);
                if (mysqli_num_rows($result) > 0) {
                    while ($data = mysqli_fetch_assoc($result)) {
                        echo '
                        <div class="product-card">
                            <img src="'.$data['product_img1'].'" alt="'.$data['product_title'].'">
                            <h3>'.$data['product_title'].'</h3>
                            <div class="product-price">
                                <h4>৳'.$data['product_price'].'</h4>
                                <span class="old-price">৳'.$data['product_regular_price'].'</span>
                            </div>
                        </div>';
                    }
                }
                ?>
            </div>
        </div>
    </section>

    <!-- Reviews Section -->
    <section class="reviews">
        <h2 class="section-title"><?= $review_main_title; ?></h2>
        <div class="container">
            <div class="reviews-slider">
                <?php
                $sql = "SELECT * FROM reviews WHERE product_id = $product_id";
                $result = mysqli_query($conn, $sql);
                if (mysqli_num_rows($result) > 0) {
                    while ($data = mysqli_fetch_assoc($result)) {
                        echo '<div class="review-card">
                                <img src="../Admin/'.$data['review_image'].'" alt="Review">
                              </div>';
                    }
                }
                ?>
            </div>
            <div style="text-align: center; margin-top: 40px;">
                <a href="#order" class="cta-button">এখনই অর্ডার করুন</a>
            </div>
        </div>
    </section>

    <!-- OFfer section -->
    <?php

    $sql = "SELECT * FROM product_info WHERE product_id = $product_id";
    $result = mysqli_query($conn, $sql);
    if ($row = mysqli_fetch_assoc($result)) {
        $product_price = $row['product_price'];
        $product_regular_price = $row['product_regular_price'];
    }

    // ******* Calculate Product Discount Percentage ********* //
    function get_product_discount_percentage($regular_price = '', $selling_price = '') {
        // Defensive checks
        if (!is_numeric($regular_price) || !is_numeric($selling_price)) {
            return 0; // invalid input
        }

        if ($regular_price <= 0 || $selling_price < 0 || $selling_price >= $regular_price) {
            return 0; // no discount
        }

        // Calculate discount percentage
        $discount = (($regular_price - $selling_price) / $regular_price) * 100;

        return round($discount); // rounded percentage
    }

    ?>
    <section class="offers">
    <div class="container">
        <div class="offer-card">
        <h2 class="offer-title">Extra <span class="accent"><?php echo get_product_discount_percentage($product_regular_price, $product_price) ?>% Off!</span></h2>

        <hr class="divider">

        <p class="orig-price">
            রেগুলার মূল্য:
            <span class="strike"><?= $product_regular_price ?> Tk.</span>
        </p>

        <p class="deal-price">
            অফার মূল্য: <strong><?= $product_price ?> Tk.</strong>
            <span class="wavy" aria-hidden="true"></span>
        </p>

        <!-- decorative image (uses your uploaded file) -->
        <img class="offer-deco" src="/mnt/data/7693a936-693a-4c81-863b-bc815249604a.png" alt="">
        </div>
    </div>
    </section>

    <style>
    /* container reset */
    .offers { background: #d8f3fb; padding: 32px 0; font-family: "Helvetica Neue", Arial, sans-serif; }
    .offers .container { max-width: 980px; margin: 0 auto; padding: 0 16px; }

    /* card */
    .offer-card {
    position: relative;
    text-align: center;
    padding: 28px 24px 40px;
    border-radius: 6px;
    background: rgba(255,255,255,0.02);
    overflow: visible;
    }

    /* title */
    .offer-title {
    margin: 0;
    font-size: 28px;
    font-weight: 800;
    color: #0b2436;
    letter-spacing: 0.2px;
    }
    .offer-title .accent {
    color: #1fa83a;            /* green */
    font-weight: 900;
    }

    /* divider */
    .divider {
    margin: 18px auto;
    width: 92%;
    border: none;
    border-top: 1px solid rgba(11,36,54,0.15);
    }

    /* original price row */
    .orig-price {
    margin: 12px 0 6px;
    color: #626d76;
    font-size: 18px;
    line-height: 1.2;
    }
    .orig-price .strike {
    color: #e23b3b;
    margin-left: 8px;
    text-decoration: line-through;
    font-weight: 700;
    }

    /* deal price row */
    .deal-price {
    margin: 6px 0 0;
    font-size: 26px;
    font-weight: 800;
    color: #071d1f;
    position: relative;
    display: inline-block;
    }

    /* wavy underline: using SVG data-URI background for a green squiggle under the price */
    .deal-price .wavy {
    display: inline-block;
    width: 120px;
    height: 18px;
    vertical-align: middle;
    margin-left: 8px;
    background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='120' height='18' viewBox='0 0 120 18'><path d='M0 9c10-6 20 6 30 0s20-6 30 0 20 6 30 0 20-6 30 0' fill='none' stroke='%231fa83a' stroke-width='3' stroke-linecap='round' stroke-linejoin='round'/></svg>");
    background-repeat: no-repeat;
    background-size: contain;
    transform: translateY(2px);
    pointer-events: none;
    }

    /* decorative image (optional) */
    .offer-deco {
    position: absolute;
    right: 18px;
    bottom: -18px;
    width: 140px;
    opacity: 0.12;
    transform: rotate(-6deg);
    user-select: none;
    pointer-events: none;
    }

    /* responsive tweaks */
    @media (max-width: 640px) {
    .offer-title { font-size: 20px; }
    .deal-price { font-size: 20px; }
    .deal-price .wavy { width: 90px; height: 14px; }
    .offer-deco { width: 100px; right: 8px; bottom: -12px; }
    }
    </style>

    <!-- End -->

    <!-- Order Form Section -->
    <section class="order-form" id="order">
        <div class="container">
            <h2 class="section-title"><?= $checkout_main_title; ?></h2>
            <h3 style="text-align: center; margin-bottom: 40px;">নিচের ফর্মে আপনার নাম, মোবাইল নম্বর ও সম্পূর্ণ ঠিকানা লিখে ”Place Order“ ক্লিক করুন</h3>
            <div class="form-container">
                <form method="POST" action="" id="orderForm">
                    <div class="form-grid">
                        <div>
                            <h3 class="form-section-title">Billing details</h3>
                            
                            <div class="form-group">
                                <label for="name">Your Name *</label>
                                <input type="text" id="name" name="name" placeholder="Type your full Name here...." required>
                            </div>

                            <div class="form-group">
                                <label for="address">Your Address *</label>
                                <textarea id="address" name="address" rows="3" placeholder="Type your full Address here...." required></textarea>
                            </div>

                            <div class="form-group">
                                <label for="phone">Your Phone Number *</label>
                                <input type="tel" id="phone" name="phone" placeholder="Type your 11 digits Number here...." required pattern="[0-9]{11}">
                            </div>

                            <div class="form-group">
                                <label for="email">Your Email (Optional)</label>
                                <input type="email" id="email" name="email" placeholder="Type your email here....">
                            </div>

                            <div class="product-selection">
                                <?php
                                $sql = "SELECT * FROM product_info WHERE product_id = $product_id LIMIT 1";
                                $result = mysqli_query($conn, $sql);
                                if ($row = mysqli_fetch_assoc($result)) {
                                    $productTitle = $row['product_title'];
                                    $productPrice = $row['product_price'];
                                    $doublePrice = $productPrice * 2;
                                    
                                    echo '
                                    <div class="product-option active" data-price="'.$productPrice.'" data-shipping="'.$outside_delivery_charge.'">
                                        <input type="radio" name="product" id="product1" value="1-box" checked>
                                        <label for="product1">
                                            <h4>1 '.$productTitle.' (৳'.$productPrice.')</h4>
                                        </label>
                                    </div>
                                    
                                    <div class="product-option" data-price="'.$doublePrice.'" data-shipping="0">
                                        <input type="radio" name="product" id="product2" value="2-box">
                                        <label for="product2">
                                            <h4>2 '.$productTitle.' (৳'.$doublePrice.') - FREE DELIVERY</h4>
                                        </label>
                                    </div>';
                                }
                                ?>
                            </div>
                        </div>

                        <div>
                            <h3 class="form-section-title">Your order</h3>
                            
                            <div class="order-summary">
                                <?php
                                $sql = "SELECT * FROM product_info WHERE product_id = $product_id LIMIT 1";
                                $result = mysqli_query($conn, $sql);
                                $defaultProduct = mysqli_fetch_assoc($result);
                                $defaultPrice = $defaultProduct['product_price'];
                                $defaultTotal = $defaultPrice + $outside_delivery_charge;
                                ?>
                                
                                <div class="summary-row">
                                    <span>Product</span>
                                    <span id="productName">1 <?= $defaultProduct['product_title'] ?> × 1</span>
                                </div>

                                <div class="summary-row">
                                    <span>Subtotal</span>
                                    <span id="subtotal">৳<?= number_format($defaultPrice, 2) ?></span>
                                </div>

                                <div class="summary-row">
                                    <span>Shipping</span>
                                    <span>
                                        <div class="shipping-options" style="text-align: right;">
                                            <label>
                                                <input type="radio" name="shipping" value="outside-dhaka" data-cost="<?= $outside_delivery_charge ?>" checked>
                                                Outside Dhaka: ৳<?= $outside_delivery_charge ?>
                                            </label>
                                            <label>
                                                <input type="radio" name="shipping" value="inside-dhaka" data-cost="<?= $inside_delivery_charge ?>">
                                                Inside Dhaka: ৳<?= $inside_delivery_charge ?>
                                            </label>
                                        </div>
                                    </span>
                                </div>

                                <div class="summary-row summary-total">
                                    <span>Total</span>
                                    <span id="total">৳<?= number_format($defaultTotal, 2) ?></span>
                                </div>

                                <button type="submit" class="submit-btn">
                                    Place Order <span id="orderTotal">৳<?= number_format($defaultTotal, 2) ?></span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="quantity" id="quantityField" value="1">
                </form>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-links">
                <a href="tel:<?= $websitePhone ?>">Call us: <?= $websitePhone ?></a>
                <a href="#">Privacy Policy</a>
                <a href="#">Terms & Conditions</a>
            </div>
            <p>© <?= date('Y') ?> <?= $websiteName ?>. All Rights Reserved</p>
        </div>
    </footer>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Slick Slider JS -->
    <script src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
    
    <script>
        // Slick Slider
        $('.reviews-slider').slick({
            slidesToShow: 3,
            slidesToScroll: 1,
            autoplay: true,
            autoplaySpeed: 2500,
            arrows: false,
            dots: true,
            responsive: [
                { breakpoint: 992, settings: { slidesToShow: 2 } },
                { breakpoint: 768, settings: { slidesToShow: 1 } }
            ]
        });

        // Countdown Timer
        function startCountdown() {
            const endDate = new Date();
            endDate.setHours(endDate.getHours() + 24);

            function updateCountdown() {
                const now = new Date().getTime();
                const distance = endDate - now;

                if (distance < 0) {
                    document.getElementById('countdown').innerHTML = '<p>Offer Ended!</p>';
                    return;
                }

                const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                document.getElementById('hours').textContent = hours.toString().padStart(2, '0');
                document.getElementById('minutes').textContent = minutes.toString().padStart(2, '0');
                document.getElementById('seconds').textContent = seconds.toString().padStart(2, '0');
            }

            updateCountdown();
            setInterval(updateCountdown, 1000);
        }

        // Order Calculation
        function updateOrderSummary() {
            const selectedProduct = document.querySelector('input[name="product"]:checked');
            const selectedShipping = document.querySelector('input[name="shipping"]:checked');

            if (!selectedProduct || !selectedShipping) return;

            const productOption = selectedProduct.closest('.product-option');
            const productPrice = parseInt(productOption.dataset.price);
            const productShipping = parseInt(productOption.dataset.shipping);
            
            // Get product name from label
            const productLabel = productOption.querySelector('label h4').textContent;
            
            // If product has free shipping (2-box), use 0, otherwise use selected shipping
            const shippingPrice = productShipping === 0 ? 0 : parseInt(selectedShipping.dataset.cost);

            const subtotal = productPrice;
            const total = subtotal + shippingPrice;

            // Update quantity field
            const quantity = selectedProduct.value === '1-box' ? 1 : 2;
            document.getElementById('quantityField').value = quantity;

            // Update HTML
            document.getElementById('productName').textContent = productLabel.replace(/\(.*?\)/g, '').trim() + ' × 1';
            document.getElementById('subtotal').textContent = '৳' + subtotal.toFixed(2);
            document.getElementById('total').textContent = '৳' + total.toFixed(2);
            document.getElementById('orderTotal').textContent = '৳' + total.toFixed(2);

            // Update shipping options display
            const shippingContainer = document.querySelector('.shipping-options');
            if (productShipping === 0 && shippingContainer) {
                shippingContainer.parentElement.innerHTML = 
                    '<span style="color: #4caf50; font-weight: bold;">FREE DELIVERY</span>';
            }
        }

        // Product Selection
        document.querySelectorAll('input[name="product"]').forEach(radio => {
            radio.addEventListener('change', function() {
                document.querySelectorAll('.product-option').forEach(option => {
                    option.classList.remove('active');
                    option.style.borderColor = '#e0e0e0';
                });
                this.closest('.product-option').classList.add('active');
                this.closest('.product-option').style.borderColor = '#0030FF';
                updateOrderSummary();
            });
        });

        // Shipping Selection
        document.querySelectorAll('input[name="shipping"]').forEach(radio => {
            radio.addEventListener('change', updateOrderSummary);
        });

        // Smooth Scroll
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });

        // Form Validation
        document.getElementById('orderForm').addEventListener('submit', function(e) {
            const phone = document.getElementById('phone').value;
            if (!/^[0-9]{11}$/.test(phone)) {
                e.preventDefault();
                alert('Please enter a valid 11-digit phone number');
                return false;
            }
        });

        // Initialize
        startCountdown();
        updateOrderSummary();
    </script>
</body>
</html>