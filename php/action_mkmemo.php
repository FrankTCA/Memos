<?php
session_start();
require "creds.php";

if (!isset($_SESSION['gist_user_id'])) {
    die("Unauthorized!");
}

if (!isset($_POST["name"]) || !isset($_POST["title"]) || !isset($_POST["url"])) {
    die("Not enough information received!");
}

$name = $_POST['name'];
$title = $_POST['title'];
$url = $_POST['url'];

$creds = new Creds();

if ($creds->is_development()) {
    echo "Credentials initialized!<br>";
    echo "Memo name is $name, title is $title, and url is $url<br>";
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
    echo "MySQL connection successful!<br>";
}

$stmt = $conn->prepare("SELECT * FROM `memos` WHERE name LIKE ?;");
$stmt->bind_param("s", $n);

$n = $name;
$stmt->execute();
$result = $stmt->get_result();

while ($row = mysqli_fetch_assoc($result)) {
    die("Name already in use!");
}

if ($creds->is_development()) {
    echo "Performed initial mysql scan to ensure it is not already used!<br>";
}

$stmt2 = $conn->prepare("INSERT INTO `memos` (user_id, `name`, url, title) VALUES (?, ?, ?, ?);");
$stmt2->bind_param("iss", $uc, $n2, $u, $tit);

$uc = $_SESSION['gist_user_id'];
$n2 = $name;
$u = $url;
$tit = $title;
$stmt2->execute();

echo "success";
