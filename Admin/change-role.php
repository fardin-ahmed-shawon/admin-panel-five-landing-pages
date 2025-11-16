<?php
$current_page = basename($_SERVER['PHP_SELF']); // Get the current page name
$page_title = 'Edit User'; // Set the page title

require 'header.php';

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
    $userId = intval($_GET['id']);

    $stmt = $conn->prepare("SELECT admin_username, role_id, admin_picture FROM admin_info WHERE admin_id = ?");

    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
    } else {
        echo "<script>alert('User not found!'); window.location.href='user-list.php';</script>";
        exit();
    }
    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['user_id'])) {
    $userId = intval($_POST['user_id']);
    $username = $_POST['admin_username'];
    $role = $_POST['role_id'];
    $pictureName = $user['admin_picture'] ?? 'NULL'; // Keep the old picture by default

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

    $stmt = $conn->prepare("UPDATE admin_info SET admin_username = ?, role_id = ?, admin_picture = ? WHERE admin_id = ?");
    $stmt->bind_param("sssi", $username, $role, $pictureName, $userId);

    if ($stmt->execute()) {
        echo "<script>alert('User details updated successfully!'); window.location.href='view-users.php';</script>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}
?>

<div class="content-wrapper">
    <div class="row">
        <div class="card col-md-6 mx-auto p-4" style="border-radius: 0;">
            <div class="card-body">
                <h1 class="text-center mb-4">Change Role</h1>
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="user_id" value="<?php echo $userId; ?>">

                    <div class="form-group">
                        <label for="username">Username</label>
                        <input class="form-control" id="username" type="text" name="admin_username" value="<?php echo htmlspecialchars($user['admin_username']); ?>" required readonly>
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
                                    $selected = ($row['id'] == $user['role_id']) ? 'selected' : '';
                                    echo "<option value='{$row['id']}' $selected>{$row['role_name']}</option>";
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
                        <?php if ($user['admin_picture']): ?>
                            <img src="uploads/<?php echo htmlspecialchars($user['admin_picture']); ?>" alt="Current Profile Picture" style="width: 100px; margin-top: 10px;">
                        <?php endif; ?>
                    </div>

                    <button type="submit" class="btn btn-gradient-primary mt-3">Update User</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require 'footer.php'; ?>