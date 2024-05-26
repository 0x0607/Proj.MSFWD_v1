<?php
require '../sys.init.php';          // 頁面初始化
require './admin.pages.php';        // 所有頁面
/*************************************************************
 *
 * 管理頁面的主頁
 * 
 *************************************************************/
// 載入資源
$TEMPLATES_DIR = "../templates/";
$TEMPLATE_DIR = "admin";
$INCLUDE_JS[] = "/javascripts/hiddenMenu.js";
$INCLUDE_CSS[] = "/css/admin.css";
$BREAD_CRUMB[] = ["displayname"=>"管理","link"=>"/admin/"];
/*************************************************************/
// 沒有登入
if(!isset($_SESSION['member']['mid'])) header("location: " . SERVER_URL . "/account/?route=login");

// 不具有任何權限
if (!$MEMBER_PERMISSIONS) header("location: " . SERVER_URL . "/account/");
//if (!$MEMBER_PERMISSIONS) httpRespondJson(["status" => 401, "data" => "No access."]);

// 錯誤的動作請求
if (!isset($PAGES[$PAGE])) httpRespondJson(["status" => 400, "data" => "Invalid page."]);
// 沒有存取權限
if ($PAGES[$PAGE]['permission']) {
    // if (!(($PAGES[$PAGE]['permission'] & $MEMBER_PERMISSIONS) || $MEMBER_PERMISSIONS & 1)) httpRespondJson(["status" => 401, "data" => "Invalid access."]);
    if (!(($PAGES[$PAGE]['permission'] & $MEMBER_PERMISSIONS) || $MEMBER_PERMISSIONS & 1)) header("location: ".SERVER_URL."/admin/");
    else $BREAD_CRUMB[] = ["displayname"=>$PAGES[$PAGE]['displayname'],"link"=>"/admin/?route={$PAGE}"];
}
/*************************************************************/
// 網站使用物件
// 將頁面資訊寫進smartyAssign
$smarty->assign("page_information", $PAGES[$PAGE]);
// 引入相關PHP
include_once "./php/{$PAGES[$PAGE]['filename']}.php";
include_once "./navbar.php";
/************************************************************/
// 引入JS及網站名稱與簡介
$PAGE_NAME = WEBSITE['displayname']."｜".$PAGES[$PAGE]['displayname'];
$PAGE_DESC = "管理者専用ページ.";
/************************************************************/
// 開始運作
require '../sys.base.php';
// $now = time();
// number_format(convertTimestampToCSharpTick($now), 0, '', '');
