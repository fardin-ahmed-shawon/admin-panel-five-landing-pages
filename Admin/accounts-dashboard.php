<?php
$current_page = basename($_SERVER['PHP_SELF']); // Get the current page name
$page_title = 'Accounts'; // Set the page title
?>
<?php require 'header.php'; ?>

<?php
// Date info
$today = date('Y-m-d');
$month = date('Y-m');
$year = date('Y');

// Initialize variables
$totalCollection = $monthlyCollection = $yearlyCollection = $dailyCollection = 0;
$totalExpense = $monthlyExpense = $yearlyExpense = $dailyExpense = 0;

// Collection (Revenue)
$collectionQuery = "SELECT total_price, order_date FROM order_info WHERE order_status = 'Completed'";
$collectionResult = mysqli_query($conn, $collectionQuery);
while ($row = mysqli_fetch_assoc($collectionResult)) {
    $amount = (int)$row['total_price'];
    $date = $row['order_date'];

    $totalCollection += $amount;
    if (strpos($date, $month) === 0) $monthlyCollection += $amount;
    if (strpos($date, $year) === 0) $yearlyCollection += $amount;
    if (strpos($date, $today) === 0) $dailyCollection += $amount;
}

// Expenses
$expenseQuery = "SELECT expense_amount, expense_date FROM expense_info";
$expenseResult = mysqli_query($conn, $expenseQuery);
while ($row = mysqli_fetch_assoc($expenseResult)) {
    $amount = (float)$row['expense_amount'];
    $date = $row['expense_date'];

    $totalExpense += $amount;
    if (strpos($date, $month) === 0) $monthlyExpense += $amount;
    if (strpos($date, $year) === 0) $yearlyExpense += $amount;
    if (strpos($date, $today) === 0) $dailyExpense += $amount;
}

// Profits and Losses
$totalProfit = $totalCollection - $totalExpense;
$yearlyProfit = $yearlyCollection - $yearlyExpense;
$monthlyProfit = $monthlyCollection - $monthlyExpense;
$dailyProfit = $dailyCollection - $dailyExpense;

$totalLoss = $totalProfit < 0 ? abs($totalProfit) : 0;
$yearlyLoss = $yearlyProfit < 0 ? abs($yearlyProfit) : 0;
$monthlyLoss = $monthlyProfit < 0 ? abs($monthlyProfit) : 0;
$dailyLoss = $dailyProfit < 0 ? abs($dailyProfit) : 0;
?>

<!--------------------------->
<!-- START MAIN AREA -->
<!--------------------------->
<div class="content-wrapper">
          <div class="row">
            <!-- Total Collection -->
            <div class="col-sm-6 col-md-3 mt-4">
              <div class="card" style="background: linear-gradient(90deg, #2A7B9B, #57C79C, #2A7B9B);">
                <div class="card-statistic-3 p-4 text-light">
                  <div class="card-icon card-icon-large"><i class="mdi mdi-cash-multiple" style="font-size: 2rem;"></i></div>
                  <h4 class="mb-2 text-light card-title mb-0">Total Collection</h4>
                  <h2>৳ <?= number_format($totalCollection) ?></h2>
                </div>
              </div>
            </div>
            <!-- Total Expense -->
            <div class="col-sm-6 col-md-3 mt-4">
              <div class="card" style="background: linear-gradient(90deg, #8000ff, #5d00ff, #8000ff);">
                <div class="card-statistic-3 p-4 text-light">
                  <div class="card-icon card-icon-large"><i class="mdi mdi-cash-minus" style="font-size: 2rem;"></i></div>
                  <h4 class="mb-2 text-light card-title mb-0">Total Expense</h4>
                  <h2>৳ <?= number_format($totalExpense) ?></h2>
                </div>
              </div>
            </div>
            <!-- Total Profit -->
            <div class="col-sm-6 col-md-3 mt-4">
              <div class="card" style="background: linear-gradient(90deg, #ff0048, #ff007b, #ff0048);">
                <div class="card-statistic-3 p-4 text-light">
                  <div class="card-icon card-icon-large"><i class="mdi mdi-cash-plus" style="font-size: 2rem;"></i></div>
                  <h4 class="mb-2 text-light card-title mb-0">Total Profit</h4>
                  <h2>৳ <?= number_format(max($totalProfit, 0)) ?></h2>
                </div>
              </div>
            </div>
            <!-- Total Loss -->
            <div class="col-sm-6 col-md-3 mt-4">
              <div class="card" style="background: linear-gradient(90deg, #0088ff, #00d4ff, #00b2ff);">
                <div class="card-statistic-3 p-4 text-light">
                  <div class="card-icon card-icon-large"><i class="mdi mdi-cash-remove" style="font-size: 2rem;"></i></div>
                  <h4 class="mb-2 text-light card-title mb-0">Total Loss</h4>
                  <h2>৳ <?= number_format($totalLoss) ?></h2>
                </div>
              </div>
            </div>
          </div>

          <!-- Repeat for Yearly, Monthly, Daily -->
          <?php
          $timeFrames = [
              "Yearly" => [$yearlyCollection, $yearlyExpense, $yearlyProfit, $yearlyLoss],
              "Monthly" => [$monthlyCollection, $monthlyExpense, $monthlyProfit, $monthlyLoss],
              "Daily" => [$dailyCollection, $dailyExpense, $dailyProfit, $dailyLoss],
          ];
          foreach ($timeFrames as $label => [$col, $exp, $prof, $loss]) {
          ?>
          <div class="row mt-4">
            <div class="col-sm-6 col-md-3">
              <div class="card bg-dark">
                <div class="card-statistic-3 p-4 text-light">
                <div class="card-icon card-icon-large"><i class="mdi mdi-cash-multiple" style="font-size: 2rem;"></i></div>
                  <h4 class="mb-2 text-light card-title mb-0"><?= $label ?> Collection</h4>
                  <h2>৳ <?= number_format($col) ?></h2>
                </div>
              </div>
            </div>
            <div class="col-sm-6 col-md-3">
              <div class="card bg-dark">
                <div class="card-statistic-3 p-4 text-light">
                  <div class="card-icon card-icon-large"><i class="mdi mdi-cash-minus" style="font-size: 2rem;"></i></div>
                  <h4 class="mb-2 text-light card-title mb-0"><?= $label ?> Expense</h4>
                  <h2>৳ <?= number_format($exp) ?></h2>
                </div>
              </div>
            </div>
            <div class="col-sm-6 col-md-3">
              <div class="card bg-dark">
                <div class="card-statistic-3 p-4 text-light">
                  <div class="card-icon card-icon-large"><i class="mdi mdi-cash-plus" style="font-size: 2rem;"></i></div>
                  <h4 class="mb-2 text-light card-title mb-0"><?= $label ?> Profit</h4>
                  <h2>৳ <?= number_format(max($prof, 0)) ?></h2>
                </div>
              </div>
            </div>
            <div class="col-sm-6 col-md-3">
              <div class="card bg-dark">
                <div class="card-statistic-3 p-4 text-light">
                  <div class="card-icon card-icon-large"><i class="mdi mdi-cash-remove" style="font-size: 2rem;"></i></div>
                  <h4 class="mb-2 text-light card-title mb-0"><?= $label ?> Loss</h4>
                  <h2>৳ <?= number_format($loss) ?></h2>
                </div>
              </div>
            </div>
          </div>
          <?php } ?>
</div>
<!--------------------------->
<!-- END MAIN AREA -->
<!--------------------------->
<script>
    $(function () {
      $('[data-toggle="tooltip"]').tooltip()
    });
</script>
<?php require 'footer.php'; ?>
