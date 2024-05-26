<?php
if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) exit;
$INCLUDE_JS[] = "/javascripts/admin.page_edit.js";
$INCLUDE_CSS[] = "/css/admin.page_edit.css";
$defalutValue = [
    "pid" => "",
    "name" => "",
    "displayname" => "",
    "description" => "",
    "nav" => 1
];
$action = 'PAGE_ADD';
$pageTitle = '頁面建檔';
$pageNameReadonly = false;
$pageData = [];
if (isset($_GET['pid'])) {
    $pageData = $pageManage->getPageInformationById(intval($_GET['pid']));
    if (!empty($pageData)) {
        $pid = intval($_GET['pid']);
        $action = 'PAGE_CHANGE_INFORMATION';
        $BREAD_CRUMB[] = ["displayname" => $pageData['displayname'], "link" => "/admin/?route={$PAGE}&pid={$pid}"];
        $pageTitle = '頁面調整';
        $defalutValue = [
            "name" => $pageData['name'],
            "displayname" => $pageData['displayname'],
            "description" => $pageData['description'],
            "nav" => $pageData['nav']
        ];
        $HASH_RAWDATA["pid"] = $pid;
        $pageNameReadonly = ($pageData['status'] == 2);

        $file->setPath(WEBSITE_FILE_DIR . "/pages/{$pid}/");
        $pageConfigContent = $file->readFile("page_components_config.json");
        if (!empty($pageConfigContent)) {
            $pageConfigComponents = json_decode($pageConfigContent, true);
            // 釋放記憶體
            unset($pageConfigContent);
            if ($pageConfigComponents !== null && json_last_error() === JSON_ERROR_NONE) {
                // 檢索所有元件ID
                $componentsIds = [];
                // $positionX X軸 $positionY Y軸
                foreach ($pageConfigComponents as $positionX => $positionXData) {
                    foreach ($positionXData as $positionY => $componentData) {
                        // 確認啟用狀態
                        // if ($componentData['status']) $componentsIds[] = $componentData['cid'];
                        // else unset($pageConfigComponents[$positionX][$positionY]);
                        $componentsIds[] = $componentData['cid'];
                    }
                }
                // 讀取元件對應資訊 DB
                $componentTemplates = $pageManage->getComponentTemplates($componentsIds);
                foreach ($pageConfigComponents as $positionX => $positionXData) {
                    foreach ($positionXData as $positionY => $componentData) {
                        $pageConfigComponents[$positionX][$positionY]['name'] = $componentTemplates[$componentData['cid']]['displayname'];
                        $pageComponentInformation = $file->readFile("{$componentData['id']}.json");
                        if (!empty($pageComponentInformation)) {
                            $pageComponentInformationData = json_decode($pageComponentInformation, true);
                            // 釋放記憶體
                            unset($pageComponentInformation);
                            if ($pageComponentInformationData !== null && json_last_error() === JSON_ERROR_NONE) {
                                $pageConfigComponents[$positionX][$positionY]['displayname'] = $pageComponentInformationData['displayname'];
                            }
                        }
                    }
                }
                $smarty->assign("pageComponentList", $pageConfigComponents);
            }
        } else $file->writeFile("[[]]", "page_components_config.json");
        // $pageManage->getComponentTemplates($componentsIds);
        // $filesData = $file->getMatchingFiles("/([0-9]{18,20})\.json/");
        // foreach ($filesData as $f) {
        //     print_r(json_decode($file->readFile($f), true));
        // }

    }
}

$formInput = [
    "配置" => [
        [
            "displayname" => [
                "label" => "名稱",
                "value" => $defalutValue['displayname'],
                "placeholder" => "頁面名稱",
                "type" => "text",
                "required" => true,
                "readonly" => false,
            ]
        ],
        [
            "description" => [
                "label" => "簡介",
                "value" => $defalutValue['description'],
                "placeholder" => "頁面簡介",
                "type" => "textarea",
                "required" => false,
                "readonly" => false
            ]
        ],
        [
            "name" => [
                "label" => "頁面連結",
                "value" => $defalutValue['name'],
                "placeholder" => "範例'homo'、'about'，若無填寫則自動生成",
                "type" => "text",
                "required" => false,
                "readonly" => $pageNameReadonly
            ]
        ]
    ],
    "其他配置" => [
        [
            "nav" => [
                "label" => "導覽列",
                "value" => boolval($defalutValue['nav']),
                "placeholder" => "是否顯示導覽列",
                "type" => "switch",
                "required" => false,
                "readonly" => false
            ]
        ]
    ]
];
if ($action == 'PAGE_ADD') $formInput["其他配置"][] = ["status" => ["label" => "狀態", "value" => false, "placeholder" => "是否直接啟用頁面", "type" => "switch", "required" => false, "readonly" => false]];
$smarty->assign("formInput", $formInput);
$smarty->assign("submit_action", $action);
$smarty->assign("page_title", $pageTitle);
if (isset($pid)) $smarty->assign("pid", $pid);
if (!empty($pageData)) {
    $smarty->assign("pName", $pageData['name']);
    unset($pageData);
}
