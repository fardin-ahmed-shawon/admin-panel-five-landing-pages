<?php
$current_page = basename($_SERVER['PHP_SELF']); // Get the current page name
$page_title = 'Delete Category'; // Set the page title
?>
<?php require 'header.php'; ?>

<?php
// Handle delete request
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  if (isset($_POST['delete_main_category'])) {
      $mainCategoryId = $_POST['main_ctg_id'];
      $deleteMainCategoryQuery = "DELETE FROM main_category WHERE main_ctg_id = $mainCategoryId";
      $conn->query($deleteMainCategoryQuery);
  } elseif (isset($_POST['delete_sub_category'])) {
      $subCategoryId = $_POST['sub_ctg_id'];
      $deleteSubCategoryQuery = "DELETE FROM sub_category WHERE sub_ctg_id = $subCategoryId";
      $conn->query($deleteSubCategoryQuery);
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
            <div class="row">
              <h1>Delete Category</h1>
              <!-- Table Area -->
              <div style="overflow-y: auto;">
                <table class="table table-bordered">
                  <?php
                    // Fetch main categories
                    $mainCategoriesQuery = "SELECT * FROM main_category";
                    $mainCategoriesResult = $conn->query($mainCategoriesQuery);

                    if ($mainCategoriesResult->num_rows > 0) {
                        echo '<tbody>';
                        while ($mainCategory = $mainCategoriesResult->fetch_assoc()) {
                            $mainCategoryId = $mainCategory['main_ctg_id'];
                            $mainCategoryName = $mainCategory['main_ctg_name'];

                            // Escape the main category name to prevent SQL syntax errors
                            $escapedMainCategoryName = $conn->real_escape_string($mainCategoryName);

                            // Fetch sub categories for the current main category
                            $subCategoriesQuery = "SELECT * FROM sub_category WHERE main_ctg_name = '$escapedMainCategoryName'";
                            $subCategoriesResult = $conn->query($subCategoriesQuery);

                            $subCategoriesCount = $subCategoriesResult->num_rows;
                            echo '<tr>';
                            echo '<td rowspan="' . ($subCategoriesCount + 1) . '">' . $mainCategoryId . '</td>';
                            echo '<td rowspan="' . ($subCategoriesCount + 1) . '">' . $mainCategoryName . '</td>';
                            echo '<th>Serial No</th>';
                            echo '<th>Sub Category Name</th>';
                            echo '<th>Action</th>';
                            echo '<td rowspan="' . ($subCategoriesCount + 1) . '">';
                            echo '<form method="POST" action="">';
                            echo '<input type="hidden" name="main_ctg_id" value="' . $mainCategoryId . '">';
                            echo '<button type="submit" name="delete_main_category" class="btn btn-dark">Delete</button>';
                            echo '</form>';
                            echo '</td>';
                            echo '</tr>';

                            if ($subCategoriesCount > 0) {
                                $serialNo = 1;
                                while ($subCategory = $subCategoriesResult->fetch_assoc()) {
                                    echo '<tr>';
                                    echo '<td>' . $serialNo++ . '</td>';
                                    echo '<td>' . $subCategory['sub_ctg_name'] . '</td>';
                                    echo '<td>';
                                    echo '<form method="POST" action="">';
                                    echo '<input type="hidden" name="sub_ctg_id" value="' . $subCategory['sub_ctg_id'] . '">';
                                    echo '<button type="submit" name="delete_sub_category" class="btn btn-danger">Delete</button>';
                                    echo '</form>';
                                    echo '</td>';
                                    echo '</tr>';
                                }
                            }
                        }
                        echo '</tbody>';
                    }
                  ?>
               </table>
              </div>
            </div>
</div>
<!--------------------------->
<!-- END MAIN AREA -->
<!--------------------------->

<?php require 'footer.php'; ?>