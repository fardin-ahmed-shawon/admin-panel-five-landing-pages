<?php
$current_page = basename($_SERVER['PHP_SELF']); // Get the current page name
$page_title = 'Edit Product'; // Set the page title
?>
<?php require 'header.php'; ?>

<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Retrieve product ID from query string
if (isset($_GET['id'])) {
    $productId = $_GET['id'];

    // Fetch product details
    $query = "SELECT * FROM product_info WHERE product_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    $stmt->close();

    if (!$product) {
        echo "<script>alert('Product not found!'); window.location.href='viewProduct.php';</script>";
        exit();
    }
} else {
    echo "<script>alert('No product ID provided!'); window.location.href='viewProduct.php';</script>";
    exit();
}

// Fetch product sizes
$product_sizes = [];
$size_query = "SELECT size FROM product_size_list WHERE product_id = ?";
$size_stmt = $conn->prepare($size_query);
$size_stmt->bind_param("i", $productId);
$size_stmt->execute();
$size_result = $size_stmt->get_result();
while ($row = $size_result->fetch_assoc()) {
    $product_sizes[] = $row['size'];
}
$size_stmt->close();

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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_title = $_POST['product_title'];
    $product_purchase_price = $_POST['product_purchase_price'];
    $product_regular_price = $_POST['product_regular_price'];
    $product_price = $_POST['product_price'];
    $product_main_ctg_id = $_POST['product_main_ctg'];
    $product_sub_ctg_id = $_POST['product_sub_ctg'];
    $available_stock = $_POST['available_stock'];
    $product_keyword = $_POST['product_keyword'];
    $product_code = $_POST['product_code'];
    $product_short_description = $_POST['product_short_description'];
    $product_description = $_POST['product_description'];
    $product_type = $_POST['product_type'];
    $product_sizes_post = isset($_POST['product_sizes']) ? $_POST['product_sizes'] : [];

    if ($product_title != $product['product_title']) {
      // Produt title to slug conversion
      $product_slug = make_title_to_slug($product_title);
      // checking that the product slug is unique or not
          $check_slug_query = "SELECT * FROM product_info WHERE product_slug = '$product_slug'";
          $check_slug_result = mysqli_query($conn, $check_slug_query);
          $row_count = mysqli_num_rows($check_slug_result);

          if ($row_count > 0) {
              while ($row_count > 0) {
                  $product_slug .= '-' . rand(1, 1000); // Append a random number to make it unique
                  $check_slug_result = mysqli_query($conn, "SELECT * FROM product_info WHERE product_slug = '$product_slug'");
                  $row_count = mysqli_num_rows($check_slug_result);
              }
          }
      // Produt title to slug conversion end
    } else {
      $product_slug = $product['product_slug']; // Keep existing slug if title hasn't changed
    }

    // Array to store image details
    $images = [
        ['name' => $_FILES['product_img1']['name'], 'tmp_name' => $_FILES['product_img1']['tmp_name']],
        ['name' => $_FILES['product_img2']['name'], 'tmp_name' => $_FILES['product_img2']['tmp_name']],
        ['name' => $_FILES['product_img3']['name'], 'tmp_name' => $_FILES['product_img3']['tmp_name']],
        ['name' => $_FILES['product_img4']['name'], 'tmp_name' => $_FILES['product_img4']['tmp_name']]
    ];

    $uploadSuccess = true;
    $compressedFiles = [];

    foreach ($images as $index => $image) {
        if (!empty($image['name'])) {
            $folder = '../img/' . basename($image['name']);
            $compressed_folder = '../img/compressed_' . basename($image['name']);

            if (move_uploaded_file($image['tmp_name'], $folder)) {
                if (compressImage($folder, $compressed_folder, 60)) {
                    $compressedFiles[] = $compressed_folder; // Track the compressed file
                    unlink($folder); // Remove original after compression
                } else {
                    $uploadSuccess = false;
                    break;
                }
            } else {
                $uploadSuccess = false;
                break;
            }
        } else {
            $compressedFiles[] = $product['product_img' . ($index + 1)]; // Keep existing image if no new upload
        }
    }

    if ($uploadSuccess) {
        // Update query
        $query = "UPDATE product_info SET product_title = ?, product_purchase_price = ?, product_regular_price = ?, product_price = ?, main_ctg_id = ?, sub_ctg_id = ?, available_stock = ?, product_keyword = ?, product_code = ?, product_short_description = ?,  product_description = ?, product_img1 = ?, product_img2 = ?, product_img3 = ?, product_img4 = ?, product_type = ?, product_slug = ? WHERE product_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param(
            "sdddssissssssssssi",
            $product_title,
            $product_purchase_price,
            $product_regular_price,
            $product_price,
            $product_main_ctg_id,
            $product_sub_ctg_id,
            $available_stock,
            $product_keyword,
            $product_code,
            $product_short_description,
            $product_description,
            $compressedFiles[0],
            $compressedFiles[1],
            $compressedFiles[2],
            $compressedFiles[3],
            $product_type,
            $product_slug,
            $productId
        );

        if ($stmt->execute()) {
            // Update product sizes
            $conn->query("DELETE FROM product_size_list WHERE product_id = $productId");
            if (!empty($product_sizes_post)) {
                $size_query = "INSERT INTO product_size_list (product_id, size) VALUES (?, ?)";
                $size_stmt = $conn->prepare($size_query);
                foreach ($product_sizes_post as $size) {
                    $size_stmt->bind_param("is", $productId, $size);
                    $size_stmt->execute();
                }
                $size_stmt->close();
            }
            echo "<script>alert('Product updated successfully!'); window.location.href='viewProduct.php';</script>";
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Failed to upload one or more images.";
    }
}
?>

<style>
  #success-box {
    width: 100%;
    margin: auto;
    text-align: center;
    font-size: 18px;
    padding: 20px;
    color: #0A3622;
    background: #D1E7DD;
  }
</style>

<!--------------------------->
<!-- START MAIN AREA -->
<!--------------------------->
<div class="content-wrapper">
            <div class="page-header">
              <h3 class="page-title">
                <span class="page-title-icon bg-gradient-primary text-white me-2">
                  <i class="mdi mdi-home"></i>
                </span> Product
              </h3>
            </div>
            <br>
            <?php
            if (isset($product_added_status)) {
              echo '<div id="success-box">'.$product_added_status.'</div>';
            }
            ?>
            <div class="row">
              <div class="p-4" style="background: #fff; max-width: 1200px; margin: auto; border-radius: 10px;">
                <br>
                <h1 class="text-center">Update Product</h1>
                <div class="content">
                    <!-- Product Update form -->
                    <form action="" method="post" enctype="multipart/form-data">
                      <div class="user-details full-input-box">
                        <div class="row">
                        <div class="col-md-8">
                        <!-- title -->
                        <div class="input-box">
                          <label class="details">Product Title *</label>
                          <input name="product_title" type="text" placeholder="Enter your product title" value="<?php echo htmlspecialchars($product['product_title']); ?>" required>
                        </div>
                        <!-- purchase price -->
                        <div class="input-box">
                          <label class="details">Purchase Price *</label>
                          <input name="product_purchase_price" type="text" placeholder="Enter product purchase price" value="<?php echo htmlspecialchars($product['product_purchase_price']); ?>" required>
                        </div>
                        <!-- regular price -->
                        <div class="input-box">
                          <label class="details">Regular Price *</label>
                          <input name="product_regular_price" type="text" placeholder="Enter product regular price" value="<?php echo htmlspecialchars($product['product_regular_price']); ?>" required>
                        </div>
                        <!-- Selling price -->
                        <div class="input-box">
                          <label class="details">Selling Price *</label>
                          <input name="product_price" type="text" placeholder="Enter product selling price" value="<?php echo htmlspecialchars($product['product_price']); ?>" required>
                        </div>
                        <!-- Main Category -->
                        <div class="input-box">
                          <label class="details">Choose Main Category *</label>
                          <select id="main_ctg_name" name="product_main_ctg" required>
                            <option value="">Select Main Category</option>
                            <?php
                            $result = mysqli_query($conn, "SELECT main_ctg_id, main_ctg_name FROM main_category");
                            while ($row = mysqli_fetch_assoc($result)) {
                                $selected = $row['main_ctg_id'] == $product['main_ctg_id'] ? 'selected' : '';
                                echo "<option value='{$row['main_ctg_id']}' $selected>{$row['main_ctg_name']}</option>";
                            }
                            ?>
                          </select>
                        </div>
                        <!-- Sub Category -->
                        <div class="input-box">
                          <label class="details">Choose Sub Category</label>
                          <select id="main_sub_name" name="product_sub_ctg" required>
                            <option value="">Select Sub Category</option>
                            <!-- Sub categories will be loaded here -->
                          </select>
                        </div>
                        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                        <script>
                          function loadSubCategories(main_ctg_name, selected_sub_ctg_id = '') {
                              if (main_ctg_name) {
                                  $.get('getSubCategories.php', {main_ctg_name: main_ctg_name}, function(data) {
                                      $('#main_sub_name').html(data);
                                      if (selected_sub_ctg_id) {
                                          $('#main_sub_name').val(selected_sub_ctg_id);
                                      }
                                  });
                              } else {
                                  $('#main_sub_name').html('<option value="">Select Sub Category</option>');
                              }
                          }

                          // On main category change, fetch sub categories
                          $('#main_ctg_name').on('change', function() {
                              var main_ctg_name = $('#main_ctg_name option:selected').text();
                              if ($(this).val() === "") {
                                  $('#main_sub_name').html('<option value="">Select Sub Category</option>');
                                  return;
                              }
                              loadSubCategories(main_ctg_name);
                          });

                          // On page load, fetch sub categories for the selected main category and select the current sub category
                          $(document).ready(function() {
                              var main_ctg_name = $('#main_ctg_name option:selected').text();
                              var selected_sub_ctg_id = "<?php echo htmlspecialchars($product['sub_ctg_id']); ?>";
                              if (main_ctg_name && selected_sub_ctg_id) {
                                  loadSubCategories(main_ctg_name, selected_sub_ctg_id);
                              }
                          });
                        </script>
                        <!-- Total Stock -->
                        <div class="input-box">
                          <label class="details">Total Stock *</label>
                          <input name="available_stock" type="text" placeholder="Enter your total stock amount" value="<?php echo htmlspecialchars($product['available_stock']); ?>" required>
                        </div>

                        <!-- Size Selection -->
                        <div class="input-box">
                          <label class="details">Choose Size (If available)</label>
                          <div class="size-options">
                            <?php
                              $sql = "SELECT id, size_label FROM size_labels ORDER BY id ASC";
                              $result = $conn->query($sql);

                              if ($result && $result->num_rows > 0) {
                                  while ($row = $result->fetch_assoc()) {
                                      $size = htmlspecialchars($row['size_label']);
                                      $checked = in_array($size, $product_sizes) ? 'checked' : '';
                                      echo '
                                        <label class="size-chip">
                                          <input type="checkbox" name="product_sizes[]" value="'.$size.'" '.$checked.'>
                                          <span>'.$size.'</span>
                                        </label>
                                      ';
                                  }
                              } else {
                                  echo "<p>No sizes found.</p>";
                              }
                            ?>
                          </div>
                        </div>

                        <style>
                          .size-options {
                            display: flex;
                            flex-wrap: wrap;
                            gap: 6px;
                            margin-top: 6px;
                          }

                          .size-chip {
                            position: relative;
                            cursor: pointer;
                            user-select: none;
                          }

                          .size-chip input {
                            display: none;
                          }

                          .size-chip span {
                            display: inline-block;
                            padding: 6px 14px;
                            border-radius: 16px;
                            background: #f9fafb;
                            border: 1px solid #d1d5db;
                            font-size: 14px;
                            font-weight: 500;
                            color: #374151;
                            transition: all 0.2s ease;
                          }

                          .size-chip input:checked + span {
                            background: #2563eb;
                            color: #fff;
                            border-color: #2563eb;
                            box-shadow: 0 2px 6px rgba(37, 99, 235, 0.25);
                            transform: scale(1.05);
                          }

                          .size-chip span:hover {
                            background: #e5e7eb;
                          }
                        </style>


                        <!-- keyword -->
                        <div class="input-box">
                          <label class="details">Product Keyword</label>
                          <input name="product_keyword" type="text" placeholder="Enter your product keyword" value="<?php echo htmlspecialchars($product['product_keyword']); ?>">
                        </div>
                        <!-- product code -->
                        <div class="input-box">
                          <label class="details">Product Code</label>
                          <input name="product_code" type="text" placeholder="Enter your product code" value="<?php echo htmlspecialchars($product['product_code']); ?>">
                        </div>
                        <!-- product type -->
                        <div class="input-box">
                          <label class="details">Choose Product Type</label>
                          <select id="product_type" name="product_type">
                            <option value="">Select Product Type</option>
                            <option value='new_arrival' <?php echo ($product['product_type'] == 'new_arrival') ? 'selected' : ''; ?>>New Arrival</option>
                            <option value='top_selling' <?php echo ($product['product_type'] == 'top_selling') ? 'selected' : ''; ?>>Top Selling</option>
                            <option value='trending' <?php echo ($product['product_type'] == 'trending') ? 'selected' : ''; ?>>Trending</option>
                            <option value='top_rated' <?php echo ($product['product_type'] == 'top_rated') ? 'selected' : ''; ?>>Top Rated</option>
                          </select>
                        </div>
                        <!-- -->
                        <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
                        <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
                        <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
                        <!-- short Description -->
                        <div class="form-group m-auto"> 
                          <label class="details">Product Short Description *</label>
                          <textarea id="summernote2" rows="4" name="product_short_description" cols="58" class="mytextarea"><?php echo htmlspecialchars($product['product_short_description'] ?? ''); ?></textarea>
                        </div>
                        <br><br>
                        <script>
                          $('#summernote2').summernote({
                            placeholder: 'Write short description here',
                            tabsize: 2,
                            height: 200
                          });
                        </script>
                        <!-- Description -->
                        <div class="form-group m-auto"> 
                          <label class="details">Product Long Description *</label>
                          <textarea id="summernote" rows="4" name="product_description" cols="58" class="mytextarea"><?php echo htmlspecialchars($product['product_description']); ?></textarea>
                        </div>
                        <br><br>
                        <script>
                          $('#summernote').summernote({
                            placeholder: 'Write long description here',
                            tabsize: 2,
                            height: 300
                          });
                        </script>

                        </div>
                        <div class="col-md-4">
                        <!-- main image -->
                        <div class="img-upload-box">
                          <label class="details">Attach Primary Image *</label>
                          <h4>(1000 X 1000)</h4>
                          <input type="file" name="product_img1" id="file" class="inputfile"/><br>
                        </div>
                        <!-- image 2 -->
                        <div class="img-upload-box">
                          <label class="details">Attach Image 2</label>
                          <h4>(1000 X 1000)</h4>
                          <input type="file" name="product_img2" id="file" class="inputfile"/><br>
                        </div>
                        <!-- image 3 -->
                        <div class="img-upload-box">
                          <label class="details">Attach Image 3</label>
                          <h4>(1000 X 1000)</h4>
                          <input type="file" name="product_img3" id="file" class="inputfile"/><br>
                        </div>
                        <!-- image 4 -->
                        <div class="img-upload-box">
                          <label class="details">Attach Image 4</label>
                          <h4>(1000 X 1000)</h4>
                          <input type="file" name="product_img4" id="file" class="inputfile"/><br>
                        </div>
                        </div>
                        </div>
                      </div>
                      <!-- Submit button -->
                      <div class="button">
                        <input type="submit" value="Update Product">
                      </div>
                    </form>
                    <!-- <div class="button">
                      <a href="viewProduct.php" class="w-100 btn btn-danger">
                        Cancel
                      </a>
                    </div> -->
                </div>
              </div>
            </div>
</div>
<!--------------------------->
<!-- END MAIN AREA -->
<!--------------------------->

<?php require 'footer.php'; ?>