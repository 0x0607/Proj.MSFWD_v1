<?php
require_once "../sys.global.php";
/****************************************************************************************
 * 
 * 初始化 API 調試
 * 
 ****************************************************************************************/
// $LOG_STATUS_CODE["api.init"] = "1|API_INITIALIZE";
// $LOG_REASON["api.init"] = "";
/****************************************************************************************/
// 沒有動作及資料
// if (!isset($_POST['data'])) httpRespondJson(["status" => 400, "data" => json_encode($_POST,true)]);
if (!isset($_POST['data'])) httpRespondJson(["status" => 400, "data" => "Invalid request parameters."]);
$DATA = json_decode($_POST['data'], true);

// 無效的動作
if (!isset($DATA['action'])) httpRespondJson(["status" => 400, "data" => "Invalid action."]);
// if (!isset($DATA['action'])) httpRespondJson(["status" => 400, "data" => json_encode($_POST,true)]);
$ACTION = $DATA['action'];

// 沒有提供初始化變量
// if (!isset($DATA['iv'])) httpRespondJson(["status" => 401, "data" => "Invalid session."]);

// 解析防竄改雜湊資訊
if (isset($DATA['hash'], $DATA['iv'])) {
    $DATA = array_merge($DATA, decryptData($DATA['hash'], WEBSITE_HASH_KEY, $DATA['iv']));
    unset($DATA['hash']);
}
// else httpRespondJson(["status" => 401, "data" => "Invalid session."]);