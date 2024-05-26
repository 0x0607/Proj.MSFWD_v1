<?php

/*************************************************************
 * 
 * 頁面管理 : 增刪頁面
 * @param obj $db 資料庫
 * 
 *************************************************************/
class pageManage
{
    private $conn;
    private $wid = null;
    private $pages = "w_pages";
    private $pageComponent = "w_page_component";
    private $components = "s_components";

    public function __construct($db)
    {
        $this->conn = $db;
    }
    /************************************************
     * ### 設置 website_id ###
     * @param int $wid website_id
     ************************************************/
    public function setWebsiteId($wid)
    {
        return $this->wid = $wid;
    }
    /************************************************
     * ### 檢索頁面 ###
     * @param obj $pid 頁面編號
     ************************************************/
    public function getPageInformationById($pid = false): array
    {
        $result = [];
        if ($pid) {
            // wid 實際上是不用配置 因為pid具有唯一性
            $sql = "SELECT * FROM {$this->pages} WHERE `id` = ? AND `wid` = ? AND `status` <> -1;";
            $row = $this->conn->prepare($sql, [$pid, $this->wid]);
            $result = empty($row) ? [] : $row[0];
        }
        return $result;
    }
    /************************************************
     * ### 取得頁面資訊 ###
     * @param string $pagename 頁面名稱
     * 這個func非常重要，他也是輸出網站pid的來源
     ************************************************/
    public function getPageInformation($pagename = false): array
    {
        $result = [];
        if ($pagename) {
            $sql = "SELECT * FROM {$this->pages} WHERE `name` = ? AND `wid` = ? AND `status` <> -1;";
            $row = $this->conn->prepare($sql, [$pagename, $this->wid]);
            $result = empty($row) ? [] : $row[0];
        }
        return $result;
    }
    /************************************************
     * ### 取得頁面資訊 ###
     * @param string $pagename 頁面名稱
     * 這個func非常重要，他也是輸出網站pid的來源
     ************************************************/
    public function getAllPageInformation(): array
    {
        $sql = "SELECT * FROM {$this->pages} WHERE `wid` = ? AND `status` <> -1;";
        $row = $this->conn->prepare($sql, [$this->wid]);
        $result = empty($row) ? [] : $row;
        return $result;
    }
    /************************************************
     * ### 新增頁面 ###
     * @param int $pid 頁面編號
     * @param int $pInformation 頁面資訊
     ************************************************/
    public function addPage($pid, $pInformation): bool
    {
        if (!isset($pInformation['name'])) return false;
        $check = $this->getPageInformation($pInformation['name']);
        if (!empty($check)) return false;
        $params = [
            $pid,
            $this->wid,
            $pInformation['name'],
            isset($pInformation['displayname']) ? $pInformation['displayname'] : "未命名頁面({$this->wid})",
            isset($pInformation['description']) ? $pInformation['description'] : "新頁面",
            isset($pInformation['nav']) ? $pInformation['nav'] : 1,
            isset($pInformation['status']) ? $pInformation['status'] : 1
        ];

        $sql = "INSERT INTO {$this->pages} (`id`, `wid`, `name`, `displayname`, `description`, `nav`, `status`) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $row = $this->conn->prepare($sql, $params);
        return empty($row);
    }
    /************************************************
     * ### 編輯頁面 ###
     * @param int $pid 頁面編號
     ************************************************/
    public function editPage($pid, $pInformation): bool
    {
        $pOriginalInformation = $this->getPageInformationById($pid);
        if (empty($pOriginalInformation) || !isset($pInformation['name'])) return false;
        $check = $this->getPageInformation($pInformation['name']);
        if (!empty($check) && ($pOriginalInformation['name'] != $pInformation['name'])) return false;
        $params = [
            $pInformation['name'],
            isset($pInformation['displayname']) ? $pInformation['displayname'] : $pOriginalInformation['displayname'],
            isset($pInformation['description']) ? $pInformation['description'] : $pOriginalInformation['description'],
            isset($pInformation['nav']) ? $pInformation['nav'] : $pOriginalInformation['nav'],
            // (isset($pInformation['status']) && ($pOriginalInformation['status'] != 2)) ? $pInformation['status'] : $pOriginalInformation['status'],
            $pid,
            $this->wid
        ];
        // wid 實際上是不用配置 因為pid具有唯一性
        $sql = "UPDATE {$this->pages} SET `name` = ?, `displayname` = ?, `description` = ?, `nav` = ? WHERE `id` = ? AND `wid` = ?";
        $row = $this->conn->prepare($sql, $params);
        return empty($row);
    }
    /************************************************
     * ### 變更頁面狀態 ###
     * @param int $pid 頁面編號
     ************************************************/
    public function changePageStatus($pid): bool
    {
        // wid 實際上是不用配置 因為pid具有唯一性，系統頁面不能關閉
        $sql = "UPDATE `{$this->pages}` SET `status` = NOT `status` WHERE `wid` = ? AND `id` = ? AND `status` <> 2;";
        $result = empty($this->conn->prepare($sql, [$this->wid, $pid]));
        return $result;
    }
    /************************************************
     * ### 變更頁面狀態 ###
     * @param int $pid 頁面編號
     ************************************************/
    public function deletePage($pid): bool
    {
        // wid 實際上是不用配置 因為pid具有唯一性
        $sql = "UPDATE `{$this->pages}` SET `status` = -1 WHERE `wid` = ? AND `id` = ? AND `status` <> 2;";
        $result = empty($this->conn->prepare($sql, [$this->wid, $pid]));
        return $result;
    }
    /************************************************
     * ### 取得頁面元件 ###
     * @param string $pid 頁面編號
     ************************************************/
    public function getPageComponent($pid): array
    {
        $sql = "SELECT `components`.`name`, `components`.`template` 
        FROM {$this->pageComponent} AS `page_component`
        JOIN {$this->components} AS `components` ON `components`.`id` = `page_component`.`cid`
        WHERE `pid` = ? AND `status` <> 0 ORDER BY `position` ASC;";
        $row = $this->conn->prepare($sql, [$pid]);
        return $row;
    }
    /************************************************
     * ### 取得頁面元件 ###
     * @param array $idList ID編號
     ************************************************/
    public function getComponentTemplates($idList): array
    {
        if (empty($idList)) return [];
        $placeholders = implode(", ", array_fill(0, count($idList), "?"));
        $sql = "SELECT `id` AS `cid`, `name` AS `php_name`, `template`, `size_x`, `fill_y`, `is_absolute_position`, `description` AS `displayname` 
        FROM `{$this->components}` WHERE `id` IN ({$placeholders});";
        $row = $this->conn->prepare($sql, $idList);
        foreach ($row as $r) {
            $cid = $r['cid'];
            unset($r['cid']);
            $res[$cid] = $r;
        }
        return $res;
    }
    /************************************************
     * ### 取得頁面元件 ###
     * @param string $pid 頁面編號
     ************************************************/
    // public function getComponent($cid) : array
    // {
    //     $sql = "SELECT * FROM {$this->components} WHERE `id` = ? ;";
    //     $row = $this->conn->prepare($sql, [$cid]);
    //     return $row;
    // }
}
