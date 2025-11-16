<?php
//require 'dbconnection.php';
require '../database/dbConnection.php';

$product_slug = $_GET['slug'] ?? '';

// fetch product id based on slug
if ($product_slug != '') {

    $sql = "SELECT product_id FROM landing_pages WHERE product_slug='$product_slug'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_num_rows($result);

    if ($row > 0) {

        $data = mysqli_fetch_assoc($result);
        $product_id = $data['product_id'];

    } else {
        // If no product found with the given slug, redirect to a default page or show an error
        header("Location: 404.php");
        exit;
    }
} else {
    // If no slug is provided, redirect to a default page or show an error
    header("Location: 404.php");
    exit;
}
// END

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

        // Delivery Information
        $inside_location = $data['inside_location'];
        $inside_delivery_charge = $data['inside_delivery_charge'];
        $outside_delivery_charge = $data['outside_delivery_charge'];

        // Video location
        $vdo = '';

        $logo = $data['logo'];

    }
}
// END


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

    }
}
// END

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title><?php echo $websiteName; ?></title>
        <meta content="width=device-width, initial-scale=1.0" name="viewport">
        <meta content="Product Landing Page" name="keywords">
        <meta content="Product Landing Page" name="description">

        <!-- Favicon -->
        <link href="../Admin/<?= $logo ?>" rel="icon">

        <!-- Google Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Montserrat:400|Quicksand:500,600,700&display=swap" rel="stylesheet">

        <!-- Remix Icons -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.css">

        <!-- CSS Libraries -->
        <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
        <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">

        <!-- Template Stylesheet -->
        <link href="css/style.css" rel="stylesheet">
        <link href="css/checkout.css" rel="stylesheet">

        <style>
            .container {
                max-width: 1600px;
            }
            #success-box {
                margin: auto;
                text-align: center;
                font-size: 20px;
                font-weight: 500;
                padding: 20px;
                color: #0A3622;
                background: #D1E7DD;
            }
            #testimonials .testimonial-item img {
                margin: 0 auto;
                max-width: 100%;
                max-height: 100%;
                border: 1px solid rgba(0, 0, 0, .1);
                border-radius: 5px;
            }
        </style>

    </head>

    <body>
    <?php
        if (isset($_GET['or_msg'])) {
            echo '<div style="z-index: 9999; position: fixed; width: 100%;" id="success-box">Order Successfully Placed...</div>';
        }
    ?>
        
        <!-- Header Start-->
        <div id="header" style="margin-top: 0;">
            <div class="container">
                <div id="logo" class="pb-5" style="border-radius: 50%; display: flex; align-items: center; justify-content: space-between;">
                    <a href="<?= $site_link; ?>/landing/<?= $product_slug; ?>"><img style="width: 200px;" src="../Admin/<?= $logo ?>" alt="Logo" /></a>
                </div>
                <div class="row align-items-center">
                    <div class="col-md-7">
                        <div class="header-content">
                        
                            <h2><span>
                                <?= $home_title; ?>
                            </span></h2>

                            <ul class="fa-ul">
                                <li><span class="fa-li"><i class="far fa-arrow-alt-circle-right"></i>
                                </span><?= $home_des; ?></li>
                            </ul>

                            <a class="btn" href="#products">Order Now</a>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="header-img">
                            <img src="../Admin/<?= $home_img; ?>" alt="Product Image">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Header End-->
        
        <!-- Feature Start-->
        <div id="feature">
            <div class="container">
                <div class="section-header">
                    <h2>Product Features</h2>
                    <p>
                        Our product has many features. Here are some of the features of our product. You can check out the features below.
                    </p>
                </div>
                <div class="row align-items-center">

                    <?php
                        // Fetch total number of features
                        $sql = "SELECT COUNT(*) as total FROM features";
                        $result = mysqli_query($conn, $sql);
                        $data = mysqli_fetch_assoc($result);
                        $totalFeatures = $data['total'];

                        // Calculate the midpoint
                        $midpoint = ceil($totalFeatures / 2); // Round up for odd numbers
                    ?>

                    <div class="col-md-4">
                        <!-- Fetch first half -->
                        <?php
                            $sql = "SELECT * FROM features WHERE product_id = $product_id  LIMIT $midpoint";
                            $result = mysqli_query($conn, $sql);
                            if (mysqli_num_rows($result) > 0) {
                                while ($data = mysqli_fetch_assoc($result)) {
                                    $ft_title = $data['feature_title'];
                                    $ft_des = $data['feature_description'];

                                    echo '
                                        <div class="product-feature">
                                            <div class="product-content">
                                                <h2>'.$ft_title.'</h2>
                                                <p>'.$ft_des.'</p>
                                            </div>
                                            <div class="product-icon">
                                                <i class="fa fa-check"></i>
                                            </div>
                                        </div>
                                    ';
                                }
                            }
                        ?>
                    </div>

                    <div class="col-md-4">
                        <div class="product-img">
                            <img src="../Admin/<?= $feature_img; ?>" alt="Product Image">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <!-- Fetch second half -->
                        <?php
                            $sql = "SELECT * FROM features WHERE product_id = $product_id  LIMIT $midpoint, $totalFeatures";
                            $result = mysqli_query($conn, $sql);
                            if (mysqli_num_rows($result) > 0) {
                                while ($data = mysqli_fetch_assoc($result)) {
                                    $ft_title = $data['feature_title'];
                                    $ft_des = $data['feature_description'];

                                    echo '
                                        <div class="product-feature">
                                            <div class="product-icon">
                                                <i class="fa fa-check"></i>
                                            </div>
                                            <div class="product-content">
                                                <h2>'.$ft_title.'</h2>
                                                <p>'.$ft_des.'</p>
                                            </div>
                                        </div>
                                    ';
                                }
                            }
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <!-- Feature End-->
        
        
        <!-- Products Start -->
        <div id="products">
            <div class="container">
                <div class="section-header">
                    <h2>Get Your Products</h2>
                    <p>
                        Choose your favorite product from our collection. Here is our product collection.
                    </p>
                </div>
                <div class="row align-items-center">
                    <!-- Product List -->

                    <?php 
                        
                        $sql = "SELECT * FROM product_info WHERE product_id = $product_id";
                        $result = mysqli_query($conn, $sql);
                        $row = mysqli_num_rows($result);

                        if ($row > 0) {
                            while ($data = mysqli_fetch_assoc($result)) {
                                $productId = $data['product_id'];
                                $productName = $data['product_title'];
                                $productPrice = $data['product_price'];
                                $productQuantity = $data['available_stock'];
                                $productImg = $data['product_img1'];

                                echo '
                                    <div class="col-md-3 mx-auto">
                                        <div class="product-single" product-id="'.$productId.'" product-name="'.$productName.'" product-img="'.$productImg.'" product-price="'.$productPrice.'" product-quantity="1">
                                            <div class="product-img">
                                                <img src="'.$productImg.'" alt="Product Image">
                                            </div>
                                            <div class="product-content">
                                                <h2>'.$productName.'</h2>
                                                <div style="display: flex; gap: 10px; align-items: center; justify-content: center;">
                                                <h3>৳ '.$productPrice.'</h3>
                                                    <h3 style="color: gray; text-decoration: line-through;">৳ '.$productPrice.'</h3>
                                                </div>
                                                <button class="btn" onclick="addToCart(this)">Add to Cart</button>
                                            </div>
                                        </div>
                                    </div>
                                ';

                            }
                        }

                    ?>

                </div>
            </div>
        </div>
        <!-- Products End -->


        <!-- Product Gallery -->
        <div id="testimonials">
            <div class="container">
                <div class="section-header">
                    <h2>Gallery</h2>
                    <p>
                        Here are some of the products images from our collection. We are happy to serve you.
                    </p>
                </div>
                <div class="owl-carousel testimonials-carousel">

                <?php
                    $sql = "SELECT * FROM gallery WHERE product_id = $product_id";
                    $result = mysqli_query($conn, $sql);
                    $row = mysqli_num_rows($result);
                    if ($row > 0) {
                        while ($data = mysqli_fetch_assoc($result)) {
                            $galleryImg = $data['gallery_image'];

                            echo '
                                <div class="testimonial-item">
                                    <img src="../Admin/'.$galleryImg.'" alt="">
                                </div>
                            ';
                        }
                    }
                ?>

                </div>
            </div>
        </div>
        <!-- Product Gallery End -->


        <!-- Checkout Start -->
        <?php
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            session_start();
            // Retrieve form data
            $fullName = $_POST['fullName'];
            $phone = $_POST['phone'];
            $email = $_POST['email'];
            $address = $_POST['address'];
            $city = $_POST['city'];
            $payment_method = $_POST['payment'] ?? 'Cash On Delivery';
            $accNum = $_POST['accNum'] ?? '';
            $transactionID = $_POST['transactionID'] ?? '';

            // Generate a unique invoice number
            function generateInvoiceNo() {
                $timestamp = microtime(true) * 10000;
                $uniqueString = 'INV-' . strtoupper(base_convert($timestamp, 10, 36));
                return $uniqueString;
            }
            $invoice_no = generateInvoiceNo();
            $_SESSION['temporary_invoice_no'] = $invoice_no;

            // Validate payment details for mobile banking
            if ($payment_method != "Cash On Delivery" && ($accNum == "" || $transactionID == "")) {
                ?>
                <META HTTP-EQUIV="Refresh" CONTENT="2; URL=index.php#checkout">
                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        let msg_box = document.getElementById("msg");
                        if (msg_box) {
                            msg_box.style.display = "block";
                            msg_box.innerText = "Please Provide both Account Number and Transaction ID!";
                            setTimeout(() => {
                                msg_box.style.display = "none";
                            }, 3000);
                        }
                    });
                </script>
                <?php
                exit;
            } else {
                // Retrieve cart data from POST request
                $cartData = json_decode($_POST['carts'], true);

                foreach ($cartData as $product) {
                    $product_id = $product['id'];
                    $product_title = $product['name'];
                    $product_quantity = $product['quantity'];
                    $total_price = $product['price'] * $product_quantity;

                    // Insert data into order_info table
                    $sql = "INSERT INTO order_info (user_full_name, user_phone, user_email, user_address, city_address, invoice_no, product_id, product_title, product_quantity, total_price, payment_method)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param(
                        "ssssssisiss",
                        $fullName,
                        $phone,
                        $email,
                        $address,
                        $city,
                        $invoice_no,
                        $product_id,
                        $product_title,
                        $product_quantity,
                        $total_price,
                        $payment_method
                    );

                    if ($stmt->execute()) {
                        if ($payment_method != "Cash On Delivery") {
                            $order_no = $conn->insert_id;
                            // Insert data into payment_info table
                            $sql_payment = "INSERT INTO payment_info (invoice_no, order_no, payment_method, acc_number, transaction_id)
                            VALUES (?, ?, ?, ?, ?)";
                            $stmt_payment = $conn->prepare($sql_payment);
                            $stmt_payment->bind_param("sisss", $invoice_no, $order_no, $payment_method, $accNum, $transactionID);
                            $stmt_payment->execute();
                            $stmt_payment->close();
                        }
                    }
                    $stmt->close();
                }
                $conn->close();
                // Redirect or show success message
                echo "<script>window.location.href = 'index.php?or_msg=successful';</script>";
                exit;
            }
        }
        ?>

        <div id="checkout">
            <div class="container" id="products">
                <div class="section-header">
                    <h2>Checkout</h2>
                    <p>
                        Place your order now and get a discount. Hurry up! Limited time offer.
                    </p>
                </div>
                <div class="row align-items-center">
                    <div class="col-12">
                        <div class="product-single">
                            <form action="" method="post" enctype="multipart/form-data">
                                <div class="row">
                                    <!-- Billing Address Section -->
                                    <div class="col-md-6 text-left">
                                        <h4>Billing Address</h4>
                                        <br>
                                        <div class="content">
                                            <div class="user-details full-input-box">
                                                <!-- Input for Full Name -->
                                                <div class="input-box form-group">
                                                    <span class="details">Full Name<i class="text-danger">*</i></span>
                                                    <input class="form-control" name="fullName" type="text" placeholder="Enter your full name" required="">
                                                </div>
                                                <!-- Input for Phone Number -->
                                                <div class="input-box form-group">
                                                    <span class="details">Phone Number<i class="text-danger">*</i></span>
                                                    <input class="form-control" minlength="11" name="phone" type="text" placeholder="Enter your number" required="">
                                                </div>
                                                <!-- Input for Email -->
                                                <div class="input-box form-group">
                                                    <span class="details">Email</span>
                                                    <input class="form-control" name="email" type="email" placeholder="Enter your email">
                                                </div>
                                                <!-- Input for Address -->
                                                <div class="input-box form-group">
                                                    <span class="details">Address<i class="text-danger">*</i></span>
                                                    <input class="form-control" name="address" type="text" placeholder="Enter your address" required="">
                                                </div>
                                                <br>
                                                <!-- Input for City -->
                                                <div class="radio-input-box form-group">
                                                    <span class="details">Choose Your Delivery Location<i class="text-danger">*</i></span>
                                                    <br>
                                                    <input name="city" type="radio" id="dhaka" value="Inside Dhaka" checked="">
                                                    <label for="dhaka">Inside Dhaka</label>
                                                    <br>
                                                    <input name="city" type="radio" id="outside" value="Outside Dhaka">
                                                    <label for="outside">Outside Dhaka</label>
                                                    <br><br>
                                                    <i>
                                                        <p class="text-muted">* Delivery Charge Inside <?php echo $inside_location; ?> <?php echo $inside_delivery_charge; ?> ৳</p>
                                                        <p class="text-muted">* Delivery Charge Outside <?php echo $inside_location; ?> <?php echo $outside_delivery_charge; ?> ৳</p>
                                                    </i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Order Summary and Payment Section -->
                                    <div class="col-md-6 text-left">
                                        <div>
                                            <h4>Your Order</h4>
                                            <br>
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="order-list">
                                                        <div class="order-titles">
                                                            <h5>Products</h5>
                                                            <h5>Subtotal</h5>
                                                        </div>
                                                        <hr>
                                                        <div class="order-items" id="order-items">
                                                            <!-- Cart items will be added dynamically -->
                                                        </div>
                                                        <div class="subtotal">
                                                            <div class="subtotal-title">Subtotal</div>
                                                            <div class="subtotal-price amount" id="subtotal-price">৳ </div>
                                                        </div>
                                                        <br>
                                                        <div class="shipping">
                                                            <div class="shipping-title">Shipping</div>
                                                            <div class="shipping-price amount" id="shipping-price">৳ </div>
                                                        </div>
                                                        <hr>
                                                        <div class="total-product-price">
                                                            <div class="total-product-price-title">Total</div>
                                                            <div class="total-product-price-price amount" id="total-price">৳ </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <br><br>
                                        <div>
                                            <h4>Payment Method</h4>
                                            <br>
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="payment-method">
                                                        <div class="payment-method-title">
                                                            <h5>Choose Your Payment Method</h5><br>
                                                            <p>We Accept Cash On Delivery & Mobile Banking.</p>
                                                        </div>
                                                        <div class="payment-method-list">

                                                            <input type="radio" id="cash-on-delivery" name="payment" value="Cash On Delivery" checked>
                                                            <label for="cash-on-delivery">Cash on Delivery</label><br>

                                                            <input type="radio" id="bkash" name="payment" value="bKash">
                                                            <label for="bkash">bKash</label><br>

                                                            <!-- <input type="radio" id="rocket" name="payment" value="Rocket">
                                                            <label for="rocket">Rocket</label><br> -->

                                                            <input type="radio" id="nagad" name="payment" value="Nagad">
                                                            <label for="nagad">Nagad</label><br>

                                                        </div>
                                                    </div>
                                                    <br>
                                                    <!-- Payment Details Section -->
                                                    <div id="payment-details" style="display: none;">
                                                        <div>
                                                        *You Need To Send Us The <b style="color: red;">Total</b> Amount*
                                                        <br>
                                                        Account Number: <b style="color: red;"><?php echo $accNum;?></b>
                                                        </div><br>
                                                        <div class="form-group">
                                                            <label for="accNum">Enter Account Number</label>
                                                            <input class="form-control" name="accNum" type="text" placeholder="Enter your account number">
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="transactionID">Enter Transaction ID</label>
                                                            <input class="form-control" name="transactionID" type="text" placeholder="Enter your transaction ID">
                                                        </div>
                                                    </div><br>
                                                    <div class="checkout-btn">
                                                        <button type="submit" class="btn btn-dark">Place Order</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const paymentRadios = document.querySelectorAll('input[name="payment"]');
                const paymentDetails = document.getElementById('payment-details');

                paymentRadios.forEach(radio => {
                    radio.addEventListener('change', function () {
                        if (this.value !== 'Cash On Delivery') {
                            paymentDetails.style.display = 'block';
                        } else {
                            paymentDetails.style.display = 'none';
                        }
                    });
                });
            });
        </script>
        <!-- Checkout End -->


        <!-- Testimonials Start -->
        <div id="testimonials">
            <div class="container">
                <div class="section-header">
                    <h2>100% Customer Satisfaction</h2>
                    <p>
                        Here are some of the reviews from our customers. We are happy to serve you.
                    </p>
                </div>
                <div class="owl-carousel testimonials-carousel">

                <?php
                    $sql = "SELECT * FROM reviews WHERE product_id = $product_id";
                    $result = mysqli_query($conn, $sql);
                    $row = mysqli_num_rows($result);
                    if ($row > 0) {
                        while ($data = mysqli_fetch_assoc($result)) {
                            $reviewImg = $data['review_image'];

                            echo '
                                <div class="testimonial-item">
                                    <img src="../Admin/'.$reviewImg.'" alt="">
                                </div>
                            ';
                        }
                    }
                ?>

                </div>
            </div>
        </div>
        <!-- Testimonials End -->
    

        <!-- GET In Touch -->
        <div id="faqs">
            <div class="container">
                <div class="section-header">
                    <h2>Get in Touch</h2>
                    <p>
                    You can contact with us through the following methods. We are here to help you.
                    </p>
                </div>
                <div class="row align-items-center">
                    <div class="col-12 text-center">
                        <div class="contact-info">
                            <h3><i class="fa fa-map-marker"></i><?php echo $websiteAddress; ?></h3>
                            <h3><i class="fa fa-envelope"></i><?php echo $websiteEmail; ?></h3>
                            <h3><i class="fa fa-phone"></i><?php echo $websitePhone; ?></h3>
                            <a class="btn" href="#">Contact Us</a>
                            <div class="social">
                                <a target="_blank" href="<?php echo $websiteTwitterLink; ?>"><i class="fab fa-twitter"></i></a>
                                <a target="_blank" href="<?php echo $websiteFbLink; ?>"><i class="fab fa-facebook"></i></a>
                                <a target="_blank" href="<?php echo $websiteInstaLink; ?>"><i class="fab fa-instagram"></i></a>
                                <a target="_blank" href="<?php echo $websiteYtLink; ?>"><i class="fab fa-youtube"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- GET In Touch Start -->


        <!-- Footer Start -->
        <div id="footer">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <p>&copy; Copyright Easy Tech Solutions</a>. All Rights Reserved</p>
                    </div>
                </div>
            </div>
        </div>
        <!-- Footer End -->
        
        
        <!-- Back to Top -->
        <a href="#" class="back-to-top"><i class="fa fa-chevron-up"></i></a>

        
        <!-- JavaScript Libraries -->
        <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
        <script src="lib/easing/easing.min.js"></script>
        <script src="lib/menuspy/menuspy.min.js"></script>
        <script src="lib/owlcarousel/owl.carousel.min.js"></script>

        <!-- Template Javascript -->
        <script src="js/main.js"></script>
        <script src="js/cart_calculation.js"></script>

        <script>
            document.addEventListener("DOMContentLoaded", function () {
                const shippingPriceElement = document.getElementById("shipping-price");
                const totalPriceElement = document.getElementById("total-price");
                const subtotalPriceElement = document.getElementById("subtotal-price");

                // Function to update shipping price dynamically
                function updateShippingPrice() {
                    const selectedCity = document.querySelector('input[name="city"]:checked').value;
                    let shippingPrice = 0;

                    if (selectedCity === "Inside Dhaka") {
                        shippingPrice = <?php echo $inside_delivery_charge; ?>;
                    } else if (selectedCity === "Outside Dhaka") {
                        shippingPrice = <?php echo $outside_delivery_charge; ?>;
                    }

                    // Update the shipping price in the DOM
                    shippingPriceElement.textContent = `৳ ${shippingPrice}`;

                    // Update the total price
                    const subtotal = parseInt(subtotalPriceElement.textContent.replace("৳", "").trim());
                    const totalPrice = subtotal + shippingPrice;
                    totalPriceElement.textContent = `৳ ${totalPrice}`;
                }

                // Add event listeners to the radio buttons
                const cityRadios = document.querySelectorAll('input[name="city"]');
                cityRadios.forEach(radio => {
                    radio.addEventListener("change", updateShippingPrice);
                });

                // Initialize the shipping price on page load
                updateShippingPrice();
            });


            // Send product data from the localstora to the server
            document.addEventListener('DOMContentLoaded', () => {
                const form = document.querySelector('form');
                form.addEventListener('submit', (event) => {
                    event.preventDefault();

                    const carts = JSON.parse(localStorage.getItem('carts')) || [];
                    const formData = new FormData(form);

                    // Add cart data to form data
                    formData.append('carts', JSON.stringify(carts));

                    // Send the form data to the server
                    fetch(form.action, {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.text())
                    .then(data => {
                        document.body.innerHTML = data;
                        localStorage.clear();
                        window.location.href = "<?= $site_link;?>/landing/<?= $product_slug; ?>?or_msg=successful";
                    })
                    .catch(error => console.error('Error:', error));
                });
            });

        </script>

    </body>
</html>
