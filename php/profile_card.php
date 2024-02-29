<?php
$LOG_STATUS_CODE["profile_card"] = "1|OK";
$LOG_REASON["profile_card"] = "";
/****************************************************************************************
 * 
 * 資訊卡頁面
 * 
 ****************************************************************************************/
// 引入的CSS及JS
$includeCss[] = "./css/profileCard.css";
// $includeJs[] = "./javascripts/.js";
/****************************************************************************************/
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
// 必須至少登入或者有附上mid 或者帳戶
if (isset($_GET['account'])) $findAccountKey = strtolower(trim($_GET['account']));
else if (isset($_SESSION["member"]['mid'])) $findAccountKey = $_SESSION["member"]['mid'];

// 擷取使用者資訊
$DATA = json_encode(["action" => "USER_GET_INFORMATION", "find" => $findAccountKey]);
$userResult = apiRespondJsonData($DATA, SERVER_API_URL);
if (isset($userResult)) {
    $res = json_decode($userResult, true);
    $data = $res['data'];
    $data['introduction'] = nl2br($data['introduction']);
    $smarty->assign("user", $data);
} else {
    header("location: ?route=login");
    exit;
}
