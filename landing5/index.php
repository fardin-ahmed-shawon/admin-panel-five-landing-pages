<?php
// Site Configuration
$site_title = "শাহী সিডমিক্স - Dhaka Food Service";
$site_language = "bn";
$site_charset = "UTF-8";

// Header Section
$header = array(
    'logo_text' => "DHAKA FOOD SERVICE"
);

// Hero Section
$hero_section = array(
    'title' => "মেষ, চরি, গুজরাট, মাল্টিগ্রেইন ও কোষ্ঠকাঠিন্যে কমান প্রাকৃতিকভাবে - ",
    'product_name' => "শাহী সিডমিক্স!",
    'description' => "\"শাহী সিডমিক্স\"-এ রয়েছে এমন সব প্রাকৃতিক উপাদান, যা প্রোটিন, ফ্যাটিবার, ক্যালসিয়াম, ম্যাগনেসিয়াম, জিংক, কপার এবং ওমেগা-৩ এর মতো গুরুত্বপূর্ণ পুষ্টিগুণে পরিপূর্ণ।"
);

// Countdown Section
$countdown_section = array(
    'title' => "⚠️ বিশেষ অফারটি সীমিত সময়ের জন্য",
    'initial_days' => "12",
    'initial_hours' => "46", 
    'initial_minutes' => "57"
);

// Product Hero Section
$product_hero = array(
    'image' => "images/hero.webp",
    'alt' => "hero.webp"
);

// Features Section
$features_section = array(
    'title' => "\"শাহী সিডমিক্স\" কেন খাবেন?",
    'features' => array(
        "এটি রয়েছে ন্যাচারাল উপাদান যা আপনার দেহকে শক্তিশালী এবং সুস্থ করে তোলে।",
        "দুধের সাথে মিশিয়ে খেলে আপনার স্বাস্থ্য ও সতেজতা বৃদ্ধি পায়।",
        "হজমে, ভিটামিন সি, ও সেলিনিয়াম পূর্ণ কম্বিনেশন নেওয়ার চমৎকার উপায়।",
        "দৃষ্টিশক্তি ও চোখের স্বাস্থ্যের জন্য অত্যন্ত উপকারী।",
        "গর্ভাবস্থায় প্রয়োজনীয় মিনারেল ও পুষ্টি সরবরাহ করে।"
    )
);

// Seed Mix Details Section
$seedmix_section = array(
    'title' => "এক চামচ SeedMix-এ হয় উপাদের ৩T",
    'image' => "images/bannerbelow.webp",
    'alt' => "bannerbelow.webp"
);

// Reviews Section
$reviews_section = array(
    'title' => "আমাদের এই \"শাহী সিডমিক্স\" সম্পর্কে সম্মানিত ক্রাহকদের কিছু মন্তব্য",
    'reviews' => array(
        array(
            'image' => "images/review.webp",
            'alt' => "review.webp",
            'text' => "\"শাহী সিডমিক্স ব্যবহার করার পর থেকে আমার স্বাস্থ্যের উন্নতি হয়েছে। এটি সত্যিই অসাধারণ!\"",
            'avatar' => "র",
            'name' => "রহিমা বেগম",
            'location' => "ঢাকা"
        ),
        array(
            'image' => "images/review.webp",
            'alt' => "review.webp",
            'text' => "\"আমার কোষ্ঠকাঠিন্যের সমস্যা অনেক কমেছে। প্রতিদিন সকালে দুধের সাথে খাই, খুবই উপকার পাচ্ছি।\"",
            'avatar' => "ক",
            'name' => "কামরুল হাসান",
            'location' => "চট্টগ্রাম"
        ),
        array(
            'image' => "images/review.webp",
            'alt' => "review.webp",
            'text' => "\"গর্ভাবস্থায় আমার ডাক্তার এই সিডমিক্স খাওয়ার পরামর্শ দিয়েছিলেন। এখন আমার শিশুও সুস্থ আছে।\"",
            'avatar' => "স",
            'name' => "সাবরিনা আক্তার",
            'location' => "সিলেট"
        ),
        array(
            'image' => "images/review.webp",
            'alt' => "review.webp",
            'text' => "\"দীর্ঘদিন ধরে হজমের সমস্যা ছিল। শাহী সিডমিক্স খাওয়ার পর থেকে সমস্যা অনেক কমেছে।\"",
            'avatar' => "ম",
            'name' => "মোহাম্মদ আলী",
            'location' => "রাজশাহী"
        ),
        array(
            'image' => "images/review.webp",
            'alt' => "review.webp",
            'text' => "\"ওজন কমাতে সাহায্য করেছে এবং শরীরে শক্তি বেড়েছে। সত্যিই চমৎকার একটি পণ্য।\"",
            'avatar' => "ফ",
            'name' => "ফারহানা ইসলাম",
            'location' => "খুলনা"
        )
    )
);

// Order Form Section
$order_form = array(
    'title' => "অর্ডার করতে এখানেই নিচের তথ্যটি পূরণ করুন"
);

// Form Labels
$form_labels = array(
    'phone' => "আপনার ১১ ডিজিটের মোবাইল নম্বর *",
    'address' => "সম্পূর্ণ ঠিকানা: বাসা, রোড, থানা *",
    'country' => "Country / Region *"
);

// Product Options
$product_options = array(
    array(
        'id' => "product1",
        'value' => "17",
        'name' => "শাহী সিডমিক্স - ১৭ শাই",
        'price' => "1250",
        'original_price' => "1500",
        'shipping' => "150",
        'highlight' => false
    ),
    array(
        'id' => "product2", 
        'value' => "500",
        'name' => "শাহী সিডমিক্স - ৫০০ গ্রাম",
        'price' => "600",
        'original_price' => "800",
        'shipping' => "100",
        'highlight' => false
    ),
    array(
        'id' => "product3",
        'value' => "250",
        'name' => "শাহী সিডমিক্স - ২৫০ গ্রাম",
        'price' => "350",
        'original_price' => "500",
        'shipping' => "100",
        'highlight' => true
    )
);

// Order Summary
$order_summary = array(
    'title' => "আপনার অর্ডার",
    'product' => "PRODUCT",
    'subtotal' => "Subtotal",
    'shipping' => "Shipping",
    'total' => "Total"
);

// Payment Info
$payment_info = array(
    'title' => "💵 ক্যাশ অন ডেলিভারি",
    'description' => "পণ্য হাতে পেয়ে টাকা পরিশোধ করুন।"
);

// Privacy Notice
$privacy_notice = "আপনার ব্যক্তিগত তথ্য আপনার অর্ডার প্রসেস করতে, এই ওয়েবসাইট জুড়ে আপনার অভিজ্ঞতা সমর্থন করতে এবং আমাদের গোপনীয়তা নীতিতে বর্ণিত অন্যান্য উদ্দেশ্যে ব্যবহার করা হবে।";

// Submit Button
$submit_button = "🔒 অর্ডার করুন";

// Footer Section
$footer = array(
    'contact_title' => "আরো জানতে কল করুন!",
    'phone_number' => "01947-001199",
    'copyright' => "© 2025 Dhaka Food Service. সর্বস্বত্ব সংরক্ষিত"
);

// Sticky CTA
$sticky_cta = array(
    'text' => "🛒 অর্ডার করতে চাই"
);

// CTA Button Text
$cta_button_text = "এখনই অর্ডার করুন";
?>

<!DOCTYPE html>
<html lang="<?php echo $site_language; ?>">

<head>
    <meta charset="<?php echo $site_charset; ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $site_title; ?></title>
    <!-- Swiper JS CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
            <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Noto Sans Bengali', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
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
            text-align: center;
        }

        .logo img {
            max-width: 200px;
            height: auto;
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

        /* Product Hero Image */
        .product-hero {
            background: #fff;
            padding: 40px 20px;
            position: relative;
        }

        .product-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;

        }

        .product-image-container {
            position: relative;
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.4);
        }

        .hero-text-overlay {
            position: absolute;
            top: 20px;
            left: 20px;
            background: rgba(255, 255, 255, 0.95);
            padding: 10px 20px;
            border-radius: 10px;
            z-index: 10;
        }

        .hero-text-overlay h3 {
            color: #ff4500;
            font-size: 1.3rem;
            margin-bottom: 5px;
        }

        .hero-text-overlay p {
            color: #333;
            font-size: 0.9rem;
        }

        /* Seed Bowl Display */
        .seed-bowls {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
            padding: 30px 0;
            position: relative;
        }

        .seed-bowls img {
            object-fit: contain;
            min-width: 300px;
        }

        .seed-bowl {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 8px solid #d4a574;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .seed-bowl.center {
            width: 140px;
            height: 140px;
            border-color: #ff9800;
        }

        .seed-label {
            position: absolute;
            bottom: -25px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(255, 255, 255, 0.9);
            padding: 3px 10px;
            border-radius: 5px;
            font-size: 0.8rem;
            font-weight: 600;
            white-space: nowrap;
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
        .seedmix-image{
            display: flex;
            justify-content: center;
        }
        .seedmix-image img{
object-fit: contain;
min-width: 300px;
        }

        /* Features with Checkmarks */
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

        /* Seed Mix Details */
        .seedmix-details {
            background: #fff;
            padding: 40px 20px;
        }

        .seedmix-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            max-width: 900px;
            margin: 30px auto;
        }

        .seed-card {
            text-align: center;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
            border: 2px solid #e0e0e0;
        }

        .seed-card .seed-circle {
            width: 100px;
            height: 100px;
            margin: 0 auto 15px;
            border-radius: 50%;
            border: 6px solid #d4a574;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
            font-weight: 600;
            color: #333;
        }

        .seed-card h4 {
            margin: 10px 0;
            color: #2d5016;
        }

        /* Reviews Section */
        .reviews {
            background: #f8f9fa;
            padding: 60px 20px;
        }

        /* Swiper Container */
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

        .review-text {
            font-size: 0.95rem;
            color: #555;
            line-height: 1.5;
        }

        .reviewer-info {
            margin-top: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .reviewer-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #e0e0e0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: #666;
        }

        .reviewer-details h4 {
            margin: 0;
            font-size: 1rem;
            color: #333;
        }

        .reviewer-details p {
            margin: 0;
            font-size: 0.85rem;
            color: #777;
        }

        /* Swiper Navigation */
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

        .product-option {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 15px;
            cursor: pointer;
            border: 2px solid #e0e0e0;
            transition: all 0.3s ease;
            position: relative;
        }

        .product-option:hover,
        .product-option.selected {
            border-color: #28a745;
            background: #e8f5e9;
        }

        .product-option input[type="radio"] {
            margin-right: 10px;
        }

        .product-option h4 {
            display: inline;
            font-size: 1rem;
            color: #000;
        }

        .product-option p {
            margin: 8px 0 0 25px;
            font-size: 0.9rem;
            color: #666;
        }

        .highlight-badge {
            position: absolute;
            top: -10px;
            right: 10px;
            background: #ff6b6b;
            color: #fff;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: bold;
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

            .seedmix-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .seed-bowls {
                gap: 10px;
            }

            .seed-bowl {
                width: 90px;
                height: 90px;
            }

            .seed-bowl.center {
                width: 110px;
                height: 110px;
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
                width: 280px;
            }
            
            /* Hide navigation arrows on mobile */
            .swiper-button-next,
            .swiper-button-prev {
                display: none;
            }
        }

        @media (max-width: 480px) {
            .seedmix-grid {
                grid-template-columns: 1fr;
            }

            .section-title {
                font-size: 1.4rem;
                padding: 15px;
            }
            
            .review-card {
                width: 250px;
            }
        }
    </style>
</head>

<body>
    <!-- Header -->
    <header>
        <div class="container">
            <div class="logo">
                <svg width="200" height="60" viewBox="0 0 200 60" xmlns="http://www.w3.org/2000/svg">
                    <rect fill="#2d5016" width="200" height="60" rx="8" />
                    <path d="M 100 10 L 110 20 L 105 25 L 95 25 L 90 20 Z" fill="#4caf50" />
                    <path d="M 95 25 L 105 25 L 103 35 L 97 35 Z" fill="#66bb6a" />
                    <text x="100" y="48" font-size="14" font-weight="bold" text-anchor="middle" fill="#fff" font-family="Arial"><?php echo $header['logo_text']; ?></text>
                </svg>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <h1><?php echo $hero_section['title']; ?><span class="product-name"><?php echo $hero_section['product_name']; ?></span></h1>
            <p><?php echo $hero_section['description']; ?></p>
        </div>
    </section>

    <!-- Countdown Timer -->
    <section class="countdown-section">
        <div class="container">
            <h3><?php echo $countdown_section['title']; ?></h3>
            <div class="countdown">
                <div class="countdown-item">
                    <span id="days"><?php echo $countdown_section['initial_days']; ?></span>
                    <label>দিন</label>
                </div>
                <div class="countdown-item">
                    <span id="hours"><?php echo $countdown_section['initial_hours']; ?></span>
                    <label>ঘন্টা</label>
                </div>
                <div class="countdown-item">
                    <span id="minutes"><?php echo $countdown_section['initial_minutes']; ?></span>
                    <label>মিনিট</label>
                </div>
            </div>
        </div>
    </section>

    <!-- Product Hero with Seed Bowls -->
    <section class="product-hero">
        <div class="container">
            <div class="seed-bowls">
                <img src="<?php echo $product_hero['image']; ?>" alt="<?php echo $product_hero['alt']; ?>">
            </div>
        </div>
    </section>

    <h2 class="section-title"><span class="highlight"><?php echo $features_section['title']; ?></span></h2>

    <!-- Features List -->
    <section class="features-list">
        <div class="features-container">
            <?php foreach($features_section['features'] as $feature): ?>
            <div class="feature-item">
                <?php echo $feature; ?>
            </div>
            <?php endforeach; ?>
            <div style="text-align: center; margin-top: 30px;">
                <button class="cta-button" onclick="document.getElementById('order').scrollIntoView({behavior: 'smooth'})"><?php echo $cta_button_text; ?></button>
            </div>
        </div>
    </section>

    <!-- Seed Mix Details -->
    <section class="seedmix-details">
        <div class="container">
            <h2 style="text-align: center; font-size: 2rem; margin-bottom: 20px; color: #2d5016;"><?php echo $seedmix_section['title']; ?></h2>
            <div class="seedmix-image">
                <img src="<?php echo $seedmix_section['image']; ?>" alt="<?php echo $seedmix_section['alt']; ?>">
            </div>
        </div>
    </section>

    <h2 class="section-title"><?php echo $reviews_section['title']; ?></h2>

    <!-- Reviews -->
    <section class="reviews">
        <div class="container">
            <!-- Swiper -->
            <div class="swiper mySwiper">
                <div class="swiper-wrapper">
                    <?php foreach($reviews_section['reviews'] as $review): ?>
                    <div class="swiper-slide">
                        <div class="review-card">
                            <img src="<?php echo $review['image']; ?>" alt="<?php echo $review['alt']; ?>">
                            <div class="review-text">
                                <?php echo $review['text']; ?>
                            </div>
                            <div class="reviewer-info">
                                <div class="reviewer-avatar"><?php echo $review['avatar']; ?></div>
                                <div class="reviewer-details">
                                    <h4><?php echo $review['name']; ?></h4>
                                    <p><?php echo $review['location']; ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <!-- Navigation buttons -->
                <div class="swiper-button-next"></div>
                <div class="swiper-button-prev"></div>
                <!-- Pagination -->
                <div class="swiper-pagination"></div>
            </div>
            <div style="text-align: center; margin-top: 30px;">
                <button class="cta-button" onclick="document.getElementById('order').scrollIntoView({behavior: 'smooth'})"><?php echo $cta_button_text; ?></button>
            </div>
        </div>
    </section>

    <!-- Order Form -->
    <section class="order-form" id="order">
        <div class="container">
            <h2 class="section-title"><?php echo $order_form['title']; ?></h2>

            <div class="form-container">
                <form id="orderForm">
                    <div class="form-grid">
                        <div>
                            <div class="form-group">
                                <label><?php echo $form_labels['phone']; ?></label>
                                <input type="tel" required pattern="[0-9]{11}" placeholder="01XXXXXXXXX">
                            </div>

                            <div class="form-group">
                                <label><?php echo $form_labels['address']; ?></label>
                                <textarea rows="3" required placeholder="আপনার সম্পূর্ণ ঠিকানা লিখুন"></textarea>
                            </div>

                            <div class="form-group">
                                <label><?php echo $form_labels['country']; ?></label>
                                <input type="text" value="Bangladesh" readonly style="background: #f0f0f0;">
                            </div>

                            <h3 style="margin: 20px 0 15px; font-size: 1.1rem;">Your Products</h3>

                            <?php foreach($product_options as $product): ?>
                            <div class="product-option <?php echo $product['highlight'] ? 'highlight' : ''; ?>" data-price="<?php echo $product['price']; ?>" data-shipping="<?php echo $product['shipping']; ?>">
                                <input type="radio" name="product" id="<?php echo $product['id']; ?>" value="<?php echo $product['value']; ?>" <?php echo $product['id'] === 'product1' ? 'checked' : ''; ?>>
                                <label for="<?php echo $product['id']; ?>">
                                    <h4><?php echo $product['name']; ?></h4>
                                </label>
                                <p>Price: <del>৳<?php echo $product['original_price']; ?></del> <strong style="color: #dc3545; font-size: 1.1rem;">৳ <?php echo number_format($product['price'], 2); ?></strong></p>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <div>
                            <div class="order-summary">
                                <h3><?php echo $order_summary['title']; ?></h3>

                                <div class="summary-row">
                                    <span><strong><?php echo $order_summary['product']; ?></strong></span>
                                    <span><strong><?php echo $order_summary['subtotal']; ?></strong></span>
                                </div>

                                <div class="summary-row">
                                    <span id="productName"><?php echo $product_options[0]['name']; ?> × 1</span>
                                    <span id="productPrice">৳ <?php echo number_format($product_options[0]['price'], 2); ?></span>
                                </div>

                                <div class="summary-row">
                                    <span><?php echo $order_summary['subtotal']; ?></span>
                                    <span id="subtotal">৳ <?php echo number_format($product_options[0]['price'], 2); ?></span>
                                </div>

                                <div class="summary-row">
                                    <span><?php echo $order_summary['shipping']; ?></span>
                                    <span id="shipping">ঢাকায় বাইরে ৳ <?php echo number_format($product_options[0]['shipping'], 2); ?></span>
                                </div>

                                <div class="summary-row summary-total">
                                    <span><?php echo $order_summary['total']; ?></span>
                                    <span id="total">৳ <?php echo number_format($product_options[0]['price'] + $product_options[0]['shipping'], 2); ?></span>
                                </div>
                            </div>

                            <div style="margin-top: 20px; padding: 15px; background: #fff3cd; border-radius: 5px; border-left: 4px solid #ffc107;">
                                <p style="margin: 0; font-size: 0.9rem; color: #856404;">
                                    <strong><?php echo $payment_info['title']; ?></strong><br>
                                    <?php echo $payment_info['description']; ?>
                                </p>
                            </div>

                            <p style="margin-top: 15px; font-size: 0.85rem; color: #666; line-height: 1.5;">
                                <?php echo $privacy_notice; ?>
                            </p>

                            <button type="submit" class="submit-btn">
                                <?php echo $submit_button; ?> <span id="orderTotal">৳ <?php echo number_format($product_options[0]['price'] + $product_options[0]['shipping'], 2); ?></span>
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
            <h2 class="footer-contact"><?php echo $footer['contact_title']; ?></h2>
            <p style="font-size: 1.8rem; font-weight: bold; margin: 15px 0;">📞 <?php echo $footer['phone_number']; ?></p>
            <p style="margin-top: 20px; opacity: 0.8;"><?php echo $footer['copyright']; ?></p>
        </div>
    </footer>

    <!-- Sticky CTA -->
    <div class="sticky-cta" id="stickyCta">
        <button class="cta-button" onclick="document.getElementById('order').scrollIntoView({behavior: 'smooth'})">
            <?php echo $sticky_cta['text']; ?>
        </button>
    </div>

    <!-- Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script>
        // Initialize Swiper
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

        // Countdown Timer
        function startCountdown() {
            let days = <?php echo $countdown_section['initial_days']; ?>;
            let hours = <?php echo $countdown_section['initial_hours']; ?>;
            let minutes = <?php echo $countdown_section['initial_minutes']; ?>;

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
            setInterval(updateCountdown, 60000); // Update every minute
        }

        // Product Selection and Price Calculation
        const products = [
            <?php foreach($product_options as $product): ?>
            '<?php echo $product['value']; ?>': {
                name: '<?php echo $product['name']; ?>',
                price: <?php echo $product['price']; ?>,
                original: <?php echo $product['original_price']; ?>
            }<?php echo $product !== end($product_options) ? ',' : ''; ?>
            <?php endforeach; ?>
        ];

        function updateOrderSummary() {
            const selectedProduct = document.querySelector('input[name="product"]:checked');
            const productOption = selectedProduct.closest('.product-option');
            const productValue = selectedProduct.value;
            const productPrice = parseInt(productOption.dataset.price);
            const shippingCost = parseInt(productOption.dataset.shipping);

            const total = productPrice + shippingCost;

            // Update summary
            document.getElementById('productName').textContent = products[productValue].name + ' × 1';
            document.getElementById('productPrice').textContent = '৳ ' + productPrice.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
            document.getElementById('subtotal').textContent = '৳ ' + productPrice.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
            document.getElementById('shipping').textContent = 'ঢাকায় বাইরে ৳ ' + shippingCost.toFixed(2);
            document.getElementById('total').textContent = '৳ ' + total.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
            document.getElementById('orderTotal').textContent = '৳ ' + total.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });

            // Update visual selection
            document.querySelectorAll('.product-option').forEach(opt => {
                opt.classList.remove('selected');
            });
            productOption.classList.add('selected');
        }

        // Event listeners for product selection
        document.querySelectorAll('input[name="product"]').forEach(radio => {
            radio.addEventListener('change', updateOrderSummary);
        });

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
            e.preventDefault();
            alert('আপনার অর্ডার সফলভাবে সাবমিট হয়েছে! আমরা শীঘ্রই আপনার সাথে যোগাযোগ করব।\n\nধন্যবাদ Dhaka Food Service এর সাথে থাকার জন্য! 🌟');
        });

        // Initialize
        startCountdown();
        updateOrderSummary();
        document.getElementById('product1').closest('.product-option').classList.add('selected');
    </script>
</body>
</html>