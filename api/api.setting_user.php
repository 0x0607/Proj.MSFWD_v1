<?php
if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) exit;
/****************************************************************************************
 * 
 * 針對會員設置專用
 * 
 * Note: 
 * int $_SESSION['mid'] : 會員編號
 * array $DATA : 數據
 * 
 ****************************************************************************************/
$userResult = false;
$respondJson = ["status" => 200, "data" => "0|NO"];
switch ($ACTION) {
    case "USER_REGISTER":
        if (!isset($_SESSION["member"])) {
            $banWords = ["unknown", "admin", "administrator", "習", "近平", "翠", "896", "64"];
            $account = htmlspecialchars($DATA['account']);
            $nickname = htmlspecialchars($DATA['nickname']);
            $password = $DATA['password'];
            $passwordCheck = $DATA['passwordCheck'];
            if ($password === $passwordCheck) {
                if (!sensitiveWordFilter($account, $banWords) && !sensitiveWordFilter($nickname, $banWords)) {
                    if (checkAccount($account) && checkPassword($password)) {
                        $accountNotExists = empty($memberManage->getMemberInformationByAccount($account));
                        // 判斷帳號是否重註冊 (如果資料庫撈的到資料代表有註冊)
                        if ($accountNotExists) {
                            $mid = $sf->getId();
                            $mInformation = [
                                "account" => $account,
                                "nickname" => $nickname,
                                "password" => $password,
                                "last_ip_address" => $DATA['last_ip_address']
                            ];
                            $userResult = $memberManage->addMember($mid, $mInformation);
                            $permissions->changeMemberRoles(WEBSITE['id'], $mid, true);
                            $respondJson["data"] = "1|OK";
                        } else {
                            $LOG_REASON["api"] = "Account already exists.";
                            $respondJson["data"] = "Account already exists.";
                        }
                    } else {
                        $LOG_REASON["api"] = "Invalid account or password.";
                        $respondJson["data"] = "Invalid account or password.";
                    }
                } else {
                    $LOG_REASON["api"] = "Invalid account or nickname.";
                    $respondJson["data"] = "Invalid account or nickname.";
                }
            } else {
                $LOG_REASON["api"] = "Passwords do not match.";
                $respondJson["data"] = "Passwords do not match.";
            }
        } else $respondJson["data"] = "User has logged in.";
        break;
    case "USER_CHANGE_INFORMATION":
        if (isset($_SESSION["member"])) {
            $updateUserData = ['account' => [], 'profile' => []];
            if (!empty($DATA['password']) || !empty($DATA['passwordOld'])) {
                if (!empty($DATA['password']) && !empty($DATA['passwordOld'])) {
                    $CheckPassword = $memberManage->memberLogin($_SESSION['member']['account'], $DATA['passwordOld']);
                    if (!empty($CheckPassword)) {
                        $updateUserData['account']['password'] = $DATA['password'];
                    } else {
                        $respondJson['data'] = "Error password.";
                        break;
                    }
                } else {
                    $respondJson['data'] = "Invalid password.";
                    break;
                }
            }
            // 基礎設置
            if (!empty($DATA['account'])) {
                if (strtolower($DATA['account']) != $_SESSION['member']['account']) {
                    $CheckAccount = $memberManage->getMemberInformationByAccount($DATA["account"]);
                    if (empty($CheckAccount)) {
                        $updateUserData['account']['account'] = $DATA['account'];
                        $_SESSION['member']['account'] = $DATA['account'];
                    }
                    else {
                        $respondJson["data"] = 'Duplicate account name.';
                        break;
                    }
                }
            }
            if (!empty($DATA['nickname'])) {
                $updateUserData['account']['nickname'] = $DATA['nickname'];
                $_SESSION['member']['nickname'] = $DATA['nickname'];
            };
            // 如果有上傳圖片的話，則一起更新圖片
            if (isset($DATA['avatar'])) {
                $updateUserData['profile']['avatar'] = $DATA['avatar'];
                $_SESSION['member']['avatar'] = $DATA['avatar'];
            }
            // // 如果完全沒有資料則不更新
            if (!(empty($updateUserData['account']) && empty($updateUserData['profile']))) $userResult = $memberManage->updateMemberInformation($_SESSION['member']['mid'], $updateUserData);
            // if($userResult) $respondJson["data"] = '1|OK';

            $respondJson["data"] = '1|OK';
        } else $respondJson["data"] = "User is not logged in";
        break;
        // 用戶本地登入
    case "USER_LOCAL_LOGIN":
        if (isset($DATA['password'], $DATA['account'])) {
            if (empty($DATA['password'])) {
                $respondJson['data'] = "Invalid password.";
                break;
            }
            $DATA["mid"] = $memberManage->memberLogin($DATA['account'], $DATA['password']);
            if (!empty($DATA["mid"])) {
                // 更新資訊
                $updateInformation = [
                    "account" => [
                        "last_ip_address" => $DATA['last_ip_address']
                    ]
                ];
                // 更新用戶資訊，並且配發該網站everyone身分組
                $memberManage->updateMemberInformation($DATA["mid"], $updateInformation);
                $permissions->changeMemberRoles(WEBSITE['id'], $DATA["mid"], true);
                $localLoginResult = true;
            } else {
                $respondJson['data'] = "Invalid account or password.";
                break;
            }
        } else {
            $respondJson['data'] = "Invalid account or password.";
            break;
        }
        // 取得用戶資訊
    case "USER_GET_INFORMATION":
        $userInformation = [
            "mid" => 114514191981000404,
            'nickname' => "unknown",
            'avatar' => "unknown",
            'account' => "unknown",
            'introduction' => "unknown",
            'theme' => $colorPicker->randomColor(),
            // 'last_ip_address' => "0.0.0.0",
            'status' => 1,
            'roles' => []
        ];
        // 擷取使用者資訊
        $userResult = null;
        if (isset($DATA["mid"])) $userResult = $memberManage->getMemberInformation($DATA["mid"]);
        else if (isset($DATA["account"])) $userResult = $memberManage->getMemberInformationByAccount($DATA["account"]);
        // 用戶不存在
        if (empty($userResult)) {
            $LOG_REASON["api"] = "Invalid account or memberId.";
            // 彩蛋
            if (isset($DATA["mid"])) {
                if ($DATA["mid"] == 114514 || $DATA["mid"] == 810 || $DATA["mid"] == 1919810) {
                    $userInformation["nickname"] = "やじゅうせんぱい";
                    $userInformation["avatar"] = "/assets/images/114514.png";
                    $userInformation["introduction"] = "24歲，是學生，身高170公分，體重74公斤。
                    沒有特別在做什麼運動，不過有在健身。
                    曾經去過風俗店，比較喜歡王道征途，泡泡系。
                    脫衣服的時候被別人看到完全不會害羞，體現出王者一般的遊刃有餘。
                    喜歡曬太陽，所以曬痕很明顯。";
                    $userInformation["roles"][] = "水泳部";
                }
            }
            $respondJson["data"] = $userInformation;
        } else {
            // 用戶資訊     P.S: id與mid是相同的東西只是因為我很長混用，只好浪費一點點記憶體囉
            $userInformation['id'] = $userResult['id'];
            $userInformation['mid'] = $userResult['id'];
            $userInformation['nickname'] = $userResult['nickname'];
            $userInformation['account'] = $userResult['account'];
            $userInformation['avatar'] = $userResult['avatar'];
            $userInformation['introduction'] = $userResult['introduction'];
            $userInformation['status'] = $userResult['status'];
            // 帳號被啟用
            if ($userInformation['status']) {
                // $userInformation['last_ip_address'] = $userResult['last_ip_address'];
                if (!empty($userResult['theme'])) $userInformation['theme'] = $userResult['theme'];

                // 網站上的身份組
                $wRoles = $permissions->getMemberRoles($userResult['id']);
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

                            // 抓取玩家數據
                            if (!empty($unInformation)) {
                                $userInformation['unturned_name'] = $unInformation['CharacterName'];
                                if (!empty($unInformation)) {
                                    $userInformation['introduction'] = "國籍：" . (is_null($unInformation['CountryCode']) ? 'Unknown' : $unInformation['CountryCode']) . PHP_EOL;
                                    $userInformation['introduction'] .= "最後上線時間：" . (is_null($unInformation['LastJoin']) ? 'Unknown' : $unInformation['LastJoin']) . PHP_EOL;
                                    $userInformation['introduction'] .= "持有貨幣：" . (is_null($unInformation['Balance']) ? '0' : $unInformation['Balance']) . PHP_EOL;
                                }
                            }
                            // 抓取遊戲內身份組
                            $unRoles = $unturnedManage->getUserGroups($steamId);
                            if (!empty($unRoles)) {
                                $unRoleDisplayName = [];
                                foreach ($unRoles as $role) $unRoleDisplayName[] = $role['DisplayName'];
                                if (!empty($unRoleDisplayName)) $userInformation['roles'] = $unRoleDisplayName;

                                // 以遊戲內身分組第一個身分作為顯示顏色
                                if (!empty($unRoles)) $userInformation['theme'] = $unRoles[0]['Color'];
                            }
                        }
                    }
                }
            } else {
                $userInformation['introduction'] = "The account has been disabled. Please contact the website administrator :((";
                $userInformation['theme'] = "#990000";
            }
        }
        if (isset($localLoginResult)) {
            if ($userResult['status']) {
                $_SESSION["member"] = $userInformation;
                if($DATA["remember_account"]) setcookie("last_account", $_SESSION["member"]['account'], time() + (3600 * 24 * 365), "/");
                else setcookie("last_account", $_SESSION["member"]['account'], time() - (3600 * 24 * 365), "/");
                $respondJson["data"] = '1|OK';
            } else $respondJson["data"] = 'The account has been disabled. Please contact the website administrator :((';
        } else $respondJson["data"] = $userInformation;
        break;
}

/****************************************************************************************/
// if ($userResult) $LOG_REASON["api"] = "Success.";
// else $LOG_REASON["api"] = "Invalid request parameters.";
