<?php
$LOG_STATUS_CODE["api.done"] = "1|OK";
$LOG_REASON["api.done"] = "";
/****************************************************************************************
 * 
 * 支付完成後返回
 * 
 ****************************************************************************************/
require_once('../api/newebpay/neweb.installation.php');
/****************************************************************************************/
if ($_SERVER['HTTP_USER_AGENT'] != "pay2go" || !isset($_POST['TradeInfo'])) {
    httpRespondJson(["status" => 401, "data" => "Invalid session"]);
    exit;
}
/****************************************************************************************/
// 解析付款資訊
$respondType = $newebPayObj->getRespondType();
$rawData = $newebPayObj->create_aes_decrypt($_POST['TradeInfo']);
if (!$rawData) {
    http_response_code(401);
    exit;
}
if ($respondType == "JSON") {
    $data = json_decode($rawData, true);
    $data = array_merge($data, $data['Result']);
    unset($data['Result']);
} else $data = parse_str(urldecode($rawData), $data)[0];
/****************************************************************************************/
// 取得訂單資訊
$orderInformation = $productManage->getOrder($data['MerchantOrderNo']);
/****************************************************************************************/
// 判斷訂單是否完成
if ($data['Status'] == "SUCCESS" && isset($data['MerchantID']) && !empty($orderInformation)) {
    $LOG_STATUS_CODE["api.done"] = '1|OK';
} else http_response_code(404);
/****************************************************************************************/
// 建立訂單付款資訊
$orderPaidInformation = [
    $data['MerchantOrderNo'],                                               // order_id
    $data['TradeNo'],                                                       // trade_no
    $data['Message'],                                                       // message
    $data['Amt'],                                                           // amount
    (empty($data['PaymentType']) ? "unknown" : $data['PaymentType']),       // payment_method
    $data['IP'],                                                            // ip_address
    $LOG_STATUS_CODE["api.done"],                                           // status
    $data['PayTime']                                                        // pay_time
];
$result = $productManage->addOrderPaid($orderPaidInformation);

// 發貨程序
if ($result) {

    // 判斷是否為虛擬商品，如果是則自動發貨
    $orderInformation = $productManage->getOrderInformation($data['MerchantOrderNo']);
    if (!empty($orderInformation)) {
        foreach ($orderInformation as $oinfo) {
            $virtualProduct = $productManage->getVirtualProduct($oinfo['product_id']);
            if (!empty($virtualProduct)) {
                $virtualProduct["oid"] = $orderInformation[0]['order_id'];
                // $vProduct[] = $virtualProduct;
                $VirtualProductresult = json_decode(apiRespondJsonData(json_encode($virtualProduct), SERVER_API_URL),true);
                $LOG_STATUS_CODE["api.done"] = $VirtualProductresult['data'];
                // 如果發貨完成則將狀態設為完成
                $productManage->setOrderStatus($data['MerchantOrderNo'], $LOG_STATUS_CODE["api.done"]);
            }
        }
    }
    // 其他發貨
    else {
    }
} else $LOG_STATUS_CODE["api.done"] = "0|NO";


echo $LOG_STATUS_CODE["api.done"];
