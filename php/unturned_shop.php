<?php
$LOG_STATUS_CODE["unturned_shop"] = "1|OK";
$LOG_REASON["unturned_shop"] = "";
/****************************************************************************************
 * 
 * 錯誤資訊元件
 * 
 ****************************************************************************************/
// 引入的CSS及JS
// $includeCss[]="./css/".WEBSITE['stylesheet']."/.css";
$includeCss[] = "./css/productList.css";
// $includeJs[] = "./javascripts/.js";
/****************************************************************************************/
$products = [];
$shopType = '';
if (isset($_GET['type']) && isset($unturnedManage)) {
    switch (strtolower($_GET['type'])) {
        case 'vehicle':
            $shopType = 'vehicle';
            break;
        case 'item':
            $shopType = 'item';
            break;
        default:
            break;
    }
    $products = $unturnedManage->getShop($shopType);
}
$smarty->assign("type", $shopType);
$smarty->assign("products", $products);
