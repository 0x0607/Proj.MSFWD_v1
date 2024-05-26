<?php
if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) exit;
$pageData = $pageManage->getAllPageInformation();
foreach ($pageData as $pageKey => $value) {
    $HASH_RAWDATA["pid"] = $pageData[$pageKey]['id'];
    $pageData[$pageKey]['hashdata'] = encryptData($HASH_RAWDATA, WEBSITE_HASH_KEY, $DATA['iv']);
}
$smarty->assign("pages_list", $pageData);
unset($pageData);
