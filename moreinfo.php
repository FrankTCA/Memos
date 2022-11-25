<?php
session_start();
require "php/creds.php";
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>More Info</title>
        <script type="text/javascript" src="resources/jquery.min.js"></script>
        <link rel="stylesheet" href="resources/global.css">
        <meta name="robots" content="noindex,nofollow">
    </head>
    <body>
    <?php
        if (!isset($_SESSION['gist_user_id'])) {
    ?>
        <div class="container" id="notallowed">
            <h1 class="center">Access denied.</h1>
            <p class="center">You are not permitted to access the dashboard at this time.</p>
            <p class="center">Please register here if you haven't already: <a href="register.php">https://infotoast.org/memos/register.php</a></p>
        </div>
    <?php
        } else if (!isset($_GET['link'])) {
    ?>
        <div class="container" id="nolink">
            <h1 class="center">Invalid information</h1>
            <p class="center">You appear to be accessing this page without the proper headers!</p>
            <p class="center">Please access this page from the dash!</p>
        </div>
    <?php
        } else {
            $creds = new Creds();
            $conn = new mysqli($creds->get_host(), $creds->get_username(), $creds->get_password(), $creds->get_database());
            if ($conn->connect_error) {
                if ($creds->is_development()) {
                    die("Connection to database failed: " . $conn->connect_error);
                } else {
                    die("Error connecting to database! Please contact an admin!");
                }
            }

            $link = $_GET['link'];
            $userid = $_SESSION['gist_user_id'];

            $stmt = $conn->prepare("SELECT * FROM `memos` WHERE name = ?;");
            $stmt->bind_param("s", $lnk);

            $lnk = $link;
            $stmt->execute();
            $results = $stmt->get_result();

            while ($row = mysqli_fetch_assoc($results)) {
                $linkid = $row['id'];
                if ($row['user_id'] != $userid) {
    ?>
        <div class="container" id="differentuser">
            <h1 class="center">Access denied.</h1>
            <p class="center">You are attempting to see someone else's link!</p>
            <p class="center">Go back to the dashboard and report this problem if it continues.</p>
        </div>
    <?php
                } else {
    ?>
        <div class="container">
            <h1 class="center">Memo Access Log</h1>
            <p class="center">This is a log that shows who accessed your memo and when.</p>
            <p class="center">
                <?php
                    $stmt2 = $conn->prepare("SELECT can_see_ips FROM `users` WHERE id = ?;");
                    $stmt2->bind_param("i", $uid);

                    $uid = $userid;
                    $stmt2->execute();
                    $results2 = $stmt2->get_result();

                    while ($row2 = mysqli_fetch_assoc($results2)) {
                        if ($row2['can_see_ips'] == 1) {
                            $canSeeIps = true;
                            echo "Your user account has been allowed to see user IPs. Use this privilege with caution.";
                        } else {
                            $canSeeIps = false;
                            echo "IP visibility has been turned off for your account. You can still see the countries and user agents of users.";
                        }
                    }
                ?></p>
            <h3>Access Log:</h3>
            <div style="overflow-x: auto;">
                <table style="width: 100%">
                    <tr>
                        <th style="width: 5%">Log ID</th>
                        <?php
                            if ($canSeeIps) {
                        ?>
                        <th style="width: 15%">Client IP</th>
                        <?php
                            }
                        ?>
                        <th style="width: 10%">Timestamp Accessed</th>
                        <th>User Agent</th>
                        <th style="width: 15%">Country</th>
                    </tr>
                    <?php
                        $stmt3 = $conn->prepare("SELECT * FROM `log` WHERE memo_id = ?;");
                        $stmt3->bind_param("i", $lid);

                        $lid = $linkid;
                        $stmt3->execute();
                        $results3 = $stmt3->get_result();

                        while ($row3 = mysqli_fetch_assoc($results3)) {
                            echo "<tr><td>".$row3['id']."</td><td>";
                            if ($canSeeIps) {
                                echo $row3['ip']."</td><td>";
                            }
                            echo $row3['timestamp']."</td><td>".$row3['user_agent']."</td><td>".$row3['country']."</td></tr>";
                        }
                    ?>
                </table>
            </div>
        </div><?php
                }
            }
        }
    ?>
    </body>
</html>
