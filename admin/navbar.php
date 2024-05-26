<?php
if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) exit;
/****************************************************************************************
 * 
 * 用於顯示對應權限之導覽頁
 * 
 ****************************************************************************************/
if (!isset($PAGES, $MEMBER_PERMISSIONS)) exit;
$INCLUDE_CSS[] = "/css/navbar.admin.css";

foreach ($PAGES as $key => $p) {
    // 使用者具有該頁面操作權限 或 使用者為最高權限 則加入頁面選單
    if (($p['permission'] & $MEMBER_PERMISSIONS) || $MEMBER_PERMISSIONS & 1) {
        $menu[] = [
            'displayname' => $PAGES[$key]['displayname'],
            'link' => $key,
            'status' => $p['status'],
            'icon' => $p['icon']
        ];
    }
}


// if(isset($PAGES[$PAGE]['nav'])){
//     $menu = $WEB_NAV;
//     foreach ($PAGES as $key => $p) {
//         if ($p['visible']) {
//             $menu[] = [
//                 'displayname' => $PAGES[$key]['displayname'],
//                 'link' => $key,
//                 'icon'  => $PAGES[$key]['icon'],
//             ];
//         }
//     }
//     $WEB_NAV = $menu;
// }else $WEB_NAV='';
$WEB_NAV = $menu;