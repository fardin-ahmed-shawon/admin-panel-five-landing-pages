<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Login</title>
  <link rel="shortcut icon" href="assets/images/favicon.png" />
  <link rel="stylesheet" href="css/login.css" />
  <style>
        .msg-box {
            max-width: 500px;
            margin: auto;
            color: red;
            background: #ebebeb;
            padding: 10px;
            margin-bottom: 10px;
            transition: width 3s linear;
            display: none;
        }
        .credentials {
            background: #f1f1f1;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
    </style>
</head>
<body>
  <div class="login_form">
    <!-- Login form container -->
    <form action="#" method="post">
      <h3>Admin Login</h3>

      <br>
      
      <!-- <div class="credentials">
        <p>username: admin_39_</p>
        <p>password: 87654321</p>
      </div> -->

      <br><br>
      <h3 class="msg-box"></h3>
      <!-- username input box -->
      <div class="input_box">
        <label for="username">Username</label>
        <input name="username" type="username" id="username" placeholder="Enter username address" required />
      </div>
      <!-- Paswwrod input box -->
      <div class="input_box">
        <div class="password_title">
          <label for="password">Password</label>
        </div>
        <input name="password" type="password" id="password" placeholder="Enter your password" required />
      </div>
      <!-- Login button -->
      <button type="submit">Log In</button>
    </form>
  </div>

<?php

include('database/dbConnection.php'); // Include database connection file

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Fetch hashed password and role from the database
    $stmt = $conn->prepare("SELECT 
                                admin_info.admin_password, 
                                admin_info.role_id, 
                                roles.role_name
                            FROM 
                                admin_info
                            JOIN 
                                roles ON admin_info.role_id = roles.id
                            WHERE 
                                admin_info.admin_username = ?
                            ");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $hashedPwd = $row['admin_password'];

        if (password_verify($password, $hashedPwd)) {
            // Password is correct
            $_SESSION['admin'] = $username;
            $_SESSION['role'] = $row['role_name'];

            echo "<script>window.location.href = 'index.php';</script>";
            exit();

        }
    }

    // If we reach here, authentication failed
    echo '<script>
        const msg_box = document.querySelector(".msg-box");
        if (msg_box) {
            msg_box.style.display = "block";
            msg_box.innerHTML = "Wrong Credentials!";
            setTimeout(() => {
                msg_box.style.display = "none";
            }, 2000);
        }
    </script>';
}
?>

</body>
</html>