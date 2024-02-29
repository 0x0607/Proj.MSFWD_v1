<?php
require_once("../sys.global.php");
if (!isset($_SESSION['member']['mid'])) httpRespondJson(["status" => 401, "data" => "No access."]);
if ($_SESSION['member']['mid'] != 589605057335390208) httpRespondJson(["status" => 401, "data" => "No access."]);
$now = time();
?>

<html>

<head>
    <title>簡易快速架站工具</title>
</head>

<body>
    <!-- <p>現在時間為<time><?php echo date("Y-m-d H:i:s", $now); ?></time></p> -->
    <p>Timestamp：<code><?php echo $now; ?></code></p>
    <p>C#Tick：<code><?php echo number_format(convertTimestampToCSharpTick($now), 0, '', ''); ?></code></p>

    <form>
    </form>
    <div style="margin: 0 auto;width: 87%">
        <h1 style="margin: 80px auto 0 auto;">網站列表</h1>
        <table style="background-color: rgba(0,0,0,.45); color:white; border-radius: 20px; width: 100%; padding: 20px 10px; margin: 0px auto;">
            <tr>
                <td style="text-align: center;">icon</td>
                <td>id</td>
                <td>domain name</td>
                <td>status</td>
            </tr>
            <?php
            echo "<!--Hello world-->";
            $website = $websiteManage->getAllWebsite();
            foreach ($website as $w) {
                echo "<tr>";
                echo "<td style='text-align: center;'><img src='{$w['icon']}' style='width:64px;' onerror=\"this.src='../assets/images/error404.png'\"></img></td>";
                echo "<td>{$w['id']}</td>";
                echo "<td><a style='color:white;' href='http://{$w['domain']}' target='_blank'>{$w['domain']}</a></td>";
                echo "<td>" . ($w['status'] ? "<span style='background-color:green;'>ENABLE</span>" : "<span style='background-color:red;'>DISABLE</span>") . "</td>";
                echo "</tr>";
            }
            ?>
        </table>
    </div>


</body>