<?php
if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) exit;
/*************************************************************
 *
 * 網站頁面的初始化 Part.3
 * 
 *************************************************************/
// GLOBAL
require 'sys.global.php';
/*************************************************************/
// Smarty配置
ob_start();
require_once 'libs/smarty/Smarty.class.php';
$smarty = new Smarty;
$smarty->caching = CONFIG_GENERAL['smarty_caching'];
$smarty->cache_lifetime = CONFIG_GENERAL['smarty_cache_lifetime'];
$smarty->force_compile = WEBSITE_DEBUG;
$smarty->debugging = WEBSITE_DEBUG && false;
/*************************************************************/
// 資源
$TEMPLATES = 'index.htm';
$TEMPLATE_DIR = "index";
$TEMPLATES_DIR = "templates/";
$THEME_COLOR_CSS = "/f/website/" . WEBSITE['id'] . "/color.css?v=" . WEBSITE['theme_version'];
$INCLUDE_CSS = array(
    "/css/stylesheet.css",
    "/css/scrollbar.css"
);
$INCLUDE_JS = array(
    // "/javascripts/function.utils.js",
    // "/javascripts/jquery-3.2.1.min.js",
    // "/javascripts/jquery-3.3.1.slim.min.js",
    // "/javascripts/bootstrap.min.js",
    // "/javascripts/popper.min.js"
);
/*************************************************************/
// 頁面確認 breadcrumb 麵包屑導覽
// $BREAD_CRUMB = [];
$BREAD_CRUMB = [["displayname" => "網站", "link" => "/"]];
$PAGE = isset($_GET['route']) ? trim($_GET['route']) : "home";
$LAST_PAGE = (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : "/");
// 上一次瀏覽路徑
$smarty->assign("SERVER_HTTP_REFERER", $LAST_PAGE);
/************************************************************/
// 除錯模式
$smarty->assign("SERVER_DEBUG", WEBSITE_DEBUG ? "enable" : "disable");
/************************************************************/
// 加密之IV及API位址
$DATA = ["iv" => $SESSION_HASHIV];
$HASH_RAWDATA = ["last_ip_address" => $USER_IP_ADDRESS];
$smarty->assign("SERVER_API_URL", SERVER_API_URL);
$smarty->assign("SERVER_IV", $SESSION_HASHIV);
/************************************************************/
// 基礎資訊
$smarty->assign("version", $VERSION);
$smarty->assign("generator", $GENERATOR);
$smarty->assign("design", $DESIGN);
$smarty->assign("author", $AUTHOR);
/************************************************************/
// 網站資訊
$smarty->assign("wid", WEBSITE['id']);
$smarty->assign("siteName", WEBSITE['displayname']);
$smarty->assign("serverDomainName", SERVER_DOMAIN_NAME);
$smarty->assign("distribution", WEBSITE['distribution']);
$smarty->assign("icon", WEBSITE_ICON_URL);
$smarty->assign("language", $WEBSITE_LANGUAGE);
$smarty->assign("themeColorCss", $THEME_COLOR_CSS);
$smarty->assign("serverUrl", SERVER_URL);
/************************************************************/
$file->setPath(WEBSITE_FILE_DIR);
// 解析網站配色
$tempFileData = $file->readFile('color.css');
if (!empty($tempFileData)) {
    $tempDecodeData = $colorPicker::parseColorToArray($tempFileData);
    if (isset($tempDecodeData['sunbaby-bg'])) $smarty->assign("themeColor", $tempDecodeData['sunbaby-bg']);
}
// 導覽列初值
$NO_NAV = false;
$PAGE_ICON = WEBSITE_ICON_URL;
$WEB_NAV = '';
$file->setPath(WEBSITE_FILE_DIR);
$tempFileData = $file->readFile('nav_config.json');
if (!empty($tempFileData)) {
    $tempDecodeData = json_decode($tempFileData, true);
    if ($tempDecodeData !== null && json_last_error() === JSON_ERROR_NONE) {
        $WEB_NAV = $tempDecodeData;
    }
} else $WEB_NAV = '';
// 頁數
$PAGE_NUM = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($PAGE_NUM < 1) $PAGE_NUM = 1;
// 排序
if (isset($_GET['order'])) {
    $validOrder = ['asc', 'desc'];
    $PAGE_ORDER = in_array(trim($_GET['order']), $validOrder) ? trim($_GET['order']) : 'asc';
    unset($validOrder);
}else $PAGE_ORDER = 'asc';
// 釋放記憶體
unset($tempFileData, $tempDecodeData);
/************************************************************/
// 使用者資訊
if (isset($_SESSION["member"]['mid'])) $smarty->assign("member", $_SESSION["member"]);
// Array
// (
//     [mid] => 用戶編號
//     [account] => 帳號
//     [nickname] => 暱稱
//     [avatar] => 頭像
//     [introduction] => 簡介
//     [theme] => 主題顏色
//     [last_ip_address] => 最後登入位址
// )
// 用戶權限
$smarty->assign("memberPermissions", $MEMBER_PERMISSIONS);
/************************************************************/