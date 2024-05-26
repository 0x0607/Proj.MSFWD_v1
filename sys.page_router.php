<?php
if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) exit;
/*************************************************************
 *
 * 頁面選擇器 Part.5 By MaizuRoad
 * 
 *************************************************************/
// 網站使用物件
$includedComponents = ['backend' => [], 'frontend' => []];
// 檢索分頁設定值
$file->setPath(CONFIG_PATH['web'] . "/" . WEBSITE['id'] . "/pages/{$PAGE_ID}/");
$pageConfigContent = $file->readFile("page_components_config.json");
// 欄位數量
$grid_columns = 0;
// 若無分頁設定則忽略
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
                // if ($componentData['status']) {
                $componentsIds[] = $componentData['cid'];
                $componentsMap[$positionY][$positionX] = true;
                // } else unset($pageConfigComponents[$positionX][$positionY]);
            }
            // 欄位數目增加
            $grid_columns++;
        }
        // 讀取元件對應資訊 DB
        $componentTemplates = $pageManage->getComponentTemplates($componentsIds);
        $gridSizeX = [];
        // $positionX X軸 $positionY Y軸
        foreach ($pageConfigComponents as $positionX => $positionXData) {
            foreach ($positionXData as $positionY => $componentData) {
                // 檢索對應設定值，若無設定值則忽略
                $componentConfigContent = $file->readFile("{$componentData['id']}.json");
                if (!empty($componentConfigContent)) {
                    $decodedData = json_decode($componentConfigContent, true);
                    // 釋放記憶體
                    unset($componentConfigContent);
                    if ($decodedData !== null && json_last_error() === JSON_ERROR_NONE) {
                        $componentID = $componentData['cid'];
                        // 同Y軸 若 X軸未配置則配置當前X軸座標(簡單來說就是設0，$positionX起始值一定為0)
                        $gridSizeX[$positionY] = $gridSizeX[$positionY] ?? $positionX;
                        // 設至Y軸座標
                        $componentSizeY = $positionY + 1;
                        // 如果有配置元件大小
                        if ($componentTemplates[$componentID]['size_x'] != 0) {
                            $positionEnd = $gridSizeX[$positionY] + $componentTemplates[$componentID]['size_x'];
                        }
                        // 沒有配置元件大小為自適應
                        else $positionEnd = $positionX;
                        $componentStyle = '';
                        // 浮動元件則設置為絕對位址且不受版面滿版時自動隱藏影響
                        if ($componentTemplates[$componentID]['is_absolute_position']) $componentStyle = "position: absolute";
                        // X軸版面滿版時自動隱藏
                        else if ($positionEnd > $grid_columns) $componentStyle = "display: none  /*座標Pos: ({$positionEnd} , {$componentSizeY})*/";
                        else {
                            // 如果沒有配置大小則預設滿版，如果相同Y軸沒有物件則預設也為滿版
                            if ($componentTemplates[$componentID]['size_x'] === 0 || count($componentsMap[$positionY]) === 1) {
                                $gridSizeX[$positionY] = $grid_columns + 1;
                                $componentSizeX = "1 / " . ($grid_columns + 1);
                            }
                            // 配置元件實際大小
                            else $componentSizeX = ($gridSizeX[$positionY] + 1) . " / {$positionEnd}";
                            // 寫入配置
                            $componentStyle = "grid-column:{$componentSizeX}; grid-row:{$componentSizeY}; ";
                            $gridSizeX[$positionY] += $componentTemplates[$componentID]['size_x'];
                        }
                        $component = [
                            'template' => $componentTemplates[$componentID]['template'],
                            'style' => "style='{$componentStyle}'"
                        ] + $decodedData;
                        // 引入前端
                        $includedComponents['frontend'][] = $component;
                        // 引入後端
                        $phpName = $componentTemplates[$componentID]['php_name'];
                        if (!in_array($phpName, $includedComponents['backend'])) {
                            require_once "php/{$phpName}.php";
                        }
                    }
                }
            }
        }
    }
} else echo "<!-- blank page ;( -->";

// 將元件資訊及導覽列資訊寫進smartyAssign
$smarty->assign("pageComponents", $includedComponents['frontend']);
// 處理介面布局
$smarty->assign("MAIN_GRID_SETTING", "style=\"grid-template-columns: repeat({$grid_columns}, 1fr)\"");
// 釋放記憶體
unset(
    $pageConfigComponents,
    $componentsIds,
    $componentsMap,
    $componentConfigContent,
    $includedComponents,
    $componentTemplates,
    $componentsInfo,
    $grid_columns,
    $gridSizeX
);
