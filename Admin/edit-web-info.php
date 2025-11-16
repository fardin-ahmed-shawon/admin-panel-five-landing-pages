<?php
$current_page = basename($_SERVER['PHP_SELF']); // Get the current page name
$page_title = 'Edit Web Info'; // Set the page title
?>
<?php require 'header.php'; ?>

<?php
// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'] ?? null;
    $address = $_POST['address'] ?? null;
    $inside_location = $_POST['inside_location'] ?? null;
    $inside_delivery_charge = $_POST['inside_delivery_charge'] ?? null;
    $outside_delivery_charge = $_POST['outside_delivery_charge'] ?? null;
    $phone = $_POST['phone'] ?? null;
    $wp_api_num = $_POST['wp_api_num'] ?? null;
    $messenger_username = $_POST['messenger_username'] ?? null;
    $acc_num = $_POST['acc_num'] ?? null;
    $email = $_POST['email'] ?? null;
    $fb_link = $_POST['fb_link'] ?? null;
    $insta_link = $_POST['insta_link'] ?? null;
    $twitter_link = $_POST['twitter_link'] ?? null;
    $yt_link = $_POST['yt_link'] ?? null;
    $location = $_POST['location'] ?? null;
    $top_banner_ad_content = $_POST['top_banner_ad_content'] ?? null;

    // Ensure only one row exists
    $checkQuery = "SELECT * FROM website_info";
    $result = $conn->query($checkQuery);

    if ($result->num_rows > 0) {
        // Update existing row
        $updateQuery = "UPDATE website_info SET 
            name='$name', address='$address', 
            inside_location='$inside_location', inside_delivery_charge='$inside_delivery_charge',outside_delivery_charge='$outside_delivery_charge', 
            phone='$phone', wp_api_num='$wp_api_num', messenger_username='$messenger_username', acc_num='$acc_num', email='$email', fb_link='$fb_link', 
            insta_link='$insta_link', twitter_link='$twitter_link', yt_link='$yt_link', location='$location', top_banner_ad_content='$top_banner_ad_content' 
             WHERE id=1";
        $conn->query($updateQuery);
    } else {
        // Insert new row
        $insertQuery = "INSERT INTO website_info (id, name, address, inside_location, inside_delivery_charge,  outside_delivery_charge, phone, wp_api_num, messenger_username, acc_num, email, fb_link, insta_link, twitter_link, yt_link, top_banner_ad_content) 
            VALUES (1, '$name', '$address', '$inside_location', '$inside_delivery_charge', '$outside_delivery_charge', '$phone', '$wp_api_num', '$messenger_username', '$acc_num', '$email', '$fb_link', '$insta_link', '$twitter_link', '$yt_link', '$top_banner_ad_content')";
        $conn->query($insertQuery);
    }
}

// Fetch data to display in the sidebar
$infoQuery = "SELECT * FROM website_info WHERE id=1";
$infoResult = $conn->query($infoQuery);
$info = $infoResult->fetch_assoc();
?>

<!--------------------------->
<!-- START MAIN AREA -->
<!--------------------------->
<div class="content-wrapper">
                <div class="row">
                    <div class="col-md-8">
                        <div class="card card-body p-5">
                            <h4>Update Website Information</h4>
                            <br><br>
                            <form method="POST" action="">
                                <div class="form-group">
                                    <label for="name">Website Name</label>
                                    <input type="text" class="form-control" id="name" name="name" value="<?= $info['name'] ?? '' ?>">
                                </div>
                                <div class="form-group">
                                    <label for="address">Address</label>
                                    <textarea class="form-control" id="address" name="address"><?= $info['address'] ?? '' ?></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="inside_location">Inside Delivery Location (Your District Location)</label>
                                    <textarea class="form-control" id="inside_location" name="inside_location"><?= $info['inside_location'] ?? '' ?></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="inside_delivery_charge">Inside Delivery Charge</label>
                                    <input type="number" class="form-control" id="inside_delivery_charge" name="inside_delivery_charge" value="<?= $info['inside_delivery_charge'] ?? '' ?>">
                                </div>
                                <div class="form-group">
                                    <label for="outside_delivery_charge">Outside Delivery Charge (Outside Of Your District)</label>
                                    <input type="number" class="form-control" id="outside_delivery_charge" name="outside_delivery_charge" value="<?= $info['outside_delivery_charge'] ?? '' ?>">
                                </div>
                                <div class="form-group">
                                    <label for="phone">Phone</label>
                                    <input type="text" class="form-control" id="phone" name="phone" value="<?= $info['phone'] ?? '' ?>">
                                </div>
                                <div class="form-group">
                                    <label for="wp">WhatsApp API Number (Remove the first digit '0' from your whatsapp number)</label>
                                    <input type="text" class="form-control" id="wp" name="wp_api_num" value="<?= $info['wp_api_num'] ?? '' ?>">
                                </div>
                                <div class="form-group">
                                    <label for="messenger_username">Messenger Username</label>
                                    <input type="text" class="form-control" id="messenger_username" name="messenger_username" value="<?= $info['messenger_username'] ?? '' ?>">
                                </div>
                                <div class="form-group">
                                    <label for="acc_num">Account Number (Mobile Banking)</label>
                                    <input type="text" class="form-control" id="acc_num" name="acc_num" value="<?= $info['acc_num'] ?? '' ?>">
                                </div>
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" value="<?= $info['email'] ?? '' ?>">
                                </div>
                                <div class="form-group">
                                    <label for="fb_link">Facebook Link</label>
                                    <input type="text" class="form-control" id="fb_link" name="fb_link" value="<?= $info['fb_link'] ?? '' ?>">
                                </div>
                                <div class="form-group">
                                    <label for="insta_link">Instagram Link</label>
                                    <input type="text" class="form-control" id="insta_link" name="insta_link" value="<?= $info['insta_link'] ?? '' ?>">
                                </div>
                                <div class="form-group">
                                    <label for="twitter_link">Twitter Link</label>
                                    <input type="text" class="form-control" id="twitter_link" name="twitter_link" value="<?= $info['twitter_link'] ?? '' ?>">
                                </div>
                                <div class="form-group">
                                    <label for="yt_link">YouTube Link</label>
                                    <input type="text" class="form-control" id="yt_link" name="yt_link" value="<?= $info['yt_link'] ?? '' ?>">
                                </div>
                                <div class="form-group d-none">
                                    <label for="location">Google Map location</label>
                                    <input type="text" class="form-control" id="location" name="location" value="<?= $info['location'] ?? '' ?>" placeholder="Enter your google map iframe link">
                                </div>
                                <div class="form-group">
                                    <label for="top_banner_ad_content">Top Banner Ad Content</label>
                                    <input type="text" class="form-control" id="top_banner_ad_content" name="top_banner_ad_content" value="<?= $info['top_banner_ad_content'] ?? '' ?>">
                                </div>
                                <button type="submit" class="btn btn-primary">Save</button>
                            </form>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card card-body p-5">
                            <h4>Preview Information</h4>
                            <br><br>
                            <p><strong>Website Name:</strong> <?= $info['name'] ?? 'N/A' ?></p>
                            <p><strong>Address:</strong> <?= $info['address'] ?? 'N/A' ?></p>
                            <p><strong>Inside Location:</strong> <?= $info['inside_location'] ?? 'N/A' ?></p>
                            <p><strong>Inside Delivery Charge:</strong> <?= $info['inside_delivery_charge'] ?? 'N/A' ?></p>
                            <p><strong>Outside Delivery Charge:</strong> <?= $info['outside_delivery_charge'] ?? 'N/A' ?></p>
                            <p><strong>Phone:</strong> <?= $info['phone'] ?? 'N/A' ?></p>
                            <p><strong>WhatsApp API Number:</strong> <?= $info['wp_api_num'] ?? 'N/A' ?></p>
                            <p><strong>Messenger Username:</strong> <?= $info['messenger_username'] ?? 'N/A' ?></p>
                            <p><strong>Account Number:</strong> <?= $info['acc_num'] ?? 'N/A' ?></p>
                            <p><strong>Email:</strong> <?= $info['email'] ?? 'N/A' ?></p>
                            <p><strong>Facebook:</strong> <a href="<?= $info['fb_link'] ?? '#' ?>" target="_blank"><?= $info['fb_link'] ?? 'N/A' ?></a></p>
                            <p><strong>Instagram:</strong> <a href="<?= $info['insta_link'] ?? '#' ?>" target="_blank"><?= $info['insta_link'] ?? 'N/A' ?></a></p>
                            <p><strong>Twitter:</strong> <a href="<?= $info['twitter_link'] ?? '#' ?>" target="_blank"><?= $info['twitter_link'] ?? 'N/A' ?></a></p>
                            <p><strong>YouTube:</strong> <a href="<?= $info['yt_link'] ?? '#' ?>" target="_blank"><?= $info['yt_link'] ?? 'N/A' ?></a></p>
                            <p class="d-none"><strong>Google Map location:</strong> <?= $info['location'] ?? 'N/A' ?></p>
                            <p><strong>Top Banner Ad Content:</strong> <?= $info['top_banner_ad_content'] ?? 'N/A' ?></p>
                        </div>
                    </div>
                </div>
</div>
<!--------------------------->
<!-- END MAIN AREA -->
<!--------------------------->

<?php require 'footer.php'; ?>