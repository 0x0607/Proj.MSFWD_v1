<?php

/****************************************************************************************
 * 
 * 網站系統
 * @param obj $db 資料庫
 * 
 ****************************************************************************************/
class websiteManage
{
    protected $conn;
    private $website = "s_website";
    private $newebpay = "s_newebpay";
    
    public function __construct($db)
    {
        $this->conn = $db;
    }
    /************************************************
     * ### 透過domainName取得網站資訊 ###
     * @param int|string $value 網站的編號 或 網域名稱
     ************************************************/
    public function getAllWebsiteInformation(): array
    {
        $sql = "SELECT `website`.`id`,`website`.`prefix`,
        `website`.`displayname`,`website`.`distribution`,`website`.`icon`,
        `website`.`theme_version`,`website`.`status`,`website`.`created_at`,`website`.`updated_at`, 
        `newebpay`.`store_id`,`newebpay`.`store_hash_key`,
        `newebpay`.`store_hash_iv`,`newebpay`.`store_return_url`,`newebpay`.`store_client_back_url`
        FROM `{$this->website}` AS `website`
        JOIN `{$this->newebpay}` AS `newebpay` ON `newebpay`.`wid` = `website`.`id`;";
        $result = $this->conn->each($sql);
        if (empty($result)) return [];
        return $result;
    }
    /************************************************
     * ### 透過domainName取得網站資訊 ###
     * @param int|string $value 網站的編號 或 網域名稱
     ************************************************/
    public function getWebsiteInformationByDomain($domainName): array
    {
        $sql = "SELECT `website`.`id`,`website`.`prefix`,
        `website`.`displayname`,`website`.`distribution`,`website`.`icon`,
        `website`.`theme_version`,`website`.`status`,`website`.`created_at`,`website`.`updated_at`, 
        `newebpay`.`store_id`,`newebpay`.`store_hash_key`,
        `newebpay`.`store_hash_iv`,`newebpay`.`store_return_url`,`newebpay`.`store_client_back_url`
        FROM `{$this->website}` AS `website`
        JOIN `{$this->newebpay}` AS `newebpay` ON `newebpay`.`wid` = `website`.`id`
        WHERE `domain` = ? LIMIT 1;";
        $result = $this->conn->prepare($sql, [$domainName]);
        if (empty($result)) return [];
        return $result[0];
    }
    /************************************************
     * ### 取得網站資訊 ###
     * @param int|string $value 網站的編號 或 網域名稱
     ************************************************/
    public function getWebsiteInformation($value): array
    {
        $sql = "SELECT `website`.`domain`,`website`.`prefix`,
        `website`.`displayname`,`website`.`distribution`,`website`.`icon`,
        `website`.`theme_version`,`website`.`status`,`website`.`created_at`,`website`.`updated_at`, 
        `newebpay`.`store_id`,`newebpay`.`store_hash_key`,
        `newebpay`.`store_hash_iv`,`newebpay`.`store_return_url`,`newebpay`.`store_client_back_url`
        FROM `{$this->website}` AS `website`
        JOIN `{$this->newebpay}` AS `newebpay` ON `newebpay`.`wid` = `website`.`id`
        WHERE `id` = ? LIMIT 1;";

        $result = $this->conn->prepare($sql, [$value]);
        if (empty($result)) return [];
        return $result[0];
    }
    /************************************************
     * ### 初始化網站 ###
     * @param int $wid 網站的編號
     * @param string $domainName 網域名稱
     ************************************************/
    public function initWebsite($wid, $domainName): bool
    {
        $result = true;
        $domainName = htmlspecialchars($domainName);
        $res = $this->getWebsiteInformationByDomain($domainName);
        // 如果沒有建立過的網域才能建立新網站
        if (empty($res)) {
            $wParams = [
                $wid,
                $domainName,
                'Demo',
                'DemoWebsite',
                'Taiwan',
                '/assets/images/logo.png',
            ];
            $nParams = [
                $wid,
                '0',
                '0',
                '0',
                '0',
                "https://{$domainName}/projects/steam",
                "https://{$domainName}/projects/steam"
            ];
            // --------------------------------------------------------------------------------
            $sql = "INSERT INTO `{$this->website}` (`id`, `domain`, `name`, `displayname`, `distribution`, `icon`) 
            VALUES (?, ?, ?, ?, ?, ?);";
            $result &= empty($this->conn->prepare($sql, $wParams));
            // --------------------------------------------------------------------------------
            $sql = "INSERT INTO `{$this->newebpay}` VALUES(?, ?, ?, ?, ?, ?, ?);";
            $result &= empty($this->conn->prepare($sql, $nParams));
            return $result;
        } else return false;
    }
    /************************************************
     * ### 更新網站資訊 ###
     * @param int $wid 網站編號
     * @param array $wInformation 網站資訊    
     * @param bool $autoCreateWebsite 如果不存在是否建立  
     * ```
     * ["website" => [  
     * "domain"=> String  
     * "name"=> String  
     * "distribution"=> String  
     * "icon"=> String  
     * "background"=> String  
     * "stylesheet"=> String  
     * "theme"=> String
     * ],  
     * ["newebpay" =>[  
     * "store_prefix"=> String  
     * "store_id"=> String  
     * "store_hash_key"=> String  
     * "store_hash_iv"=> String  
     * "store_return_url"=> String  
     * "store_client_back_url"=> String  
     * "store_notify_url"=> String  
     * ]]
     * ```
     ************************************************/
    public function updateWebsiteInformation($wid, $wInformation = ["website" => [], "newebpay" => []]): bool
    {
        $result = true;
        $wOriginalInformation = $this->getWebsiteInformation($wid);
        if (empty($wOriginalInformation)) return false;
        // if (!preg_match('/^[a-zA-Z0-9_\-@$.\s]+$/', $wInformation)) return false;
        $website = isset($wInformation["website"]) ? $wInformation["website"] : [];
        $newebpay = isset($wInformation["newebpay"]) ? $wInformation["newebpay"] : [];
        // 網站簡碼只能使用英文數字及_、-
        // if (!preg_match('/^[a-zA-Z0-9_\-]+$/', $website['name'])) return false;
        if (!empty($website)) {
            $params = [
                empty($website['domain']) ? $wOriginalInformation['domain'] : $website['domain'],
                empty($website['prefix']) ? $wOriginalInformation['prefix'] : $website['prefix'],
                empty($website['displayname']) ? $wOriginalInformation['displayname'] : htmlspecialchars($website['displayname']),
                empty($website['distribution']) ? $wOriginalInformation['distribution'] : htmlspecialchars($website['distribution']),
                empty($website['icon']) ? $wOriginalInformation['icon'] : $website['icon'],
                // empty($website['theme']) ? $wOriginalInformation['theme'] : $website['theme'],
                $wid
            ];
            $themeVersion = isset($website['theme']) ? ", `theme_version` = `theme_version` + 1" : "";
            $sql = "UPDATE `{$this->website}` SET `domain` = ?, `prefix` =?, 
                `displayname` = ?, `distribution` = ?, `icon` = ? {$themeVersion}
                WHERE `id` = ? ;";
            $result &= empty($this->conn->prepare($sql, $params));
        }
        if (!empty($newebpay)) {
            $params = [
                // empty($newebpay['store_prefix']) ? $wOriginalInformation['store_prefix'] : $newebpay['store_prefix'],
                empty($newebpay['store_id']) ? $wOriginalInformation['store_id'] : $newebpay['store_id'],
                empty($newebpay['store_hash_key']) ? $wOriginalInformation['store_hash_key'] : $newebpay['store_hash_key'],
                empty($newebpay['store_hash_iv']) ? $wOriginalInformation['store_hash_iv'] : $newebpay['store_hash_iv'],
                empty($newebpay['store_return_url']) ? $wOriginalInformation['store_return_url'] : $newebpay['store_return_url'],
                empty($newebpay['store_client_back_url']) ? $wOriginalInformation['store_client_back_url'] : $newebpay['store_client_back_url'],
                $wid
            ];
            $sql = "UPDATE `{$this->newebpay}` SET `store_id` =?, `store_hash_key` = ?, `store_hash_iv` = ?, 
                `store_return_url` = ?, `store_client_back_url` = ? WHERE `wid` = ? ;";
            $result &= empty($this->conn->prepare($sql, $params));
        }
        return $result;
    }
    /************************************************
     * ### 更新網站狀態 ###
     * @param int $wid 網站編號
     ************************************************/
    public function changeWebsiteStatus($wid): bool
    {
        $sql = "UPDATE `{$this->website}` SET `status` = NOT `status` WHERE `id` = ? ;";
        $result = empty($this->conn->prepare($sql, [$wid]));
        return $result;
    }
}
