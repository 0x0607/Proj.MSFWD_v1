<?php
if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) exit;
/****************************************************************************************
 * 
 * 虛擬商品系統
 * 
 * Note: 
 * int WEBSITE_ID : 網站編號
 * int $_SESSION['mid'] : 會員編號
 * array $DATA : 數據
 * 
 ****************************************************************************************/
$deliveryResult = false;
$respondJson = ["status" => 401, "data" => "0|NO"];
if (isset($DATA['oid'])) {
    $orderInformation = $productManage->getOrder($DATA['oid']);
    // 訂單必須存在
    if (!empty($orderInformation)) {
        // 訂單不能已出貨
        if ($orderInformation['status'] != '1|OK') {
            $respondJson['status'] = 200;
            switch ($ACTION) {
                case "UNTURNED_DELIVERY":
                    if (isset($unturnedManage, $steamauthManage)) {

                        $steamInformation = $steamauthManage->getMemberSteamauth($orderInformation[0]['mid']);
                        if (!empty($steamInformation)) {
                            $orderInformation[0]['items'] = $productManage->getOrderInformation($DATA['oid']);

                            $steamId = $steamInformation['steam_id'];
                            $productInformation = $DATA['product'];
                            $productInformation['ExpireSecond'] = 3600 + 86400 * 30 * $orderInformation[0]['items'][0]['quantity'];
                            $unturnedManage->delivery($steamId, $productInformation);
                            $deliveryResult = true;
                        }
                    }
            }
        }
    }
}
if ($deliveryResult) {
    $respondJson['data'] = "1|OK";
} else {
    $LOG_REASON["api"] = "Auto delivery failed.";
}
