<?php
if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) exit;
$INCLUDE_JS[] = "/javascripts/admin.page_component_edit.js";
function page_component_edit_back()
{
    header("location: /admin/?route=manage.pages");
    exit;
}
//沒有配置 id則跳轉出去
if (!isset($_GET['pid'], $_GET['id'])) page_component_edit_back();
// 確認是否存在頁面
$pageData = $pageManage->getPageInformationById(intval($_GET['pid']));
if (!empty($pageData)) {
    $pid = intval($_GET['pid']);
    $action = 'PAGE_CHANGE_COMPONENT';
    // $BREAD_CRUMB[] = ["displayname" => $pageData['displayname'], "link" => "/admin/?route={$PAGE}&pid={$pid}"];
    $pageTitle = '頁面元件調整';
    $file->setPath(WEBSITE_FILE_DIR . "/pages/{$pid}/");
    $pageConfigContent = $file->readFile("page_components_config.json");
    if (!empty($pageConfigContent)) {
        $pageConfigComponents = json_decode($pageConfigContent, true);
        // 釋放記憶體
        unset($pageConfigContent);
        if ($pageConfigComponents !== null && json_last_error() === JSON_ERROR_NONE) {
            // 檢索所有元件ID
            // $positionX X軸 $positionY Y軸
            foreach ($pageConfigComponents as $positionX => $positionXData) {
                foreach ($positionXData as $positionY => $componentData) {
                    // 確認啟用狀態
                    if (trim($_GET['id']) == $componentData['id']) {
                        $component_id = $componentData['id'];
                        $component_cid = $componentData['cid'];
                    }
                }
            }
            if (isset($component_id, $component_cid)) {
                // 讀取元件對應資訊 DB
                $componentTemplates = $pageManage->getComponentTemplates([$component_cid]);
                $pageComponentInformation = $file->readFile("{$component_id}.json");
                $pageComponentData = json_decode($pageComponentInformation, true);
                // 釋放記憶體
                unset($pageConfigContent);
                if ($pageComponentData !== null && json_last_error() === JSON_ERROR_NONE) {
                    require_once("./php/components/{$componentTemplates[$component_cid]['php_name']}.php");
                    $smarty->assign("submit_action", "COMPONENT_EDIT");
                }
                // $smarty->assign("pageComponentData", $pageConfigComponents);
            }
        }
        $smarty->assign("pName", "{$PAGE}&pid={$pid}#{$component_id}");
    } else page_component_edit_back();
}else page_component_edit_back();
// 參數參考對照表
// "1.0": {                                 // 版本(通常為1.0)
//   "param1": {                            // 參數param1 名稱可自訂
//     "multiplier": 2,                     // 元素重複數量
//     "value": {                           // 參數內的值
//       "ex1": {                           // 值ex1 名稱可自訂
//         "type": "text",                  // 型別 預設皆為文字框， image/hidden/textarea/select/radio/checkbox/switch/color
//         "displayname": "參數1的ex1",     // 顯示名稱
//         "default": ""                    // 初值
//       },
//       "ex2": {                           // 值ex2 名稱可自訂
//         "type": "image",                 // 型別 預設皆為文字框， image/hidden/textarea/select/radio/checkbox/switch/color
//         "displayname": "參數1的ex2",     // 顯示名稱
//         "default": ""                    // 初值
//       }
//     }
//   },
//   "param2": {                            // 參數2 名稱可自訂
//     "multiplier": 1,                     // 元素重複數量
//     "value": {                           // 參數內的值
//       "ex1": {                           // 值ex2 名稱可自訂
//         "type": "textarea",              // 型別 預設皆為文字框， image/hidden/textarea/select/radio/checkbox/switch/color
//         "displayname": "參數2的ex1",     // 顯示名稱
//         "default": ""                    // 初值
//       }
//     }
//   }
// }