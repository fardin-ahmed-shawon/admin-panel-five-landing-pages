<?php
$current_page = basename($_SERVER['PHP_SELF']); // Get the current page name
$page_title = 'Edit Category'; // Set the page title
?>
<?php require 'header.php'; ?>

<?php
$type = $_GET['type'] ?? '';
$id = $_GET['id'] ?? '';

if ($type === 'main') {
    $stmt = $conn->prepare("SELECT * FROM main_category WHERE main_ctg_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $category = $stmt->get_result()->fetch_assoc();
    $stmt->close();
} elseif ($type === 'sub') {
    $stmt = $conn->prepare("SELECT * FROM sub_category WHERE sub_ctg_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $category = $stmt->get_result()->fetch_assoc();
    $stmt->close();
} else {
    die("Invalid category type.");
}

// Image Compression Function
function compressImage($source, $destination, $quality = 75) {
    $imgInfo = getimagesize($source);
    if (!$imgInfo) return false;

    $mime = $imgInfo['mime'];
    switch ($mime) {
        case 'image/jpeg': $image = imagecreatefromjpeg($source); break;
        case 'image/png': $image = imagecreatefrompng($source); break;
        case 'image/webp': $image = imagecreatefromwebp($source); break;
        default: return false;
    }

    // Resize Image to 800x800 (Square Shape)
    $newWidth = 800;
    $newHeight = 800;
    $newImage = imagecreatetruecolor($newWidth, $newHeight);
    imagecopyresampled($newImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, imagesx($image), imagesy($image));
    $image = $newImage;

    // Save Compressed Image
    switch ($mime) {
        case 'image/jpeg': imagejpeg($image, $destination, $quality); break;
        case 'image/png': imagepng($image, $destination, round($quality / 10)); break;
        case 'image/webp': imagewebp($image, $destination, $quality); break;
    }
    imagedestroy($image);
    return true;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    if ($type === 'main') {
        // Save old name before update
        $old_name = $category['main_ctg_name'];


        // category title to slug conversion
        $category_slug = make_title_to_slug($name);
        // checking that the product slug is unique or not
          $check_slug_query = "SELECT * FROM main_category WHERE main_ctg_slug = '$category_slug'";
          $check_slug_result = mysqli_query($conn, $check_slug_query);
          $row_count = mysqli_num_rows($check_slug_result);

          if ($row_count > 0) {
              while ($row_count > 0) {
                  $category_slug .= '-' . rand(1, 1000); // Append a random number to make it unique
                  $check_slug_result = mysqli_query($conn, "SELECT * FROM main_category WHERE main_ctg_slug = '$category_slug'");
                  $row_count = mysqli_num_rows($check_slug_result);
              }
          }
        // category title to slug conversion end


        // Handle image upload if a new image is provided
        $img_sql = "";
        if (!empty($_FILES['main_ctg_img']['name'])) {
            $originalPath = '../img/' . basename($_FILES['main_ctg_img']['name']);
            $compressedPath = '../img/compressed_' . basename($_FILES['main_ctg_img']['name']);

            if (move_uploaded_file($_FILES['main_ctg_img']['tmp_name'], $originalPath)) {
                if (compressImage($originalPath, $compressedPath, 60)) {
                    unlink($originalPath); // Delete the original image
                    $img_sql = ", main_ctg_img = ?";
                    $img_path = $compressedPath;
                }
            }
        }

        // Update main_category name (and image if uploaded)
        if ($img_sql) {
            $stmt = $conn->prepare("UPDATE main_category SET main_ctg_name = ?, main_ctg_slug = ? $img_sql WHERE main_ctg_id = ?");
            $stmt->bind_param("sssi", $name, $category_slug, $img_path, $id);
        } else {
            $stmt = $conn->prepare("UPDATE main_category SET main_ctg_name = ?, main_ctg_slug = ? WHERE main_ctg_id = ?");
            $stmt->bind_param("ssi", $name, $category_slug, $id);
        }
        $stmt->execute();
        $stmt->close();

        // Update all sub_category rows that reference the old main_ctg_name
        $stmt = $conn->prepare("UPDATE sub_category SET main_ctg_name = ? WHERE main_ctg_name = ?");
        $stmt->bind_param("ss", $name, $old_name);
        $stmt->execute();
        $stmt->close();
    } else {

        // category title to slug conversion
        $category_slug = make_title_to_slug($name);
        // checking that the product slug is unique or not
          $check_slug_query = "SELECT * FROM sub_category WHERE sub_ctg_slug = '$category_slug'";
          $check_slug_result = mysqli_query($conn, $check_slug_query);
          $row_count = mysqli_num_rows($check_slug_result);

          if ($row_count > 0) {
              while ($row_count > 0) {
                  $category_slug .= '-' . rand(1, 1000); // Append a random number to make it unique
                  $check_slug_result = mysqli_query($conn, "SELECT * FROM sub_category WHERE sub_ctg_slug = '$category_slug'");
                  $row_count = mysqli_num_rows($check_slug_result);
              }
          }
        // category title to slug conversion end   

        $stmt = $conn->prepare("UPDATE sub_category SET sub_ctg_name = ?, sub_ctg_slug = ? WHERE sub_ctg_id = ?");
        $stmt->bind_param("ssi", $name, $category_slug, $id);
        $stmt->execute();
        $stmt->close();
    }
    header("Location: viewCategory.php");
    exit();
}
?>

<!--------------------------->
<!-- START MAIN AREA -->
<!--------------------------->
<div class="content-wrapper">
            <h2>Edit <?php echo ucfirst($type); ?> Category</h2>
            <form method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Name:</label>
                    <input type="text" name="name" value="<?php echo htmlspecialchars($type === 'main' ? $category['main_ctg_name'] : $category['sub_ctg_name']); ?>" required class="form-control">
                </div>
                <?php if ($type === 'main'): ?>
                <div class="form-group">
                    <label>Current Image:</label><br>
                    <?php if (!empty($category['main_ctg_img'])): ?>
                        <img src="<?php echo htmlspecialchars($category['main_ctg_img']); ?>" alt="Category Image" style="max-width:120px;max-height:120px;">
                    <?php else: ?>
                        <span>No image uploaded.</span>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label>Change Image:</label>
                    <input type="file" name="main_ctg_img" accept="image/*" class="form-control">
                    <small class="form-text text-muted">Leave blank to keep current image.</small>
                </div>
                <?php endif; ?>
                <br>
                <button type="submit" class="btn btn-primary">Update</button>
                <a href="viewCategory.php" class="btn btn-secondary">Cancel</a>
            </form>
</div>
<!--------------------------->
<!-- END MAIN AREA -->
<!--------------------------->

<?php require 'footer.php'; ?>