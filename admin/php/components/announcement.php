<?php
if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) exit;
$formInput = [
    "標題文字" => [
        [
            "title" => [
                "label" => "標題文字",
                "value" => "新建的公告欄",
                "placeholder" => "",
                "type" => "text",
                "required" => true,
                "readonly" => false
            ]
        ]
    ],
    "內容設置" => [
        [
            "title" => [
                "label" => "公告標題",
                "value" => "未命名標題",
                "placeholder" => "留空將不顯示",
                "type" => "text",
                "required" => true,
                "readonly" => false,
            ]
        ],
        [
            "content" => [
                "label" => "公告內文",
                "value" => "",
                "placeholder" => "公告內文",
                "type" => "textarea",
                "required" => false,
                "readonly" => false
            ]
        ]
    ]
];
// $formInputAdd = [
//     [
//         "title" => [
//             "label" => "公告標題",
//             "value" => "",
//             "placeholder" => "留空將不顯示",
//             "type" => "text",
//             "required" => false,
//             "readonly" => false,
//         ]
//     ],
//     [
//         "content" => [
//             "label" => "公告內文",
//             "value" => "",
//             "placeholder" => "公告內文",
//             "type" => "textarea",
//             "required" => false,
//             "readonly" => false
//         ]
//     ]
// ];
// for ($i = 2; $i < 5; $i++) $formInput["內容設置{$i}"] = $formInputAdd;
$smarty->assign("formInput", $formInput);
