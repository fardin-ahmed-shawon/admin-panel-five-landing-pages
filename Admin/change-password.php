<?php
$current_page = basename($_SERVER['PHP_SELF']); // Get the current page name
$page_title = 'Change Password'; // Set the page title
?>
<?php require 'header.php'; ?>

<?php
$password_updated_status = '';

if (isset($_POST['changePass'])) {
    $oldPassword = $_POST['oldPassword'];
    $newPassword = $_POST['newPassword'];
    $confirmPassword = $_POST['confirmPassword'];
    $username = $_SESSION['admin'];

    if ($newPassword !== $confirmPassword) {
        $password_updated_status = "New passwords do not match!";
    } else {
        // Fetch current hashed password from DB
        $stmt = $conn->prepare("SELECT admin_password FROM admin_info WHERE admin_username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows == 1) {
            $row = $result->fetch_assoc();
            $hashedPassword = $row['admin_password'];

            // Verify old password
            if (password_verify($oldPassword, $hashedPassword)) {
                // Hash new password
                $newHashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

                // Update password
                $updateStmt = $conn->prepare("UPDATE admin_info SET admin_password = ? WHERE admin_username = ?");
                $updateStmt->bind_param("ss", $newHashedPassword, $username);
                if ($updateStmt->execute()) {
                    $password_updated_status = "Password Successfully Updated!";
                } else {
                    $password_updated_status = "Error updating password!";
                }
                $updateStmt->close();
            } else {
                $password_updated_status = "Old password is incorrect!";
            }
        } else {
            $password_updated_status = "User not found!";
        }

        $stmt->close();
    }
}
?>

<!--------------------------->
<!-- START MAIN AREA -->
<!--------------------------->
<div class="content-wrapper">
          <div class="row">
            <div class="col-md-6 mt-3 mx-auto">
              <div class="card p-3">
                <?php if (!empty($password_updated_status)): ?>
                  <div class="card-header">
                    <div id="success-box"><?= $password_updated_status; ?></div>
                  </div>
                <?php endif; ?>

                <div class="card-body">
                  <h1 class="text-center">Change Password</h1>
                  <br>
                  <form action="" method="POST">
                    <div class="form-group">
                      <label for="oldPassword">Enter Old Password *</label>
                      <input type="password" class="form-control" id="oldPassword" name="oldPassword" required>
                    </div><br>
                    <div class="form-group">
                      <label for="newPassword">Enter New Password *</label>
                      <input type="password" class="form-control" id="newPassword" name="newPassword" required>
                    </div><br>
                    <div class="form-group">
                      <label for="confirmPassword">Enter Confirm Password *</label>
                      <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" required>
                    </div><br>
                    <button name="changePass" type="submit" class="btn btn-primary">Change Password</button>
                  </form>
                </div>
              </div>
            </div>
          </div>  
</div>
<!--------------------------->
<!-- END MAIN AREA -->
<!--------------------------->

<?php require 'footer.php'; ?>
