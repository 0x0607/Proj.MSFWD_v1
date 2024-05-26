<?php
// use function PHPSTORM_META\type;
if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) exit;
/****************************************************************************************
 * 
 * 公佈欄編輯
 * 
 * Note: 
 * int $_SESSION['mid'] : 會員編號
 * array $DATA : 數據
 * 
 ****************************************************************************************/
$msgBoardResult = false;
$respondJson = ["status" => 200, "data" => "0|NO"];
switch ($ACTION) {
    case "MSGBOARD_ADD":
        $DATA['pid'] = $sf->getId();
        $updateMsgBoardData = [];
        if (!empty($DATA['title'])) $updateMsgBoardData['title'] = $DATA['title'];
        if (!empty($DATA['message'])) $updateMsgBoardData['message'] = $DATA['message'];
        if (!empty($DATA['author'])) $updateMsgBoardData['author'] = htmlspecialchars($DATA['author']);
        if (!empty($DATA['weight'])) $updateMsgBoardData['weight'] = intval($DATA['weight']);
        if (!empty($DATA['tid'])) {
            if ($DATA['tid'] == 0) $updateMsgBoardData['tid'] = null;
            else $updateMsgBoardData['tid'] = intval($DATA['tid']);
        }
        if (!empty($_SESSION['member']['mid'])) $updateMsgBoardData['mid'] = $_SESSION['member']['mid'];
        else break;

        if (!empty($updateMsgBoardData)) $msgBoardResult = $msgBoard->addpost($DATA['pid'], $updateMsgBoardData);
        if ($msgBoardResult) $LOG_REASON["api"] = "Post create success.";
        break;
    case "MSGBOARD_CHANGE_POST":
        if (isset($DATA['pid'])) {
            $updateMsgBoardData = [];
            if (!empty($DATA['title'])) $updateMsgBoardData['title'] = $DATA['title'];
            if (!empty($DATA['message'])) $updateMsgBoardData['message'] = $DATA['message'];
            if (!empty($DATA['author'])) $updateMsgBoardData['author'] = htmlspecialchars($DATA['author']);
            if (!empty($DATA['weight'])) $updateMsgBoardData['weight'] = intval($DATA['weight']);
            if (!empty($DATA['tid'])) {
                if ($DATA['tid'] == 0) $updateMsgBoardData['tid'] = null;
                else $updateMsgBoardData['tid'] = intval($DATA['tid']);
            }
            if (!empty($_SESSION['member']['mid'])) $updateMsgBoardData['mid'] = $_SESSION['member']['mid'];
            else break;

            if (!empty($updateMsgBoardData)) $msgBoardResult = $msgBoard->updataPost($DATA['pid'], $updateMsgBoardData);
            if ($msgBoardResult) $LOG_REASON["api"] = "Post update success.";
        }
        break;
    case "MSGBOARD_CHANGE_STATUS":
        if (isset($DATA['pid'])) {
            $msgBoardResult = $msgBoard->changePostStatus($DATA['pid']);
            if ($msgBoardResult) $LOG_REASON["api"] = "Post update success.";
        }
        break;
    case "MSGBOARD_DELETE":
        if (isset($DATA['pid'])) {
            $msgBoardResult = $msgBoard->deletePost($DATA['pid']);
            if ($msgBoardResult) $LOG_REASON["api"] = "Post delete success.";
        }
        break;
    case "MSGBOARD_ADD_TYPE":
        $DATA['tid'] = $sf->getId();
        if (!empty($DATA['typename'])) {
            $validTypename = ['沒有分類', 'null', '無', 'NaN'];
            if (in_array($DATA['typename'], $validTypename)) break;
            $msgBoardResult = $msgBoard->addPostType($DATA['tid'], htmlspecialchars(trim($DATA['typename'])));
            if ($msgBoardResult) $LOG_REASON["api"] = "Post type add success.";
        }
        break;
    case "MSGBOARD_DELETE_TYPE":
        if (isset($DATA['tid'])) {
            $msgBoardResult = $msgBoard->deletePostType($DATA['tid'], htmlspecialchars(trim($DATA['typename'])));
            if ($msgBoardResult) $LOG_REASON["api"] = "Post type deleted.";
        }
        break;
    case "MSGBOARD_CHANGE_TYPE":
        if (isset($DATA['tid'])) {
            if (!empty($DATA['typename'])) {
                $validTypename = ['沒有分類', 'null', '無', 'NaN'];
                if (in_array($DATA['typename'], $validTypename)) break;
                $msgBoardResult = $msgBoard->updatePostType($DATA['tid'], htmlspecialchars(trim($DATA['typename'])));
                if ($msgBoardResult) $LOG_REASON["api"] = "Post type update success.";
            }
        }
        break;
}
/****************************************************************************************/
if ($msgBoardResult) {
    $respondJson["data"] = "1|OK";
} else {
    $respondJson['data'] = "Invalid request parameters.";
    $LOG_REASON["api"] = "Invalid request parameters.";
}
