<?php
if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) exit;
/****************************************************************************************
 * 
 * 針對頁面設置專用
 * 
 * Note: 
 * int $_SESSION['mid'] : 會員編號
 * array $DATA : 數據
 * 
 ****************************************************************************************/
$pageResult = false;
$respondJson = ["status" => 200, "data" => "0|NO"];
switch ($ACTION) {
    case "PAGE_ADD":
        $DATA['pid'] = $sf->getId();
    case "PAGE_CHANGE_INFORMATION":
        $newPageData = [];
        if (!empty($DATA['displayname'])) {
            if (mb_strlen($DATA['displayname']) < 10) $newPageData['displayname'] = htmlspecialchars(trim($DATA['displayname']));
        }
        if (!empty($DATA['name']) && preg_match('/^[a-zA-Z0-9\.\-_]+$/', $DATA['name'])) $newPageData['name'] = $DATA['name'];
        else $newPageData['name'] = "p_{$DATA['pid']}";
        if (!empty($DATA['description'])) $newPageData['description'] = $DATA['description'];
        if (isset($DATA['nav'])) $newPageData['nav'] = intval($DATA['nav']);
        if (isset($DATA['status']) && $ACTION == "PAGE_ADD") {
            if (intval($DATA['status']) < 2) $newPageData['status'] = intval($DATA['status']);
        }
        if (isset($newPageData['name'])) {
            if ($ACTION == "PAGE_ADD") {
                $file->setPath(WEBSITE_FILE_DIR . "/pages/{$DATA['pid']}/");
                $file->writeFile("[[]]", "page_components_config.json");
                $pageResult = $pageManage->addPage($DATA['pid'], $newPageData);
                if(!$pageResult) $respondJson["data"] = "Invalid page name(link).";
            } else $pageResult = $pageManage->editPage($DATA['pid'], $newPageData);
        }
        break;
    case "PAGE_CHANGE_STATUS":
        if (isset($DATA['pid'])) {
            $pageResult = $pageManage->changePageStatus($DATA['pid']);
            if ($pageResult) {
                $LOG_REASON["api"] = "Page update success.";
                $respondJson["data"] = "1|OK";
            } else $respondJson["data"] = "Page change fail.";
        }
        break;
    case "PAGE_DELETE":
        if (isset($DATA['pid'])) {
            $pageResult = $pageManage->deletePage($DATA['pid']);
            if ($pageResult) {
                $LOG_REASON["api"] = "Page delete success.";
                $respondJson["data"] = "1|OK";
            } else $respondJson["data"] = "Page delete fail.";
        }
        break;
    case "PAGE_CHANGE_COMPONENT_POS":
        if (isset($DATA['pid'])) {
            $pid = trim($DATA['pid']);
            $checkPid = $pageManage->getPageInformationById($pid);
            if ($checkPid) {
                unset($checkPid);
                // 驗證是否合法json
                $newPageConfigContent = $DATA['pos'];
                $enableComponentFile = [];
                if ($newPageConfigContent !== null && json_last_error() === JSON_ERROR_NONE) {
                    $file->setPath(WEBSITE_FILE_DIR . "/pages/{$pid}/");
                    foreach ($newPageConfigContent as $positionX => $positionXData) {
                        foreach ($positionXData as $positionY => $componentData) {
                            $component_init = [
                                "id" => $componentData['id'],
                                "displayname" => "New Component",
                                "theme"=> "default",
                                "version" => "1.0"
                            ];
                            $checkComponentFile = $file->checkFile("{$componentData['id']}.json");
                            if (!$checkComponentFile) $file->writeFile($component_init, "{$componentData['id']}.json");
                            $enableComponentFile[] = "{$componentData['id']}.json";
                        }
                    }
                    // 測試模式才會清檔，以防萬一
                    if(WEBSITE_DEBUG){
                        $componentFile = $file->getMatchingFiles("/([0-9]{18,20})\.json/");
                        foreach ($componentFile as $f) {
                            if (!in_array($f, $enableComponentFile)) {
                                $file->deleteFile($f);
                            }
                        }
                    }
                    $pageResult = $file->writeFile($newPageConfigContent, "page_components_config.json");
                } else $respondJson["data"] = "Invalid components position.";
            } else $respondJson["data"] = "Invalid page id.";
        }
        break;
}

/****************************************************************************************/
if ($pageResult) {
    $respondJson["data"] = "1|OK";
    $LOG_REASON["api"] = "Success.";
} else $LOG_REASON["api"] = "Invalid request parameters.";
