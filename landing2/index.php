<?php
// Site Configuration
$site_title = "Mens Premium Boxer - Combo Pack | FitZen";
$site_language = "bn";
$site_charset = "UTF-8";

// Hero Banner Section
$hero_banner = array(
    'line1' => "৩ পিচ প্রিমিয়াম বক্সার -",
    'line2' => "৪৯৯ টাকা মাত্র",
    'line3' => "ডেলিভার চার্জ ফ্রি"
);

// Quality Badge Section
$quality_badge = "১০০% ফ্যাশিক কাস্টমার";

// CTA Section
$cta_button_text = "অর্ডার করতে ক্লিক করুন";

// Notice Box Section
$notice_text = "আমরা নিচ্চিত সম্পূর্ণ কম্বো স্পেক্ট্রর কাপড় দিয়ে ডেরি খাকিনা খুবি কয়েক্টাই।";

// Product Images
$product_images = array(
    'showcase' => "images/landing2hero.jpg",
    'form' => "images/landing2form.jpg"
);

// Price Section
$price_section = array(
    'title' => "রেগুলার মূল্য ৭৯৯ টাকা।<br>অফার প্রাইস মাত্র ৪৯৯ টাকা।<br>(ডেলিভার চার্জ ফ্রি)"
);

// Reviews Section
$reviews_title = "From Our Happy Customer";

$reviews = array(
    array(
        'name' => "Mohammad Abdur Rahim",
        'text' => "আলহামদুলিল্লাহ!<br>
                  পৌছেছে পুরুষ মনঃ বুকের ভালো লেগেছে, লম্বন আন্দামহাদের ❤️<br>
                  এবর তাহ ছেড়াড় বট করা হচ্ছে উনাদের বিতরিত 🔥<br>
                  আনার ভালো লেগেছে। খর্বা সাম্পা-আবারে ❤️<br>
                  দেয়া খর্ব গেছে ভাবিন টাইশিমাহার লোগাম্বরু সান করব, আসিন ❤️<br>
                  আপনাদের উপশর ধন্যবাদ FitZen"
    ),
    array(
        'name' => "Shariful Islam",
        'text' => "Firstly it's a good quality as a price & gets quick delivery. You guys try to it honestly.<br>
                  Thanks to FitZen. Keep going...❤️"
    ),
    array(
        'name' => "KamruzZaman Tapu",
        'text' => "আলহামদুলিল্লাহ, প্রজাই ভাত পেয়েছি।<br>
                  আনেক ভালো এবর সফট। হাইলি<br>
                  রিকমান্ডেড।।"
    )
);

// Product Details
$product_details = array(
    'title' => "Mens Premium Boxer - Combo Pack",
    'sku' => "Boxer-V01",
    'offer_price' => "499",
    'regular_price' => "799",
    'savings' => "৩০০ ৳ off"
);

// Size Options
$size_options = array(
    'M' => "M (ঘেষাড়-৩০-৩২-ইঞ্চি",
    'L' => "L (ঘেষাড়-৩২-৩৪-ইঞ্চি", 
    'XL' => "XL (ঘেষাড়-৩৪-৩৬-ইঞ্চি",
    'XXL' => "XXL (ঘেষাড়-৩৬-৩৮-ইঞ্চি"
);

// Form Labels
$form_labels = array(
    'name' => "আপনার নাম লিখুন *",
    'phone' => "ফোন নাম্বর লিখুন *",
    'address' => "আপনার সম্পূর্ণ ঠিকানা লিখুন *",
    'district' => "জেলা নির্বাচন *"
);

// District Options
$districts = array(
    '' => "জেলা নাম্বর, রোড, থানাডাকঘর, জেলার নাম, জেলা",
    'dhaka' => "ঢাকা",
    'chittagong' => "চট্টগ্রাম", 
    'rajshahi' => "রাজশাহী",
    'khulna' => "খুলনা",
    'barisal' => "বরিশাল",
    'sylhet' => "সিলেট",
    'rangpur' => "রংপুর",
    'mymensingh' => "ময়মনসিংহ"
);

// Order Form Section
$order_form = array(
    'title' => "অর্ডার টি সম্পূর্ণ করুতে আপনার নাম,<br>মোবাইল নাম্বার ও ঠিকানা নিচে লিখুন",
    'subtitle' => "- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -",
    'billing_title' => "বিলিং ডিটেইল",
    'order_summary_title' => "অর্ডার সারাংশ (+)",
    'submit_button' => "অর্ডার টি কনফার্ম করুন"
);

// Footer Section
$footer_text = "&copy; 2025 FitZen. All Rights Reserved";
?>

<!DOCTYPE html>
<html lang="<?php echo $site_language; ?>">
<head>
    <meta charset="<?php echo $site_charset; ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $site_title; ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: "Hind Siliguri", sans-serif;
            line-height: 1.6;
            color: #333;
            background: #fff;
        }

        .container {
            max-width: 1400px; /* Increased from 1200px */
            margin: 0 auto;
            padding: 0 30px; /* Increased padding */
        }

        /* Hero Banner - Enhanced */
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

        .hero-banner::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -5%;
            width: 250px;
            height: 250px;
            background: rgba(255, 255, 255, 0.03);
            border-radius: 50%;
            animation: float 8s ease-in-out infinite reverse;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }

        .hero-banner h1 {
            font-size: 1.8rem;
            font-weight: 700;
            line-height: 1.5;
            margin-bottom: 5px;
            position: relative;
            z-index: 1;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }

        .hero-banner .line1 {
            font-size: 3.6rem;
            font-weight: 500;
            animation: slideInLeft 0.6s ease-out;
        }

        .hero-banner .line2 {
            font-size: 3rem;
            font-weight: 800;
            margin: 10px 0;
            color: #fff;
            text-shadow: 3px 3px 6px rgba(0, 0, 0, 0.4);
            animation: slideInRight 0.6s ease-out;
        }

        .hero-banner .line3 {
            font-size: 3.5rem;
            font-weight: 500;
            color: #ffd54f;
            animation: slideInLeft 0.6s ease-out 0.2s backwards;
        }

        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        /* Quality Badge - Enhanced */
        .quality-badge {
            background: linear-gradient(to bottom, #fafafa, #f5f5f5);
            padding: 15px 20px;
            text-align: center;
            border-bottom: 2px solid #e0e0e0;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
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
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }

        /* CTA Section - Enhanced */
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
            position: relative;
            overflow: hidden;
        }

        .cta-button::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .cta-button:hover::before {
            width: 300px;
            height: 300px;
        }

        .cta-button:hover {
            background: linear-gradient(135deg, #57a65a 0%, #4caf50 100%);
            transform: translateY(-3px);
            box-shadow: 0 6px 25px rgba(102, 187, 106, 0.5);
        }

        /* Notice Box - Enhanced */
        .notice-box {
            max-width: 700px;
            margin: 25px auto;
            padding: 20px 30px;
            border: 2px solid #66bb6a;
            border-radius: 12px;
            text-align: center;
            background: linear-gradient(135deg, #ffffff 0%, #f1f8e9 100%);
            box-shadow: 0 4px 12px rgba(102, 187, 106, 0.15);
            position: relative;
        }

        .notice-box::before {
            content: '✓';
            position: absolute;
            top: -15px;
            left: 50%;
            transform: translateX(-50%);
            background: #66bb6a;
            color: #fff;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.2rem;
        }

        .notice-box p {
            color: #2e7d32;
            font-size: 1.05rem;
            font-weight: 600;
            line-height: 1.7;
        }

        /* Product Showcase - Enhanced */
.product-showcase {
    padding: 0px 20px 60px 20px;
    background: linear-gradient(to bottom, #f5f5f5, #fafafa);
    text-align: center;
}

        .product-image-wrapper {
            max-width: 700px; /* Increased from 580px */
            margin: 0 auto;
            background: #fff;
            padding: 50px 40px;
            border-radius: 20px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            position: relative;
        }

        .product-image-wrapper::before {
            content: '';
            position: absolute;
            top: -5px;
            left: -5px;
            right: -5px;
            bottom: -5px;
            background: linear-gradient(135deg, #66bb6a, #4caf50);
            border-radius: 20px;
            z-index: -1;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .product-image-wrapper:hover::before {
            opacity: 0.1;
        }

        .product-image-wrapper:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.18);
        }

        .product-image-wrapper img {
            width: 100%;
            height: auto;
            display: block;
            border-radius: 10px;
        }

        /* Price Section - Enhanced */
        .price-section {
            text-align: center;
            padding: 50px 20px;
            background: #fff;
        }

        .price-section h2 {
            font-size: 1.5rem;
            line-height: 1.9;
            margin-bottom: 30px;
            color: #333;
            font-weight: 700;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.05);
        }

        /* Reviews Section - Enhanced */
        .reviews-section {
            padding: 0px 20px 80px 20px; /* Increased padding */
            background: linear-gradient(to bottom, #f9f9f9, #f5f5f5);
        }

        .reviews-section h2 {
            text-align: center;
            font-size: 3rem; /* Increased from 2.5rem */
            margin-bottom: 60px; /* Increased margin */
            color: #333;
            font-weight: 700;
            position: relative;
            display: inline-block;
            width: 100%;
        }

        .reviews-section h2::after {
            content: '';
            position: absolute;
            bottom: -15px; /* Increased from -10px */
            left: 50%;
            transform: translateX(-50%);
            width: 150px; /* Increased from 100px */
            height: 5px; /* Increased from 4px */
            background: linear-gradient(90deg, #66bb6a, #4caf50);
            border-radius: 2px;
        }

        .swiper {
            width: 100%;
            padding: 30px 0 80px 0; /* Increased padding */
        }

        .review-card {
            background: linear-gradient(135deg, #2a2a2a 0%, #1e1e1e 100%);
            color: #fff;
            padding: 50px 35px; /* Increased padding */
            border-radius: 25px; /* Increased border radius */
            text-align: center;
            height: 550px; /* Increased height */
            display: flex;
            flex-direction: column;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .review-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 35px rgba(0, 0, 0, 0.3);
        }

        .review-header {
            font-size: 2rem; /* Increased from 1.7rem */
            margin-bottom: 35px; /* Increased margin */
            color: #b8b8b8;
            font-weight: 500;
            line-height: 1.4;
        }

        .review-content {
            background: linear-gradient(to bottom, #fff, #fafafa);
            color: #333;
            padding: 35px 28px; /* Increased padding */
            border-radius: 18px; /* Increased border radius */
            margin-bottom: 28px; /* Increased margin */
            flex: 1;
            display: flex;
            flex-direction: column;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            height: 220px; /* Increased height */
            scrollbar-width: 1px;
        }

        .reviewer-info {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 18px;
        }

        .reviewer-avatar {
            width: 55px; /* Increased from 48px */
            height: 55px; /* Increased from 48px */
            border-radius: 50%;
            background: linear-gradient(135deg, #e0e0e0, #d0d0d0);
            flex-shrink: 0;
            border: 2px solid #66bb6a;
        }

        .reviewer-name {
            font-weight: 700;
            font-size: 1.15rem; /* Increased from 1.05rem */
            color: #333;
            text-align: left;
        }

        .follow-link {
            color: #1976d2;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .follow-link:hover {
            color: #1565c0;
        }

        .review-text {
            text-align: left;
            line-height: 1.9;
            font-size: 1.1rem; /* Increased from 0.98rem */
            color: #444;
            text-overflow: ellipsis;
            overflow-x: hidden;
            width: 100%;
            text-wrap: wrap;
        }

        .review-stars {
            color: #ffa726;
            font-size: 1.9rem; /* Increased from 1.7rem */
            margin-top: 25px; /* Increased margin */
            letter-spacing: 4px;
            filter: drop-shadow(0 2px 4px rgba(255, 167, 38, 0.3));
        }

        .swiper-pagination-bullet {
            background: #666;
            opacity: 0.5;
            width: 12px; /* Increased from 10px */
            height: 12px; /* Increased from 10px */
            transition: all 0.3s ease;
        }

        .swiper-pagination-bullet-active {
            background: #66bb6a;
            opacity: 1;
            width: 35px; /* Increased from 30px */
            border-radius: 5px;
        }

        /* Order Form Section - Enhanced */
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

        .form-subtitle {
            text-align: center;
            color: #bbb;
            margin-bottom: 40px;
            font-size: 1.2rem;
            letter-spacing: 2px;
        }

        .form-container {
            max-width: 850px; /* Increased from 750px */
            margin: 0 auto;
        }

        .product-info {
            display: grid;
            grid-template-columns: 250px 1fr; /* Increased from 220px 1fr */
            gap: 30px;
            margin-bottom: 35px;
            background: #fff;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid #f0f0f0;
        }

        .product-info img {
            width: 100%;
            height: auto;
            border-radius: 12px;
            background: #fafafa;
            padding: 15px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .product-details h3 {
            font-size: 1.3rem;
            margin-bottom: 10px;
            font-weight: 700;
            color: #333;
        }

        .product-details .sku {
            color: #888;
            font-size: 0.9rem;
            margin-bottom: 15px;
            font-weight: 500;
        }

        .product-details .price-display {
            margin-bottom: 18px;
        }

        .product-details .offer-price {
            color: #e53935;
            font-size: 2rem;
            font-weight: 800;
            margin-right: 12px;
        }

        .product-details .regular-price {
            text-decoration: line-through;
            color: #999;
            font-size: 1.2rem;
            margin-right: 10px;
        }

        .savings-badge {
            background: linear-gradient(135deg, #e53935, #c62828);
            color: #fff;
            padding: 4px 14px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 700;
            box-shadow: 0 2px 8px rgba(229, 57, 53, 0.3);
        }

        .size-selector {
            margin: 18px 0;
        }

        .size-selector label {
            display: block;
            margin-bottom: 12px;
            font-weight: 700;
            color: #333;
            font-size: 1rem;
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
            font-size: 0.95rem;
            text-align: center;
            font-weight: 500;
        }

        .size-option:hover {
            border-color: #66bb6a;
            background: linear-gradient(135deg, #f1f8e9, #e8f5e9);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 187, 106, 0.2);
        }

        .size-option.selected {
            border-color: #66bb6a;
            background: linear-gradient(135deg, #e8f5e9, #c8e6c9);
            font-weight: 700;
            box-shadow: 0 4px 15px rgba(102, 187, 106, 0.3);
        }

        .size-status {
            color: #66bb6a;
            margin-top: 10px;
            font-weight: 700;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
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
            color: #333;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .quantity-controls {
            display: flex;
            align-items: center;
            border: 2px solid #ddd;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .quantity-selector button {
            width: 42px;
            height: 42px;
            border: none;
            background: linear-gradient(to bottom, #f8f8f8, #f0f0f0);
            cursor: pointer;
            font-size: 1.4rem;
            color: #333;
            transition: all 0.3s ease;
            font-weight: 700;
        }

        .quantity-selector button:hover {
            background: linear-gradient(to bottom, #66bb6a, #57a65a);
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
            background: #fff;
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
            font-size: 0.95rem;
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
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
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
            color: #333;
            font-weight: 500;
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
            position: relative;
            overflow: hidden;
        }

        .submit-button::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .submit-button:hover::before {
            width: 400px;
            height: 400px;
        }

        .submit-button:hover {
            background: linear-gradient(135deg, #57a65a 0%, #4caf50 100%);
            transform: translateY(-3px);
            box-shadow: 0 6px 30px rgba(102, 187, 106, 0.5);
        }

        /* Footer - Enhanced */
        footer {
            background: linear-gradient(135deg, #2c3e50 0%, #1a252f 100%);
            color: #fff;
            padding: 35px 20px;
            text-align: center;
            box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.1);
        }

        footer p {
            font-size: 0.95rem;
            font-weight: 500;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .container {
                max-width: 100%;
                padding: 0 20px;
            }
            
            .hero-banner {
                padding: 28px 15px;
            }

            .hero-banner h1 {
                font-size: 1.4rem;
            }

            .hero-banner .line1 {
                font-size: 1.3rem;
            }

            .hero-banner .line2 {
                font-size: 1.6rem;
            }

            .hero-banner .line3 {
                font-size: 1.25rem;
            }

            .quality-badge p {
                font-size: 0.95rem;
            }

            .stars {
                font-size: 1.05rem;
            }

            .cta-button {
                padding: 14px 40px;
                font-size: 1.05rem;
            }

            .notice-box {
                padding: 18px 22px;
                margin: 18px 20px;
            }

            .notice-box p {
                font-size: 1rem;
            }

            .product-showcase {
                padding: 45px 15px;
            }

            .product-image-wrapper {
                padding: 40px 25px;
                max-width: 100%;
            }

            .price-section {
                padding: 40px 15px;
            }

            .price-section h2 {
                font-size: 1.3rem;
                line-height: 1.8;
            }

            .reviews-section {
                padding: 50px 15px;
            }

            .reviews-section h2 {
                font-size: 2.2rem;
                margin-bottom: 40px;
            }

            .review-card {
                padding: 35px 22px;
                height: auto;
            }

            .review-header {
                font-size: 1.6rem;
                margin-bottom: 22px;
            }

            .review-content {
                padding: 22px 18px;
                height: auto;
            }

            .reviewer-avatar {
                width: 44px;
                height: 44px;
            }

            .reviewer-name {
                font-size: 0.95rem;
            }

            .review-text {
                font-size: 0.93rem;
            }

            .review-stars {
                font-size: 1.5rem;
            }

            .order-form-section {
                padding: 50px 15px 60px;
            }

            .order-form-section h2 {
                font-size: 1.4rem;
            }

            .form-subtitle {
                font-size: 1.05rem;
                margin-bottom: 30px;
            }

            .product-info {
                grid-template-columns: 1fr;
                padding: 25px;
                gap: 22px;
            }

            .product-info img {
                max-width: 280px;
                margin: 0 auto;
            }

            .product-details h3 {
                font-size: 1.15rem;
            }

            .product-details .offer-price {
                font-size: 1.7rem;
            }

            .product-details .regular-price {
                font-size: 1.05rem;
            }

            .size-options {
                grid-template-columns: 1fr;
            }

            .size-option {
                font-size: 0.9rem;
                padding: 10px 14px;
            }

            .quantity-selector {
                flex-wrap: wrap;
            }

            .form-group label {
                font-size: 0.9rem;
            }

            .form-group input,
            .form-group textarea,
            .form-group select {
                font-size: 0.95rem;
                padding: 12px 14px;
            }

            .order-summary {
                padding: 20px;
            }

            .order-summary h3 {
                font-size: 1.05rem;
            }

            .summary-row {
                font-size: 1rem;
            }

            .summary-total {
                font-size: 1.3rem;
            }

            .submit-button {
                font-size: 1.15rem;
                padding: 16px;
            }
        }

        @media (max-width: 480px) {
            .hero-banner {
                padding: 22px 15px;
            }

            .hero-banner h1 {
                font-size: 1.2rem;
            }

            .hero-banner .line1 {
                font-size: 1.1rem;
            }

            .hero-banner .line2 {
                font-size: 1.4rem;
            }

            .hero-banner .line3 {
                font-size: 1.05rem;
            }

            .cta-button {
                padding: 12px 35px;
                font-size: 1rem;
            }

            .reviews-section h2 {
                font-size: 1.9rem;
            }

            .product-info {
                padding: 18px;
            }
        }
    </style>
</head>
<body>
    <!-- Hero Banner -->
    <section class="hero-banner">
        <div class="container">
            <h1>
                <div class="line1"><?php echo $hero_banner['line1']; ?></div>
                <div class="line2"><?php echo $hero_banner['line2']; ?></div>
                <div class="line3"><?php echo $hero_banner['line3']; ?></div>
            </h1>
        </div>
    </section>

    <!-- Quality Badge -->
    <section class="quality-badge">
        <div class="container">
            <p><?php echo $quality_badge; ?> <span class="stars">⭐⭐⭐⭐⭐</span></p>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <a href="#order" class="cta-button"><?php echo $cta_button_text; ?></a>
        </div>
    </section>

    <!-- Notice Box -->
    <section class="cta-section" style="padding-top: 0;">
        <div class="container">
            <div class="notice-box">
                <p><?php echo $notice_text; ?></p>
            </div>
        </div>
    </section>

    <!-- Product Showcase -->
    <section class="product-showcase">
        <div class="container">
            <div class="product-image-wrapper">
                <img src="<?php echo $product_images['showcase']; ?>" alt="FitZen Best Mens Boxer">
            </div>
        </div>
    </section>

    <!-- Price Section -->
    <section class="price-section">
        <div class="container">
            <h2><?php echo $price_section['title']; ?></h2>
            <a href="#order" class="cta-button"><?php echo $cta_button_text; ?></a>
        </div>
    </section>

    <!-- Reviews Section -->
    <section class="reviews-section">
        <div class="container">
            <h2><?php echo $reviews_title; ?></h2>
            
            <div class="swiper reviewSwiper">
                <div class="swiper-wrapper">
                    <?php foreach($reviews as $review): ?>
                    <div class="swiper-slide">
                        <div class="review-card">
                            <div class="review-header">From Our<br>Happy Customer</div>
                            <div class="review-content">
                                <div class="reviewer-info">
                                    <div class="reviewer-avatar"></div>
                                    <div>
                                        <div class="reviewer-name"><?php echo $review['name']; ?> · <a href="#" class="follow-link">Follow</a></div>
                                    </div>
                                </div>
                                <div class="review-text">
                                    <?php echo $review['text']; ?>
                                </div>
                            </div>
                            <div class="review-stars">⭐⭐⭐⭐⭐</div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="swiper-pagination"></div>
            </div>
        </div>
    </section>

    <!-- Order Form Section -->
    <section class="order-form-section" id="order">
        <div class="container">
            <h2><?php echo $order_form['title']; ?></h2>
            <p class="form-subtitle"><?php echo $order_form['subtitle']; ?></p>

            <div class="form-container">
                <form id="orderForm">
                    <div class="product-info">
                        <img src="<?php echo $product_images['form']; ?>" alt="Mens Premium Boxer">
                        <div class="product-details">
                            <h3><?php echo $product_details['title']; ?></h3>
                            <p class="sku">SKU: <?php echo $product_details['sku']; ?></p>
                            <div class="price-display">
                                <span class="offer-price">৳<?php echo $product_details['offer_price']; ?></span>
                                <span class="regular-price">৳<?php echo $product_details['regular_price']; ?></span>
                                <span class="savings-badge"><?php echo $product_details['savings']; ?></span>
                            </div>

                            <div class="size-selector">
                                <label>Select Your Size:</label>
                                <div class="size-options">
                                    <?php foreach($size_options as $size => $label): ?>
                                    <div class="size-option" data-size="<?php echo $size; ?>"><?php echo $label; ?></div>
                                    <?php endforeach; ?>
                                </div>
                                <p class="size-status" id="sizeStatus">STATUS: PLEASE SELECT A VARIATION</p>
                            </div>

                            <div class="quantity-selector">
                                <label>QUANTITY</label>
                                <div class="quantity-controls">
                                    <button type="button" id="decreaseQty">−</button>
                                    <input type="text" id="quantity" value="1" min="1" readonly>
                                    <button type="button" id="increaseQty">+</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h3><?php echo $order_form['billing_title']; ?></h3>

                        <div class="form-group">
                            <label for="name"><?php echo $form_labels['name']; ?></label>
                            <input type="text" id="name" name="name" placeholder="আপনার পূর্ণ নাম লিখুন" required>
                        </div>

                        <div class="form-group">
                            <label for="phone"><?php echo $form_labels['phone']; ?></label>
                            <input type="tel" id="phone" name="phone" placeholder="আপনার ১১ সংখ্যার নাম্বার লিখুন" required>
                        </div>

                        <div class="form-group">
                            <label for="address"><?php echo $form_labels['address']; ?></label>
                            <textarea id="address" name="address" rows="3" placeholder="বাসা নাম্বর, রোড, থানাডাকঘর, জেলা" required></textarea>
                        </div>

                        <div class="form-group">
                            <label for="district"><?php echo $form_labels['district']; ?></label>
                            <select id="district" name="district" required>
                                <?php foreach($districts as $value => $name): ?>
                                <option value="<?php echo $value; ?>"><?php echo $name; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>ডেলিভারি বাধামান</label>
                            <div style="margin-top: 10px;">
                                <label style="display: flex; align-items: center; font-weight: normal;">
                                    <input type="radio" name="shipping" value="free" checked style="margin-right: 8px; width: auto;">
                                    Free Delivery
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="order-summary">
                        <h3><?php echo $order_form['order_summary_title']; ?></h3>
                        <div class="summary-row summary-total">
                            <span>টোটাল</span>
                            <span id="totalPrice">৳0</span>
                        </div>
                    </div>

                    <button type="submit" class="submit-button">
                        <?php echo $order_form['submit_button']; ?> ৳<span id="finalTotal">0</span>
                    </button>
                </form>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <p><?php echo $footer_text; ?></p>
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
                    spaceBetween: 20,
                },
                768: {
                    slidesPerView: 2,
                    spaceBetween: 25,
                },
                1024: {
                    slidesPerView: 3,
                    spaceBetween: 30,
                },
            },
        });

        // Size Selection
        const sizeOptions = document.querySelectorAll('.size-option');
        const sizeStatus = document.getElementById('sizeStatus');
        let selectedSize = null;

        sizeOptions.forEach(option => {
            option.addEventListener('click', function() {
                sizeOptions.forEach(opt => opt.classList.remove('selected'));
                this.classList.add('selected');
                selectedSize = this.getAttribute('data-size');
                sizeStatus.style.color = '#66bb6a';
                sizeStatus.textContent = 'STATUS: SIZE ' + selectedSize + ' SELECTED';
                updateTotal();
            });
        });

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
            const price = <?php echo $product_details['offer_price']; ?>;
            const quantity = parseInt(quantityInput.value);
            const total = price * quantity;

            document.getElementById('totalPrice').textContent = '৳' + total;
            document.getElementById('finalTotal').textContent = total;
        }

        // Initialize total
        updateTotal();

        // Form Submission
        document.getElementById('orderForm').addEventListener('submit', function(e) {
            e.preventDefault();

            if (!selectedSize) {
                alert('দয়া করে একটি সাইজ নির্বাচন করুন');
                window.scrollTo({
                    top: document.querySelector('.size-selector').offsetTop - 100,
                    behavior: 'smooth'
                });
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

            const formData = {
                name: name,
                phone: phone,
                address: address,
                district: district,
                size: selectedSize,
                quantity: quantityInput.value,
                total: document.getElementById('finalTotal').textContent
            };

            console.log('Order Data:', formData);
            
            // Success message
            alert('আপনার অর্ডার সফলভাবে সম্পন্ন হয়েছে!\n\nঅর্ডার ডিটেইল:\nনাম: ' + formData.name + '\nফোন: ' + formData.phone + '\nসাইজ: ' + formData.size + '\nপরিমাণ: ' + formData.quantity + '\nমোট: ৳' + formData.total);
            
            // Reset form
            this.reset();
            sizeOptions.forEach(opt => opt.classList.remove('selected'));
            selectedSize = null;
            sizeStatus.style.color = '#66bb6a';
            sizeStatus.textContent = 'STATUS: PLEASE SELECT A VARIATION';
            quantityInput.value = 1;
            updateTotal();
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

        // Prevent zoom on double tap for mobile
        let lastTouchEnd = 0;
        document.addEventListener('touchend', function(e) {
            const now = Date.now();
            if (now - lastTouchEnd <= 300) {
                e.preventDefault();
            }
            lastTouchEnd = now;
        }, false);
    </script>
</body>
</html>