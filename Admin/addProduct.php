<?php
$current_page = basename($_SERVER['PHP_SELF']); // Get the current page name
$page_title = 'Add Product'; // Set the page title
?>
<?php require 'header.php'; ?>

<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

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
    $product_title = $_POST['product_title'];
    $product_purchase_price = $_POST['product_purchase_price'];
    $product_regular_price = $_POST['product_regular_price'];
    $product_price = $_POST['product_price'];
    $product_main_ctg_id = $_POST['product_main_ctg'];
    $product_sub_ctg_id = $_POST['product_sub_ctg'];
    $available_stock = $_POST['available_stock'];
    $product_keyword = $_POST['product_keyword'];
    $product_short_description = $_POST['product_short_description'];
    $product_description = $_POST['product_description'];
    $product_code = $_POST['product_code'];
    $product_type = $_POST['product_type'];
    $product_sizes = isset($_POST['product_sizes']) ? $_POST['product_sizes'] : [];


    // Produt title to slug conversion
    $product_slug = make_title_to_slug($product_title);
    // checking that the product slug is unique or not
        $check_slug_query = "SELECT * FROM product_info WHERE product_slug = '$product_slug'";
        $check_slug_result = mysqli_query($conn, $check_slug_query);
        $row_count = mysqli_num_rows($check_slug_result);

        if ($row_count > 0) {
            while ($row_count > 0) {
                $product_slug .= '-' . rand(1, 1000); // Append a random number to make it unique
                $check_slug_result = mysqli_query($conn, "SELECT * FROM products WHERE product_slug = '$product_slug'");
                $row_count = mysqli_num_rows($check_slug_result);
            }
        }
    // Produt title to slug conversion end


    // Array to store image details
    $images = [
        ['name' => $_FILES['product_img1']['name'], 'tmp_name' => $_FILES['product_img1']['tmp_name']],
        ['name' => $_FILES['product_img2']['name'], 'tmp_name' => $_FILES['product_img2']['tmp_name']],
        ['name' => $_FILES['product_img3']['name'], 'tmp_name' => $_FILES['product_img3']['tmp_name']],
        ['name' => $_FILES['product_img4']['name'], 'tmp_name' => $_FILES['product_img4']['tmp_name']]
    ];

    $uploadSuccess = true;
    $originalFiles = [];
    $compressedFiles = [];

    foreach ($images as $index => $image) {
        if (!empty($image['name'])) {
            $folder = '../img/' . basename($image['name']);
            $compressed_folder = '../img/compressed_' . basename($image['name']);

            if (move_uploaded_file($image['tmp_name'], $folder)) {
                if (compressImage($folder, $compressed_folder, 60)) {
                    $originalFiles[] = $folder; // Track the original file
                    $compressedFiles[] = $compressed_folder; // Track the compressed file
                } else {
                    $uploadSuccess = false;
                    break;
                }
            } else {
                $uploadSuccess = false;
                break;
            }
        } else {
            $compressedFiles[] = null; // No file uploaded for this index
        }
    }

    if ($uploadSuccess) {
        // Prepare the SQL query for product_info
        $query = "INSERT INTO product_info (product_title, product_purchase_price, product_regular_price, product_price, main_ctg_id, sub_ctg_id, available_stock, product_keyword, product_code, product_short_description, product_description, product_img1, product_img2, product_img3, product_img4, product_type, product_slug) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sdddssissssssssss", $product_title, $product_purchase_price, $product_regular_price, $product_price, $product_main_ctg_id, $product_sub_ctg_id, $available_stock, $product_keyword, $product_code, $product_short_description, $product_description, $compressedFiles[0], $compressedFiles[1], $compressedFiles[2], $compressedFiles[3], $product_type, $product_slug);

        // Execute the query
        if ($stmt->execute()) {
            $product_id = $stmt->insert_id; // Get the inserted product ID

            // Insert sizes into product_size_list
            $size_query = "INSERT INTO product_size_list (product_id, size) VALUES (?, ?)";
            $size_stmt = $conn->prepare($size_query);

            foreach ($product_sizes as $size) {
                $size_stmt->bind_param("is", $product_id, $size);
                $size_stmt->execute();
            }

            $product_added_status = "Product Added Successfully!";
            // Delete the original images after successful database entry
            foreach ($originalFiles as $file) {
                unlink($file);
            }
        } else {
            echo "Error: " . $stmt->error;
        }

    } else {
        echo "Failed to upload one or more images.";
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
                </span> Product
              </h3>
            </div>
            <div class="row">
              <div class="p-4" style="background: #fff; max-width: 1200px; margin: auto; border-radius: 10px;">
                <div class="content">
                    <?php
                    if (isset($product_added_status)) {
                        echo "<script>
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: '".addslashes($product_added_status)."',
                                confirmButtonColor: '#3085d6',
                                confirmButtonText: 'OK'
                            });
                        </script>";
                    }
                    ?>
                    <br>
                   <h1 class="text-center">Add Product</h1>
                    <!-- Product Add form -->
                    <form action="" method="post" enctype="multipart/form-data">
                      <div class="user-details full-input-box">
                      <div class="row">
                      <div class="col-md-8">
                        <!-- title -->
                        <div class="input-box">
                          <label class="details">Product Title *</label>
                          <input name="product_title" type="text" placeholder="Enter your product title" required>
                        </div>
                        <!-- purchase price -->
                        <div class="input-box">
                          <label class="details">Purchase Price *</label>
                          <input name="product_purchase_price" type="text" placeholder="Enter product purchase price" required>
                        </div>
                        <!-- regular price -->
                        <div class="input-box">
                          <label class="details">Regular Price *</label>
                          <input name="product_regular_price" type="text" placeholder="Enter product regular price" required>
                        </div>
                        <!-- Selling price -->
                        <div class="input-box">
                          <label class="details">Selling Price *</label>
                          <input name="product_price" type="text" placeholder="Enter product selling price" required>
                        </div>

                        <!-- Main Category -->
                        <div class="input-box">
                          <label class="details">Choose Main Category *</label>
                          <select id="main_ctg_name" name="product_main_ctg" required>
                            <option value="">Select Main Category</option>
                            <?php
                              $result = mysqli_query($conn, "SELECT main_ctg_id, main_ctg_name FROM main_category");
                              while ($row = mysqli_fetch_assoc($result)) {
                                // Escape the category name to prevent XSS
                                $category_id = htmlspecialchars($row['main_ctg_id'], ENT_QUOTES, 'UTF-8');
                                $category_name = htmlspecialchars($row['main_ctg_name'], ENT_QUOTES, 'UTF-8');
                                echo "<option value='$category_id'>$category_name</option>";
                              }
                            ?>
                          </select>
                        </div>
                        
                        <!-- Sub Category -->
                        <div class="input-box">
                          <label class="details">Choose Sub Category *</label>
                          <select id="main_sub_name" name="product_sub_ctg" required>
                            <option value="">Select Sub Category</option>
                            <!-- Sub categories will be loaded here -->
                          </select>
                        </div>
                        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                        <script>
                          $('#main_ctg_name').on('change', function() {
                              // Get the selected option's text (the visible name)
                              var main_ctg_name = $('#main_ctg_name option:selected').text();
                              // If the first option is selected, treat as empty
                              if ($(this).val() === "") {
                                  $('#main_sub_name').html('<option value="">Select Sub Category</option>');
                                  return;
                              }
                              $.get('getSubCategories.php', {main_ctg_name: main_ctg_name}, function(data) {
                                  $('#main_sub_name').html(data);
                              });
                          });
                        </script>

                        <!-- Total Stock -->
                        <div class="input-box">
                          <label class="details">Total Stock *</label>
                          <input name="available_stock" type="text" placeholder="Enter your total stock amount" required>
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
                                      echo '
                                        <label class="size-chip">
                                          <input type="checkbox" name="product_sizes[]" value="' . $size . '">
                                          <span>' . $size . '</span>
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
                            gap: 8px;
                            margin-top: 8px;
                          }

                          .size-chip {
                            position: relative;
                            cursor: pointer;
                          }

                          .size-chip input {
                            display: none; /* hide the actual checkbox */
                          }

                          .size-chip span {
                            display: inline-block;
                            padding: 6px 12px;
                            border-radius: 20px;
                            background: #f3f4f6;
                            border: 1px solid #d1d5db;
                            font-size: 14px;
                            font-weight: 500;
                            transition: all 0.2s ease;
                          }

                          .size-chip input:checked + span {
                            background: #2563eb; /* blue highlight */
                            color: #fff;
                            border-color: #2563eb;
                            box-shadow: 0 2px 6px rgba(37, 99, 235, 0.3);
                          }

                          .size-chip span:hover {
                            background: #e5e7eb;
                          }
                        </style>


                        <!-- keyword -->
                        <div class="input-box">
                          <label class="details">Product Keyword</label>
                          <input name="product_keyword" type="text" placeholder="Enter your product keyword">
                        </div>
                        <!-- product code -->
                        <div class="input-box">
                          <label class="details">Product Code</label>
                          <input name="product_code" type="text" placeholder="Enter your product code">
                        </div>
                        <!-- product type -->
                        <div class="input-box">
                          <label class="details">Choose Product Type</label>
                          <select id="product_type" name="product_type">
                            <option value="">Select Product Type</option>
                            <option value='new_arrival'>New Arrival</option>
                            <option value='top_selling'>Top Selling</option>
                            <option value='trending'>Trending</option>
                            <option value='top_rated'>Top Rated</option>
                          </select>
                        </div>
                        <!-- -->
                        <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
                        <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
                        <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
                        <!-- short description -->
                        <div class="form-group m-auto"> 
                          <label class="details">Product Short Description *</label>
                          <textarea id="summernote1" rows="4" name="product_short_description" cols="58" class="mytextarea"> </textarea>
                        </div>
                        <br><br>
                        <script>
                          $('#summernote1').summernote({
                            placeholder: 'Write short description here',
                            tabsize: 2,
                            height: 200
                          });
                        </script>
                        <!-- Description -->
                        <div class="form-group m-auto"> 
                          <label class="details">Product Long Description *</label>
                          <textarea id="summernote" rows="4" name="product_description" cols="58" class="mytextarea"> </textarea>
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
                        <!-- ===== Font and SVG Support (if not included globally) ===== -->

                        <!-- main image -->
                        <div class="img-upload-box">
                          <label class="details">Attach Primary Image *</label>
                          <h4>(1000 X 1000)</h4>
                          <input type="file" name="product_img1" id="file" class="inputfile" required/><br>
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
                        <input type="submit" value="Add Product">
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