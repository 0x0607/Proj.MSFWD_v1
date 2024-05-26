<?php
if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) exit;
/****************************************************************************************
 * 
 * 登出系統元件
 * 
 ****************************************************************************************/
// 引入的CSS及JS
// $INCLUDE_CSS[]="./css/".WEBSITE['stylesheet']."/.css";
// $INCLUDE_JS[] = "./javascripts/.js";
/****************************************************************************************/
// unset($_SESSION['account']);
// unset($_SESSION['mid']);
if(isset($_SESSION["member"])){
    $_SESSION = array();
    session_destroy();
    // $log->addSystemLog($sf->getId(), WEBSITE_ID, $OPERATOR_ID, $USER_IP_ADDRESS, "LOGOUT_SUCCESS");
    echo "<!--跳轉頁面中...-->";
}
header("location: " . SERVER_URL);
// header("location: " . $LAST_PAGE);
exit;
