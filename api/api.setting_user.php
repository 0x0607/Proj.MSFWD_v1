<?php
if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) exit;
/****************************************************************************************
 * 
 * 針對會員設置專用
 * 
 * Note: 
 * int WEBSITE_ID : 網站編號
 * int $_SESSION['mid'] : 會員編號
 * array $DATA : 數據
 * 
 ****************************************************************************************/
$result = false;
$respondJson = ["status" => 401, "data" => "0|NO"];
if (!isset($_SESSION['mid'])) {
    $respondJson["status"] = 200;
    switch ($ACTION) {
        case "USER_REGISTER":
            $banWords = ["unknown", "admin", "administrator", "習", "近平", "翠", "896", "64"];
            $account = htmlspecialchars($DATA['account']);
            $nickname = htmlspecialchars($DATA['nickname']);
            $password = $DATA['password'];
            $passwordCheck = $DATA['passwordCheck'];
            if ($password === $passwordCheck) {
                if (!sensitiveWordFilter($account, $banWords) && !sensitiveWordFilter($nickname, $banWords)) {
                    if (checkAccount($account) && checkPassword($password)) {
                        $accountNotExists = empty($memberManage->getMemberInformation($account));
                        // 判斷帳號是否重註冊 (如果資料庫撈的到資料代表有註冊)
                        if ($accountNotExists) {
                            $mid = $sf->getId();
                            $mInformation = [
                                "account" => $account,
                                "nickname" => $nickname,
                                "password" => $password,
                                "last_ip_address" => $DATA['last_ip_address']
                            ];
                            $result = $memberManage->addMember($mid, $mInformation);
                            $permissions->changeMemberRoles(WEBSITE_ID, WEBSITE_ID, $mid, true);
                            $respondJson["data"] = "1|OK";
                        } else {
                            $LOG_REASON["api"] = "Account already exists.";
                            $respondJson["data"] = "Account already exists.";
                        }
                    } else {
                        $LOG_REASON["api"] = "Invalid account or password.";
                        $respondJson["data"] = "X Invalid account or password.";
                    }
                } else {
                    $LOG_REASON["api"] = "Invalid account or nickname.";
                    $respondJson["data"] = "Invalid account or nickname.";
                }
            } else {
                $LOG_REASON["api"] = "Passwords do not match.";
                $respondJson["data"] = "Passwords do not match.";
            }
            break;
        case "USER_CHANGE_INFORMATION":
            $result = $memberManage->updateMemberInformation(WEBSITE_ID, ["account" => $DATA['account'], "profile" => $DATA['profile']]);
            break;
        case "USER_GET_INFORMATION":
            $userInformation = [
                "id" => 0,
                'nickname' => "disable_account",
                'avatar' => "unknown",
                'account' => "unknown",
                'introduction' => "",
                'theme' => $colorPicker->randomColor(),
                'roles' => []
            ];
            // 擷取使用者資訊
            $userResult = $memberManage->getMemberInformation($DATA["find"]);
            // 用戶不存在
            if (empty($userResult)) {
                $LOG_REASON["api"] = "Invalid account or memberId.";
                $respondJson["data"] = $userInformation;
            } else {
                // 用戶資訊
                $userInformation['id'] = $userResult['id'];
                $userInformation['nickname'] = $userResult['nickname'];
                $userInformation['account'] = $userResult['account'];
                $userInformation['avatar'] = $userResult['avatar'];
                $userInformation['introduction'] = $userResult['introduction'];
                if (!empty($userResult['theme'])) $userInformation['theme'] = $userResult['theme'];

                // 網站上的身份組
                $wRoles = $permissions->getMemberRoles($userResult['id'], WEBSITE_ID);
                $webRoleDisplayName = [];
                foreach ($wRoles as $role) $webRoleDisplayName[] = $role['displayname'];
                if (!empty($webRoleDisplayName)) $userInformation['roles'] = $webRoleDisplayName;

                // 如果啟用Steam串接
                if (isset($steamauthManage)) {
                    $userSteamauth = $steamauthManage->getMemberSteamauth($userResult['id']);

                    // 如果使用者有串接Steam
                    if (!empty($userSteamauth)) {
                        $steamId = $userSteamauth['steam_id'];
                        $userInformation['steam_id'] = $steamId;
                        
                        // 取得最新的Steam用戶資訊
                        // $userSteamInformation = $steamauthManage->getISteamUser($steamId);
                        // $userInformation['nickname'] = $userSteamInformation['personaname'];
                        // $userInformation['avatar'] = $userSteamInformation['avatarfull'];

                        // 如果啟用Unturned
                        if (isset($unturnedManage)) {
                            $unInformation = $unturnedManage->getUserInformation($steamId);

                            if (!empty($unInformation)) {
                                $userInformation['unturned_name'] = $unInformation['CharacterName'];

                                // 抓取遊戲內身份組
                                $unRoles = $unturnedManage->getUserGroups($steamId);
                                $unRoleDisplayName = [];
                                foreach ($unRoles as $role) $unRoleDisplayName[] = $role['DisplayName'];
                                if (!empty($unRoleDisplayName)) $userInformation['roles'] = $unRoleDisplayName;

                                // 以遊戲內身分組第一個身分作為顯示顏色
                                if (!empty($unRoles)) $userInformation['theme'] = $unRoles[0]['Color'];

                                if (!empty($unInformation)) {
                                    $userInformation['introduction'] = "國籍：" . (is_null($unInformation['CountryCode']) ? 'Unknown' : $unInformation['CountryCode']) . '<br/>';
                                    $userInformation['introduction'] .= "最後上線時間：" . (is_null($unInformation['LastJoin']) ? 'Unknown' : $unInformation['LastJoin']) . '<br/>';
                                    $userInformation['introduction'] .= "持有貨幣：" . (is_null($unInformation['Balance']) ? '0' : $unInformation['Balance']) . '<br/>';
                                    $userInformation['introduction'] .= "卡瑪值：" . (is_null($unInformation['points']) ? '0' : $unInformation['points']) . '<br/>';
                                }
                            }
                        }
                    }
                }
            }
            $respondJson["data"] = $userInformation;
            break;
    }
}
/****************************************************************************************/
if ($result) $LOG_REASON["api"] = "Success.";
// else $LOG_REASON["api"] = "Invalid request parameters.";
