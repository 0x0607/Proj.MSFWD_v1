<?php
if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) exit;
/*************************************************************
 *
 * 執行smarty Part.6
 * 
 *************************************************************/
$smarty->assign("dir", $TEMPLATE_DIR);
/************************************************************/
// 引入JS及網站名稱與簡介
$smarty->assign("route", $PAGE);
$smarty->assign("css", $INCLUDE_CSS);
$smarty->assign("js", $INCLUDE_JS);
$smarty->assign("ogimage", $PAGE_ICON);
$smarty->assign("title", $PAGE_NAME);
$smarty->assign("description", $PAGE_DESC);
$smarty->assign("page", $PAGE_NUM);
$smarty->assign("page_order", $PAGE_ORDER);
// 導覽列
if (!empty($WEB_NAV) && !$NO_NAV) $smarty->assign("webnav", $WEB_NAV);
// 麵包屑
if (!empty($WEB_NAV)) $smarty->assign("breadcrumb", $BREAD_CRUMB);
/************************************************************/
// 加密傳送資訊
$smarty->assign("SERVER_SEND_HASH_DATA", encryptData($HASH_RAWDATA, WEBSITE_HASH_KEY, $SESSION_HASHIV));
/*************************************************************/
// 開始運作
$smarty->setTemplateDir($TEMPLATES_DIR);
$smarty->display($TEMPLATES);
