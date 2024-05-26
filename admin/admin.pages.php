<?php
// if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) exit;
/*************************************************************
 *
 * 管理頁面的所有頁面，這裡是寫死的
 * 
 *************************************************************/
$PAGES = [
    "home" => [
        "filename" => "home",
        "displayname" => "管理頁面首頁",
        "status" => false,
        "icon" => "house-flag",
        "permission" => 0
    ],
    "manage.website" => [
        "filename" => "website_edit",
        "displayname" => "站台調整",
        "status" => true,
        "icon" => "sitemap",
        "permission" => 1
    ],
    "manage.website.theme" => [
        "filename" => "website_theme_edit",
        "displayname" => "站台外觀調整",
        "status" => true,
        "icon" => "palette",
        "permission" => 1 << 2
    ],
    "manage.roles" => [
        "filename" => "roles_edit",
        "displayname" => "身分組調整",
        "status" => true,
        "icon" => "user-group",
        "permission" => 1 << 1
    ],
    "manage.role" => [
        "filename" => "role_edit",
        "displayname" => "權限調整",
        "status" => false,
        "icon" => "user-gear",
        "permission" => 1 << 1
    ],
    "manage.menu" => [
        "filename" => "menu_edit",
        "displayname" => "導覽列調整",
        "status" => true,
        "icon" => "route",
        "permission" => 1 << 3
    ],
    "manage.submenu" => [
        "filename" => "submenu_edit",
        "displayname" => "次選單調整",
        "status" => false,
        "icon" => "route",
        "permission" => 1 << 3
    ],
    "manage.pages" => [
        "filename" => "page_list",
        "displayname" => "頁面清單",
        "status" => true,
        "icon" => "pager",
        "permission" => 1 << 4
    ],
    "create.page" => [
        "filename" => "page_edit",
        "displayname" => "頁面建檔",
        "status" => false,
        "icon" => "pager",
        "permission" => 1 << 4
    ],
    "manage.page" => [
        "filename" => "page_edit",
        "displayname" => "頁面調整",
        "status" => false,
        "icon" => "pager",
        "permission" => 1 << 4
    ],
    "manage.page.component" => [
        "filename" => "page_component_edit",
        "displayname" => "元件編輯",
        "status" => false,
        "icon" => "pager",
        "permission" => 1 << 4
    ],
    "create.product" => [
        "filename" => "product_edit",
        "displayname" => "商品建檔",
        "status" => true,
        "icon" => "shop",
        "permission" => 1 << 5
    ],
    "manage.product" => [
        "filename" => "product_edit",
        "displayname" => "商品調整",
        "status" => false,
        "icon" => "shop",
        "permission" => 1 << 6
    ],
    "manage.products" => [
        "filename" => "product_list",
        "displayname" => "商品清單",
        "status" => true,
        "icon" => "shop",
        "permission" => 1 << 6
    ],
    "view.orders" => [
        "filename" => "order_view",
        "displayname" => "訂單查詢",
        "status" => false,
        "icon" => "file-lines",
        "permission" => 1 << 7
    ],
    "manage.orders" => [
        "filename" => "order_edit",
        "displayname" => "訂單調整",
        "status" => false,
        "icon" => "file-lines",
        "permission" => 1 << 8
    ],
    "manage.sales_report" => [
        "filename" => "sales_report",
        "displayname" => "銷售報表工具",
        "status" => true,
        "icon" => "file-lines",
        "permission" => 1 << 10
    ],
    "manage.msgboard" => [
        "filename" => "msgboard_edit",
        "displayname" => "公告張貼",
        "status" => false,
        "icon" => "file-lines",
        "permission" => 1 << 9
    ],
    "manage.msgboards" => [
        "filename" => "msgboard_list",
        "displayname" => "公告管理工具",
        "status" => true,
        "icon" => "file-lines",
        "permission" => 1 << 9
    ]
];
// 只有root登入才有的功能，這項功能於/api/也是鎖定root
if ($_SESSION['member']['mid'] == 589605057335390208) {
    $PAGES["manage.websites"] = [
        "filename" => "websites_edit",
        "displayname" => "站台管理 *",
        "status" => true,
        "icon" => "server",
        "permission" => 1
    ];
}