<?php
$current_page = basename($_SERVER['PHP_SELF']);
$page_title = 'Landing Page List';
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
    <h1>Landing Page List</h1>
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
              <td>URL</td>
              <td>Actions</td>
            </tr>
          </thead>
          <tbody>
            <?php
            $search = isset($_GET['search']) ? trim($_GET['search']) : '';

            $sql = "SELECT p.*, 
                        mc.main_ctg_name, 
                        sc.sub_ctg_name, 
                        lp.id AS landing_id
                  FROM product_info p
                  LEFT JOIN main_category mc ON p.main_ctg_id = mc.main_ctg_id
                  LEFT JOIN sub_category sc ON p.sub_ctg_id = sc.sub_ctg_id
                  INNER JOIN landing_pages lp ON lp.product_id = p.product_id";


            if (!empty($search)) {
                $search_safe = mysqli_real_escape_string($conn, $search);
                $sql .= " AND (p.product_title LIKE '%$search_safe%' 
                          OR p.product_code LIKE '%$search_safe%')";
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
                $landingId = (int)$item['landing_id'];

                echo '<tr>';
                echo '<td>'. $id .'</td>';
                echo '<td><img src="../img/'. htmlspecialchars($item['product_img1']) .'" alt="img" style="width:50px;height:50px;"></td>';
                echo '<td>'. $title .'</td>';
                echo '<td>'.$site_link.'landing/'. $slug .'</td>';
                echo '<td>';
                echo '<a href="'.$site_link.'landing/'. $slug .'" class="btn btn-dark btn-sm" target="_blank">Preview <span class="mdi mdi-eye"></span></a> ';
                echo '<button class="btn btn-dark btn-sm" onclick="confirmEdit('. $landingId .')">Edit <span class="mdi mdi-square-edit-outline"></span></button> ';
                echo '<button class="btn btn-dark btn-sm" onclick="confirmDelete('. $id .')">Delete <span class="mdi mdi-trash-can-outline"></span></button> ';
                echo '</td>';
                echo '</tr>';
              }
            } else {
              echo "<tr><td colspan='10' class='text-center text-danger'>No matching products found.</td></tr>";
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

<script>
  function confirmEdit(landingId) {
    window.location.href = `editLanding.php?id=${landingId}`;
  }

  function confirmDelete(productId) {
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
            window.location.href = `deleteLanding.php?id=${productId}`;
        }
    });
  }
</script>
<?php require 'footer.php'; ?>