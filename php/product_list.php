<?php
$LOG_STATUS_CODE["product_list"] = "1|OK";
$LOG_REASON["product_list"] = "";
/****************************************************************************************
 * 
 * 商品元件
 * 
****************************************************************************************/
// 引入的CSS及JS
$INCLUDE_CSS[]="./css/productList.css";
$INCLUDE_CSS[]="./css/buy_membership.css";
// $INCLUDE_JS[] = "./javascripts/.js";
/****************************************************************************************/
// $products = [];
// if(isset($_GET['keyword'])){
//     $productList = $productManage->searchProduct(WEBSITE_ID,$_GET['keyword']);
//     foreach($productList as $product){
//         $products[] = $productManage->getProductInformation(WEBSITE_ID,$product['id']);
//     }
// }
$products = $productManage->getProductInformation();
foreach($products as $key => $product){
    if(empty($product['description'])) $products[$key]['description'] = "沒有介紹";
    else $products[$key]['descriptions'] = explode("\n", $product['description']);
}

$smarty->assign("products", $products);
?>