<?php
// use function PHPSTORM_META\type;
if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) exit;
/****************************************************************************************
 * 
 * 公佈欄編輯
 * 
 * Note: 
 * int $_SESSION['mid'] : 會員編號
 * array $DATA : 數據
 * 
 ****************************************************************************************/
$permissionResult = false;
$respondJson = ["status" => 200, "data" => "0|NO"];
switch ($ACTION) {
    case "GROUP_ADD":
    case "GROUP_CHANGE_INFORMATION":
        $updateRoleData = [];
        if (!empty($DATA['displayname'])) $updateRoleData['displayname'] = $DATA['displayname'];
        if (isset($DATA['permissions'])) {
            $updateRoleData['permissions'] = 0;
            $mPermission = $permissions->getMemberPermissions($_SESSION['member']['mid']);
            // 判斷使用者是否為最高權限，如果不是則無法修改"最高權限"並跳出
            //  | $DATA['permissions'][1]
            if ((!($mPermission & 1)) & ($DATA['permissions'][0])) {
                $respondJson["data"] = "Invalid access, Requires permission *(0x1)!!";
                break;
            }
            foreach ($DATA['permissions'] as $p => $value) {
                if ($value) $updateRoleData['permissions'] |= 1 << $p;
            }
        } else break;
        if (!empty($updateRoleData)) {
            if ($ACTION == "GROUP_CHANGE_INFORMATION" && isset($DATA['rid'])) $permissionResult = $permissions->updateRoleInformation($DATA['rid'], $updateRoleData);
            else if ($ACTION == "GROUP_ADD") {
                $DATA['rid'] = $sf->getId();
                $permissionResult = $permissions->addRoles($DATA['rid'], $updateRoleData);
            }
        }
        break;
    case "GROUP_DELETE":
        if (isset($DATA['rid'])) {
            $permissionResult = $permissions->deleteRole($DATA['rid']);
        }
        break;
    case "SEARCH_USER":
        if (isset($DATA['searchAccount'])) {
            $respondJson["data"] = $permissions->searchNoRoleUser($DATA['searchAccount']);
        }
        break;
    case "MEMBER_ADD_GROUP":
        // 待補充系統身分組部分，有時間補洞
        if (isset($DATA['addAccount'], $DATA['rid'])) {
            $searchMember = $memberManage->getMemberInformationByAccount($DATA['addAccount']);
            if (!empty($searchMember)) {
                $mid = $searchMember['id'];
                $rid = $DATA['rid'];
                $permissionResult = $permissions->addMemberRole($mid, $rid);
                if(!$permissionResult) $respondJson['data'] = "Already exists.";
            } else $respondJson['data'] = "Invalid account.";
        } else $respondJson['data'] = "Invalid account and role.";
        break;
    case "MEMBER_REMOVE_GROUP":
        if (isset($DATA['mid'], $DATA['rid'])) {
            $mid = $DATA['mid'];
            $rid = $DATA['rid'];
            $permissionResult = $permissions->removeMemberRole($mid, $rid);
        }
        break;
}
/****************************************************************************************/
if ($permissionResult) {
    $respondJson["data"] = "1|OK";
} 
// else {
//     $respondJson['data'] = "Invalid request parameters.";
//     $LOG_REASON["api"] = "Invalid request parameters.";
// }
