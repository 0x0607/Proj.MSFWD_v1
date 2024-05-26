<?php
if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) exit;
/****************************************************************************************
 * 
 * 登入系統元件
 * 
 ****************************************************************************************/
// 引入的CSS及JS
$INCLUDE_CSS[] = "/css/login.css";
$INCLUDE_JS[] = "/javascripts/login_check.js";
/****************************************************************************************/
$NO_NAV = true;
if (isset($_POST['test'])) {
    exit("TEST");
} else {
    $localAuth = false;
    $login_method = isset($_GET['login']) ? $_GET['login'] : "";
    switch ($login_method) {
        case "steam":
            require_once "login.steam.php";
            break;
        case "line":
            require_once "login.line.php";
            break;
        case "discord":
            require_once "login.discord.php";
            break;
        case "facebook":
            require_once "login.facebook.php";
            break;
        case "google":
            require_once "login.google.php";
            break;
        default:
            echo '<!-- local login -->';
            $localAuth = true;
            break;
    }
    if (!$localAuth && isset($mid)) {
        $externalAuthInfo = apiRespondJsonData(json_encode(["action" => "USER_GET_INFORMATION", "mid" => $mid]), SERVER_API_URL);
        if (!empty($externalAuthInfo)) {
            $externalAuthResult = json_decode($externalAuthInfo, true);
            $_SESSION["member"] = $externalAuthResult['data'];
            setcookie("last_account", $_SESSION["member"]['account'], time() - 1, "/");
        }
    }

    if (isset($_SESSION["member"])) {
        header("location: " . SERVER_URL);
        exit;
    }
}
if (isset($_COOKIE['last_account'])) $smarty->assign("last_account", $_COOKIE['last_account']);
