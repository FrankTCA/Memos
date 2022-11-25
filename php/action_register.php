<?php
require "./creds.php";

if (!(isset($_POST['un']) && isset($_POST['pw']))) {
    die("Proper information not given!");
}

$username = $_POST["un"];
$password = $_POST["pw"];

$username = filter_var($username, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

if ($username !== $_POST["un"]) {
    die("Invalid characters in username.");
}

$creds = new Creds();

if ($creds->is_development()) {
    echo "Credentials initialized.<br>";
    echo "Username: $username<br>Password: $password<br>";
}

$conn = new mysqli($creds->get_host(), $creds->get_username(), $creds->get_password(), $creds->get_database());

if ($conn->connect_error) {
    if ($creds->is_development()) {
        die("Connection to database failed: " . $conn->connect_error);
    } else {
        die("Error connecting to database! Please contact an admin!");
    }
}

if ($creds->is_development()) {
    echo "SQL Connection successful.<br>";
}

$stmt = $conn->prepare("SELECT * FROM `users` WHERE name LIKE ?;");
$stmt->bind_param("s",$uname);

$uname = $username;
$stmt->execute();
$result = $stmt->get_result();

while ($row = mysqli_fetch_assoc($result)) {
    die("Username already taken! Contact Frank if you are locked out!");
}

if ($creds->is_development()) {
    echo "SELECT statement made.<br>";
}

$stmt2 = $conn->prepare("INSERT INTO `users` (`name`, `pass_hash`) VALUES (?, SHA2(?, 224));");
$stmt2->bind_param("ss", $un,$bc);

$un = $username;
$bc = $password;
$stmt2->execute();

echo "success";
