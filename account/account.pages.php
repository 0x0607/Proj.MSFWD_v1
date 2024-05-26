<?php
// if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) exit;
/*************************************************************
 *
 * 帳戶頁面，這裡是寫死的
 * 
 *************************************************************/
$PAGES = [
    "login" => [
        "filename" => "login",
        "displayname" => "登入",
        "nonav"=>1
    ],
    "logout" => [
        "filename" => "logout",
        "displayname" => "登出",
        "nonav"=>1
    ],
    "register" => [
        "filename" => "register",
        "displayname" => "註冊",
        "nonav"=>1
    ],
    "profile" => [
        "filename" => "profile",
        "displayname" => "資訊"
    ],
    "setting" => [
        "filename" => "setting",
        "displayname" => "設置"
    ]
];
