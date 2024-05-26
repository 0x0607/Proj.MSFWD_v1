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
$respondJson = ["status" => 200, "data" => "0|NO"];
// 如果有上傳的圖片才會觸發
if (isset($_FILES['uploadfile'])) {
    $uploadedFiles = $_FILES['uploadfile'];
    $filePath = [];
    $imageResult = false;
    switch ($ACTION) {
            // 變更使用者圖片
        case "USER_CHANGE_INFORMATION":
            $file->setPath(CONFIG_PATH['avatar'] . "/" . $_SESSION['member']['mid']);
            $filePath = $file->upload($uploadedFiles, 'avatar');
            if (!empty($filePath)) $DATA['avatar'] = $filePath[0];
            break;
            // 變更網站圖示
        case "WEBSITE_CHANGE_INFORMATION":
            $file->setPath(WEBSITE_FILE_DIR);
            $filePath = $file->upload($uploadedFiles, 'icon');
            if (!empty($filePath)) $DATA['icon'] = $filePath[0];
            break;
            // 變更商品圖示
        case "PRODUCT_ADD":
        case "PRODUCT_CHANGE_INFORMATION":
            // 新的商品新增一個一個一個pid
            if (!isset($DATA['pid']) && $ACTION == 'PRODUCT_ADD')  $DATA['pid'] = $sf->getId();
            if (isset($DATA['pid'])) {
                $file->setPath(WEBSITE_FILE_DIR . "/products" );
                $filePath = $file->upload($uploadedFiles, $DATA['pid']);
                if (!empty($filePath)) $DATA['productImages'] = $filePath;
            }
            break;
    }
}
/****************************************************************************************/
    // if ($imageResult) $LOG_REASON["api"] = "Success.";
    // else $LOG_REASON["api"] = "Invalid file updload.";
    // if (!$imageResult) $LOG_REASON["api"] = "Invalid file updload.";