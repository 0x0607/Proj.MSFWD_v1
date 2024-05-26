<?php
if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) exit;
/****************************************************************************************
 * 
 * Steam登入
 * 
Array
(
    [steamid] => int(17)
    [communityvisibilitystate] => int(1)
    [profilestate] => int(1)
    [personaname] => string
    [commentpermission] => int(1)
    [profileurl] => string(url)
    [avatar] => string(url)
    [avatarmedium] => string(url)
    [avatarfull] => string(url)
    [avatarhash] => string(40)
    [lastlogoff] => int(10)
    [personastate] => int(1)
    [realname] => string
    [primaryclanid] => int(18)
    [timecreated] => int(10)
    [personastateflags] => int(1)
    [loccountrycode] => string
    [locstatecode] => int
    [loccityid] => int
)
 ****************************************************************************************/
// steamauth Version 4.0
if (isset($steamauthOpenId)) {
    if (!$steamauthOpenId->mode) {
        $steamauthOpenId->identity = 'https://steamcommunity.com/openid';
        header('Location: ' . $steamauthOpenId->authUrl());
    } elseif ($steamauthOpenId->mode == 'cancel') $STATUS_CODE['login'] = "LOGIN_FAIL";
    else {
        if ($steamauthOpenId->validate()) {
            $steamId = $steamauthManage->getSteamIdentity($steamauthOpenId->identity);
            $steamInformation = $steamauthManage->getISteamUser($steamId);
            // 傳送數據
            // $HASH_RAWDATA["steam"] = $steamInformation;
            // $DATA = json_encode(["action" => "USER_STEAM_LOGIN", "hash" => encryptData($HASH_RAWDATA, WEBSITE_HASH_KEY, $DATA['iv']), "iv" => $DATA['iv']]);
            // $loginResult = apiRespondJsonData($DATA, SERVER_API_URL);
            
            $newNickName = $steamInformation['personaname'];
            // $newAvatar = $steamInformation['avatarfull'];

            $userSteamauth = $steamauthManage->getMemberSteamauth($steamId);
            $loginResult = !empty($userSteamauth);

            if ($loginResult) $mid = $userSteamauth['mid'];
            // 如果非本地端綁定之帳戶則自動建立帳戶
            else {
                $mNewAccountInformation = [
                    "account" => "s_" . $steamId,
                    "nickname" => $steamInformation['personaname'],
                    "last_ip_address" => $USER_IP_ADDRESS
                ];
                $mid = $sf->getId();
                $loginResult = $memberManage->addMember($mid, $mNewAccountInformation);
                $loginResult &= $steamauthManage->addMemberSteamauth($mid, $steamId);
                $loginResult &= $memberManage->updateMemberInformation($mid, ["profile"=>["avatar"=>$steamInformation['avatarfull']]]);
            }
            // 配發該網站everyone身分組
            $permissions->changeMemberRoles(WEBSITE['id'], $mid, true);
        }
    }
}