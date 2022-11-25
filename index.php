<?php
session_start();
require "php/creds.php";
if (isset($_SESSION["gist_user_id"]) && !(isset($_GET["name"]))) {
    header("Location: https://infotoast.org/memos/dash.php");
    die();
} elseif (isset($_GET["name"])) {
    $linkName = $_GET["name"];
    $creds = new Creds();
    $clientip = $_SERVER['REMOTE_ADDR'];
    $clientcountry = $_SERVER['HTTP_CF_IPCOUNTRY'];
    $useragent = $_SERVER['HTTP_USER_AGENT'];
    if ($creds->is_development()) {
        echo "Client IP: $clientip<br>Client Country: $clientcountry<br>User Agent: $useragent<br>Link name: $linkName<br>";
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

    $n = $linkName;
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = mysqli_fetch_assoc($result)) {
        $memoid = $row['id'];
        $url = $row['url'];
        $title = $row['title'];
        $shouldindex = $row['shouldindex'];
        $stmt2 = $conn->prepare("INSERT INTO `log` (memo_id, ip, user_agent, country) VALUES (?, ?, ?, ?);");
        $stmt2->bind_param("isss", $lid2, $uip, $uag, $ctr);

        $lid2 = $memoid;
        $uip = $clientip;
        $uag = $useragent;
        $ctr = $clientcountry;
        $stmt2->execute();
    ?>
    <!DOCTYPE html>
    <html>
        <head>
            <meta charset="UTF-8">
            <?php
                if ($shouldindex) {
                    ?>
                    <meta name="robots" content="noindex">
                    <?php
                }
                echo "<title>".$title."</title>";
            ?>
        </head>
        <body>
        <?php
            echo "<script=\"".$url.".js\"></script>";
        ?>
        </body>
    </html>
        <?php
    }
        ?>
<?php
} else {
    header("Location: https://infotoast.org/memos/login.html");
    die();
}

