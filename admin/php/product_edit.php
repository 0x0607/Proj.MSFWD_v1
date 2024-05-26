<?php
if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) exit;
$defalutValue = [
    "pid"=>"",
    "uploadfile" => "",
    "name" => "",
    "description" => "",
    "price" => "",
    "quantity" => ""
];
$action = 'PRODUCT_ADD';
$pageTitle = '商品建檔';
if (isset($_GET['pid'])) {
    $product = $productManage->getProductInformation(intval($_GET['pid']));
    if (!empty($product)) {
        $pid = intval($_GET['pid']);
        $action = 'PRODUCT_CHANGE_INFORMATION';
        $BREAD_CRUMB[] = ["displayname"=>$product['name'],"link"=>"/admin/?route={$PAGE}&pid={$pid}"];
        $pageTitle = '商品調整';
        $defalutValue = [
            "uploadfile" => $product['images'],
            "name" => $product['name'],
            "description" => $product['description'],
            "price" => $product['price'],
            "quantity" => $product['quantity'],
        ];
        $HASH_RAWDATA["pid"] = $pid;
    }
}

$formInput = [
    "商品圖示" => [
        ["uploadfile" => [
            "label" => "商品圖示",
            "value" => $defalutValue['uploadfile'],
            "placeholder" => "",
            "type" => "image",
            "required" => false,
            "readonly" => false,
            "loop" => 4
        ]]
        // ["delete_uploadfile" => [
        //     "label" => "刪除圖片",
        //     "value" => "",
        //     "placeholder" => "",
        //     "type" => "hidden",
        //     "required" => false,
        //     "readonly" => true
        // ]]
    ],
    "商品設置" => [
        [
            "name" => [
                "label" => "名稱",
                "value" => $defalutValue['name'],
                "placeholder" => "商品名稱",
                "type" => "text",
                "required" => true,
                "readonly" => false,
            ]
        ],
        [
            "description" => [
                "label" => "簡介",
                "value" => $defalutValue['description'],
                "placeholder" => "商品簡介",
                "type" => "textarea",
                "required" => false,
                "readonly" => false
            ]
        ]
        // [
        //     "types" => [
        //         "label" => "分類",
        //         "value" => "",
        //         "placeholder" => "商品分類",
        //         "type" => "text",
        //         "required" => false,
        //         "readonly" => true
        //     ]
        // ]
    ],
    // "客制化設定" => [
    //     [
    //         "specification" => [
    //             "label" => "客制化",
    //             "value" => "",
    //             "placeholder" => "客制化設置",
    //             "type" => "text",
    //             "required" => false,
    //             "readonly" => true
    //         ],
    //         "color" => [
    //             "label" => "顏色",
    //             "value" => "",
    //             "placeholder" => "顏色設置",
    //             "type" => "text",
    //             "required" => false,
    //             "readonly" => true
    //         ]
    //     ]
    // ],

    // "虛擬產品" => [
    //     [
    //         "delivery" => [
    //             "label" => "發貨模式",
    //             "value" => [
    //                 "Unturned 千方百計" => "unturned",
    //                 "Minecraft 我的世界" => "minecraft",
    //                 "PalWorld 幻獸帕魯" => "palworld"
    //             ],
    //             "placeholder" => "- 請選擇服務 -",
    //             "type" => "select",
    //             "required" => false,
    //             "readonly" => true,
    //         ]
    //     ],
    //     [
    //         "virtual_product" => [
    //             "label" => "虛擬商品參數",
    //             "value" => "",
    //             "placeholder" => "虛擬商品參數",
    //             "type" => "textarea",
    //             "required" => false,
    //             "readonly" => true,
    //         ]
    //     ]
    // ],
    "售價與數量" => [
        [
            "price" => [
                "label" => "售價",
                "value" => $defalutValue['price'],
                "placeholder" => "至少為29元新台幣",
                "type" => "number",
                "required" => true,
                "readonly" => false,
            ],
            "quantity" => [
                "label" => "數量",
                "value" => $defalutValue['quantity'],
                "placeholder" => "至少為1件，-1為無限",
                "type" => "number",
                "required" => true,
                "readonly" => false,

            ]
        ]
    ]
    // "其他配置" => [["status" => ["label" => "狀態","value" => false,"placeholder" => "完成後是否直接上架","type" => "switch","required" => false,"readonly" => false,]]]
];
if ($action == 'PRODUCT_ADD') $formInput["其他配置"] = [["status" => ["label" => "狀態", "value" => false, "placeholder" => "完成後是否直接上架", "type" => "switch", "required" => false, "readonly" => false,]]];
$smarty->assign("formInput", $formInput);
$smarty->assign("submit_action", $action);
$smarty->assign("page_title", $pageTitle);
if(isset($pid)) $smarty->assign("pid",$pid);