<?php

/****************************************************************************************
 * 
 * 登入系統元件
 * 
 ****************************************************************************************/
// 引入的CSS及JS
$includeCss[] = "./css/login.css";
$includeJs[] = "./javascripts/login_check.js";
/****************************************************************************************/
if (!isset($_SESSION["member"])) {
    $login_method = isset($_GET['login']) ? $_GET['login'] : '';
    switch (WEBSITE_SETTINGS_GENERAL['login_method']) {
        case "steam":
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
                        $HASH_RAWDATA["steam"] = $steamInformation;
                        $DATA = json_encode(["action" => "USER_STEAM_LOGIN", "hash" => encryptData($HASH_RAWDATA, WEBSITE_HASH_KEY, $DATA['iv']), "iv" => $DATA['iv']]);
                        $loginResult = apiRespondJsonData($DATA, SERVER_API_URL);
                    }
                }
            }
            break;
        case "line":
        case "discord":
        case "facebook":
        default:
            echo '<!-- local login -->';
            break;
    }
    // 登入成功
    if (isset($loginResult)) {
        $res = json_decode($loginResult, true);
        $status = $res['status'];
        $data = $res['data'];
        if ($status == 200) {
            $_SESSION['member'] = $data;
            header("location: ?route=member");
            exit;
        }
    }
    // 登入失敗
} else {
    header("location: ?route=member");
    exit;
}
