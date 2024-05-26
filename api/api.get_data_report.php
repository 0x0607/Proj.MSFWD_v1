<?php
if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) exit;
/****************************************************************************************
 * 
 * 虛擬商品系統
 * 
 * Note: 
 * int $_SESSION['mid'] : 會員編號
 * array $DATA : 數據
 * 
 ****************************************************************************************/
$getDataResult = false;
$respondJson = ["status" => 200, "data" => ""];

switch ($ACTION) {
    case "GET_TOTAL_AMOUNTS":
        for($month=1;$month<=12;$month++){
            $totalAmounts[$month] = 0;
            $lastYearTotalAmounts[$month] = 0;
        }
        $transactions  = $productManage->getOrderPaidByTime();
        if (!empty($transactions)) {
            $lastYear = date("Y") - 1;
            foreach ($transactions as $transaction) {
                $createdAt = DateTime::createFromFormat("Y-m-d H:i:s", $transaction["created_at"]);
                $month = $createdAt->format("n");
                $year = $createdAt->format("Y");

                if ($year == $lastYear) {
                    $lastYearTotalAmounts[$month] += $transaction["amount"];
                } else {
                    $totalAmounts[$month] += $transaction["amount"];
                }
            }
        }
        $respondJson['data'] = json_encode(["year" => $totalAmounts, "lastyear" => $lastYearTotalAmounts]);
        $getDataResult = true;
}

if (!$getDataResult) {
    $LOG_REASON["api"] = "Get Report failed.";
    $respondJson['data'] = "[]";
}
