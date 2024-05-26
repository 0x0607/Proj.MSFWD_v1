<?php
// if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) exit;
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
// unset($DATA);
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
    // $ISHASH = true;
}
// 合併多餘的data
if (isset($DATA['data'])) {
    if(is_array($DATA['data'])){
        $DATA = array_merge($DATA, $DATA['data']);
        unset($DATA['data']);
    }
    // $ISHASH = true;
}

// 取得一些名稱有問題的陣列值(並且將其重新整理)
foreach ($DATA as $key => $value) {
    if (preg_match('/^(\w+)\[(\d+)\]$/', $key, $matches)) {
        $name = $matches[1];
        $index = $matches[2];

        $DATA[$name][$index] = $value;
        unset($DATA[$key]);
    }
}

// else httpRespondJson(["status" => 401, "data" => "Invalid session."]);