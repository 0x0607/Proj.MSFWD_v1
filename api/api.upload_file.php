<?php
if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) exit;
/****************************************************************************************
 * 
 * 上傳檔案系統
 * 
 * Note: 
 * int WEBSITE_ID : 網站編號
 * int $_SESSION['mid'] : 會員編號
 * array $DATA : 數據
 * 
 ****************************************************************************************/
$path = $DATA['path'] . "?v=" . $sf->getId();
$result = false;
if (isset($_FILES['uploadfile'])) {
    switch ($ACTION) {
            // 變更使用者圖片
        case "USER_CHANGE_AVATAR":
            $file->setPath("avatar");
            $filePath = $file->upload($_FILES['uploadfile'], $_SESSION['mid']);
            $memberManage->updateMemberInformation($mid, ["profile" => ["avatar" => $path]]);
            break;
            // 變更網站圖示
        case "WEBSITE_CHANGE_ICON":
            $file->setPath("website");
            $filePath = $file->upload($_FILES['uploadfile'], WEBSITE_ID);
            $websiteManage->updateWebsiteInformation(WEBSITE_ID, ["website" => ["icon" => $path]]);
            break;
            // 變更商品圖示
        case "PRODUCT_CHANGE_IMAGE":
            // $file->setPath("products");
            // $filePath = $file->upload($_FILES['uploadfile'], $DATA['product_id']);
            // if (!isset($DATA['product_id'], $data['image_num'])) {
            //     $LOG_STATUS_CODE["api.updateImage"] = "0|{$ACTION}";
            //     $REASON = "Invalid picture number or product number.";
            // }
            // $productInformation = $productManage->getProductInformation(WEBSITE_ID, $DATA['product_id']);
            // $productImages = $productInformation['images'];
            // $productImages[$data['image_num']] = $path;

            // $productManage->updateProductInformation(WEBSITE_ID, $DATA['product_id'], ["images" => json_encode($productImages)]);
            break;
    }
}
/****************************************************************************************/
if ($result) $LOG_REASON["api"] = "Success.";
else $LOG_REASON["api"] = "Invalid file updload.";
