<?php
if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) exit;
/****************************************************************************************
 * 
 * 個人化頁面
 * 
 ****************************************************************************************/
// 引入的CSS及JS
$INCLUDE_CSS[] = "/css/memberProfileEdit.css";
$INCLUDE_JS[] = "/javascripts/login_check.js";
/****************************************************************************************/
// 沒有登入
if (!isset($_SESSION["member"]['mid'])) header("location: ?route=login");

// 擷取使用者資訊
$HASH_RAWDATA["mid"] = $_SESSION["member"]['mid'];
$userResult = apiRespondJsonData(json_encode(["action" => "USER_GET_INFORMATION", "hash" => encryptData($HASH_RAWDATA, WEBSITE_HASH_KEY, $DATA['iv']), "iv" => $DATA['iv']]), SERVER_API_URL);
if (isset($userResult)) {
    $res = json_decode($userResult, true);
    $data = $res['data'];
    $PAGE_ICON = SERVER_URL . $data['avatar'];
    $PAGE_NAME = $data['nickname'];
    $data['introduction'] = nl2br($data['introduction']);
    $smarty->assign("user", $data);
} else {
    header("location: " . SERVER_URL . "/account/");
    exit;
}
// Array
// (
//     [id] => 589605057335390208
//     [nickname] => Administrator
//     [avatar] => ../assets/uploads/avatar/589605057335390208.png?v=1342660002845814784
//     [account] => root
//     [introduction] => 這個人很懶，什麼都沒寫
//     [theme] => #EA6C6C
//     [roles] => Array
//         (
//             [0] => 所有人
//             [1] => 超級管理員
//         )

// )