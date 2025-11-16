<?php
$current_page = basename($_SERVER['PHP_SELF']); // Get the current page name
$page_title = 'Add User'; // Set the page title
?>
<?php 
require 'header.php'; 

// Only allow access to this page if the user is an Admin
if ($_SESSION['role'] != 'Admin') {
    echo "<script>alert('Access denied.'); window.location.href='404.php';</script>";
    exit();
}
?>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['admin_username'];
    $password = password_hash($_POST['admin_password'], PASSWORD_DEFAULT);
    $role     = $_POST['role_id'];

    $pictureName = '';
    if (!empty($_FILES['admin_picture']['name'])) {
        $targetDir = "uploads/";
        $originalName = basename($_FILES["admin_picture"]["name"]);
        $pictureName = time() . '_' . $originalName;
        $targetFile = $targetDir . $pictureName;

        // Get image info
        $imageType = exif_imagetype($_FILES["admin_picture"]["tmp_name"]);
        $sourceImage = null;

        switch ($imageType) {
            case IMAGETYPE_JPEG:
                $sourceImage = imagecreatefromjpeg($_FILES["admin_picture"]["tmp_name"]);
                break;
            case IMAGETYPE_PNG:
                $sourceImage = imagecreatefrompng($_FILES["admin_picture"]["tmp_name"]);
                break;
            case IMAGETYPE_WEBP:
                $sourceImage = imagecreatefromwebp($_FILES["admin_picture"]["tmp_name"]);
                break;
            default:
                echo "Unsupported image format.";
                exit();
        }

        $resizedImage = imagecreatetruecolor(200, 200);
        $origWidth = imagesx($sourceImage);
        $origHeight = imagesy($sourceImage);

        imagecopyresampled($resizedImage, $sourceImage, 0, 0, 0, 0, 200, 200, $origWidth, $origHeight);
        imagejpeg($resizedImage, $targetFile, 85); // save compressed

        imagedestroy($sourceImage);
        imagedestroy($resizedImage);
    }

    $stmt = $conn->prepare("INSERT INTO admin_info (admin_username, admin_password, admin_picture, role_id) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssi", $username, $password, $pictureName, $role);

    if ($stmt->execute()) {
        echo "
        <script>
          Swal.fire({
            icon: 'success',
            title: 'Admin user added successfully!',
            showConfirmButton: false,
            timer: 1800
          }).then(() => {
            window.location.href = 'view-users.php';
          });
        </script>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}
?>

<!--------------------------->
<!-- START MAIN AREA -->
<!--------------------------->
<div class="content-wrapper">
          <div class="row">
            <div class="card col-md-6 mx-auto p-4" style="border-radius: 0;">
              <h1 class="text-center mb-0">Add User</h1>
              <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                  <div class="form-group">
                    <label for="username">Username</label>
                    <input class="form-control" id="username" type="text" name="admin_username" placeholder="Enter username" required>
                  </div>

                  <div class="form-group">
                    <label for="password">Password</label>
                    <input class="form-control" id="password" type="password" name="admin_password" placeholder="Enter password" required>
                  </div>

                  <div class="form-group">
                    <label for="role">Select Role</label>
                    <select class="form-control" id="role" name="role_id" required>
                      <option value="">-- Select Role --</option>
                      <?php
                        $roleQuery = "SELECT * FROM roles ORDER BY role_name ASC";
                        $result = $conn->query($roleQuery);
                        if ($result->num_rows > 0) {
                          while ($row = $result->fetch_assoc()) {
                            echo "<option value='{$row['id']}'>{$row['role_name']}</option>";
                          }
                        } else {
                          echo "<option disabled>No roles available</option>";
                        }
                      ?>
                    </select>
                  </div>

                  <div class="form-group d-none">
                    <label for="profile">Profile Picture (optional)</label>
                    <input class="form-control" id="profile" type="file" name="admin_picture" accept="image/*">
                  </div>

                  <button type="submit" class="btn btn-gradient-primary mt-3">Add User</button>
                </form>
              </div>
            </div>
          </div>
</div>
<!--------------------------->
<!-- END MAIN AREA -->
<!--------------------------->

<?php require 'footer.php'; ?>