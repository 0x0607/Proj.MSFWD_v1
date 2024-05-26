<?php
if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) exit;
/****************************************************************************************
 * 
 * 資訊卡頁面
 * 
 ****************************************************************************************/
// 引入的CSS及JS
$INCLUDE_CSS[] = "/css/profileCard.css";
// $INCLUDE_JS[] = "./javascripts/.js";
/****************************************************************************************
 * $_SESSION["member"] 資訊
 * 
 * "mid" => 會員Id,
 * "account" => 會員帳戶,
 * "nickname" => 會員暱稱,
 * "avatar" => 會員頭貼,
 * "introduction" => 會員介紹,
 * "theme" => 會員主題,
 * "last_ip_address" => 會員最後登入位址
 * 
 *****************************************************************************************/
unset($DATA);
// 必須至少登入或者有附上mid 或者帳戶
if (isset($_GET['mid'])) {
    $findKey = trim($_GET['mid']);
    $DATA = json_encode(["action" => "USER_GET_INFORMATION", "mid" => "{$findKey}"]);
} else if (isset($_GET['id'])) {
    $findKey = trim($_GET['id']);
    $DATA = json_encode(["action" => "USER_GET_INFORMATION", "mid" => "{$findKey}"]);
} else if (isset($_GET['account'])) {
    $findKey = htmlspecialchars(strtolower(trim($_GET['account'])));
    $DATA = json_encode(["action" => "USER_GET_INFORMATION", "account" => "{$findKey}"]);
} else if (isset($_SESSION["member"])) {
    $findKey = $_SESSION["member"]['mid'];
    $data = $_SESSION['member'];
} else header("location: " . SERVER_URL . "/account/?route=login");

// 其他人的帳戶
if (isset($DATA)) {
    $userResult = apiRespondJsonData($DATA, SERVER_API_URL);
    $res = json_decode($userResult, true);
    $data = $res['data'];
    // 釋放記憶體
    unset($DATA);
}
// 擷取使用者資訊
$PAGE_ICON = SERVER_URL . $data['avatar'];
$PAGE_NAME = WEBSITE['displayname'] . "｜" . $data['nickname'];
$PAGE_DESC = $data['introduction'];
$data['introduction'] = nl2br(htmlspecialchars($data['introduction']));
if (isset($_SESSION["member"]) && ($findKey == $_SESSION["member"]['mid'] || $findKey == $_SESSION["member"]['account'])) {
    $data['attendance'] = $memberManage->updateAttendance($_SESSION["member"]['mid']);
    $smarty->assign("_self_account", true);
} else $BREAD_CRUMB[] = ["displayname" => "{$data['account']}", "link" => "/account/?account={$data['account']}"];
$smarty->assign("user", $data);
// 釋放記憶體
unset($data);

// } else $BREAD_CRUMB[] = ["displayname" => "account", "link" => "/account/?account=unknown"];
// else {
    // header("location: " . SERVER_URL . "/account/?route=login");
    // exit;
// }
