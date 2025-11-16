<?php
$current_page = basename($_SERVER['PHP_SELF']); // Get the current page name
$page_title = 'Statements'; // Set the page title
?>
<?php require 'header.php'; ?>

<!--------------------------->
<!-- START MAIN AREA -->
<!--------------------------->
<div class="content-wrapper">
                <div class="page-header">
                    <h3 class="page-title">
                        <span class="page-title-icon bg-gradient-primary text-white me-2">
                            <i class="mdi mdi-finance"></i>
                        </span> Accounts
                    </h3>
                </div>
                <div class="row">
                    <h1>Monthly Statements</h1>
                    <form method="GET" class="mb-3">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="year">Year:</label>
                                <select name="year" id="year" class="form-control">
                                    <?php
                                    $currentYear = date("Y");
                                    $selectedYear = isset($_GET['year']) ? $_GET['year'] : $currentYear;
                                    for ($y = $currentYear; $y >= $currentYear - 10; $y--) {
                                        $selected = ($y == $selectedYear) ? 'selected' : '';
                                        echo "<option value='$y' $selected>$y</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-primary mt-4">Search</button>
                            </div>
                        </div>
                    </form>

                    <div class="col-lg-12 grid-margin stretch-card">
                        <div class="table-responsive w-100">
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th><b>Month</b></th>
                                    <th><b>Total Collection (Tk.)</b></th>
                                    <th><b>Total Expense (Tk.)</b></th>
                                    <th><b>Total Profit (Tk.)</b></th>
                                    <th><b>Total Loss (Tk.)</b></th>
                                    <th><b>Statement</b></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $monthNames = [
                                    'January', 'February', 'March', 'April', 'May', 'June',
                                    'July', 'August', 'September', 'October', 'November', 'December'
                                ];

                                $grandCollection = 0;
                                $grandExpense = 0;
                                $grandProfit = 0;
                                $grandLoss = 0;

                                for ($month = 1; $month <= 12; $month++) {
                                    $monthStart = "$selectedYear-" . str_pad($month, 2, '0', STR_PAD_LEFT) . "-01";
                                    $monthEnd = date("Y-m-t", strtotime($monthStart));

                                    // Total Collection
                                    $collectionQuery = "SELECT SUM(total_price) AS total FROM order_info 
                                                        WHERE order_status = 'Completed' 
                                                        AND order_date BETWEEN '$monthStart' AND '$monthEnd'";
                                    $collectionResult = mysqli_query($conn, $collectionQuery);
                                    $collection = mysqli_fetch_assoc($collectionResult)['total'] ?? 0;

                                    // Total Expense
                                    $expenseQuery = "SELECT SUM(expense_amount) AS total FROM expense_info 
                                                     WHERE expense_date BETWEEN '$monthStart' AND '$monthEnd'";
                                    $expenseResult = mysqli_query($conn, $expenseQuery);
                                    $expense = mysqli_fetch_assoc($expenseResult)['total'] ?? 0;

                                    // Profit & Loss
                                    $profit = $collection - $expense;
                                    $loss = ($profit < 0) ? abs($profit) : 0;
                                    $profit = ($profit >= 0) ? $profit : 0;

                                    // Totals
                                    $grandCollection += $collection;
                                    $grandExpense += $expense;
                                    $grandProfit += $profit;
                                    $grandLoss += $loss;

                                    echo "<tr>
                                        <td>{$monthNames[$month - 1]}</td>
                                        <td>" . number_format($collection, 2) . " Tk.</td>
                                        <td>" . number_format($expense, 2) . " Tk.</td>
                                        <td>" . number_format($profit, 2) . " Tk.</td>
                                        <td>" . number_format($loss, 2) . " Tk.</td>
                                        <td><a class='btn btn-dark' href='view-statement.php?year=$selectedYear&month=$month'>View Statement</a></td>
                                    </tr>";
                                }
                                ?>
                                </tbody>
                                <tfoot>
                                <tr>
                                    <td><b>Total</b></td>
                                    <td><b><?php echo number_format($grandCollection, 2); ?> Tk.</b></td>
                                    <td><b><?php echo number_format($grandExpense, 2); ?> Tk.</b></td>
                                    <td><b><?php echo number_format($grandProfit, 2); ?> Tk.</b></td>
                                    <td><b><?php echo number_format($grandLoss, 2); ?> Tk.</b></td>
                                    <td></td>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
</div>
<!--------------------------->
<!-- END MAIN AREA -->
<!--------------------------->

<?php require 'footer.php'; ?>