<?php
if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) exit;
/****************************************************************************************
 * 
 * 針對頁面設置專用
 * 
 * Note: 
 * int $_SESSION['mid'] : 會員編號
 * array $DATA : 數據
 * 
 ****************************************************************************************/
$pageResult = false;
$respondJson = ["status" => 200, "data" => "0|NO"];
$component_init = [
    "id" => $componentData['id'],
    "displayname" => "New Component",
    "theme"=> "default",
    "version" => "1.0"
];

/****************************************************************************************/
if ($pageResult) {
    $respondJson["data"] = "1|OK";
    $LOG_REASON["api"] = "Success.";
} else $LOG_REASON["api"] = "Invalid request parameters.";
