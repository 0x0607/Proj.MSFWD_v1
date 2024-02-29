<?php
// include "./class.connectDatabase.php";
// preg_match('/^\d{18,20}$/', $mid)
/****************************************************************************************
 * 
 * Unturned串接
 * @param obj $db 資料庫
 * 
 ****************************************************************************************/
class unturnedManage
{
    private $conn;
    private $permission_groups = "p_groups";
    private $permission_members = "p_members";
    // private $permission_permissions = "p_permissions";
    private $battlepass = "b_battlepass";
    private $uconomy_itemshop = "i_itemshop";
    private $uconomy_vehicleshop = "i_vehicleshop";
    private $uconomy_payments = "log_payments";
    private $uconomy_uconomy = "m_uconomy";
    private $moderation_expiredplayerbans = "moderation_expiredplayerbans";
    private $moderation_ipinfo = "moderation_ipinfo";
    private $moderation_playerbans = "moderation_playerbans";
    private $moderation_playerhwids = "moderation_playerhwids";
    private $moderation_playerinfo = "moderation_playerinfo";
    private $moderation_playerwarns = "moderation_playerwarns";
    private $ranks = "m_ranks";
    public function __construct($db)
    {
        $this->conn = $db;
    }
    /************************************************
     * ### 設置資料表 ###
     * @param int $tables 資料表名稱
     ************************************************/
    public function __settings($tables = [])
    {
        if ($tables['permission_groups']) $this->permission_groups = $tables['permission_groups'];
        if ($tables['permission_members']) $this->permission_members = $tables['permission_members'];
        // if($tables['permission_permissions']) $this->permission_permissions = $tables['permission_permissions'];
        if ($tables['battlepass']) $this->battlepass = $tables['battlepass'];
        if ($tables['uconomy_itemshop']) $this->uconomy_itemshop = $tables['uconomy_itemshop'];
        if ($tables['uconomy_vehicleshop']) $this->uconomy_vehicleshop = $tables['uconomy_vehicleshop'];
        if ($tables['uconomy_payments']) $this->uconomy_payments = $tables['uconomy_payments'];
        if ($tables['uconomy_uconomy']) $this->uconomy_uconomy = $tables['uconomy_uconomy'];
        if ($tables['moderation_expiredplayerbans']) $this->moderation_expiredplayerbans = $tables['moderation_expiredplayerbans'];
        if ($tables['moderation_ipinfo']) $this->moderation_ipinfo = $tables['moderation_ipinfo'];
        if ($tables['moderation_playerbans']) $this->moderation_playerbans = $tables['moderation_playerbans'];
        if ($tables['moderation_playerhwids']) $this->moderation_playerhwids = $tables['moderation_playerhwids'];
        if ($tables['moderation_playerinfo']) $this->moderation_playerinfo = $tables['moderation_playerinfo'];
        if ($tables['moderation_playerwarns']) $this->moderation_playerwarns = $tables['moderation_playerwarns'];
        if ($tables['ranks']) $this->ranks = $tables['ranks'];
    }
    /************************************************
     * ### 取得會員Unturned資訊 ###
     * @param int $steamId 會員的Steam編號
     ************************************************/
    public function getUserInformation($steamId): array
    {
        $sql = "SELECT * FROM `{$this->moderation_playerinfo}` AS `playerinfo`
        JOIN `{$this->moderation_ipinfo}` AS `ipinfo` ON `ipinfo`.`IP` = `playerinfo`.`IP`
        JOIN `{$this->uconomy_uconomy}` AS `uconomy` ON `uconomy`.`SteamID` = `playerinfo`.`PlayerID`
        LEFT JOIN `{$this->ranks}` AS `ranks` ON `ranks`.`steamId` = `playerinfo`.`PlayerID`
        WHERE `playerinfo`.`PlayerID` = ? ;";
        $result = $this->conn->prepare($sql, [$steamId]);
        if (empty($result)) return [];
        return $result[0];
    }
    /************************************************
     * ### 取得會員Unturned資訊 ###
     * @param int $steamId 會員的Steam編號
     ************************************************/
    public function getUserGroups($steamId): array
    {
        $sql = "SELECT * FROM `{$this->permission_groups}` AS `group`
        JOIN `{$this->permission_members}` AS `member` ON `member`.`GroupID` = `group`.`ID`
        WHERE `member`.`CSteamID` = ? 
        ORDER BY `group`.`Priority` ASC;";
        $result = $this->conn->prepare($sql, [$steamId]);
        if (empty($result)) return [];
        return $result;
    }
    /************************************************
     * ### 取得Unturned商品資訊 ###
     * @param string $shopType 商店類型
     ************************************************/
    public function getShop($shopType): array
    {
        switch ($shopType) {
            case "item":
                $sql = "SELECT * FROM `{$this->uconomy_itemshop}` ORDER BY `Id` ASC;";
                break;
            case "vehicle":
                $sql = "SELECT * FROM `{$this->uconomy_vehicleshop}` ORDER BY `Id` ASC;";
                break;
            default:
                break;
        }
        $result = $this->conn->each($sql);
        if (empty($result)) return [];
        return $result;
    }
    /************************************************
     * ### 發送Unturned商品 ### 
     * @param int $steamId SteamId
     ************************************************/
    public function delivery($steamId, $productInformation = ["GroupID" => "default", "Balance" => 0])
    {
        $result = true;

        if (isset($productInformation['Balance'])) {
            // 發送金錢
            $sql = "UPDATE `{$this->uconomy_uconomy}` SET `Balance` = `Balance` + ? WHERE `SteamID` = ?;";
            $result &= empty($this->conn->prepare($sql, [$productInformation['Balance'], $steamId]));
        }
        if (isset($productInformation['GroupID'])) {
            $timezoneOffset = 28800;
            $expireMoment = $productInformation['ExpireSecond'] * 10000000;
            // 沒設置到期時間則設為無限
            if (!isset($productInformation['ExpireSecond'])) $productInformation['ExpireSecond'] = -1;
            // 發送身分組
            $userGroup = $this->getUserGroups($steamId);

            // 如果用戶在資料表中有身份組
            if (!empty($userGroup)) {
                $roles = array_column($userGroup, 'GroupID');
                // 如果用戶具有身分組 $productInformation['GroupID']
                if (in_array($productInformation['GroupID'], $roles)) {
                    // 將用戶時間改為無限
                    if ($productInformation['ExpireSecond'] == -1) {
                        $sql = "UPDATE `{$this->permission_members}` SET `ExpireMoment` = -1 WHERE `CSteamID` = ? AND `GroupID` = ?;";
                        $result &= empty($this->conn->prepare($sql, [$steamId, $productInformation['GroupID']]));
                    }
                    // 將用戶時間加上新的到期日
                    else {
                        $sql = "UPDATE `{$this->permission_members}` SET `ExpireMoment` = `ExpireMoment` + ? WHERE `CSteamID` = ? AND `GroupID` = ?;";
                        $result &= empty($this->conn->prepare($sql, [$expireMoment, $steamId, $productInformation['GroupID']]));
                    }
                    return $result;
                }
            }
            // 新增用戶身分祖
            $sql = "INSERT INTO `{$this->permission_members}` (`CSteamID`, `GroupID`, `ExpireMoment`) VALUES (?, ?, ?);";
            $result &= empty($this->conn->prepare($sql, [$steamId, $productInformation['GroupID'], $expireMoment + (time() +$timezoneOffset + 62135596800) * 10000000]));
        }
        return $result;
    }
}
