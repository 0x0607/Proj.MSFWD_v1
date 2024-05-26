<?php
if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) exit;
/****************************************************************************************
 * 
 * 註冊系統元件
 * 
 ****************************************************************************************/
// 引入的CSS及JS
$INCLUDE_CSS[] = "/css/login.css";
$INCLUDE_JS[] = "/javascripts/login_check.js";
/****************************************************************************************/
$NO_NAV=true;
// 如果已經登入將直接跳轉回會員頁面
if (isset($_SESSION["member"]['mid'])) {
    header("location: " . SERVER_URL . "/account");
    exit;
}
