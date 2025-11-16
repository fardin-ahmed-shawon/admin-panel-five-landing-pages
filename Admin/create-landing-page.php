<?php
$current_page = basename($_SERVER['PHP_SELF']);
$page_title = 'Create Landing Page';
?>
<?php require 'header.php'; ?>

<!--------------------------->
<!-- START MAIN AREA -->
<!--------------------------->
<div class="content-wrapper">
  <div class="page-header">
    <h3 class="page-title">
      <span class="page-title-icon bg-gradient-primary text-white me-2">
        <i class="mdi mdi-home"></i>
      </span>
      Landing Page
    </h3>
  </div>

  <div class="row">
    <h1>Create Landing Page</h1>
    <div class="container">
      <!-- Search Form -->
      <form method="GET" class="mb-4">
        <div class="row">
          <div class="col-md-6">
            <label for="search"><b>Search Your Product:</b></label>
            <div class="d-flex">
              <input type="text" name="search" id="search" class="form-control me-2"
                placeholder="Enter product title or code"
                value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
              <button type="submit" class="btn btn-primary me-2">Search</button>
              <?php if (isset($_GET['search']) && $_GET['search'] !== ''): ?>
                <a href="<?php echo strtok($_SERVER["REQUEST_URI"], '?'); ?>" class="btn btn-dark d-flex align-items-center" title="Reset Search">Reset</a>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </form>

      <!-- Table -->
      <div class="table-responsive">
        <table class="table table-bordered table-hover">
          <thead class="thead-dark">
            <tr>
              <td>ID</td>
              <td>Image</td>
              <td>Title</td>
              <td>Code</td>
              <td>Main Category</td>
              <td>Sub Category</td>
              <td>Action</td>
            </tr>
          </thead>
          <tbody>
            <?php
            $search = isset($_GET['search']) ? trim($_GET['search']) : '';

            $sql = "SELECT p.*, mc.main_ctg_name, sc.sub_ctg_name 
                    FROM product_info p
                    LEFT JOIN main_category mc ON p.main_ctg_id = mc.main_ctg_id
                    LEFT JOIN sub_category sc ON p.sub_ctg_id = sc.sub_ctg_id";

            if (!empty($search)) {
                $search_safe = mysqli_real_escape_string($conn, $search);
                $sql .= " WHERE p.product_title LIKE '%$search_safe%' OR p.product_code LIKE '%$search_safe%'";
            }

            $sql .= " ORDER BY p.product_id DESC";
            $result = mysqli_query($conn, $sql);

            if ($result && mysqli_num_rows($result) > 0) {
              while ($item = mysqli_fetch_assoc($result)) {
                $title = htmlspecialchars($item['product_title'], ENT_QUOTES);
                $slug = htmlspecialchars($item['product_slug'], ENT_QUOTES);
                $code  = htmlspecialchars($item['product_code'], ENT_QUOTES);
                $price = htmlspecialchars($item['product_price'], ENT_QUOTES);
                $id    = (int)$item['product_id'];

                echo '<tr>';
                echo '<td>'. $id .'</td>';
                echo '<td><img src="../img/'. htmlspecialchars($item['product_img1']) .'" alt="img" style="width:50px;height:50px;"></td>';
                echo '<td>'. $title .'</td>';
                echo '<td>'. $code .'</td>';
                echo '<td>'. htmlspecialchars($item['main_ctg_name']) .'</td>';
                echo '<td>'. htmlspecialchars($item['sub_ctg_name']) .'</td>';
                
                echo '<td>';

                // Check if a landing page already exists for this product
                $check_sql = "SELECT COUNT(*) AS count FROM landing_pages WHERE product_id = $id";

                $check_result = mysqli_query($conn, $check_sql);
                $check_row = mysqli_fetch_assoc($check_result);

                if ($check_row['count'] > 0) {
                    echo '<a href="'.$site_link.'landing/'. $slug .'" class="btn btn-primary btn-sm" target="_blank">Preview <span class="mdi mdi-eye"></span></a> ';
                } else {
                    echo '<a class="btn btn-dark btn-sm" href="edit-landing-page.php?id='. $id .'">Create <span class="mdi mdi-square-edit-outline"></span></a> ';
                }
                
                echo '</tr>';
              }
            } else {
              echo "<tr><td colspan='7' class='text-center text-danger'>No matching products found.</td></tr>";
            }
            ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<!--------------------------->
<!-- END MAIN AREA -->
<!--------------------------->

<?php require 'footer.php'; ?>