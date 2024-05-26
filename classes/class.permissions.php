<?php
// include "./class.connectDatabase.php";
// preg_match('/^\d{18,20}$/', $mid)
/****************************************************************************************
 * 
 * 權限系統
 * @param obj $db 資料庫
 * 
 ****************************************************************************************/
class permissions
{
    private $conn;
    private $wid = null;
    private $member = "m_members";
    private $members_profile = "m_members_profile";
    private $member_roles = "m_member_roles";
    private $roles = "m_roles";
    private $permissions = "m_permissions";
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
     * ### 設置權限表 ###
     * @param string $member_roles
     * @param string $role_permissions
     * @param string $roles
     * @param string $permissions
     ************************************************/
    public function setTables($member_roles, $roles, $permissions): bool
    {
        $this->member_roles = $member_roles;
        // $this->role_permissions = $role_permissions;
        $this->roles = $roles;
        $this->permissions = $permissions;
        return true;
    }
    /************************************************
     * ### 取得權限資訊 ###
     * @param int $permissionId 權限Id
     ************************************************/
    public function getPermissionsList($permissionId = null): array
    {
        if (is_null($permissionId)) {
            $sql = "SELECT * FROM {$this->permissions};";
            $result = $this->conn->each($sql);
        } else {
            $sql = "SELECT * FROM {$this->permissions} WHERE `id` = ?;";
            $res = $this->conn->perpare($sql, $permissionId);
            $result = $res[0];
        }
        return empty($result) ? [] : $result;
    }
    /************************************************
     * ### 確認身份組權限 ###
     * @param int $rid 身份組id
     * @param bool $containsParentPermission 是否包含父身分組權限
     ************************************************/
    private function getRolePermissions($rid, $containsParentPermission = true): int
    {
        $sql = "SELECT `permissions`,`parent_id` FROM {$this->roles} WHERE `id` = ? AND `status` > 0;";
        $result = $this->conn->prepare($sql, [$rid]);
        if (empty($result)) return 0;
        else $permissions = $result[0]['permissions'];

        if ($result[0]['parent_id'] != $rid && !is_null($result[0]['parent_id']) && $containsParentPermission) {
            $permissions |= $this->getRolePermissions($result[0]['parent_id']);
        }
        return $permissions;
    }
    /************************************************
     * ### 新增身份組 ###
     * @param int $rid 身分組編號
     ************************************************/
    // public function addRoles($rid): bool
    // {
    //     $sql = "INSERT INTO `{$this->roles}` (`id`, `wid`, `displayname`, `parent_id`, `permissions`) VALUES (?, ?, ?, ?, ?);";
    //     $result = empty($this->conn->prepare($sql, [$rid, $this->wid, "未命名身分祖", $this->wid, 0]));
    //     return $result;
    // }
    public function addRoles($rid, $rInformation = []): bool
    {
        $params = [
            $rid,
            $this->wid,
            empty($rInformation['displayname']) ? "未命名身分祖" : $rInformation['displayname'],
            empty($rInformation['permissions']) ? 0 : $rInformation['permissions'],
            empty($rInformation['parent_id']) ? $this->wid : $rInformation['parent_id']
        ];
        $sql = "INSERT INTO `{$this->roles}` (`id`, `wid`, `displayname`, `permissions`, `parent_id`) VALUES (?, ?, ?, ?, ?);";
        $result = empty($this->conn->prepare($sql, $params));
        return $result;
    }
    /************************************************
     * ### 確認使用者身份組 ###
     * @param int $mid 使用者編號
     ************************************************/
    public function getMemberRoles($mid): array
    {
        $sql = "SELECT `role`.`displayname`, `role`.`id` 
        FROM `{$this->member_roles}` AS `memberRole` 
        JOIN `{$this->roles}` AS `role` ON `role`.`id` = `memberRole`.`rid`
        WHERE `memberRole`.`mid` = ? AND `role`.`wid` = ? AND `role`.`status` > 0 
        ORDER BY `role`.`permissions` DESC;";
        $roles = $this->conn->prepare($sql, [$mid, $this->wid]);
        return $roles;
    }
    /************************************************
     * ### 確認使用者身份組 ###
     * @param int $mid 使用者編號
     ************************************************/
    public function addMemberRole($mid, $rid): bool
    {
        $sql = "SELECT COUNT(*) FROM `{$this->member_roles}` WHERE `mid` = ? AND `rid` = ?;";
        $check = $this->conn->prepare($sql, [$mid, $rid]);
        if (!empty($check)) {
            if($check[0]['COUNT(*)']) return false;
            else{
                $sql = "INSERT `{$this->member_roles}` (`mid`, `rid`) VALUES (?, ?);";
                return empty($this->conn->prepare($sql, [$mid, $rid]));
            }
        } else return false;
    }
    /************************************************
     * ### 確認使用者身份組 ###
     * @param int $mid 使用者編號
     ************************************************/
    public function removeMemberRole($mid, $rid): bool
    {
        $sql = "DELETE FROM `{$this->member_roles}` WHERE `mid` = ? AND `rid` = ? LIMIT 3;";
        return empty($this->conn->prepare($sql, [$mid, $rid]));
    }
    /************************************************
     * ### 確認使用者是否具有管理權限 ###
     * @param int $mid 使用者編號
     ************************************************/
    public function isAdmin($mid): bool
    {
        $result = 0;
        $roles = $this->getMemberRoles($mid);
        foreach ($roles as $role) {
            $result |= $this->getRolePermissions($role['id'], true);
        }
        return $result;
    }
    /************************************************
     * ### 確認使用者是否能存取物件 ###
     * @param int $mid 使用者編號
     * @param int $permission 權限編號
     ************************************************/
    public function checkMemberPermissions($mid, $permission): bool
    {
        $result = false;
        $roles = $this->getMemberRoles($mid);
        foreach ($roles as $role) {
            $permissions = $this->getRolePermissions($role['id'], true);
            // 1 為最高權限
            if (($permission & $permissions) || (0x1 & $permissions)) {
                $result = true;
                break;
            }
        }
        return $result;
    }
    /************************************************
     * ### 取得使用者權限 ###
     * @param int $mid 使用者編號
     ************************************************/
    public function getMemberPermissions($mid): int
    {
        $result = 0;
        $roles = $this->getMemberRoles($mid);
        foreach ($roles as $role) {
            $permissions = $this->getRolePermissions($role['id'], true);
            if ($permissions) $result |= $permissions;
        }
        return $result;
    }
    /************************************************
     * ### 修改用戶身份組 ###
     * @param int $rid 身份組編號
     * @param int $mid 使用者編號
     * @param int $alwaysAdd 總是新增身分組(如果啟用則用戶有身份組則不刪除)
     ************************************************/
    public function changeMemberRoles($rid, $mid, $alwaysAdd = false): bool
    {
        $mRoles = $this->getMemberRoles($mid);
        $memberHaveRole = false;
        foreach ($mRoles as $mRole) $memberHaveRole |= ($rid == $mRole['id']);
        if ($memberHaveRole) {
            if ($alwaysAdd) return true;
            else $sql = "DELETE FROM `{$this->member_roles}` WHERE `mid` = ? AND `rid` = ?;";
        } else $sql = "INSERT INTO `{$this->member_roles}` (`mid`, `rid`) VALUES (?, ?);";
        $result = empty($this->conn->prepare($sql, [$mid, $rid]));
        return $result;
    }
    /************************************************
     * ### 取得身分組資訊 ###
     * @param int $rid 身份組編號(留空將設置為所有身分組)
     * @param int $containsParentPermission 是否包含父身分組權限(默認false)
     ************************************************/
    public function getRoleInformation($rid = null, $containsParentPermission = false): array
    {
        $params[] = $this->wid;
        $roleCondition = "";
        if (!is_null($rid)) {
            $params[] = $rid;
            $roleCondition = "AND `roles`.`id` = ?";
        }
        $sql = "SELECT `roles`.*, COUNT(`mroles`.`mid`) AS `count` FROM `{$this->roles}` AS `roles` 
        LEFT JOIN `{$this->member_roles}` AS `mroles` ON `roles`.`id` = `mroles`.`rid`
        WHERE `roles`.`wid` = ? AND `roles`.`status` > 0 {$roleCondition} GROUP BY `roles`.`id` ORDER BY `roles`.`permissions` ASC;";
        $result = $this->conn->prepare($sql, $params);
        if (empty($result)) return [];
        if ($containsParentPermission) {
            foreach ($result as $key => $rInfo) {
                $result[$key]['genealogy'][] = $rInfo['parent_id'];
                $result[$key]['permission'] |= $this->getRolePermissions($rid, $containsParentPermission);
            }
        }
        return is_null($rid) ? $result : $result[0];
    }
    /************************************************
     * ### 取得身分組資訊 ###
     * @param int $rid 身份組編號(留空將設置為所有身分組)
     * @param int $containsParentPermission 是否包含父身分組權限(默認false)
     ************************************************/
    public function getRoleMembers($rid): array
    {
        $sql = "SELECT `member`.`id` AS `mid`, `member`.`account`,`member`.`nickname`,`members_profile`.`avatar` FROM `{$this->member_roles}` AS `mRole` 
        JOIN `{$this->member}` AS `member` ON `mRole`.`mid` = `member`.`id`
        JOIN `{$this->members_profile}` AS `members_profile` ON `members_profile`.`mid` = `member`.`id`
        WHERE `mRole`.`rid` = ?;";
        return $this->conn->prepare($sql, [$rid]);
    }
    /************************************************
     * ### 取得身分組資訊 ###
     * @param int $rid 身份組編號(留空將設置為所有身分組)
     * @param int $containsParentPermission 是否包含父身分組權限(默認false)
     ************************************************/
    public function searchNoRoleUser($search): array
    {
        $sql = "SELECT `member`.`account`,`member`.`nickname` FROM `{$this->member_roles}` AS `mRole` 
        JOIN `{$this->member}` AS `member` ON `mRole`.`mid` = `member`.`id`
        WHERE (`member`.`nickname` LIKE ? OR `member`.`account` LIKE ?) AND `mRole`.`rid` = ? LIMIT 10;";
        return $this->conn->prepare($sql, ["%{$search}%", "%{$search}%", $this->wid]);
    }
    /************************************************
     * ### 更新身分組 ###
     * @param int $rid 身份組id
     * @param int $rInformation 身份組資訊
     * ```
     * [  
     * id=> Int  
     * wid=> Int  
     * name=> String  
     * displayname=> String  
     * parent_id=> Int  
     * permissions=> Int 
     * status=> Int 
     * ]
     * ```
     ************************************************/
    public function updateRoleInformation($rid, $rInformation = [], $autoCreateRole = false): bool
    {
        $result = true;
        $res = $this->getRoleInformation($rid, false);
        if (empty($res)) {
            if ($autoCreateRole) $result &= $this->addRoles($rid);
            else return false;
        }
        $rOriginalInformation = $res;
        // if (empty($rInformation['parent_id'])) {
        //     // $sql = "UPDATE `{$this->roles}` SET `parent_id` = ? WHERE `rid` = ?;";
        //     // $result &= $this->conn->prepare($sql, [$changePermission, $rid]);
        // }
        $params = [
            empty($rInformation['displayname']) ? $rOriginalInformation['displayname'] : $rInformation['displayname'],
            !isset($rInformation['permissions']) ? $rOriginalInformation['permissions'] : $rInformation['permissions'],
            empty($rInformation['parent_id']) ? $rOriginalInformation['parent_id'] : $rInformation['parent_id'],
            // empty($rInformation['status']) ? $rOriginalInformation['status'] : $rInformation['status'],
            $this->wid,
            $rid
        ];
        // $sql = "UPDATE `{$this->roles}` SET `displayname` = ?, `permissions` = `permissions` ^ ?,`parent_id` = ?, `status` = ? WHERE `wid` = ? AND `id` = ?";
        $sql = "UPDATE `{$this->roles}` SET `displayname` = ?, `permissions` = ?, `parent_id` = ? WHERE `wid` = ? AND `id` = ? AND `status` <> 2";
        $result &= empty($this->conn->prepare($sql, $params));
        return $result;
    }
    /************************************************
     * ### 更新身分組 ###
     * @param int $rid 身份組id
     * @param int $rInformation 身份組資訊
     ************************************************/
    public function deleteRole($rid): bool
    {
        $checkRole = $this->getRoleInformation($rid, false);
        if (empty($checkRole)) return false;
        $sql = "UPDATE `{$this->roles}` SET `status` = -1, `permissions` = 0 WHERE `wid` = ? AND `id` = ?";
        return empty($this->conn->prepare($sql, [$this->wid, $rid]));
    }
}
