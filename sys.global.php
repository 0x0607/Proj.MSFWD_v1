<?php
if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) exit;
/*************************************************************
 * 
 * 設定值 Part.1
 * 
 *************************************************************/
// PHP運行開始時間
$PHP_START_TIME = microtime(true);
require_once 'sys.include.php';
date_default_timezone_set('Asia/Taipei');
session_start();
/*************************************************************/
// 版本資訊
$VERSION = "alpha.1.0.0.1714187435";
$GENERATOR = "Visual Studio Code";
$AUTHOR = "snkms.com";
$DESIGN = "snkms.com";
/*************************************************************/
// 網域配置
define("SERVER_DOMAIN_NAME", $_SERVER['SERVER_NAME']);
/*************************************************************/
// 讀取設定
$ini = parse_ini_file('configs/config.ini', true);
define("CONFIG_DB", $ini['database']);          // 資料庫相關資訊
define("CONFIG_TABLES", $ini['tables']);        // 資料表相關設置
define("CONFIG_GENERAL", $ini['general']);      // 一般設置
define("CONFIG_PATH", $ini['path']);            // 路徑設置
/*************************************************************/
// Database服務
$db = new DBConnection(CONFIG_DB['dbname'], CONFIG_DB['host'], CONFIG_DB['port'], CONFIG_DB['username'], CONFIG_DB['password']);
// 檔案處理服務 預設上傳目錄f
$file = new files('/f/');
// 顏色選擇器
$colorPicker = new colorManage();
/*************************************************************/
// 全稱網址
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
define("SERVER_URL", "{$protocol}://{$_SERVER['HTTP_HOST']}");
define("SERVER_API_URL", "{$protocol}://{$_SERVER['HTTP_HOST']}/api/");
/*************************************************************/
// 透過domainName取得網站資訊
$websiteManage = new websiteManage($db);
$websiteInformation = $websiteManage->getWebsiteInformationByDomain(SERVER_DOMAIN_NAME);
if (empty($websiteInformation)) exit("ERROR DOMAIN!!");
define("WEBSITE", $websiteInformation);
// define("WEBSITE_ID", WEBSITE['id']);
// define("WEBSITE_NAME",  WEBSITE['displayname']);
// define("WEBSITE_DISTRIBUTION", WEBSITE['distribution']);
define("WEBSITE_ICON_URL", SERVER_URL . WEBSITE['icon']);
define("WEBSITE_FILE_DIR", CONFIG_PATH['web'] . '/' . WEBSITE['id']);
$file->setPath(WEBSITE_FILE_DIR);
/*************************************************************/
// 解析網站配置
$tempFileData = $file->readFile('config.json');
if (empty($tempFileData)) exit("Encountered an unresolvable error.");
$tempDecodeData = json_decode($tempFileData, true);
if ($tempDecodeData === null && json_last_error() !== JSON_ERROR_NONE) {
    // if (CONFIG_GENERAL['debug']) exit("Website configuration parsing error, reason:\n" . json_last_error_msg());
    exit("Encountered an unresolvable error");
}
define("WEBSITE_SETTINGS", $tempDecodeData);
// 釋放記憶體
unset($tempFileData, $tempDecodeData);
/*************************************************************/
// Debug模式
define("WEBSITE_DEBUG", (CONFIG_GENERAL['debug'] && WEBSITE_SETTINGS['general']['debug']));
if (!WEBSITE_DEBUG) error_reporting(0);
$db->deBugMode(WEBSITE_DEBUG);
/*************************************************************/
// 重新定位到https
if ($protocol === "http" && !WEBSITE_DEBUG) header("location: https://{$_SERVER['HTTP_HOST']}");
/*************************************************************/
// 藍新金流配置
if (!isset(WEBSITE['store_id'])) exit("ERROR NO NEWEBPAY!!");
define("STORE_PREFIX", WEBSITE['prefix']);                          // 訂單編號前置字元
define("STORE_ID", WEBSITE['store_id']);                            // 藍新提供的 MerchantID
define("STORE_HASH_KEY", WEBSITE['store_hash_key']);                // 藍新提供的HashKey
define("STORE_HASH_IV", WEBSITE['store_hash_iv']);                  // 藍新提供的HashIV
define("STORE_RETURN_URL", WEBSITE['store_return_url']);            // 使用者付款完成時要跳轉到的頁面
define("STORE_CLIENT_BACK_URL", WEBSITE['store_client_back_url']);  // [返回商店] 按鈕網址 (在付款頁面中)
define("STORE_NOTIFY_URL", SERVER_API_URL . "api.done.php");        // 使用者付款完成時，藍新金流發送交易狀態結果的接收網址   
/*************************************************************/
// 雜湊加密
// define("SESSION_HASH_KEY", $HASH_AES_256_CBC['key']);
// define("SESSION_HASH_IV", generateHashKey()['iv']);
define("WEBSITE_HASH_KEY", CONFIG_GENERAL['hash_key']);
// 產生針對 本次Session 的 Initialization Vector
$SESSION_HASHIV = generateHashKey()['iv'];
/*************************************************************/
// 語言配置
if (!isset($_COOKIE['lang'])) $WEBSITE_LANGUAGE = "zh_TW";
else $WEBSITE_LANGUAGE = $_COOKIE['lang'];
// setcookie("lang", "zh_TW");
/*************************************************************/
// steam登入
if (WEBSITE_SETTINGS['general']['enable_steamauth']) {
    define("CONFIG_STEAM", $ini['steam']);                          // Steam設置
    require_once 'api/steamauth/openid.php';
    require_once 'classes/class.steamauthManage.php';
    $steamauthOpenId = new LightOpenID($_SERVER['SERVER_NAME']);
    $steamauthManage = new steamauthManage($db, CONFIG_STEAM['apikey']);
}
if ((WEBSITE_SETTINGS['general']["login_method"] == "steam") && !WEBSITE_SETTINGS['general']['enable_steamauth']) exit("ERROR CONFIGURATION CONFLICT!!");
/*************************************************************/
// Unturned互動
if (WEBSITE_SETTINGS['general']['enable_unturned'] && isset(WEBSITE_SETTINGS['unturned'])) {
    require_once 'classes/class.unturnedManage.php';
    $unturnedDB = new DBConnection(WEBSITE_SETTINGS['unturned']['datebase']['dbname'], WEBSITE_SETTINGS['unturned']['datebase']['host'], WEBSITE_SETTINGS['unturned']['datebase']['port'], WEBSITE_SETTINGS['unturned']['datebase']['username'], WEBSITE_SETTINGS['unturned']['datebase']['password']);
    $unturnedDB->deBugMode(WEBSITE_DEBUG);
    $unturnedManage = new unturnedManage($unturnedDB);
    $unturnedManage->__settings(WEBSITE_SETTINGS['unturned']['tables']);
}
/*************************************************************/
// 會員
$memberManage = new memberManage($db);
$memberManage->setWebsiteId(WEBSITE['id']);
// 權限
$permissions = new permissions($db);
$permissions->setWebsiteId(WEBSITE['id']);
// 紀錄
// $log = new syslog($db);
// 商品
$productManage = new productManage($db);
$productManage->setWebsiteId(WEBSITE['id']);
// 頁面
$pageManage = new pageManage($db);
$pageManage->setWebsiteId(WEBSITE['id']);
// 雪花算法ID
$sf = new snowflake(31, 31);
// 公告欄
$msgBoard = new msgBoard($db);
$msgBoard->setWebsiteId(WEBSITE['id']);
/*************************************************************/
// 用戶SESSION ID
if (!isset($_COOKIE["sessionId"])) {
    setcookie("sessionId", $sf->getId(), time() + (3600 * 24 * 7), "/");
    // 登出用戶
    if (isset($_SESSION["member"])) {
        $_SESSION = array();
        session_destroy();
    }
}
/************************************************************/
// 用戶的網際協定位址及使用裝置
// if (!empty($_SERVER["HTTP_CLIENT_IP"])) $USER_IP_ADDRESS = $_SERVER["HTTP_CLIENT_IP"];
// elseif (!empty($_SERVER["HTTP_X_FORWARDED_FOR"])) $USER_IP_ADDRESS = $_SERVER["HTTP_X_FORWARDED_FOR"];
// else $USER_IP_ADDRESS = $_SERVER["REMOTE_ADDR"];
$USER_IP_ADDRESS = $_SERVER["REMOTE_ADDR"];
$USER_AGENT = $_SERVER['HTTP_USER_AGENT'] ?? "unknown";
$USER_AGENT = (strlen($USER_AGENT) > 255) ? "unknown" : $USER_AGENT;
/*************************************************************/
// 操作狀態碼 及 原木原因
$STATUS_CODE = [];
$LOG_REASON = [];
// 測試用資訊
$TESTID = 589605057335390208;
/*************************************************************/
// 調適資訊
if ((strpos($USER_AGENT, 'curl') !== false) /*&& WEBSITE_DEBUG*/) {
    echo "Conncet {$_SERVER['SERVER_NAME']} Success!!\nIP: {$USER_IP_ADDRESS}\nTimestamp: " . time();
    exit;
}
// 網站被關閉
if (!WEBSITE['status']) {
    exit(SERVER_DOMAIN_NAME . " has been deactivated.");
}
/*************************************************************/
// 取得登入之用戶權限
$MEMBER_PERMISSIONS = 0;
if (isset($_SESSION["member"])) $MEMBER_PERMISSIONS = $permissions->getMemberPermissions($_SESSION["member"]['mid']);
/*************************************************************/
