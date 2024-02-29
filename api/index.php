<?php
require_once "../api/api.init.php";
/****************************************************************************************
 * 
 * API 總機
 * 
 ****************************************************************************************/
$LOG_STATUS["api"] = "0|NO";
$LOG_ACTION["api"] = $ACTION;
$LOG_REASON["api"] = "No reason.";
/****************************************************************************************
 * 權限表一覽
 * -1:  匿名
 * 0:   一般用戶
 * 1:   所有權限(Administrator)
 * 2:   群組管理
 * 3:   網站基本資料維護
 * 4:   選單維護
 * 5:   頁面維護
 * 6:   商品建檔
 * 7:   商品調整
 * 8:   訂單查詢
 * 9:   訂單管理
 * 10:  訂單客服
 * 11:  銷售統計報表
 ***************************************************************************************/
$needPermission = [
    "TEST" => -1,
    
    "USER_LOCAL_LOGIN" => -1,
    "USER_STEAM_LOGIN" => -1,
    "USER_REGISTER" => -1,
    "USER_GET_INFORMATION" => -1,
    "UNTURNED_DELIVERY" => -1,
    "USER_CHANGE_INFORMATION" => 0,
    "USER_CHANGE_AVATAR" => 0,
    "WEBSITE_CHANGE_STATUS" => 1,
    "WEBSITE_CHANGE_NEWEBPAY" => 1,
    "WEBSITE_CHANGE_INFORMATION" => 3,
    "WEBSITE_CHANGE_ICON" => 3,
    "PRODUCT_CHANGE_INFORMATION" => 7,
    "PRODUCT_CHANGE_IMAGE" => 7,
    "PRODUCT_CHANGE_STATUS" => 6,
    "PRODUCT_ADD" => 6,
    "ORDER_VIEW" => 8,
    "SALES_REPORT_VIEW" => 11,
];
// 錯誤的動作請求
if (!isset($needPermission[$ACTION])) httpRespondJson(["status" => 400, "data" => "Invalid action."]);
if ($needPermission[$ACTION] >= 0) {
    // 沒有登入
    if (!isset($_SESSION["member"])) httpRespondJson(["status" => 401, "data" => "Invalid session."]);
    // 沒有存取權限
    if (!$permissions->checkMemberPermissions(WEBSITE_ID, $_SESSION["member"]['mid'], $needPermission[$ACTION])) httpRespondJson(["status" => 401, "data" => "Invalid access."]);
}
/***************************************************************************************/
// 回傳JSON
$respondJson = ["status" => 200, "data" => "1|OK"];
// 是否建立操作紀錄
$createLog = true;
// 判斷操作類型
switch ($ACTION) {
    case "USER_LOCAL_LOGIN":
    case "USER_STEAM_LOGIN":
        require_once "../api/api.login_user.php";
        break;
    case "USER_REGISTER":
    case "USER_GET_INFORMATION":
        require_once "../api/api.setting_user.php";
        break;
    case "WEBSITE_CHANGE_STATUS":
    case "WEBSITE_CHANGE_NEWEBPAY":
    case "WEBSITE_CHANGE_INFORMATION":
        require_once "../api/api.setting_website.php";
        break;
    case "PRODUCT_CHANGE_INFORMATION":
    case "PRODUCT_CHANGE_STATUS":
    case "PRODUCT_ADD":
        break;
    case "WEBSITE_CHANGE_ICON":
    case "PRODUCT_CHANGE_IMAGE":
    case "USER_CHANGE_AVATAR":
        require_once "../api/api.upload_file.php";
        break;
    case "UNTURNED_DELIVERY":
        require_once "../api/api.virtual_product.php";
        break;
    case "TEST":
        $respondJson = ["status" => 200, "data" => $DATA];
        $createLog = false;
        break;
}
/***************************************************************************************/

// 建立操作紀錄
if ($createLog && false) {
    $logInformation = [
        "ip_address" => $DATA['last_ip_address'],
        "status" => $LOG_STATUS["api"],
        "action" => $LOG_ACTION["api"],
        "reason" => $LOG_REASON["api"]
    ];
    $log->addSystemLog($sf->getId(), WEBSITE_ID, $OPERATOR_ID, $logInformation);
}
// EOF
httpRespondJson($respondJson);



// if (!isset($_POST['data'])) {
//     http_response_code(401);
//     exit(json_encode(["status" => 401, "data" => "Invalid session"]));
// }
// else{
//     $rawData = json_decode($_POST['data']);
//     $data = decryptData($rawData['data'], WEBSITE_HASH_KEY, $rawData['iv']);
//     $operatorId = $data['user'];
// }
// $PermissionsList = $permissions->getPermissionsList();