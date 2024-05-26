<?php

/*************************************************************
 * 
 * 公告版
 * @param obj $db 資料庫
 * 
 *************************************************************/
class msgBoard
{
    private $conn;
    private $wid = null;
    private $limit = 1000;
    private $msgboard = "c_msgboard";
    private $msgboard_type = "c_msgboard_type";

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
     * ### 設置單次獲取筆數上限 ###
     * @param int $limit 上限
     ************************************************/
    public function setLimit($limit = 1000)
    {
        if ($limit < 5000) {
            $this->limit = $limit;
            return true;
        }
        return false;
    }
    /************************************************
     * ### 取得貼文數量 ###
     * @param int $postId 貼文編號
     ************************************************/
    public function getAllPostCount(): int
    {
        $sql = "SELECT COUNT(*) FROM `{$this->msgboard}` AS `m`
        LEFT JOIN `{$this->msgboard_type}` AS `mtype` ON `m`.`tid` = `mtype`.`id` 
        WHERE `m`.`wid` = ? AND `m`.`status` <> -1";
        $result = $this->conn->prepare($sql, [$this->wid]);
        return (empty($result)) ? 0 : $result[0]['COUNT(*)'];
    }
    /************************************************
     * ### 取得所有貼文分類 ###
     * @param int $postId 貼文編號
     ************************************************/
    public function getAllPostType(): array
    {
        $sql = "SELECT * FROM {$this->msgboard_type} WHERE `wid` = ? AND `status` <> -1;";
        $result = $this->conn->prepare($sql, [$this->wid]);
        return empty($result) ? [] : $result;
    }
    /************************************************
     * ### 取得所有貼文分類 ###
     * @param int $typeId 貼文編號
     ************************************************/
    public function getPostType($typeId): array
    {
        $sql = "SELECT * FROM {$this->msgboard_type} WHERE `wid` = ? AND `id` = ? AND `status` <> -1;";
        $result = $this->conn->prepare($sql, [$this->wid, $typeId]);
        return empty($result) ? [] : $result[0];
    }
    /************************************************
     * ### 取得所有貼文分類 ###
     * @param int $typeId 貼文編號
     ************************************************/
    public function getPostTypeByTypeName($tName): array
    {
        $sql = "SELECT * FROM {$this->msgboard_type} WHERE `wid` = ? AND `type` = ? AND `status` <> -1;";
        $result = $this->conn->prepare($sql, [$this->wid, $tName]);
        return empty($result) ? [] : $result[0];
    }
    /************************************************
     * ### 新增貼文分類 ###
     * @param int $typeId 貼文編號
     ************************************************/
    public function addPostType($typeId, $typeName): bool
    {
        $checkExist = $this->getPostTypeByTypeName($typeName);
        if ($checkExist) return false;
        $sql = "INSERT INTO {$this->msgboard_type} (`wid`, `id`, `type`) VALUES (?, ?, ?);";
        return empty($this->conn->prepare($sql, [$this->wid, $typeId, htmlspecialchars($typeName)]));
    }
    /************************************************
     * ### 變更貼文分類 ###
     * @param int $typeId 貼文編號
     ************************************************/
    public function updatePostType($typeId, $typeName): bool
    {
        $checkExist = $this->getPostTypeByTypeName($typeName);
        if ($checkExist && $checkExist['id'] != $typeId) return false;
        $sql = "UPDATE {$this->msgboard_type} SET `type` = ? WHERE `wid` = ? AND `id` = ? AND `status` <> -1;";
        return empty($this->conn->prepare($sql, [htmlspecialchars(trim($typeName)), $this->wid, $typeId]));
    }
    /************************************************
     * ### 變更貼文分類狀態 ###
     * @param int $productId 商品id
     ************************************************/
    // public function changePostTypeStatus($typeId): bool
    // {
    //     $sql = "UPDATE `{$this->msgboard_type}` SET `status` = NOT `status` WHERE `wid` = ? AND `id` = ?;";
    //     $result = empty($this->conn->prepare($sql, [$this->wid, $typeId]));
    //     return $result;
    // }
    /************************************************
     * ### 變更貼文分類狀態 ###
     * @param int $productId 商品id
     ************************************************/
    public function deletePostType($typeId): bool
    {
        $sql = "UPDATE `{$this->msgboard_type}` SET `status` = -1 WHERE `wid` = ? AND `id` = ?;";
        $result = empty($this->conn->prepare($sql, [$this->wid, $typeId]));
        return $result;
    }
    /************************************************
     * ### 取得貼文 ###
     * @param int $postId 貼文編號
     ************************************************/
    public function getpost($postId = null, $offset = 0, $limit = 0, $orderby = 'weight desc'): array
    {
        $field = '';
        $params[] = $this->wid;
        if (!is_null($postId)) {
            $field = "AND `m`.`id` = ?";
            $params[] = $postId;
        }
        if ($limit < 1 || $limit > $this->limit) $limit = $this->limit;
        $sql = "SELECT `m`.*,`mtype`.`type` FROM `{$this->msgboard}` AS `m`
        LEFT JOIN `{$this->msgboard_type}` AS `mtype` ON `m`.`tid` = `mtype`.`id` AND `mtype`.`status` = 1
        WHERE `m`.`wid` = ? {$field} AND `m`.`status` <> -1 
        ORDER BY `m`.`status` DESC, `m`.{$orderby}, `m`.`id` DESC, 
        `m`.`updated_at` ASC LIMIT {$limit} OFFSET {$offset};";
        $result = $this->conn->prepare($sql, $params);
        if (empty($result)) return [];
        return is_null($postId) ? $result : $result[0];
    }
    /************************************************
     * ### 張貼貼文 ###
     * @param int $postId 貼文編號
     ************************************************/
    public function addpost($postId, $pInformation = []): bool
    {
        $checkExist = $this->getpost($postId);
        if (!empty($checkExist)) return false;
        if (isset($pInformation['tid'])) {
            if (!preg_match('/^([0-9]{18,20})$/', $pInformation['tid'])) return false;
        }
        if (!isset($pInformation['mid'])) return false;
        $params = [
            $postId,
            $this->wid,
            empty($pInformation['mid']) ? null : intval(trim($pInformation['mid'])),
            empty($pInformation['tid']) ? null : intval(trim($pInformation['tid'])),
            empty($pInformation['author']) ? "Anonymous" : htmlspecialchars(trim($pInformation['author'])),
            empty($pInformation['title']) ? "untitled" : htmlspecialchars(trim($pInformation['title'])),
            empty($pInformation['message']) ? "" : htmlspecialchars($pInformation['message']),
            empty($pInformation['weight']) ? 0 : $pInformation['weight'],
            empty($pInformation['status']) ? 1 : $pInformation['status'],
        ];
        $sql = "INSERT INTO {$this->msgboard} (`id`, `wid`, `mid`, `tid`, `author`, `title`, `message`, `weight`, `status`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?);";
        return empty($this->conn->prepare($sql, $params));
    }
    /************************************************
     * ### 張貼貼文 ###
     * @param int $postId 貼文編號
     ************************************************/
    public function updataPost($postId, $pInformation = []): bool
    {
        $pOriginalInformation = $this->getpost($postId);
        if (empty($pOriginalInformation)) return false;
        if (isset($pInformation['tid'])) {
            if (!preg_match('/^([0-9]{18,20})$/', $pInformation['tid'])) return false;
        }
        if (!isset($pInformation['mid'])) return false;
        $params = [
            empty($pInformation['tid']) ? $pOriginalInformation['tid'] : intval(trim($pInformation['tid'])),
            empty($pInformation['author']) ? $pOriginalInformation['author'] : htmlspecialchars(trim($pInformation['author'])),
            empty($pInformation['title']) ? $pOriginalInformation['title'] : htmlspecialchars(trim($pInformation['title'])),
            empty($pInformation['message']) ? $pOriginalInformation['message'] : htmlspecialchars($pInformation['message']),
            empty($pInformation['weight']) ? $pOriginalInformation['weight'] : $pInformation['weight'],
            intval(trim($pInformation['mid'])),
            $postId,
            $this->wid,
        ];
        $sql = "UPDATE {$this->msgboard} SET `tid` = ?, `author` = ?, `title` = ?, `message` = ?, `weight` = ?, `mid` = ? WHERE `id` = ? AND `wid` = ?;";
        return empty($this->conn->prepare($sql, $params));
    }
    /************************************************
     * ### 變更公告狀態 ###
     * @param int $productId 商品id
     ************************************************/
    public function changePostStatus($postId): bool
    {
        $sql = "UPDATE `{$this->msgboard}` SET `status` = NOT `status` WHERE `wid` = ? AND `id` = ?;";
        $result = empty($this->conn->prepare($sql, [$this->wid, $postId]));
        return $result;
    }
    /************************************************
     * ### 變更公告狀態 ###
     * @param int $productId 商品id
     ************************************************/
    public function deletePost($postId): bool
    {
        $sql = "UPDATE `{$this->msgboard}` SET `status` = -1 WHERE `wid` = ? AND `id` = ?;";
        $result = empty($this->conn->prepare($sql, [$this->wid, $postId]));
        return $result;
    }
}
// CREATE TABLE `c_msgboard` (
// 	`id` BIGINT(19) UNSIGNED NOT NULL COMMENT 'ID',
// 	`wid` BIGINT(19) UNSIGNED NOT NULL,
// 	`tid` BIGINT(19) UNSIGNED NULL DEFAULT NULL,
// 	`author` VARCHAR(64) NOT NULL DEFAULT '匿名',
// 	`title` VARCHAR(64) NOT NULL DEFAULT '未命名標題',
// 	`status` INT(11) NOT NULL DEFAULT '1',
// 	`message` TEXT NULL DEFAULT NULL,
// 	`created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '創建時間',
// 	`updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新時間',
// 	PRIMARY KEY (`id`),
// 	INDEX `wid` (`wid`),
// 	INDEX `tid` (`tid`),
// 	CONSTRAINT `FK_c_msgboard_s_website` FOREIGN KEY (`wid`) REFERENCES `s_website` (`id`) ON UPDATE CASCADE ON DELETE CASCADE
// )
// COLLATE='utf8_general_ci'
// ENGINE=InnoDB
// ;
