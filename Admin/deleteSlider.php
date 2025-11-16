<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

include('database/dbConnection.php');

// Get the slider ID from the query string
if (isset($_GET['si'])) {
    $slider_id = $_GET['si'];

    // Sanitize the input to prevent SQL injection
    $slider_id = mysqli_real_escape_string($conn, $slider_id);

    // Prepare the SQL query
    $sql = "DELETE FROM slider WHERE slider_id='$slider_id'";
    $result = mysqli_query($conn, $sql);

    if ($result) {
        // Redirect to the slider list page with a success message
        header("Location: slider.php?suc_msg=Slider Successfully Deleted!");
    } else {
        // Redirect with an error message
        header("Location: slider.php?unsuc_msg=Failed to Delete Slider");
    }
} else {
    // Redirect if no slider ID is provided
    header("Location: slider.php?unsuc_msg=Invalid Slider ID");
}

exit();
?>