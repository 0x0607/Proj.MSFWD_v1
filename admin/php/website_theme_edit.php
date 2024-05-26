<?php
if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) exit;
$INCLUDE_CSS[] = "/css/admin.theme.manage.css";
$INCLUDE_JS[] = "/javascripts/dynamicallyCSS.js";

$file->setPath(WEBSITE_FILE_DIR);
// 解析網站配色
$tempFileData = $file->readFile('color.css');
if (!empty($tempFileData)) {
    $web_color = $colorPicker::parseColorToArray($tempFileData);
}

// header("Content-Type: application/json");
$colorSetting = [
    // 背景顏色
    "sunbaby-bg" => ["label" => "背景顏色"],
    // 導覽列顏色
    "nav-bg" => ["label" => "導覽列背景色"],
    "nav-icon-ft" => ["label" => "導覽列標題文字顏色"],
    // "nav-ft" => ["label" => "導覽列文字顏色"],
    "nav-btn-ft" => ["label" => "導覽列按鈕文字顏色"],
    "nav-btn-bg" => ["label" => "導覽列按鈕背景顏色"],
    "nav-btn-hover-ft" => ["label" => "導覽列按壓按鈕文字顏色"],
    "nav-btn-hover-bg" => ["label" => "導覽列按壓按鈕背景顏色"],
    // "nav-icon-bg" => ["label" => "導覽列標題背景顏色"],
    // "nav-dropdown-bg" => ["label" => "導覽列下拉清單背景顏色"],
    "link-ft" => ["label" => "連結文字顏色"],
    "link-hover-ft" => ["label" => "連結懸停文字顏色"],
    // 碼顏色
    // "code-ft" => ["label" => "碼顏色"],
    // "code-hover-ft" => ["label" => "碼懸停文字顏色"],
    // 系統資訊顏色(文字、底色通用)
    // "warn" => ["label" => "系統警告顏色"],
    // "prom" => ["label" => "系統注意顏色"],
    // "succ" => ["label" => "系統完成顏色"],
    // "warn-hover" => ["label" => "系統警告懸停顏色"],
    // "prom-hover" => ["label" => "系統注意懸停顏色"],
    // "succ-hover" => ["label" => "系統完成懸停顏色"],
    // switch-slider顏色
    // "switch-slider" => ["label" => "開關滑塊顏色"],
    // "switch-slider-checked-bg" => ["label" => "已選中開關滑塊背景顏色"],
    // "switch-slider-checked-hover-bg" => ["label" => "已選中開關滑塊懸停背景顏色"],
    // "switch-slider-bg" => ["label" => "開關滑塊背景顏色"],
    // "switch-slider-hover-bg" => ["label" => "開關滑塊懸停背景顏色"],
    // button文字顏色
    // 基本顏色文字
    // "primary" => "#FFFFFF",
    // "accent" => "#2879F6",
    // "secondary" => "#000000",
    "primary-ft" => ["label" => "主要文字顏色"],
    "accent-ft" => ["label" => "強調文字顏色"],
    "secondary-ft" => ["label" => "次要文字顏色"],
    // 基本背景顏色
    "primary-bg" => ["label" => "主要背景顏色"],
    "accent-bg" => ["label" => "強調背景顏色"],
    "secondary-bg" => ["label" => "次要背景顏色"],
    "primary-btn-ft" => ["label" => "主要按鈕文字顏色"],
    "primary-btn-hover-ft" => ["label" => "主要按鈕懸停文字顏色"],
    "accent-btn-ft" => ["label" => "強調按鈕文字顏色"],
    "accent-btn-hover-ft" => ["label" => "強調按鈕懸停文字顏色"],
    "secondary-btn-ft" => ["label" => "次要按鈕文字顏色"],
    "secondary-btn-hover-ft" => ["label" => "次要按鈕懸停文字顏色"],
    // button背景顏色
    "primary-btn-bg" => ["label" => "主要按鈕背景顏色"],
    "primary-btn-hover-bg" => ["label" => "主要按鈕懸停背景顏色"],
    "accent-btn-bg" => ["label" => "強調按鈕背景顏色"],
    "accent-btn-hover-bg" => ["label" => "強調按鈕懸停背景顏色"],
    "secondary-btn-bg" => ["label" => "次要按鈕背景顏色"],
    "secondary-btn-hover-bg" => ["label" => "次要按鈕懸停背景顏色"],
    // 登出按鈕文字顏色
    // "logout-btn-ft" => ["label" => "登出按鈕文字顏色"],
    // "logout-btn-hover-ft" => ["label" => "登出按鈕懸停文字顏色"],
    // 登出按鈕背景顏色
    // "logout-btn-bg" => ["label" => "登出按鈕背景顏色"],
    // "logout-btn-hover-bg" => ["label" => "登出按鈕懸停背景顏色"],
    // 登入按鈕文字顏色
    // "login-btn-ft" => ["label" => "登入按鈕文字顏色"],
    // "login-btn-hover-ft" => ["label" => "登入按鈕懸停文字顏色"],
    // 登入按鈕背景顏色
    // "login-btn-bg" => ["label" => "登入按鈕背景顏色"],
    // "login-btn-hover-bg" => ["label" => "登入按鈕懸停背景顏色"],
    // 帳戶按鈕文字顏色
    // "user-btn-ft" => ["label" => "帳戶按鈕文字顏色"],
    // "user-btn-hover-ft" => ["label" => "帳戶按鈕懸停文字顏色"],
    // 帳戶按鈕背景顏色
    // "user-btn-bg" => ["label" => "帳戶按鈕背景顏色"],
    // "user-btn-hover-bg" => ["label" => "帳戶按鈕懸停背景顏色"],
    // 用戶設置按鈕文字顏色
    // "user-setting-btn-ft" => ["label" => "用戶設置按鈕文字顏色"],
    // "user-setting-btn-hover-ft" => ["label" => "用戶設置按鈕懸停文字顏色"],
    // 用戶設置按鈕背景顏色
    // "user-setting-btn-bg" => ["label" => "用戶設置按鈕背景顏色"],
    // "user-setting-btn-hover-bg" => ["label" => "用戶設置按鈕懸停背景顏色"],
    // input文字顏色
    // "input-ft" => ["label" => "input文字顏色"],
    // "input-focus-ft" => ["label" => "input焦點文字顏色"],
    // input背景顏色
    // "input-bg" => ["label" => "input背景顏色"],
    // "input-focus-bg" => ["label" => "input焦點背景顏色"],
    // scrollbar顏色
    // "scrollbar-track" => ["label" => "scrollbar軌道顏色"],
    // "scrollbar-thumb" => ["label" => "scrollbar滾動輪顏色"],
    // "scrollbar-thumb-hover" => ["label" => "scrollbar滾動輪懸停顏色"]
];

foreach ($web_color as $key => $color) {
    if (isset($colorSetting[$key])) $colorSetting[$key]['color'] = $color;
}
// print_r($colorSetting);
$smarty->assign("web_icon", WEBSITE_ICON_URL);
$smarty->assign("web_name", WEBSITE['displayname']);
$smarty->assign("color", $colorSetting);
