<?php
$current_page = basename($_SERVER['PHP_SELF']); // Get the current page name
$page_title = 'Settings'; // Set the page title
?>
<?php require 'header.php'; ?>

<style>
        .setting-block {
            flex: 1 1 250px;
            min-width: 230px;
            max-width: 300px;
            padding: 20px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-left: 3px solid #333;
            border-radius: 4px;
            transition: all 0.2s ease;
        }

        .setting-block:hover {
            background-color: #f1f1f1;
            border-left-color: #0d6efd;
        }

        .setting-block h6 {
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 6px;
        }

        .setting-block .btn {
        margin-top: 8px;
        }

        /* .list-group-item {
        font-size: 15px;
        padding: 12px 16px;
        border: none;
        background: none;
        transition: 0.2s;
        } */

        .list-group-item:hover {
        background-color: #f0f0f0;
        }

        .list-group-item.active {
        background-color: #0d6efd;
        color: #fff;
        }
</style>

<!--------------------------->
<!-- START MAIN AREA -->
<!--------------------------->
<div class="content-wrapper">
                <section class="content-main card card-body p-4">
                    <div class="row">
                    <!-- Left Navigation -->
                    <div class="col-lg-3 border-end pe-4">
                        <h4 class="mb-4"><b>Settings Menu</b></h4>
                        <div class="list-group list-group-flush">
                            <a href="#" class="list-group-item list-group-item-action active">General</a>
                            
                            <?php 
                            if ($_SESSION['role'] == 'Admin') {
                                ?>

                            <a href="about_us.php" class="list-group-item list-group-item-action">About Us</a>
                            <a href="contact_us.php" class="list-group-item list-group-item-action">Contact Us</a>
                            <a href="faq.php" class="list-group-item list-group-item-action">FAQ</a>
                            <a href="terms_of_use.php" class="list-group-item list-group-item-action">Terms of Use</a>
                            <a href="privacy_policy.php" class="list-group-item list-group-item-action">Privacy Policy</a>
                            <a href="shipping_delivery.php" class="list-group-item list-group-item-action">Shipping & Delivery</a>

                                <?php
                            }
                            ?>
                            
                        </div>
                    </div>

                    <!-- Right Content -->
                    <div class="col-lg-9 ps-4 mt-5 mt-lg-0">
                        <h4 class="mb-3"><b>General Settings</b></h4>
                        <p class="text-muted mb-4">Manage website information, user access, and visual settings below.</p>

                        <div class="d-flex flex-wrap gap-4">

                        <div class="setting-block">
                            <h6>Change Password</h6>
                            <p class="text-muted small">Securely update your admin password.</p>
                            <a href="change-password.php" class="btn btn-dark btn-sm">Change Password</a>
                        </div>

                        <?php 
                            
                            if ($_SESSION['role'] == 'Admin') {
                                ?>

                        <div class="setting-block">
                            <h6>Website Info</h6>
                            <p class="text-muted small">Change website name, contact, and description.</p>
                            <a href="edit-web-info.php" class="btn btn-dark btn-sm">Edit Info</a>
                        </div>

                        <div class="setting-block">
                            <h6>Logo</h6>
                            <p class="text-muted small">Upload or change the logo of your website.</p>
                            <a href="edit-logo.php" class="btn btn-dark btn-sm">Update Logo</a>
                        </div>

                        <!-- <div class="setting-block">
                            <h6>Theme</h6>
                            <p class="text-muted small">Switch between available themes.</p>
                            <a href="edit-theme.php" class="btn btn-dark btn-sm">Change Theme</a>
                        </div> -->

                                <?php
                            }
                        ?>

                    
                        </div>
                    </div>
                    </div>
                </section>
</div>
<!--------------------------->
<!-- END MAIN AREA -->
<!--------------------------->

<?php require 'footer.php'; ?>