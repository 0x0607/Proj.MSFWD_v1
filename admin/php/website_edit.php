<?php
if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) exit;
$formInput = [
    "網站圖標" => [
        ["uploadfile" => [
            "label" => "圖標",
            "value" => WEBSITE_ICON_URL,
            "placeholder" => "",
            "type" => "image",
            "required" => true,
            "readonly" => false
        ]]
    ],
    "一般設置" => [
        ["displayname" => [
            "label" => "網站名稱",
            "value" => WEBSITE['displayname'],
            "placeholder" => WEBSITE['displayname'],
            "type" => "text",
            "required" => true,
            "readonly" => false
        ]]
        // ["distribution" => [
        //     "label" => "網站地區/國家",
        //     "value" => [
        //         "中華民國(台灣)" => "Taiwan",
        //         "中華人民共和國(中國)" => "China",
        //         "美利堅合眾國(美國)" => "United States",
        //         "日本" => "Japan",
        //         "大韓民國(南韓)" => "South Korea",
        //         "越南社會主義共和國" => "Vietnam",
        //         "泰國" => "Thailand",
        //         "馬來西亞" => "Malaysia"
        //     ],
        //     "placeholder" => WEBSITE['distribution'],
        //     "type" => "select",
        //     "required" => false,
        //     "readonly" => true
        // ]]
    ],
    "金流設置" => [
        ["store_id" => [
            "label" => "藍新-商店代號",
            "value" => "",
            "placeholder" => "StoreId",
            "type" => "password",
            "required" => false,
            "readonly" => false
        ]],
        ["store_hash_key" => [
            "label" => "藍新-HashKey",
            "value" => "",
            "placeholder" => "HashKey",
            "type" => "password",
            "required" => false,
            "readonly" => false
        ]],
        ["store_hash_iv" => [
            "label" => "藍新-HashIV",
            "value" => "",
            "placeholder" => "HashIV",
            "type" => "password",
            "required" => false,
            "readonly" => false
        ]]
    ]
];

$smarty->assign("formInput", $formInput);
$smarty->assign("submit_action", "WEBSITE_CHANGE_INFORMATION");