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
              <td rowspan="6">URL</td>
              <td>Preview</td>
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
                  $id    = (int)$item['product_id'];
                  $landingId = (int)$item['landing_id'];

                  // 6 Landing URLs
                  $urls = [
                      $site_link . "landing1/" . $slug,
                      $site_link . "landing2/" . $slug,
                      $site_link . "landing3/" . $slug,
                      $site_link . "landing4/" . $slug,
                      $site_link . "landing5/" . $slug,
                      $site_link . "landing/"  . $slug,
                  ];

                  // FIRST ROW
                  echo "<tr>";
                  echo "<td rowspan='6'>{$id}</td>";
                  echo "<td rowspan='6'>
                          <img src='../img/".htmlspecialchars($item['product_img1'])."' style='width:50px;height:50px;'>
                        </td>";
                  echo "<td rowspan='6'>{$title}</td>";

                  // URL (row 1)
                  echo "<td><a href='{$urls[0]}' target='_blank'>{$urls[0]}</a></td>";

                  // Preview + Copy URL
                  echo "<td>
                          <a href='{$urls[0]}' class='btn btn-dark btn-sm' target='_blank'>Preview</a>
                          <button class='btn btn-primary btn-sm' onclick=\"copyURL('{$urls[0]}')\">Copy URL</button>
                        </td>";

                  // Edit / Delete (rowspan)
                  echo "<td rowspan='6'>
                          <button class='btn btn-dark btn-sm' onclick='confirmEdit({$landingId})'>Edit</button>
                          <button class='btn btn-dark btn-sm' onclick='confirmDelete({$id})'>Delete</button>
                        </td>";
                  echo "</tr>";

                  // Remaining rows (URLs + Preview + Copy)
                  for ($i = 1; $i < 6; $i++) {
                      echo "<tr>
                              <td><a href='{$urls[$i]}' target='_blank'>{$urls[$i]}</a></td>
                              <td>
                                  <a href='{$urls[$i]}' class='btn btn-dark btn-sm' target='_blank'>Preview</a>
                                  <button class='btn btn-primary btn-sm' onclick=\"copyURL('{$urls[$i]}')\">Copy URL</button>
                              </td>
                            </tr>";
                  }
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

<script>
  function copyURL(url) {
      navigator.clipboard.writeText(url).then(() => {
          alert("URL Copied!");
      }).catch(err => {
          console.error("Copy failed", err);
      });
  }
</script>

<?php require 'footer.php'; ?>