<?php
// include "./class.connectDatabase.php";
// preg_match('/^\d{18,20}$/', $mid)
/****************************************************************************************
 * 
 * Steam串接
 * @param obj $db 資料庫
 * @param string $steamApiKey Steam API Key
 * 
 ****************************************************************************************/
class steamauthManage
{
    private $conn;
    private $steam = "api_steamauth";
    private $steamApiKey = "";
    public function __construct($db, $steamApiKey)
    {
        $this->conn = $db;
        $this->settingSteamApiKey($steamApiKey);
    }
    /************************************************
     * ### 設置Steam API Key ###
     * @param string $steamApiKey Steam API Key
     ************************************************/
    public function settingSteamApiKey($steamApiKey)
    {
        $this->steamApiKey = $steamApiKey;
    }
    /************************************************
     * ### 取得會員Steam資訊 ###
     * @param int $value 會員Steam編號 或 會員編號
     ************************************************/
    public function getMemberSteamauth($value): array
    {
        $field = 'steam_id';
        if ($value > 1e17) $field = 'mid';
        $sql = "SELECT * FROM `{$this->steam}` WHERE `$field` = ? LIMIT 1;";
        $result = $this->conn->prepare($sql, [$value]);
        if (empty($result)) return [];
        return $result[0];
    }
    /************************************************
     * ### 新增會員Steam資訊 ###
     * @param int $mid 會員編號
     * @param int $steamId 會員的Steam編號
     ************************************************/
    public function addMemberSteamauth($mid, $steamId): bool
    {
        $sql = "INSERT INTO `{$this->steam}` (`steam_id`, `mid`) VALUES (?, ?);";
        $result = $this->conn->prepare($sql, [$steamId, $mid]);
        return empty($result);
    }
    /************************************************
     * ### 更新會員Steam資訊 ###
     * @param int $steamId 會員的Steam編號
     * @param array $steamInformation 會員Steam資訊
     ************************************************/
    public function updateMemberSteamauth($mid, $steamId): bool
    {
        $sql = "UPDATE `{$this->steam}` SET `mid` = ? WHERE `steam_id` = ?;";
        $result = $this->conn->prepare($sql, [$mid, $steamId]);
        return empty($result);
    }
    /************************************************
     * ### 透過steamauthOpenId拆分出SteamId ###
     * @param string $sIdentity 透過steamauthOpenId取得之資訊
     ************************************************/
    public function getSteamIdentity($sIdentity)
    {
        preg_match("/^https?:\/\/steamcommunity\.com\/openid\/id\/(7[0-9]{15,25}+)$/", $sIdentity, $matches);
        $steamId = $matches[1];
        return empty($matches[1]) ? 0 : $steamId;
    }
    /************************************************
     * ### 透過steampowered取得Steam帳戶資訊 ###
     * @param string $steamIds SteamIds
     ************************************************/
    public function getISteamUser($steamIds): array
    {
        $url = file_get_contents("https://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key={$this->steamApiKey}&steamids={$steamIds}");
        $iSteamUser = json_decode($url, true);
        if (count($iSteamUser['response']['players']) == 1) $result = $iSteamUser['response']['players'][0];
        else $result = $iSteamUser['response']['players'];
        return $result;
    }
    /************************************************
     * ### 透過steampowered取得伺服器資訊 ###
     * @param string $filter 過濾器格式 EX : `addr\127.0.0.1`
     ************************************************/
    public function getIGameServersService($filter): array
    {
        $url = file_get_contents("https://api.steampowered.com/IGameServersService/GetServerList/v1/?key={$this->steamApiKey}&filter={$filter}");
        $iGameServersService = json_decode($url, true);
        if (count($iGameServersService['response']['servers']) == 1) $result = $iGameServersService['response']['servers'][0];
        else $result = $iGameServersService['response']['servers'];
        return $result;
    }
}
