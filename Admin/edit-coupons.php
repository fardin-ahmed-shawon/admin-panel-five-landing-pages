<?php
$current_page = basename($_SERVER['PHP_SELF']); // Get the current page name
$page_title = 'Edit Coupon'; // Set the page title
?>
<?php require 'header.php'; ?>

<?php
if (!isset($_GET['id'])) {
    header("Location: view-coupons.php");
    exit();
}

$id = intval($_GET['id']);
$stmt = $conn->prepare("SELECT * FROM coupon WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$coupon = $result->fetch_assoc();
$stmt->close();

if (!$coupon) {
    header("Location: view-coupons.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $coupon_name = $_POST['coupon_name'];
    $coupon_code = $_POST['coupon_code'];
    $discount_price = $_POST['discount_price'];

    if (empty($coupon_name) || empty($coupon_code) || empty($discount_price)) {
        $error_message = "All fields are required.";
    } else {
        $stmt = $conn->prepare("UPDATE coupon SET coupon_name=?, coupon_code=?, coupon_discount=? WHERE id=?");
        $stmt->bind_param("sssi", $coupon_name, $coupon_code, $discount_price, $id);
        if ($stmt->execute()) {
            $success_message = "Coupon updated successfully!";
            // Refresh coupon data
            $coupon['coupon_name'] = $coupon_name;
            $coupon['coupon_code'] = $coupon_code;
            $coupon['coupon_discount'] = $discount_price;
        } else {
            $error_message = "Error: " . $stmt->error;
        }
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
                            <i class="mdi mdi-pencil"></i>
                        </span> Edit Coupon
                    </h3>
                </div>
                <style>
                </style>
                <div class="row">
                    <div class="col-md-4 card mx-auto mt-5 cd">
                        <div class="card-body">
                            <br>
                            <h6>Edit Coupon</h6>
                            <br><hr><br>
                            <?php if (isset($success_message)): ?>
                                <div class="alert alert-success"><?php echo $success_message; ?></div>
                            <?php endif; ?>
                            <?php if (isset($error_message)): ?>
                                <div class="alert alert-danger"><?php echo $error_message; ?></div>
                            <?php endif; ?>
                            <form action="" method="POST">
                                <div class="form-group">
                                    <label>Coupon Name *</label>
                                    <input type="text" class="form-control" name="coupon_name" value="<?php echo htmlspecialchars($coupon['coupon_name']); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label>Coupon Code *</label>
                                    <input type="text" class="form-control" name="coupon_code" value="<?php echo htmlspecialchars($coupon['coupon_code']); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label>Discount Price *</label>
                                    <input type="text" class="form-control" name="discount_price" value="<?php echo htmlspecialchars($coupon['coupon_discount']); ?>" required>
                                </div>
                                <button type="submit" class="btn btn-primary">Update Coupon</button>
                                <a href="view-coupons.php" class="btn btn-secondary">Back</a>
                            </form>
                            <br>
                        </div>
                    </div>
                </div>
</div>
<!--------------------------->
<!-- END MAIN AREA -->
<!--------------------------->

<?php require 'footer.php'; ?>