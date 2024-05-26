<?php
require 'sys.init.php';          // 頁面初始化
/*************************************************************
 *
 * 主頁 Part.4
 * 
 *************************************************************/
// 載入資源
$INCLUDE_CSS[] = '/css/navbar.default.css';
$INCLUDE_JS[] = '/javascripts/nav.dynamicllyMenu.js';
/*************************************************************/
// 讀取頁面資訊
$PAGE_INFORMATION = $pageManage->getPageInformation($PAGE);
if (empty($PAGE_INFORMATION)) {
    $PAGE = "home";
    $PAGE_INFORMATION = $pageManage->getPageInformation($PAGE);
}
// 刪除的頁面
if ($PAGE_INFORMATION['status'] <= 0) {
    $PAGE = "home";
    $PAGE_INFORMATION = $pageManage->getPageInformation($PAGE);
}
if ($PAGE != "home") {
    $BREAD_CRUMB[] = ["displayname" => $PAGE_INFORMATION['displayname'], "link" => "/?route={$PAGE}"];
}
$PAGE_NAME = WEBSITE['displayname'] . "｜" . $PAGE_INFORMATION['displayname'];
$PAGE_DESC = $PAGE_INFORMATION['description'];
$PAGE_ID = $PAGE_INFORMATION['id'];
$WEB_NAV = $PAGE_INFORMATION['nav'] ? $WEB_NAV : '';
/*************************************************************/
// 頁面選擇器
require 'sys.page_router.php';
/************************************************************/
// 紀錄訪問頁面資訊
// include_once 'sys.visit_history.php';
/************************************************************/
// 開始運作
require 'sys.base.php';
