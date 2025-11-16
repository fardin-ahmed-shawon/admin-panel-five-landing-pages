<?php
$servername = "localhost";

//--------------------------------------------------------------------

// For local development ---------------------------------------------
$site_link = "http://localhost/test/clothdrob/";
$username = "root";
$password = "";
$database_name = "clothdrob";

// For production -----------------------------------------------------
// $site_link = "https://clothdrob.com/";
// $username = "clothd";
// $password = "Vp6IY-Lese6!56";
// $database_name = "clothd_easy_commerce";

//---------------------------------------------------------------------

// Create connection
$conn = mysqli_connect($servername, $username, $password, $database_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>