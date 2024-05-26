<?php
$LOG_STATUS_CODE["product_details_page"] = "1|OK";
$LOG_REASON["product_details_page"] = "";
/****************************************************************************************
 * 
 * 商品詳細頁面
 * 
 ****************************************************************************************/
// 引入的CSS及JS
$INCLUDE_CSS[] = "./css/product_details_page.css";
$INCLUDE_JS[] = "./javascripts/updateProducts.js";
$INCLUDE_JS[] = "./javascripts/uploadImage.js";
/****************************************************************************************/
// 沒有登入
if (!isset($_GET['pid'])) {
    header("location: ?route=login");
    exit;
}
$pid = intval($_GET['pid']);
$product = $productManage->getProductInformation($pid);
$smarty->assign("product", $product);
/****************************************************************************************/
