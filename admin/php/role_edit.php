<?php
if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) exit;
$INCLUDE_JS[]="/javascripts/admin.role_edit.js";
$action = 'GROUP_ADD';
$pageTitle = '新建身分組';
$defalutValue = "未命名身分組";
$defalutPermission = 0;
$isSystemRole = false;
$permissionList = $permissions->getPermissionsList();
if (isset($_GET['rid'])) {
    $permissionData = $permissions->getRoleInformation(intval($_GET['rid']));
    if (!empty($permissionData)) {
        if ($permissionData['status'] != 2) {
            $rid = intval($_GET['rid']);
            $action = 'GROUP_CHANGE_INFORMATION';
            $BREAD_CRUMB[] = ["displayname" => $permissionData['displayname'], "link" => "/admin/?route={$PAGE}&rid={$rid}"];
            $pageTitle = '調整身分組';
            $defalutValue = $permissionData['displayname'];
            $defalutPermission = $permissionData['permissions'];
            $isSystemRole = ($permissionData['status'] == 2);
            $HASH_RAWDATA["rid"] = $rid;

            $roleMembersList = $permissions->getRoleMembers($rid);
            $smarty->assign("roleMembersList", $roleMembersList);
        }
    }
}
foreach ($permissionList as $key => $value) {
    $pList[$key] = ["permissions[{$key}]" => [
        "label" => "{$value['displayname']} ({$value['name']})",
        // 位元運算的魅力 ¯\_(ツ)_/¯
        "value" => ($defalutPermission & (1 << $key)),
        "placeholder" => "",
        "type" => "switch",
        "required" => false,
        "readonly" => $isSystemRole
    ]];
}
unset($permissionList);
// 判斷使用者是否為最高權限，如果沒有則"最高權限"與"群組管理"設為唯獨
$mPermission = $permissions->getMemberPermissions($_SESSION['member']['mid']);
$pList[0]["permissions[0]"]["readonly"] = (!($mPermission & 1)) | $isSystemRole;
// $pList[1]["permissions[1]"]["readonly"] = (!($mPermission & 1)) | $isSystemRole;

$formInput = [
    "身分組資訊" => [
        [
            "displayname" => [
                "label" => "身份組名稱",
                "value" => $defalutValue,
                "placeholder" => $defalutValue,
                "type" => "text",
                "required" => true,
                "readonly" => false | $isSystemRole
            ]
        ]
    ],
    "身分組權限" => $pList
];
$smarty->assign("formInput", $formInput);
$smarty->assign("submit_action", $action);
$smarty->assign("page_title", $pageTitle);
if (isset($rid)) $smarty->assign("rid", $rid);
