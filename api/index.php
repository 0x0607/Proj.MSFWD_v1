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
 ***************************************************************************************
 // 範本JS POST (function.utils.js)
    var data = postData({action:"TEST",data:"Test Message."})
    .then(response => {
        console.log(response.data);
    })
    .catch(error => {
        console.log('Error:', error);
    });
 ***************************************************************************************/
$needPermission = [
    "TEST" => -1,
    "SNOWFLAKE" => -1,
    "SNOW" => -1,
    "SN" => -1,
    "SF" => -1,
    "USER_LOCAL_LOGIN" => -1,
    "USER_STEAM_LOGIN" => -1,
    "USER_REGISTER" => -1,
    "USER_GET_INFORMATION" => -1,
    "UNTURNED_DELIVERY" => -1,
    "USER_CHANGE_INFORMATION" => 0,
    "WEBSITE_CHANGE_INFORMATION" => 1,
    "GROUP_ADD" => 2,
    "GROUP_CHANGE_INFORMATION" => 2,
    "GROUP_DELETE" => 2,
    "MEMBER_ADD_GROUP" => 2,
    "MEMBER_REMOVE_GROUP" => 2,
    "SEARCH_USER" => 2,
    "WEBSITE_CHANGE_THEME" => 3,
    "PRODUCT_CHANGE_INFORMATION" => 7,
    "PRODUCT_CHANGE_STATUS" => 7,
    "PRODUCT_DELETE" => 6,
    "PRODUCT_ADD" => 6,
    "PAGE_ADD" => 5,
    "PAGE_CHANGE_INFORMATION" => 5,
    "PAGE_CHANGE_STATUS" => 5,
    "PAGE_CHANGE_COMPONENT_POS" => 5,
    "PAGE_CHANGE_COMPONENT" => 5,
    "PAGE_DELETE" => 5,
    "ORDER_VIEW" => 8,
    "MSGBOARD_ADD" => 10,
    "MSGBOARD_CHANGE_POST" => 10,
    "MSGBOARD_CHANGE_STATUS" => 10,
    "MSGBOARD_DELETE" => 10,
    "MSGBOARD_ADD_TYPE" => 10,
    "MSGBOARD_DELETE_TYPE" => 10,
    "MSGBOARD_CHANGE_TYPE" => 10,
    "GET_TOTAL_AMOUNTS" => 11
];
// 錯誤的動作請求
if (!isset($needPermission[$ACTION])) httpRespondJson(["status" => 400, "data" => "Invalid action."]);
// -1為匿名登入 0為一般用戶 order為具有一定權限之管理員
if ($needPermission[$ACTION] != -1) {
    // 沒有登入
    if (!isset($_SESSION["member"])) httpRespondJson(["status" => 401, "data" => "Invalid session."]);
    // 沒有存取權限
    if ($needPermission[$ACTION] > 0) {
        if (!$permissions->checkMemberPermissions($_SESSION["member"]['mid'], 1 << ($needPermission[$ACTION] - 1))) httpRespondJson(["status" => 200, "data" => "Invalid access."]); //httpRespondJson(["status" => 401, "data" => "Invalid access."]);
    }
}
/***************************************************************************************/
// 回傳JSON
$respondJson = ["status" => 200, "data" => "1|OK"];
// 是否建立操作紀錄
$createLog = true;
// 判斷操作類型
switch ($ACTION) {
        // case "USER_LOCAL_LOGIN":
        // case "USER_STEAM_LOGIN":
        //     require_once "../api/api.login_user.php";
        //     break;
    case "USER_CHANGE_INFORMATION":
        require_once "../api/api.upload_file.php";
    case "USER_LOCAL_LOGIN":
    case "USER_REGISTER":
    case "USER_GET_INFORMATION":
        require_once "../api/api.setting_user.php";
        break;
    case "WEBSITE_CHANGE_INFORMATION":
        require_once "../api/api.upload_file.php";
        require_once "../api/api.setting_website.php";
        break;
    case "WEBSITE_CHANGE_THEME":
        require_once "../api/api.write_file.php";
        require_once "../api/api.setting_website.php";
        break;
    case "PRODUCT_CHANGE_INFORMATION":
    case "PRODUCT_ADD":
        require_once "../api/api.upload_file.php";
    case "PRODUCT_CHANGE_STATUS":
    case "PRODUCT_DELETE":
        require_once "../api/api.setting_product.php";
        break;
    case "GROUP_ADD":
    case "GROUP_CHANGE_INFORMATION":
    case "GROUP_DELETE":
    case "SEARCH_USER":
    case "MEMBER_ADD_GROUP":
    case "MEMBER_REMOVE_GROUP":
        require_once "../api/api.setting_role.php";
        break;
    case "PAGE_ADD":
    case "PAGE_CHANGE_INFORMATION":
    case "PAGE_CHANGE_STATUS":
    case "PAGE_DELETE":
    case "PAGE_CHANGE_COMPONENT_POS":
        require_once "../api/api.setting_page.php";
        break;
    case "PAGE_CHANGE_COMPONENT":
        require_once "../api/api.setting_page_component.php";
        break;
    case "UNTURNED_DELIVERY":
        require_once "../api/api.virtual_product.php";
        break;
    case "GET_TOTAL_AMOUNTS":
        require_once "../api/api.get_data_report.php";
        break;
    case "MSGBOARD_ADD":
    case "MSGBOARD_CHANGE_POST":
    case "MSGBOARD_CHANGE_STATUS":
    case "MSGBOARD_DELETE":
    case "MSGBOARD_ADD_TYPE":
    case "MSGBOARD_DELETE_TYPE":
    case "MSGBOARD_CHANGE_TYPE":
        require_once "../api/api.setting_msgboard.php";
        break;
    case "SNOWFLAKE":
    case "SNOW":
    case "SN":
    case "SF":
        $respondJson = ["status" => 200, "data" => strval($sf->getId())];
        $createLog = false;
        break;
    case "TEST":
        $respondJson = ["status" => 200, "data" => $DATA];
        $createLog = false;
        break;
}
/***************************************************************************************/

// 建立操作紀錄
// if ($createLog && false) {
//     $logInformation = [
//         "ip_address" => $DATA['last_ip_address'],
//         "status" => $LOG_STATUS["api"],
//         "action" => $LOG_ACTION["api"],
//         "reason" => $LOG_REASON["api"]
//     ];
//     $log->addSystemLog($sf->getId(), WEBSITE_ID, $OPERATOR_ID, $logInformation);
// }
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