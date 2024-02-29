<?php
$LOG_STATUS_CODE["WEBSITE_INFORMATION_CHANGE"] = "0|WEBSITE_INFORMATION_CHANGE";
$LOG_REASON[""] = "";
/****************************************************************************************
 * 
 * 網站編輯頁面
 * 
 ****************************************************************************************/
// 引入的CSS及JS
$includeCss[] = "./css/memberProfileEdit.css";
$includeJs[] = "./javascripts/uploadImage.js";
/****************************************************************************************/
// 沒有登入
if (!isset($_SESSION['mid'])) {
    header("location: ?route=login");
    exit;
}
$displayname = WEBSITE_NAME;
$distribution = WEBSITE_DISTRIBUTION;
$icon = WEBSITE_ICON_URL;
/****************************************************************************************/
if (isset($_POST['passwordCheck']) && isset($_POST['displayname']) && isset($_POST['distribution'])) {
    // 如果密碼不吻合，無法變更資訊
    $result = $memberManage->memberLogin($_SESSION['account'], $_POST['passwordCheck']);
    if ($result) {
        $wInformations = [
            "website" => [
                "displayname" => htmlspecialchars($_POST['displayname']),
                "distribution" => htmlspecialchars(strtolower($_POST['distribution']))
            ]
        ];
        $result = $websiteManage->updateWebsiteInformation(WEBSITE_ID, $wInformations);
        if ($result) $LOG_STATUS_CODE["WEBSITE_INFORMATION_CHANGE"] = "1|WEBSITE_INFORMATION_CHANGE";
        else $LOG_REASON["Invalid website config set."];
    } else $LOG_REASON["Invalid access."];
    unset($_POST);
}
// $smarty->assign("displayname", $displayname);
// $smarty->assign("distribution", $distribution);
// $smarty->assign("icon", $icon);

// $log->addSystemLog($sf->getId(), WEBSITE_ID, $_SESSION['mid'], $USER_IP_ADDRESS, "CHANGE_WEBSITE_INFORMATION_SUCCESS");
// switch ($LOG_STATUS_CODE["WEBSITE_INFORMATION_CHANGE"]) {
//     case "CHANGE_WEBSITE_INFORMATION_SUCCESS":
        
//         header("location: ?route={$_GET['route']}");
//         exit;
//     case "CHANGE_WEBSITE_INFORMATION_FAIL":
//     case "PASSWORD_NO_MATCH":
        
//         break;
//     default:
//         break;
// }
// $smarty->assign("errorMessage", $LOG_STATUS_CODE["WEBSITE_INFORMATION_CHANGE"]);
