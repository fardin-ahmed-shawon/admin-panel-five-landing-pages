<?php
$current_page = basename($_SERVER['PHP_SELF']); // Get the current page name
$page_title = 'Add Coupon'; // Set the page title
?>
<?php require 'header.php'; ?>

<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form inputs
    $coupon_name = $_POST['coupon_name'];
    $coupon_code = $_POST['coupon_code'];
    $discount_price = $_POST['discount_price'];

    // $expiry_date = $_POST['expiry_date'];
    // $user_limit = $_POST['user_limit'];

    // Validate required fields
    if (empty($coupon_name) || empty($coupon_code) || empty($discount_price)) {
        $error_message = "All fields are required.";
    } else {
        // Prepare SQL query to insert data
        $sql = "INSERT INTO coupon (coupon_name, coupon_code, coupon_discount, free_shipping, created_at) 
                VALUES (?, ?, ?,'0', NOW())";

        // Use prepared statements to prevent SQL injection
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $coupon_name, $coupon_code, $discount_price);

        // Execute the query
        if ($stmt->execute()) {
            $success_message = "Coupon added successfully!";
        } else {
            $error_message = "Error: " . $stmt->error;
        }
        // Close the statement
        $stmt->close();
    }
}
?>

<!--------------------------->
<!-- START MAIN AREA -->
<!--------------------------->
<div class="content-wrapper">
            <div class="page-header">
              <h3 class="page-title">
                <span class="page-title-icon bg-gradient-primary text-white me-2">
                  <i class="mdi mdi-ticket-percent"></i>
                </span> Add Coupon
              </h3>
            </div>
            <br>
            <div class="row">
                <div class="col-md-4 card mx-auto mt-5">
                    <div class="card-body">
                                
                        <div class="row mb-2">
                            <div class="col mt-3">
                                <h6>Add Coupon</h6>
                            </div>
                        </div><hr>

                        <br>
                        <?php if (isset($success_message)): ?>
                            <div class="alert alert-success">
                                <?php echo $success_message; ?>
                            </div>
                        <?php endif; ?>
                        <?php if (isset($error_message)): ?>
                            <div class="alert alert-danger">
                                <?php echo $error_message; ?>
                            </div>
                        <?php endif; ?>

                        <form action="" method="POST">
                            <div class="form-group">
                                <label style="font-size: 17px" for="discountinput">Enter Coupon Name *</label>
                                <input type="text" class="form-control" placeholder="Enter Coupon Name" name="coupon_name" required>
                            </div>

                            <!-- <div class="form-group">
                                <label style="font-size: 17px" for="expiryDate">Choose Expiry Date</label>
                                <input type="date" class="form-control" id="expiryDate" name="expiry_date" required>
                            </div> -->

                            <!-- <div class="form-group">
                                <label style="font-size: 17px" for="discountinput">Enter User Limit</label>
                                <input type="text" class="form-control" placeholder="Enter User Limit" name="user_limit">
                            </div> -->

                            <div class="form-group">
                                <label style="font-size: 17px" for="couponCode">Generated Coupon Code</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="couponCode" name="coupon_code" readonly>
                                    <button type="button" class="btn btn-info d-inline" id="generateCode">Generate <span class="mdi mdi-reload"></span></button>
                                </div>
                            </div>

                            <div class="form-group">
                                <label style="font-size: 17px" for="discountinput">Enter Discount Percentage % *</label>
                                <input type="text" class="form-control" id="discountinput" placeholder="Enter Percentage %" name="discount_price" required>
                            </div>

                            <button type="submit" class="btn btn-primary">Add Coupon</button>
                        </form>
                        <br>
                    </div>
                </div>
            </div>
</div>
<!--------------------------->
<!-- END MAIN AREA -->
<!--------------------------->
    <script>
        // JavaScript to generate a unique coupon code
        document.getElementById('generateCode').addEventListener('click', function() {
            const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            let couponCode = '';
            for (let i = 0; i < 10; i++) {
                couponCode += characters.charAt(Math.floor(Math.random() * characters.length));
            }
            document.getElementById('couponCode').value = couponCode;
        });
    </script>
<?php require 'footer.php'; ?>