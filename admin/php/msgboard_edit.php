<?php
if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) exit;
$msgTypeData = $msgBoard->getAllPostType();
if (!empty($msgTypeData)) {
    foreach ($msgTypeData as $value) {
        $msgType["{$value['type']}"] = $value['id'];
    }
    $msgType["沒有分類"] = 0;
}
$defalutValue = [
    "title" => "",
    "message" => "",
    "author" => "請選擇",
    "weight" => 0,
    "tid" => "請選擇分類"
];
$action = 'MSGBOARD_ADD';
$pageTitle = '公告發布';
if (isset($_GET['pid'])) {
    $msgData = $msgBoard->getpost(intval($_GET['pid']));
    if (!empty($msgData)) {
        $pid = intval($_GET['pid']);
        $action = 'MSGBOARD_CHANGE_POST';
        $pageTitle = '公告編輯';
        $BREAD_CRUMB[] = ["displayname" => "{$pageTitle}-{$msgData['title']}", "link" => "/admin/?route={$PAGE}&pid={$pid}"];
        $defalutValue = [
            "title" => $msgData['title'],
            "message" => $msgData['message'],
            "author" => $msgData['author'],
            "weight" => $msgData['weight'],
            "tid" => isset($msgData['type']) ? $msgData['type'] : "請選擇分類"
        ];
        $HASH_RAWDATA["pid"] = $pid;
        $smarty->assign("pid", $pid);
    }
}
$formInput = [
    "公告設置" => [
        [
            "title" => [
                "label" => "公告標題",
                "value" => $defalutValue['title'],
                "placeholder" => "公告標題",
                "type" => "text",
                "required" => true,
                "readonly" => false,
            ]
        ],
        [
            "message" => [
                "label" => "公告內文",
                "value" => $defalutValue['message'],
                "placeholder" => "公告內文",
                "type" => "textarea",
                "required" => false,
                "readonly" => false
            ]
        ],
        [
            "author" => [
                "label" => "發布單位",
                "value" => [
                    WEBSITE['displayname'] => WEBSITE['displayname'] . "團隊",
                    $_SESSION['member']['nickname'] => $_SESSION['member']['nickname']
                    // "Anonymous" => "Anonymous"
                ],
                "placeholder" => $defalutValue['author'],
                "type" => "select",
                "required" => false,
                "readonly" => false
            ]
        ],
        [
            "tid" => [
                "label" => "分類",
                "value" => $msgType,
                "placeholder" => $defalutValue['tid'],
                "type" => "select",
                "required" => false,
                "readonly" => false
            ]
        ],
        [
            "weight" => [
                "label" => "權數(數字越大，排序越前)",
                "value" => $defalutValue['weight'],
                "placeholder" => "權數越大排序越前面",
                "type" => "number",
                "required" => false,
                "readonly" => false
            ]
        ]
    ]
];

if ($action == 'MSGBOARD_ADD') $formInput["其他配置"] = [["status" => ["label" => "狀態", "value" => true, "placeholder" => "是否直接發布", "type" => "switch", "required" => false, "readonly" => false,]]];
$smarty->assign("formInput", $formInput);
$smarty->assign("submit_action", $action);
