<?php
if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) exit;
$permissionData = $permissions->getRoleInformation();
if(!empty($permissionData)){
    foreach ($permissionData as $key => $value) {
        $HASH_RAWDATA["rid"] = $permissionData[$key]['id'];
        $permissionData[$key]['hashdata'] = encryptData($HASH_RAWDATA, WEBSITE_HASH_KEY, $DATA['iv']);
    }
    $smarty->assign("roles",$permissionData);
}