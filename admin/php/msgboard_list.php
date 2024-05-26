<?php
if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) exit;
/****************************************************************************************
 * 
 * 公告欄
 * 
 ****************************************************************************************/
$INCLUDE_CSS[] = "/css/admin.msgboard.css";
// $INCLUDE_JS[] = "/javascripts/admin.msgboard.js";
$orderby = 'weight';
$limit = 10;
$lastPage = 0;
if (isset($_GET['orderby'])) {
    $validOrderBy = ['title', 'author', 'weight', 'created_at'];
    $orderby = in_array(trim($_GET['orderby']), $validOrderBy) ? trim($_GET['orderby']) : 'weight';
}
if (!isset($_GET['order'])) $PAGE_ORDER = 'desc';

if (isset($_GET['limit'])) {
    $limit = intval(trim($_GET['limit'])) ? intval(trim($_GET['limit'])) : $limit;
}

$msgBoardCount = $msgBoard->getAllPostCount();
if ($msgBoardCount) {
    $lastPage = ceil($msgBoardCount / $limit);
    $PAGE_NUM = max(1, min($PAGE_NUM, $lastPage));
}
$msgBoardMsgs = $msgBoard->getpost(null, ($PAGE_NUM - 1) * $limit, $limit, "{$orderby} {$PAGE_ORDER}");
$msgBoardTypes = $msgBoard->getAllPostType();

foreach ($msgBoardMsgs as $key => $value) {
    $HASH_RAWDATA["pid"] = $msgBoardMsgs[$key]['id'];
    $msgBoardMsgs[$key]['hashdata'] = encryptData($HASH_RAWDATA, WEBSITE_HASH_KEY, $DATA['iv']);
}
foreach ($msgBoardTypes as $key => $value) {
    $HASH_RAWDATA["tid"] = $msgBoardTypes[$key]['id'];
    $msgBoardTypes[$key]['hashdata'] = encryptData($HASH_RAWDATA, WEBSITE_HASH_KEY, $DATA['iv']);
}
if ($lastPage > 1) $smarty->assign("lastPage", $lastPage);
if($msgBoardTypes) $smarty->assign("messagesTypes", $msgBoardTypes);
$smarty->assign("messages", $msgBoardMsgs);
$smarty->assign("orderby", $orderby);
$smarty->assign("limit", $limit);
