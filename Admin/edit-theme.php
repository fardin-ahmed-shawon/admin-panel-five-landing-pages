<?php
$current_page = basename($_SERVER['PHP_SELF']); // Get the current page name
$page_title = 'Edit Theme'; // Set the page title
?>
<?php require 'header.php'; ?>

<?php
// Fetch existing theme data
$sql = "SELECT * FROM themes LIMIT 1";
$result = $conn->query($sql);
$theme = $result->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $text_color = $_POST['text_color'];
    $button_color = $_POST['button_color'];
    $button_text_color = $_POST['button_text_color'];
    $button_hover_color = $_POST['button_hover_color'];
    $navbar_color = $_POST['navbar_color'];
    $navbar_text_color = $_POST['navbar_text_color'];
    $indicator_color = $_POST['indicator_color'];
    $search_btn_color = $_POST['search_btn_color'];
    $search_btn_text_color = $_POST['search_btn_text_color'];
    $search_btn_hover_color = $_POST['search_btn_hover_color'];
    $subscribe_btn_color = $_POST['subscribe_btn_color'];

    if ($theme) {
        $stmt = $conn->prepare("UPDATE themes SET text_color=?, button_color=?, button_text_color=?, button_hover_color=?, navbar_color=?, navbar_text_color=?, indicator_color=?, search_btn_color=?, search_btn_text_color=?, search_btn_hover_color=?, subscribe_btn_color=? WHERE id=?");
        $stmt->bind_param("sssssssssssi", $text_color, $button_color, $button_text_color, $button_hover_color, $navbar_color, $navbar_text_color, $indicator_color, $search_btn_color, $search_btn_text_color, $search_btn_hover_color, $subscribe_btn_color, $theme['id']);
        $stmt->execute();
    } else {
        $stmt = $conn->prepare("INSERT INTO themes (text_color, button_color, button_text_color, button_hover_color, navbar_color, navbar_text_color, indicator_color, search_btn_color, search_btn_text_color, search_btn_hover_color, subscribe_btn_color) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssssssss", $text_color, $button_color, $button_text_color, $button_hover_color, $navbar_color, $navbar_text_color, $indicator_color, $search_btn_color, $search_btn_text_color, $search_btn_hover_color, $subscribe_btn_color);
        $stmt->execute();
    }

    echo "<script>alert('Theme settings saved successfully!'); window.location.href='';</script>";
    exit();
}
?>

<!--------------------------->
<!-- START MAIN AREA -->
<!--------------------------->
<div class="content-wrapper">
            <div class="row">
                    <div class="col-md-8">
                        <div class="card card-body p-5">
                            <h4>Website Theme Customize</h4>
                            <br><br>
                            <form method="POST" action="">
                                <div class="form-group">
                                    <label for="text_color">Text Color</label>
                                    <input type="text" class="form-control" id="text_color" name="text_color" value="<?= $theme['text_color'] ?? '' ?>">
                                </div>
                                <div class="form-group">
                                    <label for="button_color">Button Color</label>
                                    <input type="text" class="form-control" id="button_color" name="button_color" value="<?= $theme['button_color'] ?? '' ?>">
                                </div>
                                <div class="form-group">
                                    <label for="button_text_color">Button Text Color</label>
                                    <input type="text" class="form-control" id="button_text_color" name="button_text_color" value="<?= $theme['button_text_color'] ?? '' ?>">
                                </div>
                                <div class="form-group">
                                    <label for="button_hover_color">Button Hover Color</label>
                                    <input type="text" class="form-control" id="button_hover_color" name="button_hover_color" value="<?= $theme['button_hover_color'] ?? '' ?>">
                                </div>
                                <div class="form-group">
                                    <label for="navbar_color">Navbar Color</label>
                                    <input type="text" class="form-control" id="navbar_color" name="navbar_color" value="<?= $theme['navbar_color'] ?? '' ?>">
                                </div>
                                <div class="form-group">
                                    <label for="navbar_text_color">Navbar Text Color</label>
                                    <input type="text" class="form-control" id="navbar_text_color" name="navbar_text_color" value="<?= $theme['navbar_text_color'] ?? '' ?>">
                                </div>
                                <div class="form-group">
                                    <label for="indicator_color">Indicator Color</label>
                                    <input type="text" class="form-control" id="indicator_color" name="indicator_color" value="<?= $theme['indicator_color'] ?? '' ?>">
                                </div>
                                <div class="form-group">
                                    <label for="search_btn_color">Search Button Color</label>
                                    <input type="text" class="form-control" id="search_btn_color" name="search_btn_color" value="<?= $theme['search_btn_color'] ?? '' ?>">
                                </div>
                                <div class="form-group">
                                    <label for="search_btn_text_color">Search Button Text Color</label>
                                    <input type="text" class="form-control" id="search_btn_text_color" name="search_btn_text_color" value="<?= $theme['search_btn_text_color'] ?? '' ?>">
                                </div>
                                <div class="form-group">
                                    <label for="search_btn_hover_color">Search Button Hover Color</label>
                                    <input type="text" class="form-control" id="search_btn_hover_color" name="search_btn_hover_color" value="<?= $theme['search_btn_hover_color'] ?? '' ?>">
                                </div>
                                <div class="form-group">
                                    <label for="subscribe_btn_color">Subscribe Button Color</label>
                                    <input type="text" class="form-control" id="subscribe_btn_color" name="subscribe_btn_color" value="<?= $theme['subscribe_btn_color'] ?? '' ?>">
                                </div>
                                <button type="submit" class="btn btn-primary">Save</button>
                            </form>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card card-body p-5">
                            <h4>Preview Theme</h4>
                            <br><br>
                            <p><strong>Text Color:</strong>
                                <div style="padding: 5px; background-color: <?= $theme['text_color'] ?? 'N/A' ?>"><?= $theme['text_color'] ?? 'N/A' ?></div> 
                            </p>
                            <p><strong>Button Color:</strong>
                                <div style="padding: 5px; background-color: <?= $theme['button_color'] ?? 'N/A' ?>"><?= $theme['button_color'] ?? 'N/A' ?></div>    
                            </p>
                            <p><strong>Button Text Color:</strong>
                                <div style="padding: 5px; background-color: <?= $theme['button_text_color'] ?? 'N/A' ?>"><?= $theme['button_text_color'] ?? 'N/A' ?></div>    
                            </p>
                            <p><strong>Button Hover Color:</strong>
                                <div style="padding: 5px; background-color: <?= $theme['button_hover_color'] ?? 'N/A' ?>"><?= $theme['button_hover_color'] ?? 'N/A' ?></div>
                            </p>
                            <p><strong>Navbar Color:</strong>
                                <div style="padding: 5px; background-color: <?= $theme['navbar_color'] ?? 'N/A' ?>"><?= $theme['navbar_color'] ?? 'N/A' ?></div>
                            </p>
                            <p><strong>Navbar Text Color:</strong>
                                <div style="padding: 5px; background-color: <?= $theme['navbar_text_color'] ?? 'N/A' ?>"><?= $theme['navbar_text_color'] ?? 'N/A' ?></div>
                            </p>
                            <p><strong>Indicator Color:</strong>
                                <div style="padding: 5px; background-color: <?= $theme['indicator_color'] ?? 'N/A' ?>"><?= $theme['indicator_color'] ?? 'N/A' ?></div>
                            </p>
                            <p><strong>Search Button Color:</strong>
                                <div style="padding: 5px; background-color: <?= $theme['search_btn_color'] ?? 'N/A' ?>"><?= $theme['search_btn_color'] ?? 'N/A' ?></div>
                            </p>
                            <p><strong>Search Button Text Color:</strong>
                                <div style="padding: 5px; background-color: <?= $theme['search_btn_text_color'] ?? 'N/A' ?>"><?= $theme['search_btn_text_color'] ?? 'N/A' ?></div>
                            </p>
                            <p><strong>Search Hover Color:</strong>
                                <div style="padding: 5px; background-color: <?= $theme['search_btn_hover_color'] ?? 'N/A' ?>"><?= $theme['search_btn_hover_color'] ?? 'N/A' ?></div>
                            </p>
                            <p><strong>Subscribe Button:</strong>
                                <div style="padding: 5px; background-color: <?= $theme['subscribe_btn_color'] ?? 'N/A' ?>"><?= $theme['subscribe_btn_color'] ?? 'N/A' ?></div>
                            </p>
                        </div>
                    </div>
                </div>
</div>
<!--------------------------->
<!-- END MAIN AREA -->
<!--------------------------->

<?php require 'footer.php'; ?>