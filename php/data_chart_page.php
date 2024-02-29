<?php
$LOG_STATUS_CODE["data_chart_page"] = "1|OK";
$LOG_REASON["data_chart_page"] = "";
/****************************************************************************************
 * 
 * 數據元件
 * 目前還在測試中
 * 
 ****************************************************************************************/
// 引入的CSS及JS
$includeCss[] = "./css/productEdit.css";
$includeJs[] = "./javascripts/updateProducts.js";
$includeJs[] = "./javascripts/uploadImage.js";
/****************************************************************************************/
// 沒有登入
if (!isset($_SESSION['mid'])) {
    header("location: ?route=login");
    exit;
}
$data = array();
$sql = 'SELECT * from `tank` ;';
$result = $db->each($sql);
// 範例數據
foreach ($result as $row) {
    $data['ph'][] = ["y" => $row['ph'],"label" => $row['date']];
    $data['oxy'][] = ["y" => $row['oxy'], "label" => $row['date']];
    $data['temp'][] = ["y" => $row['temp'], "label" => $row['date']];
    $data['cond'][] = ["y" => $row['cond'], "label" => $row['date']];
}
foreach ($data as $key => $row) {
    $dataChartdata[$key] = json_encode($row, JSON_NUMERIC_CHECK);
}
$smarty->assign("dataChartdata", $dataChartdata);
?>