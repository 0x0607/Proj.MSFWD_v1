<?php
$LOG_STATUS_CODE["api.updatewebsite"] = "1|OK";
$LOG_REASON["api.updatewebsite"] = "";
/****************************************************************************************
 * 
 * 新增網站
 * 
 ****************************************************************************************/
require_once "../sys.global.php";
/****************************************************************************************/
if (isset($_SESSION['mid'])) {
    if ($_SESSION['mid'] != 1344415730149355520) return false;
    header("Content-type: application/json");
    $wid = 1344589822513115136;
    $result = true;
    $websiteInformation = [
        "website" => [
            "domain" => "demo.mbfamily.online",
            "name" => "MBFAMILY",
            "displayname" => "巧可の皮炎派對"
        ],
        "newebpay" => [
            "store_return_url" => "https://www.mbfamily.online/projects/steam",
            "store_client_back_url" => "https://www.mbfamily.online/projects/steam",
            "store_notify_url" => "https://www.mbfamily.online/projects/steam/api/done.php",
        ]
    ];
    $initRoles = [
        [
            "id" => $wid,
            "wid" => $wid,
            "name" => "everyone",
            "displayname" => "所有人",
            "parent_id" => $wid,
            "permissions" => 0,
            "status" => 2
        ],
        [
            "id" => $sf->getId(),
            "wid" => $wid,
            // "wid" => $wid,
            "name" => "root",
            "displayname" => "超級管理員",
            "parent_id" => $wid,
            "permissions" => 0x1FFC,
            "status" => 2
        ],
        [
            "id" => $sf->getId(),
            "wid" => $wid,
            "name" => "admin",
            "displayname" => "管理員",
            "parent_id" => $wid,
            "permissions" => 0x2000,
            "status" => 2
        ]
    ];
    // $result &= $websiteManage->initWebsite($wid, $websiteInformation["website"]["domain"]);
    // $result &= $websiteManage->updateWebsiteInformation($wid, $websiteInformation);
    foreach ($initRoles as $role) {
        $result &= $permissions->addRoles($wid, $role['id']);
        $result &= $permissions->updateRoleInformation($wid, $role['id'], $role);
    }
    $result &= $permissions->changeMemberRoles($wid,$initRoles[1]['id'],589605057335390208);
    echo json_encode([$result]);
} else include_once "../404/index.php";
