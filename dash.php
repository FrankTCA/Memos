<?php
session_start();
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <?php
        if (isset($_SESSION['gist_user_id'])) {
        ?>
        <title>Memos Dashboard</title>
        <?php
        } else {
        ?>
        <title>Access denied</title>
        <?php
        }
        ?>
        <script type="text/javascript" src="resources/jquery.min.js"></script>
        <script type="text/javascript" src="resources/dash.js"></script>
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
    } else {
    ?>
    <div class="container" id="allowed">
        <h1 class="center">Welcome back, <?php
            echo $_SESSION['gist_user_name'];
        ?></h1>
        <p class="center">Welcome to Info Toast Memos Dashboard!</p>
        <p class="center">Here, you can create memos, as well as see who accessed them.</p>
        <hr>
        <h3>Create new Memo:</h3>
        <form>
            <span class="formInfo">Form Name:  </span><input type="text" name="frmName" id="frmNameBox" placeholder="Name to be put in URL"><span class="invalidWarning" id="frmNameWarning">Max 10 Characters</span>
            <span class="formInfo">Title:      </span><input type="text" name="frmTitle" id="frmTitleBox" placeholder="On Browser Tab"><span class="invalidWarning" id="frmTitleWarning">Max 60 Characters</span>
            <span class="formInfo">Gist URL:   </span><input type="text" name="frmUrl" id="frmUrlBox" placeholder="https://gist.github.com/FrankTCA/vmd378vmsdnjg578ksdb12vjhdf"><span class="invalidWarning" id="frmUrlWarning">Must be a valid github gist url with format https://gist.github.com/YourName/gistletters</span>
        </form>
        <button onClick="onSubmit()" class="submitbtn" id="frmSubmitBtn">Create New Memo</button>
        <p id="status">You shouldn't see this!</p>
        <hr>
        <h3>Your Existing Memos:</h3>
        <div style="overflow-x:auto;">
            <table style="width: 100%">
                <tr>
                    <th style="width: 5%">Memo ID:</th>
                    <th style="width: 15%">Link name (https://infotoast.org/memos/)</th>
                    <th style="width: 35%">Gist URL</th>
                    <th style="width: 10%">Timestamp Created:</th>
                    <th style="width: 30%">Title:</th>
                    <th style="width: 5%">Times Accessed</th>
                    <th>More Info</th>
                </tr>
                <?php
                    $creds = new Creds();
                    $conn = new mysqli($creds->get_host(), $creds->get_username(), $creds->get_password(), $creds->get_database());

                    if ($conn->connect_error) {
                        if ($creds->is_development()) {
                            die("Connection to database failed: " . $conn->connect_error);
                        } else {
                            die("Error connecting to database! Please contact an admin!");
                        }
                    }

                    function truncate_url($in) {
                        $str = $in;
                        if (strlen($str) > 25) {
                            $str = substr($str, 0, 22);
                            $str .= "...";
                        }
                        return $str;
                    }

                    function string_null_stuff($in) {
                        if (!isset($in)) {
                            return "NULL";
                        }
                        return $in;
                    }

                    $stmt = $conn->prepare("SELECT * FROM `memos` WHERE user_created = ?;");
                    $stmt->bind_param("i", $uid);

                    $uid = $_SESSION['gist_user_id'];
                    $stmt->execute();
                    $result = $stmt->get_result();

                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr><td>".$row['id']."</td><td>".$row['name']."</td><td><a href=\"".$row['url']."\">".
                            truncate_url($row['url'])."</a></td><td>".$row['time_created']."</td><td>";

                        $stmt2 = $conn->prepare("SELECT COUNT(*) FROM `log` WHERE `memo_id` = ?;");
                        $stmt2->bind_param("i", $lid);

                        $lid = $row['id'];
                        $stmt2->execute();
                        $result2 = $stmt2->get_result();

                        while ($row2 = mysqli_fetch_assoc($result2)) {
                            echo "</td><td>".$row2['COUNT(*)']."</td>";
                        }

                        echo "<td><a href=\"moreinfo.php?link=".$row['name']."\">More Info</a></td></tr>";
                    }
                ?>
            </table>
        </div>
    </div>
    <?php
    }
    ?>
    </body>
</html>
