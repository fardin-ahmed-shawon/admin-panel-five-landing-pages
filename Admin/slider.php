<?php
$current_page = basename($_SERVER['PHP_SELF']); // Get the current page name
$page_title = 'Slider'; // Set the page title
?>
<?php require 'header.php'; ?>

<?php

function compressImage($source, $destination, $quality = 95) {
  $imgInfo = getimagesize($source);
  if (!$imgInfo) return false;

  $mime = $imgInfo['mime'];
  switch ($mime) {
      case 'image/jpeg': $image = imagecreatefromjpeg($source); break;
      case 'image/png': $image = imagecreatefrompng($source); break;
      case 'image/webp': $image = imagecreatefromwebp($source); break;
      default: return false;
  }

  // Save Compressed Image without resizing
  switch ($mime) {
      case 'image/jpeg': imagejpeg($image, $destination, $quality); break;
      case 'image/png': imagepng($image, $destination, round($quality / 10)); break;
      case 'image/webp': imagewebp($image, $destination, $quality); break;
  }
  imagedestroy($image);
  return true;
}

// Slider Image Add
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if a file was uploaded
    if (isset($_FILES['slider_img']) && $_FILES['slider_img']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['slider_img']['tmp_name'];
        $fileName = $_FILES['slider_img']['name'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        // Allowed file extensions
        $allowedfileExtensions = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($fileExtension, $allowedfileExtensions)) {
            // Directory where the file will be saved
            $uploadFileDir = '../img/';
            $dest_path = $uploadFileDir . $fileName;
            $compressed_path = $uploadFileDir . 'compressed_' . $fileName;

            // Move the file to the uploads directory
            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                // Compress the image
                if (compressImage($dest_path, $compressed_path, 60)) {
                    unlink($dest_path); // Remove the original file after compression

                    // Insert the compressed file name into the database
                    $query = "INSERT INTO slider (slider_img) VALUES (?)";
                    $stmt = mysqli_prepare($conn, $query);
                    mysqli_stmt_bind_param($stmt, "s", $compressed_path);

                    if (mysqli_stmt_execute($stmt)) {
                        $suc_message = "Image uploaded and Saved Successfully.";
                    } else {
                        $err_message = "Database error: Unable to save the image.";
                    }

                    mysqli_stmt_close($stmt);
                } else {
                    $_SESSION['message'] = "Image compression failed.";
                }
            } else {
                $_SESSION['message'] = "There was an error moving the uploaded file.";
            }
        } else {
            $_SESSION['message'] = "Upload failed. Allowed file types: " . implode(", ", $allowedfileExtensions);
        }
    } else {
        $_SESSION['message'] = "No file uploaded or there was an upload error.";
    }
}

// Fetch slider images
$query = "SELECT slider_id, slider_img FROM slider";
$result = mysqli_query($conn, $query);
?>

<style>
      #success-box {
        margin: auto;
        text-align: center;
        font-size: 18px;
        padding: 20px;
        color: #0A3622;
        background: #D1E7DD;
      }
      #unsuccess-box {
        margin: auto;
        text-align: center;
        font-size: 18px;
        padding: 20px;
        color: #842029;
        background: #F8D7DA;
      }
      .slider-item img {
        width: 100%;
      }
      .slider-item button {
        margin-top: 10px;
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
                </span> Slider
              </h3>
            </div>
            <br>

            <!-- Display Existing Slider Images -->
            <div class="row">
              <div class="col-md-7">
                <h1>Existing Slider Images</h1>
                <?php
                  if (isset($_GET['suc_msg'])) {
                    echo '<div id="success-box">'.$_GET['suc_msg'].'</div>';
                  } else if (isset($_GET['unsuc_msg'])) {
                    echo '<div id="unsuccess-box">'.$_GET['unsuc_msg'].'</div>';
                  } else if (isset($suc_message)) {
                    echo '<div id="success-box">'.$suc_message.'</div>';
                  } else if (isset($err_message)) {
                    echo '<div id="unsuccess-box">'.$err_message.'</div>';
                  }
                ?>
                <div class="card p-3 rounded-0">
                    <div class="slider-images">
                      <?php
                      if (mysqli_num_rows($result) > 0) {
                          $count = 1;
                          while ($row = mysqli_fetch_assoc($result)) {
                              echo '<div class="slider-item">';
                              echo '<h4>Image No: ' . $count . '</h4>';
                              echo '<img src="' . htmlspecialchars($row['slider_img']) . '" alt="Slider Image" class="slider-img">';
                              echo '<br><a href="#"><button onclick="confirmDelete('.$row['slider_id'].')" class="btn btn-danger">Delete</button></a>';
                              echo '</div>';
                              echo '<br>';
                              echo '<hr>';
                              $count++;
                          }
                      } else {
                          echo '<h4>No slider images found.</h4>';
                      }
                      ?>
                    </div>
                </div>
              </div>
              <div class="col-md-5">
                  <!-- Add New Slider Image -->
                  <form action="" method="POST" enctype="multipart/form-data">
                    <div class="row">
                      <h1>Add Slider Image</h1>
                      <div>
                          <label class="custum-file-upload" for="file">
                            <div class="icon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="" viewBox="0 0 24 24"><g stroke-width="0" id="SVGRepo_bgCarrier"></g><g stroke-linejoin="round" stroke-linecap="round" id="SVGRepo_tracerCarrier"></g><g id="SVGRepo_iconCarrier"> <path fill="" d="M10 1C9.73478 1 9.48043 1.10536 9.29289 1.29289L3.29289 7.29289C3.10536 7.48043 3 7.73478 3 8V20C3 21.6569 4.34315 23 6 23H7C7.55228 23 8 22.5523 8 22C8 21.4477 7.55228 21 7 21H6C5.44772 21 5 20.5523 5 20V9H10C10.5523 9 11 8.55228 11 8V3H18C18.5523 3 19 3.44772 19 4V9C19 9.55228 19.4477 10 20 10C20.5523 10 21 9.55228 21 9V4C21 2.34315 19.6569 1 18 1H10ZM9 7H6.41421L9 4.41421V7ZM14 15.5C14 14.1193 15.1193 13 16.5 13C17.8807 13 19 14.1193 19 15.5V16V17H20C21.1046 17 22 17.8954 22 19C22 20.1046 21.1046 21 20 21H13C11.8954 21 11 20.1046 11 19C11 17.8954 11.8954 17 13 17H14V16V15.5ZM16.5 11C14.142 11 12.2076 12.8136 12.0156 15.122C10.2825 15.5606 9 17.1305 9 19C9 21.2091 10.7909 23 13 23H20C22.2091 23 24 21.2091 24 19C24 17.1305 22.7175 15.5606 20.9844 15.122C20.7924 12.8136 18.858 11 16.5 11Z" clip-rule="evenodd" fill-rule="evenodd"></path> </g></svg>
                            </div>
                            <div class="text">
                              <span>Click to upload image</span>
                              </div>
                              <input type="file" id="file" name="slider_img" required>
                            </label>
                      </div>
                    </div>
                    <br>
                    <input class="btn btn-dark" type="submit" value="Submit">
                  </form>
              </div>
            </div>
            <br>
</div>
<!--------------------------->
<!-- END MAIN AREA -->
<!--------------------------->
<script>
      function confirmDelete(slider_id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = `deleteSlider.php?si=${slider_id}`;
            }
        });
      }
</script>
<?php require 'footer.php'; ?>