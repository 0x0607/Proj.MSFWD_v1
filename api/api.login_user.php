<?php
if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) exit;
/****************************************************************************************
 * 
 * 針對會員登入專用
 * 
 * Note: 
 * int WEBSITE_ID : 網站編號
 * int $_SESSION["member"] : 會員編號
 * array $DATA : 數據
 * 
 ****************************************************************************************/
$loginResult = false;
$respondJson = ["status" => 401, "data" => "0|NO"];
if (!isset($_SESSION["member"])) {
    $respondJson["status"] = 200;
    switch ($ACTION) {
        case "USER_LOCAL_LOGIN":
            if (isset($DATA['password'], $DATA['account'])) $loginResult = $memberManage->memberLogin($DATA['account'], $DATA['password']);
            else $respondJson['data'] = "Invalid account or password.";
            break;
        case "USER_STEAM_LOGIN":
            $DATA['account'] = "s_" . $DATA['steam']['steamid'];
            $newNickName = $DATA['steam']['personaname'];
            $newAvatar = $DATA['steam']['avatarfull'];

            $userSteamauth = $steamauthManage->getMemberSteamauth($DATA['steam']['steamid']);
            $loginResult = !empty($userSteamauth);

            // 如果非本地端登入則自動建立帳戶
            if (empty($userSteamauth)) {
                $mNewAccountInformation = [
                    "account" => $DATA['account'],
                    "nickname" => $newNickName,
                    "last_ip_address" => $DATA['last_ip_address']
                ];
                $mid = $sf->getId();
                $loginResult = $memberManage->addMember($mid, $mNewAccountInformation);
                $loginResult &= $steamauthManage->addMemberSteamauth($mid, $DATA['steam']['steamid']);
            }
            break;
        case "USER_FACEBOOK_LOGIN":
            break;
        case "USER_LINE_LOGIN":
            break;
        case "USER_DISCORD_LOGIN":
            break;
    }
}
/****************************************************************************************/
// 登入成功
if ($loginResult) {
    $LOG_REASON["api"] = "Login success.";
    $mAccountInformation = $memberManage->getMemberInformation($DATA['account']);
    $mid = $mAccountInformation['id'];

    // 註銷帳戶則不登入
    if ($mAccountInformation['status'] == 0) {
        $LOG_REASON["api"] = "Login failed.";
        $respondJson['data'] = "Account has been disabled.";
    } else {
        // 更新資訊
        $updateInformation = [
            "account" => [
                "last_ip_address" => $DATA['last_ip_address']
            ]
        ];
        if (isset($newAvatar)) {
            $updateInformation["profile"]["avatar"] = $newAvatar;
            $mAccountInformation['avatar'] = $newAvatar;
        }
        if (isset($newNickName)) {
            $updateInformation["account"]["nickname"] = $newNickName;
            $mAccountInformation['nickname'] = $newNickName;
        }
        // 更新用戶資訊，並且配發該網站everyone身分組
        $memberManage->updateMemberInformation($mid, $updateInformation);
        $permissions->changeMemberRoles(WEBSITE_ID, WEBSITE_ID, $mAccountInformation['id'], true);
        $mInformation = [
            "mid" => $mAccountInformation['id'],
            "account" => $mAccountInformation['account'],
            "nickname" => $mAccountInformation['nickname'],
            "avatar" => $mAccountInformation['avatar'],
            "introduction" => $mAccountInformation['introduction'],
            "theme" => $mAccountInformation['theme'],
            "last_ip_address" => $mAccountInformation['last_ip_address']
        ];
        // 如果是本地端登入則值接將資訊設置完畢
        if($ACTION == "USER_LOCAL_LOGIN"){
            $_SESSION["member"] = $mInformation;
            $respondJson["data"] = '1|OK';
        }else $respondJson["data"] = $mInformation;
    }
} else {
    $respondJson['data'] = "Wrong account or password.";
    $LOG_REASON["api"] = "Login failed.";
}
