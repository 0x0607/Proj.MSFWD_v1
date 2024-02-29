<?php
$LOG_STATUS_CODE["logout"] = "1|OK";
$LOG_REASON["logout"] = "";
/****************************************************************************************
 * 
 * 登出系統元件
 * 
 ****************************************************************************************/
// 引入的CSS及JS
// $includeCss[]="./css/".WEBSITE['stylesheet']."/.css";
// $includeJs[] = "./javascripts/.js";
/****************************************************************************************/
// unset($_SESSION['account']);
// unset($_SESSION['mid']);
$_SESSION = array();
session_destroy();
// $log->addSystemLog($sf->getId(), WEBSITE_ID, $OPERATOR_ID, $USER_IP_ADDRESS, "LOGOUT_SUCCESS");
echo "<!--跳轉頁面中...-->";
header("location: /");
exit;