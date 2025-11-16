<?php
$current_page = basename($_SERVER['PHP_SELF']); // Get the current page name
$page_title = 'Privacy & Policy'; // Set the page title
?>
<?php require 'header.php'; ?>

<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    // Get the description from the form
    $description = mysqli_real_escape_string($conn, $_POST['description']);

    // Check if the description is not empty
    if (!empty($description)) {
        // Insert or update the about_us content in the footer_info table
        $query = "INSERT INTO `footer_info` (`id`, `about_us`, `contact_us`, `faq`, `terms_of_use`, `privacy_policy`, `shipping_delivery`)
                  VALUES (1, '', '', '', '', '$description', '')
                  ON DUPLICATE KEY UPDATE
                  `privacy_policy` = VALUES(`privacy_policy`);";

        if (mysqli_query($conn, $query)) {
            $product_added_status = "Privacy Policy content saved successfully!";
        } else {
            $product_added_status = "Error: " . mysqli_error($conn);
        }
    } else {
        $product_added_status = "Please fill in the Privacy Policy content.";
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
                </span> Privacy & Policy
              </h3>
            </div>
            <br>
            <?php
            if (isset($product_added_status)) {
              echo '<div id="success-box">'.$product_added_status.'</div>';
            }
            ?>
            <div class="row">
              <div class="form-container">
                <h1 class="text-center">Write Privacy & Policy</h1>
                <div class="content">
                    <!-- Product Add form -->
                    <form action="" method="post" enctype="multipart/form-data">
                      <div class="user-details full-input-box">
                        
                        <!-- Description -->

                        <!--  Script For Text Editor -->
                        <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
                        <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
                        <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>

                        <?php
                            $query = "SELECT privacy_policy FROM footer_info LIMIT 1"; // Adjust LIMIT as needed
                            $result = mysqli_query($conn, $query);

                            if ($result && mysqli_num_rows($result) > 0) {
                                $row = mysqli_fetch_assoc($result);
                                $privacy_policy_content = $row['privacy_policy'];
                            } else {
                                $privacy_policy_content = "";
                            }
                        ?>

                        <div class="form-group m-auto"> 
                          <span class="details">Write Privacy & Policy *</span>
                          <textarea id="summernote" rows="4" name="description" cols="58" class="mytextarea">
                            <?php 
                                echo htmlspecialchars($privacy_policy_content);
                            ?>
                          </textarea>
                        </div>
                        <br><br>

                          <script>
                            $('#summernote').summernote({
                              placeholder: 'Design your website',
                              tabsize: 2,
                              height: 400
                            });
                            
                          </script>

                      </div>
                      <!-- Submit button -->
                      <div class="button">
                        <input type="submit" value="Save Changes" name="submit" class="btn btn-primary">
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