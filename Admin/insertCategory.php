<?php
$current_page = basename($_SERVER['PHP_SELF']); // Get the current page name
$page_title = 'Add Category'; // Set the page title
?>
<?php require 'header.php'; ?>

<?php

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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['insert_main_category'])) {
        $main_ctg_name = $_POST['main_ctg_name'];
        $main_ctg_des = $_POST['main_ctg_des'];


        // category title to slug conversion
        $category_slug = make_title_to_slug($main_ctg_name);
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


        // Handle Image Upload and Compression
        $uploadSuccess = false;
        $compressedImagePath = null;

        if (!empty($_FILES['main_ctg_img']['name'])) {
            $originalPath = '../img/' . basename($_FILES['main_ctg_img']['name']);
            $compressedPath = '../img/compressed_' . basename($_FILES['main_ctg_img']['name']);

            if (move_uploaded_file($_FILES['main_ctg_img']['tmp_name'], $originalPath)) {
                if (compressImage($originalPath, $compressedPath, 60)) {
                    $compressedImagePath = $compressedPath;
                    unlink($originalPath); // Delete the original image
                    $uploadSuccess = true;
                }
            }
        }

        if ($uploadSuccess) {
            // Insert into Database
            $stmt = $conn->prepare("INSERT INTO main_category (main_ctg_name, main_ctg_des, main_ctg_img, main_ctg_slug) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $main_ctg_name, $main_ctg_des, $compressedImagePath, $category_slug);

            if ($stmt->execute()) {
                $category_added_status = "Main Category Added Successfully!";
            } else {
                echo "Error: " . $stmt->error;
            }
            $stmt->close();
        } else {
            echo "Failed to upload or compress the image.";
        }
    }

    if (isset($_POST['insert_sub_category'])) {
        $sub_CTG_name = $_POST['sub_ctg_name'];
        $main_CTG_name = $_POST['main_CTG_name'];


        // category title to slug conversion
        $category_slug = make_title_to_slug($sub_CTG_name);
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

        // Validate input
        if (empty($main_CTG_name) || empty($sub_CTG_name)) {
            echo "Both main category and subcategory names are required.";
        } else {
            // Prepare and bind
            $stmt = $conn->prepare("INSERT INTO sub_category (sub_ctg_name, main_ctg_name, sub_ctg_slug) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $sub_CTG_name, $main_CTG_name, $category_slug);

            // Execute the statement
            if ($stmt->execute()) {
                $category_added_status = "Sub Category Added Successfully!";
            } else {
                echo "Error: " . $stmt->error;
            }

            // Close the statement
            $stmt->close();
        }
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
                  <i class="mdi mdi-home"></i>
                </span> Product Categories
              </h3>
            </div>
            <br>
            <?php
            if (isset($category_added_status)) {
              echo '<div id="success-box">'.$category_added_status.'</div>';
            }
            ?>
            <div class="row">
              <div class="form-container">
                <h1 class="text-center">Add Main Category</h1>
                <div class="content">
                    <!-- Main Category Add form -->
                    <form action="#" method="post" enctype="multipart/form-data">
                      <div class="user-details full-input-box">
                        <!-- title -->
                        <div class="input-box">
                          <span class="details">Main Category Name</span>
                          <input name="main_ctg_name" type="text" placeholder="Enter your category name" required>
                        </div>
                        <!-- Description -->
                        <div class="input-box">
                          <span class="details">Main Category Description</span>
                          <input name="main_ctg_des" type="text" placeholder="Enter your category description" required>
                        </div>
                        <!-- main image -->
                        <div>
                          <span class="details">Attach Main Category Poster Image</span>
                          <input name="main_ctg_img" type="file" id="file" class="inputfile"/><br>
                        </div>
                      </div>
                      <!-- Submit button -->
                      <div class="button">
                        <input name="insert_main_category" type="submit" value="Add Main Category">
                      </div>
                    </form>
                </div>
                <br><hr>
                <div class="content">
                  <h1 class="text-center">Add Sub Category</h1>
                    <!-- Sub Category form -->
                    <form action="#" method="post">
                        <div class="user-details full-input-box">
                            <!-- Choose Main Category -->
                            <div class="input-box">
                                <span class="details">Choose Main Category</span>
                                <select id="main_CTG_name" name="main_CTG_name" required>
                                    <option value="">Select Main Category</option>
                                    <?php
                                    // Fetch main categories from the database
                                    $result = mysqli_query($conn, "SELECT main_ctg_name FROM main_category");
                                    while ($row = mysqli_fetch_assoc($result)) {
                                      $category_name = htmlspecialchars($row['main_ctg_name'], ENT_QUOTES, 'UTF-8');
                                      echo "<option value='$category_name'>$category_name</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <!-- title -->
                            <div class="input-box">
                                <span class="details">Enter Sub Category Name</span>
                                <input name="sub_ctg_name" type="text" placeholder="Enter your category name" required>
                            </div>
                        </div>
                        <!-- Submit button -->
                        <div class="button">
                            <input name="insert_sub_category" type="submit" value="Add Sub Category">
                        </div>
                    </form>
                </div>
              </div>
            </div>
</div>
<!--------------------------->
<!-- END MAIN AREA -->
<!--------------------------->

<?php require 'footer.php'; ?>