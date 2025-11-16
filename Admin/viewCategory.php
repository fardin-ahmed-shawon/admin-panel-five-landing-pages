<?php
$current_page = basename($_SERVER['PHP_SELF']); // Get the current page name
$page_title = 'View Category'; // Set the page title
?>
<?php require 'header.php'; ?>

<style>
      .accordion-body ul li {
        font-size: 18px;
      }
      .edit-btn {
        margin-left: 10px;
        font-size: 14px;
        padding: 2px 10px;
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
                </span> Product Categories
              </h3>
            </div>
            <br>
            <div class="row">
              <h1 class="text-center">View Category</h1>
              <!-- Accordion Start -->
              <div class="accordion accordion-flush" id="accordionFlushExample">
                <?php
                // Fetch main categories
                $mainCategoriesSql = "SELECT * FROM main_category";
                $mainCategoriesResult = $conn->query($mainCategoriesSql);

                if ($mainCategoriesResult->num_rows > 0) {
                    $index = 0;
                    while ($mainCategory = $mainCategoriesResult->fetch_assoc()) {
                        $index++;
                        $mainCtgName = $mainCategory['main_ctg_name'];
                        $mainCtgId = $mainCategory['main_ctg_id']; // Make sure this column exists

                        // Prepare statement for subcategories
                        $subCategoriesStmt = $conn->prepare("SELECT * FROM sub_category WHERE main_ctg_name = ?");
                        $subCategoriesStmt->bind_param("s", $mainCtgName);
                        $subCategoriesStmt->execute();
                        $subCategoriesResult = $subCategoriesStmt->get_result();
                        ?>
                        <div class="accordion-item">
                            <h2 class="accordion-header d-flex align-items-center" id="flush-heading<?php echo $index; ?>">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapse<?php echo $index; ?>" aria-expanded="false" aria-controls="flush-collapse<?php echo $index; ?>">
                                    <?php echo htmlspecialchars($mainCtgName); ?>
                                </button>
                                <a href="edit-category.php?type=main&id=<?php echo $mainCtgId; ?>" class="btn btn-dark btn-sm edit-btn py-2 px-3 m-3">Edit</a>
                            </h2>
                            <div id="flush-collapse<?php echo $index; ?>" class="accordion-collapse collapse" aria-labelledby="flush-heading<?php echo $index; ?>" data-bs-parent="#accordionFlushExample">
                                <div class="accordion-body">
                                    <ul>
                                        <?php
                                        if ($subCategoriesResult->num_rows > 0) {
                                            while ($subCategory = $subCategoriesResult->fetch_assoc()) {
                                                echo "<li>" . htmlspecialchars($subCategory['sub_ctg_name']) .
                                                    " <a href='edit-category.php?type=sub&id=" . $subCategory['sub_ctg_id'] . "' class='py-2 px-3 m-3 btn btn-dark btn-sm edit-btn'>Edit</a></li>";
                                            }
                                        } else {
                                            echo "<li>No subcategories available</li>";
                                        }
                                        ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <?php
                        $subCategoriesStmt->close();
                    }
                } else {
                    echo "<div class='alert alert-info'>No categories found.</div>";
                }
                $conn->close();
                ?>
              </div>
              <!-- End -->
            </div>
</div>
<!--------------------------->
<!-- END MAIN AREA -->
<!--------------------------->

<?php require 'footer.php'; ?>