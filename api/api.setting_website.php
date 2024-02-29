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
$result = false;
switch ($ACTION) {
    case "WEBSITE_CHANGE_STATUS":
        $result = $websiteManage->changeWebsiteStatus(WEBSITE_ID);
        break;
    case "WEBSITE_CHANGE_NEWEBPAY":
        $result = $websiteManage->updateWebsiteInformation(WEBSITE_ID, ["newebpay" => $DATA]);
        break;
    case "WEBSITE_CHANGE_INFORMATION":
        $result = $websiteManage->updateWebsiteInformation(WEBSITE_ID, ["website" => $DATA]);
        break;
}
/****************************************************************************************/
if ($result) $LOG_REASON["api"] = "Success.";
else $LOG_REASON["api"] = "Invalid request parameters.";
