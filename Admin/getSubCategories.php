<?php
include('database/dbConnection.php');
if (isset($_GET['main_ctg_name'])) {
    $main_ctg_name = mysqli_real_escape_string($conn, $_GET['main_ctg_name']);
    $result = mysqli_query($conn, "SELECT sub_ctg_id, sub_ctg_name FROM sub_category WHERE main_ctg_name = '$main_ctg_name'");
    $options = "<option value=''>Select Sub Category</option>";
    while ($row = mysqli_fetch_assoc($result)) {
        $category_name = htmlspecialchars($row['sub_ctg_name'], ENT_QUOTES, 'UTF-8');
        $category_id = $row['sub_ctg_id'];
        $options .= "<option value='$category_id'>$category_name</option>";
    }
    echo $options;
}
?>