<?php
require '../sys.init.php';          // 頁面初始化
require './account.pages.php';      // 所有頁面
$BREAD_CRUMB[] = ["displayname"=>"帳戶","link"=>"/account/"];
/*************************************************************
 *
 * 帳戶管理的主頁
 * 
 *************************************************************/
// 載入資源
$TEMPLATES_DIR = "../templates/";
$TEMPLATE_DIR = "account";
$INCLUDE_CSS[] = '/css/navbar.default.css';
$INCLUDE_JS[] = '/javascripts/nav.dynamicllyMenu.js';
/*************************************************************/
// 預設為帳戶頁面
if (!isset($PAGES[$PAGE])) $PAGE = 'profile';
if($PAGE!='profile') $BREAD_CRUMB[] = ["displayname"=>$PAGES[$PAGE]['displayname'],"link"=>"/account/?route={$PAGE}"];
/*************************************************************/
// 網站使用物件
// 將頁面資訊寫進smartyAssign
$smarty->assign("load_page", $PAGES[$PAGE]['filename']);
/************************************************************/
// 引入JS及網站名稱與簡介
$PAGE_NAME = WEBSITE['displayname']."｜".$PAGES[$PAGE]['displayname'];
$PAGE_DESC = "帳戶資訊";
$PAGE_NAV_THEME = "default";
/************************************************************/
// 引入相關PHP
include_once "./php/{$PAGES[$PAGE]['filename']}.php";
include_once "./navbar.php";
/************************************************************/
// 將導覽列資訊寫進smartyAssign
$smarty->assign("nav_theme", $PAGE_NAV_THEME);
// 開始運作
require '../sys.base.php';
