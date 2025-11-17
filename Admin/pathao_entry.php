<?php
$current_page = basename($_SERVER['PHP_SELF']); // Get the current page name
$page_title = 'Pathao Entry'; // Set the page title

$invoice_no = $_GET['invoice_no'] ?? '';

if (empty($invoice_no) || $invoice_no == '') {
   echo "Invoice number missing!";
}

//////////////////////////////////////////////////////////
// Sandbox //////////////////////////////////////////////

$base_url = "https://courier-api-sandbox.pathao.com";
$client_id = "7N1aMJQbWm";
$client_secret = "wRcaibZkUdSNz2EI9ZyuXLlNrnAv0TdPUPXMnD39";
$username = "test@pathao.com";
$password = "lovePathao";
$grant_type = "password";
$store_id = "107467";

// END //////////////////////////////////////////////
////////////////////////////////////////////////////


//////////////////////////////////////////////////
// Production ///////////////////////////////////

// $base_url = "https://api-hermes.pathao.com";
// $client_id = "";
// $client_secret = "";
// $username = "";
// $password = "";
// $grant_type = "password";
// $store_id = "";

// END //////////////////////////////////////////////
////////////////////////////////////////////////////

require 'header.php';

?>

<!--------------------------->
<!-- START MAIN AREA -->
<!--------------------------->
<div class="content-wrapper">
    <div class="row">
        <div class="col-md-6 mx-auto">
            <div class="card shadow-lg border border-green-200 rounded-xl p-6 bg-white text-center">
                <div class="card-body">
                    <div class="flex justify-center mb-4">
                        <i style="font-size: 70px" class="mdi mdi-check-circle-outline text-success text-6xl"></i>
                    </div>
                    <h1 class="text-2xl font-semibold mb-2 px-3">
                        <?php print_r(create_pathao_consignment($invoice_no)); ?>
                    </h1><br>
                    <!-- <h2 class="text-lg text-gray-700 flex items-center justify-center gap-2 mb-4">
                        <i class="mdi mdi-truck-fast-outline text-gray-600 text-xl"></i>
                        Consignment ID: <span class="font-mono text-info"></span>
                    </h2> -->
                    <a href="<?= get_track_parcel_url($invoice_no); ?>" class="btn btn-primary p-3 w-50 my-5">
                        <i class="mdi mdi-map-marker-path text-xl"></i>
                        Track Your Parcel
                    </a>

                    <a href="pathao-courier-list.php" class="btn btn-dark p-3 w-50 mb-5">
                        <span class="mdi mdi-keyboard-backspace"></span> Back
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<!--------------------------->
<!-- END MAIN AREA -->
<!--------------------------->

<?php require 'footer.php'; ?>