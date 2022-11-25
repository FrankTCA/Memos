<?php
session_start();
require './creds.php';

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
    echo "MySQL Connection Successful!<br>";
}

$stmt = $conn->prepare("SELECT * FROM `users` WHERE name LIKE ? AND pass_hash LIKE SHA2(?, 224);");
$stmt->bind_param("ss", $un, $bc);

$un = $username;
$bc = $password;
$stmt->execute();
$result = $stmt->get_result();

while ($row = mysqli_fetch_assoc($result)) {
    if ($creds->is_development()) {
        echo "User account exists: ".$row."<br>";
    }
    if ($row['allowed'] == 0) {
        die("Account has not yet been approved!");
    } else {
        $_SESSION['gist_user_id'] = $row['id'];
        $_SESSION['gist_user_name'] = $row['name'];
        die("success");
    }
}

die("Account/password combination does not exist! Please register!");
