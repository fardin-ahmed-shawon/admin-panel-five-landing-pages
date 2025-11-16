<?php
$current_page = basename($_SERVER['PHP_SELF']); // Get the current page name
$page_title = 'View Statement'; // Set the page title
?>
<?php require 'header.php'; ?>

<?php

$year = isset($_GET['year']) ? intval($_GET['year']) : date("Y");
$month = isset($_GET['month']) ? intval($_GET['month']) : date("n");

$monthName = date('F', mktime(0, 0, 0, $month, 10));
$startDate = "$year-" . str_pad($month, 2, '0', STR_PAD_LEFT) . "-01";
$endDate = date("Y-m-t", strtotime($startDate));

// === TOTAL COLLECTION ===
$collectionQuery = "SELECT SUM(total_price) AS total FROM order_info 
                    WHERE order_status = 'Completed' AND order_date BETWEEN '$startDate' AND '$endDate'";
$collectionResult = mysqli_query($conn, $collectionQuery);
$totalCollection = mysqli_fetch_assoc($collectionResult)['total'] ?? 0;

// === TOTAL EXPENSE BY CATEGORY ===
$expensesByCategory = [];
$totalExpense = 0;

$expenseQuery = "SELECT expense_category, SUM(expense_amount) AS total 
                 FROM expense_info 
                 WHERE expense_date BETWEEN '$startDate' AND '$endDate' 
                 GROUP BY expense_category";

$expenseResult = mysqli_query($conn, $expenseQuery);
while ($row = mysqli_fetch_assoc($expenseResult)) {
    $category = $row['expense_category'];
    $amount = $row['total'];
    $expensesByCategory[$category] = $amount;
    $totalExpense += $amount;
}

// Fetch all expense categories
$categoryQuery = "SELECT category_name FROM expense_category ORDER BY category_name ASC";
$categoryResult = mysqli_query($conn, $categoryQuery);

$profit = $totalCollection - $totalExpense;
?>

<!--------------------------->
<!-- START MAIN AREA -->
<!--------------------------->
<div class="content-wrapper">
                <div id="statement-content">
                    <br>
                    <h1 class="text-center mb-0">Statement</h1>
                    <h4 class="text-center"><?php echo "$monthName $year"; ?></h4>
                    <br>
                    <div class="row">
                        <div class="col-lg-12 grid-margin stretch-card">
                            <div class="table-responsive w-100">
                                <table class="table table-bordered">
                                    <thead class="bg-light">
                                    <tr class="table-dark">
                                        <th colspan="4"><b>Details</b></th>
                                        <th><b>Amount</b></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <!-- Collections -->
                                    <tr>
                                        <td colspan="4" class="text-center"><h4><b>Collections</b></h4></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" style="padding-left: 40px;">Sales</td>
                                        <td><?php echo number_format($totalCollection, 2); ?> Tk.</td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" style="padding-left: 40px;">Loan</td>
                                        <td>0.00 Tk.</td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" style="padding-left: 40px;"><b>Total Collections</b></td>
                                        <td><b><?php echo number_format($totalCollection, 2); ?> Tk.</b></td>
                                    </tr>

                                    <!-- Operating Expenses -->
                                    <tr>
                                        <td colspan="4" class="text-center"><h4><b>Expenses</b></h4></td>
                                        <td></td>
                                    </tr>
                                    <?php
                                    while ($catRow = mysqli_fetch_assoc($categoryResult)) {
                                        $category = $catRow['category_name'];
                                        $amount = $expensesByCategory[$category] ?? 0;
                                        echo "<tr>
                                            <td colspan='4' style='padding-left: 40px;'>$category</td>
                                            <td>" . number_format($amount, 2) . " Tk.</td>
                                        </tr>";
                                    }
                                    ?>
                                    <tr>
                                        <td colspan="4" style="padding-left: 40px;"><b>Total Expenses</b></td>
                                        <td><b><?php echo number_format($totalExpense, 2); ?> Tk.</b></td>
                                    </tr>

                                    <!-- Net Profit -->
                                    <tr class="text-center">
                                        <td colspan="4"><h4><b>Net Profit/Loss</b></h4></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" style="padding-left: 40px;">
                                            <b>(Total Revenue - Total Operating Expense)</b>
                                        </td>
                                        <td><b><?php echo number_format($profit, 2); ?> Tk.</b></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-center">
                    <button class="btn btn-dark">Download PDF</button>
                </div>
</div>
<!--------------------------->
<!-- END MAIN AREA -->
<!--------------------------->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script>
document.querySelector('.btn.btn-dark').addEventListener('click', function () {
    const { jsPDF } = window.jspdf;
    const element = document.getElementById("statement-content");

    html2canvas(element).then(canvas => {
        const imgData = canvas.toDataURL('image/png');
        const pdf = new jsPDF({ orientation: 'portrait', unit: 'pt', format: 'a4' });

        const imgProps = pdf.getImageProperties(imgData);
        const pdfWidth = pdf.internal.pageSize.getWidth();
        const pdfHeight = (imgProps.height * pdfWidth) / imgProps.width;

        pdf.addImage(imgData, 'PNG', 0, 0, pdfWidth, pdfHeight);
        pdf.save("Statement_<?php echo $monthName . '_' . $year; ?>.pdf");
    });
});
</script>
<?php require 'footer.php'; ?>
