<?php
$LOG_STATUS_CODE["member_profile_edit"] = "1|OK";
$LOG_REASON["member_profile_edit"] = "";
/****************************************************************************************
 * 
 * 個人化頁面
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
// $memberInformations = $memberManage->getMemberInformation($_SESSION['mid']);
$nickname = $_SESSION["minfo"]['nickname'];
$account = $_SESSION["minfo"]['account'];
$avatar = $_SESSION["minfo"]['avatar'];
/****************************************************************************************/
if (isset($_POST['passwordCheck']) && isset($_POST['account']) && isset($_POST['nickname'])) {
    $nickname = htmlspecialchars($_POST['nickname']);
    $account = htmlspecialchars(strtolower($_POST['account']));
    $passwordCheck = $_POST['passwordCheck'];
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    if (checkAccount($account)) {
        // 如果密碼不吻合，無法變更資訊
        if ($memberManage->memberLogin($_SESSION["minfo"]['account'], $passwordCheck)) {
            $mInformation = [
                "account" => [
                    "account" => $account,
                    "nickname" => $nickname
                ]
            ];
            if ($password != "") {
                if (checkPassword($_POST['password'])) $mInformation["account"]["password"] = $_POST['password'];
                else $STATUS_CODE['member_profile_edit'] = "PASSWORD_WRONG";
            }
            $result = $memberManage->updateMemberInformation($_SESSION['mid'], $mInformation);
            if ($result) $STATUS_CODE['member_profile_edit'] = "CHANGE_MEMBER_INFORMATION";
            else $STATUS_CODE['member_profile_edit'] = "CHANGE_MEMBER_INFORMATION_FAIL";
            $_SESSION["account"] = $account;
            $_SESSION["nickname"] = $nickname;
        } else $STATUS_CODE['member_profile_edit'] = "PASSWORD_NO_MATCH";
    } else $STATUS_CODE['member_profile_edit'] = "ACCOUNT_ERROR";
    unset($_POST);
}
switch ($STATUS_CODE['member_profile_edit']) {
    case "CHANGE_MEMBER_INFORMATION":
        // $log->addSystemLog($sf->getId(), WEBSITE_ID, $_SESSION['mid'], $USER_IP_ADDRESS, "CHANGE_MEMBER_INFORMATION");
        break;
    case "CHANGE_MEMBER_INFORMATION_FAIL":
    case "PASSWORD_NO_MATCH":
    case "ACCOUNT_ERROR":
    case "PASSWORD_WRONG":
        // $log->addSystemLog($sf->getId(), WEBSITE_ID, $_SESSION['mid'], $USER_IP_ADDRESS, "CHANGE_MEMBER_INFORMATION_FAIL");
        break;
    default:
        break;
}
$smarty->assign("errorMessage", $STATUS_CODE['member_profile_edit']);
$smarty->assign("nickname", $nickname);
$smarty->assign("account", $account);
$smarty->assign("avatar", $avatar);
