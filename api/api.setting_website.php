<?php
if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) exit;
/****************************************************************************************
 * 
 * 針對網站設置專用
 * 
 * Note: 
 * int WEBSITE_ID : 網站編號
 * int $_SESSION['mid'] : 會員編號
 * array $DATA : 數據
 * 
 ****************************************************************************************/
$webResult = false;
$respondJson = ["status" => 200, "data" => "0|NO"];
switch ($ACTION) {
    case "WEBSITE_CHANGE_THEME":
        if (isset($DATA['theme_colors'])) $webResult = $websiteManage->updateWebsiteInformation(WEBSITE['id'], ["website" => ["theme" => json_encode(["stylesheet" => "", "colors" => $DATA['theme_colors']], true)]]);
        if ($webResult) {
            // $respondJson["data"] = "1|OK";
            $LOG_REASON["api"] = "Website update success.";
        }
        break;
    case "WEBSITE_CHANGE_INFORMATION":
        $updateWebData = ['website' => [], 'newebpay' => []];
        // 基礎設置
        if (!empty($DATA['displayname'])) {
            $updateWebData['website']['displayname'] = htmlspecialchars(trim($DATA['displayname']));
            if (mb_strlen($updateWebData['website']['displayname']) > 16) break;
        }
        // if (!empty($DATA['distribution'])) $updateWebData['website']['distribution'] = $DATA['distribution'];
        // 如果有設置藍新資訊則一起更新
        if (!empty($DATA['store_id'])) $updateWebData['newebpay']['store_id'] = $DATA['store_id'];
        if (!empty($DATA['store_hash_key'])) $updateWebData['newebpay']['store_hash_key'] = $DATA['store_hash_key'];
        if (!empty($DATA['store_hash_iv'])) $updateWebData['newebpay']['store_hash_iv'] = $DATA['store_hash_iv'];
        // 如果有上傳圖片的話，則一起更新圖片
        if (isset($DATA['icon'])) $updateWebData['website']['icon'] = $DATA['icon'];
        // 如果完全沒有資料則不更新
        if (!empty($updateWebData['website']) || !empty($updateWebData['newebpay'])) $webResult = $websiteManage->updateWebsiteInformation(WEBSITE['id'], $updateWebData);
        if ($webResult) {
            // $respondJson["data"] = "1|OK";
            $LOG_REASON["api"] = "Website update success.";
        }
        break;
}
/****************************************************************************************/
// if ($webResult) {
//     $respondJson["data"] = "1|OK";
//     $LOG_REASON["api"] = "Website update success.";
// } else {
//     $respondJson['data'] = "Invalid request parameters.";
//     $LOG_REASON["api"] = "Invalid request parameters.";
// }
if ($webResult) {
    $respondJson["data"] = "1|OK";
} else {
    $respondJson['data'] = "Invalid request parameters.";
}
