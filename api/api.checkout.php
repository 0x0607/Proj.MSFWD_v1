<?php
$LOG_STATUS_CODE["api.checkout"] = "1|OK";
$LOG_REASON["api.checkout"] = "";
/****************************************************************************************
 * 
 * 支付相關資訊
 * 
 ****************************************************************************************/
require_once('../api/newebpay/neweb.installation.php');
/****************************************************************************************/
$minimumPrice = 35;         // 最小金額
$outputMethod = "FORM";     // 設置輸出模式 JSON 不輸出資料至藍新(測試用途) / String 輸出至藍新
if ($outputMethod == "FORM") header('Content-Type: text/html; charset=utf-8');
/****************************************************************************************/
// 建立訂單編號
$merchantTradeNo = STORE_PREFIX . $sf->getId();
/****************************************************************************************/
if (isset($_POST['pid'], $_SESSION["member"]['mid'])) {
    if ($_POST['pid'] && preg_match('/^([0-9]{19,20})$/', $_POST['pid'])) {
        $pInformation = $productManage->getProductInformation(WEBSITE_ID, $_POST['pid']);
        if ($pInformation['price'] <= $minimumPrice) httpRespondJson(["status" => 400, "data" => "Invalid price variable"]);
        /****************************************************************************************/
        // 藍新支付相關資訊
        $newebPayObj->settingOrderInformation($merchantTradeNo, WEBSITE_NAME . " - " . $pInformation['name'], $pInformation['price']);
        /****************************************************************************************/
        // 訂單相關
        $ordersInformation = [
            $merchantTradeNo,                                       // 訂單ID
            $_SESSION["member"]['mid'],                             // 會員ID
            WEBSITE_ID,                                             // 網站ID
            $pInformation['price'] * 1,                             // 商品總計購買金額
            sha1($merchantTradeNo . $_SESSION["member"]['mid'])     // 雜湊
        ];
        // 訂單內的物件
        $ordersItemsInformation = [
            [
                "oid" => $merchantTradeNo,              // 訂單ID
                "pid" => $pInformation['id'],           // 商品ID
                "quantity" => 1,                        // 數量
                "price" => $pInformation['price']       // 商品金額
            ]
        ];
        $result = $productManage->addOrder($ordersInformation, $ordersItemsInformation);
        /****************************************************************************************/
        if ($result) exit($newebPayObj->getOutput($outputMethod));
        else httpRespondJson(["status" => 400, "data" => "Invalid order"]);
    } else httpRespondJson(["status" => 400, "data" => "Invalid product"]);
} else httpRespondJson(["status" => 401, "data" => "Invalid session"]);
