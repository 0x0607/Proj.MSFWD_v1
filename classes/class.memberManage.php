<?php
// include "./class.connectDatabase.php";
// preg_match('/^\d{18,20}$/', $mid)
/****************************************************************************************
 * 
 * 成員系統
 * @param obj $db 資料庫
 * 
 ****************************************************************************************/
class memberManage
{
    private $conn;
    private $wid = null;
    private $limit = 1000;
    private $member = "m_members";
    private $members_profile = "m_members_profile";
    private $members_attendance = "m_attendance";
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
     * ### 確認使用者輸入之帳戶與密碼是否正確 ###
     * @param string $account 會員帳號 
     * @param string $password 會員密碼
     ************************************************/
    public function memberLogin($account, $password, $isHashPassword = true): int
    {
        $account = htmlspecialchars(strtolower($account));
        if (!$isHashPassword) $password = hash('sha256', $password);
        $sql = "SELECT `id` FROM `{$this->member}` WHERE `account` = ? AND `password` = ? LIMIT 1;";
        $result = $this->conn->prepare($sql, [$account, $password]);
        return empty($result) ? 0 : $result[0]['id'];
    }
    /************************************************
     * ### 取得會員資訊 ###
     * @param int $mid 會員的編號
     ************************************************/
    public function getMemberInformation($mid): array
    {
        // (is_int($value) && $value < 1e17)
        // if (is_string($mid)) return $this->getMemberInformationByAccount($mid);
        $mid = intval($mid);
        $sql = "SELECT * FROM `{$this->member}` AS `member`
        JOIN `{$this->members_profile}` AS `mProfile` ON `mProfile`.`mid` = `member`.`id`
        WHERE `member`.`id` = ? LIMIT 1;";
        $result = $this->conn->prepare($sql, [$mid]);
        if (empty($result)) return [];
        return $result[0];
    }
    /************************************************
     * ### 取得會員資訊透過帳號 ###
     * @param string $account 會員的帳號
     ************************************************/
    public function getMemberInformationByAccount($account): array
    {
        $account = htmlspecialchars(strtolower($account));

        $sql = "SELECT * FROM `{$this->member}` AS `member`
        JOIN `{$this->members_profile}` AS `mProfile` ON `mProfile`.`mid` = `member`.`id`
        WHERE `member`.`account` = ? LIMIT 1;";
        $result = $this->conn->prepare($sql, [$account]);
        if (empty($result)) return [];
        return $result[0];
    }
    /************************************************
     * ### 新增會員資訊 ###
     * @param int $id 會員的編號
     * @param array $mInformation 會員資訊  
     * ```
     * ["account" => [  
     * "id"=> Int  
     * "account"=> String  
     * "nickname"=> String  
     * "password"=> String  
     * "last_ip_address"=> String  
     * ],  
     * "profile" => [  
     * "introduction"=> String  
     * "theme"=> String  
     * "avatar"=> String  
     * "background"=> String  
     * ]]  
     * ```
     ************************************************/
    public function addMember($mid, $mAccount = [], $isHashPassword = true): bool
    {
        $checkExist = $this->getMemberInformation($mid);
        if (!empty($checkExist)) return false;
        if (!$isHashPassword && isset($mAccount['password'])) $mAccount['password'] = hash('sha256', $mAccount['password']);
        $result = true;
        $params = [
            $mid,
            htmlspecialchars(strtolower(trim($mAccount['account']))),
            isset($mAccount['password']) ? $mAccount['password'] : NULL,
            htmlspecialchars($mAccount['nickname']),
            isset($mAccount['last_ip_address']) ? $mAccount['last_ip_address'] : "0.0.0.0"
        ];
        // --------------------------------------------------------------------------------
        // 建立會員帳戶
        $sql = "INSERT INTO `{$this->member}` (`id`, `account`, `password`, `nickname`, `last_ip_address`) VALUES (?, ?, ?, ?, ?);";
        $result &= empty($this->conn->prepare($sql, $params));
        // 建立用戶個人化
        $sql = "INSERT INTO `{$this->members_profile}` (`mid`) VALUES (?);";
        $result &= empty($this->conn->prepare($sql, [$mid]));
        return $result;
    }
    /************************************************
     * ### 更新會員資訊 ###
     * @param int $id 會員的編號
     * @param array $mInformation 會員資訊
     * @param bool $autoCreateAccount 是否自動建立帳戶  
     * ```
     * ["account" => [  
     * "id"=> Int  
     * "account"=> String  
     * "nickname"=> String  
     * "password"=> String  
     * "last_ip_address"=> String  
     * "status"=> 0停用 1啟用 2系統用戶  
     * ],  
     * "profile" => [  
     * "introduction"=> String  
     * "theme"=> String  
     * "avatar"=> String  
     * "background"=> String  
     * ]]  
     * ```
     ************************************************/
    public function updateMemberInformation($mid, $mInformation = ["account" => [], "profile" => []]): bool
    {
        $result = true;
        $mAccount = isset($mInformation["account"]) ? $mInformation["account"] : [];
        $mProfile = isset($mInformation["profile"]) ? $mInformation["profile"] : [];
        // 檢查會員原始資訊
        $mOriginalInformation = $this->getMemberInformation($mid);
        if (empty($mOriginalInformation)) return false;
        // 會員存在，更新會員資訊
        else {
            // 更新會員帳戶資訊
            if (!empty($mAccount)) {
                // 如果沒填的欄位將會以原始資訊填入
                $params = [
                    empty($mAccount['account']) ? $mOriginalInformation['account'] : htmlspecialchars(strtolower(trim($mAccount['account']))),
                    empty($mAccount['password']) ? $mOriginalInformation['password'] : $mAccount['password'],
                    empty($mAccount['nickname']) ?  $mOriginalInformation['nickname'] : htmlspecialchars($mAccount['nickname']),
                    empty($mAccount['last_ip_address']) ? $mOriginalInformation['last_ip_address'] : $mAccount['last_ip_address'],
                    empty($mAccount['status']) ? $mOriginalInformation['status'] : $mAccount['status'],
                    $mid
                ];
                $sql = "UPDATE `{$this->member}` SET `account` = ?, `password` = ?, `nickname` = ? , `last_ip_address` = ?, `status` = ? WHERE `id` = ?";
                $result &= empty($this->conn->prepare($sql, $params));
            }
            // 更新會員個人化
            if (!empty($mProfile)) {
                // 如果沒填的欄位將會以原始資訊填入
                $params = [
                    htmlspecialchars(empty($mProfile['introduction']) ? $mOriginalInformation['introduction'] : $mProfile['introduction']),
                    empty($mProfile['avatar']) ? $mOriginalInformation['avatar'] : $mProfile['avatar'],
                    empty($mProfile['theme']) ? $mOriginalInformation['theme'] : $mProfile['theme'],
                    empty($mProfile['background']) ? $mOriginalInformation['background']  : $mProfile['background'],
                    $mid
                ];
                $sql = "UPDATE `{$this->members_profile}` SET `introduction` = ?, `avatar` = ? , `theme` = ?, `background`= ? WHERE `mid` = ?";
                $result &= empty($this->conn->prepare($sql, $params));
            }
        }
        return $result;
    }
    /************************************************
     * ### 確認簽到狀況 ###
     * @param int $mid 會員編號
     ************************************************/
    public function getAttendance($mid): array
    {
        $sql = "SELECT `day`,`nonstop_day`,`last_sign_at` FROM {$this->members_attendance} WHERE `wid` = ? AND `mid` = ? ;";
        $result = $this->conn->prepare($sql, [$this->wid, $mid]);
        return empty($result) ? [] : $result[0];
    }
    /************************************************
     * ### 簽到功能 ###
     * @param int $mid 會員編號
     ************************************************/
    public function updateAttendance($mid): array
    {
        $result = false;
        $attendanceResult = $this->getAttendance($mid);
        if ($attendanceResult) {
            if (date('Y-m-d', strtotime($attendanceResult['last_sign_at'])) != date('Y-m-d')) {
                $attendanceResult['day'] += 1;
                $attendanceResult['nonstop_day'] = (date('Y-m-d', strtotime($attendanceResult['last_sign_at'])) == date('Y-m-d', strtotime('-1 day'))) ? ($attendanceResult['nonstop_day'] + 1) : 1;
                $attendanceResult['last_sign_at'] = date('Y-m-d H:i:s');
                $sql = "UPDATE {$this->members_attendance} SET `day` = ?, `nonstop_day` = ?, `last_sign_at` = ? WHERE `wid` = ? AND `mid` = ?";
                $result = empty($this->conn->prepare($sql, [$attendanceResult['day'], $attendanceResult['nonstop_day'], $attendanceResult['last_sign_at'], $this->wid, $mid]));
            }
        } else {
            $attendanceResult = [
                "day" => 1,
                "nonstop_day" => 1,
                "last_sign_at" => date('Y-m-d H:i:s')
            ];
            $sql = "INSERT INTO {$this->members_attendance} (`wid`, `mid`, `day`, `nonstop_day`, `last_sign_at`) VALUES (?, ?, ?, ?, ?)";
            $result = empty($this->conn->prepare($sql, [$this->wid, $mid, $attendanceResult['day'], $attendanceResult['nonstop_day'], $attendanceResult['last_sign_at']]));
        }
        if ($result) return $attendanceResult;
        return [];
    }
}
