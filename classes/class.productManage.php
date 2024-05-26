<?php
// include "./class.connectDatabase.php";

// use function PHPSTORM_META\type;

/****************************************************************************************
 * 
 * 商品系統
 * @param obj $db 資料庫
 * 
 ****************************************************************************************/
class productManage
{
    private $conn;
    private $wid = null;
    private $limit = 1000;
    private $products = "i_products";
    private $orders = "p_orders";
    private $order_item = "p_order_items";
    private $paid = "p_paid";
    private $virtual_product = "api_virtual_product";
    private $timeRegex = '/^\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])$/';
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
        if ($limit > 1) {
            $this->limit = $limit;
            return true;
        }
        return false;
    }
    /************************************************
     * ### 取得貼文數量 ###
     * @param int $post_id 貼文編號
     ************************************************/
    public function getAllPostCount()
    {
        $sql = "SELECT COUNT(*) FROM `{$this->products}` WHERE `wid` = ? AND `status` <> -1";
        $result = $this->conn->prepare($sql, [$this->wid]);
        return (empty($result)) ? 0 : $result[0]['COUNT(*)'];
    }
    /************************************************
     * ### 取得商品資訊 ###
     * @param int $product_id 商品編號
     ************************************************/
    public function getProductInformation($product_id = null, $offset = 0, $limit = 0, $orderby = 'created_at asc'): array
    {
        $field = '';
        $params[] = $this->wid;
        if (!is_null($product_id)) {
            $field = "AND `id` = ?";
            $params[] = $product_id;
        }
        // $params[] = $orderby;
        if ($limit < 1 || $limit > $this->limit) $limit = $this->limit;
        $sql = "SELECT * FROM `{$this->products}` WHERE `wid` = ? AND `status` <> -1 {$field} 
        ORDER BY `status` DESC, {$orderby}, `id` DESC, `updated_at` ASC LIMIT {$limit} OFFSET {$offset};";
        $result = $this->conn->prepare($sql, $params);
        if (empty($result)) return [];
        foreach ($result as $key => $res) {
            $result[$key]['types'] = json_decode($res['types'], true);
            $result[$key]['tags'] = json_decode($res['tags'], true);
            $result[$key]['images'] = json_decode($res['images'], true);
            $result[$key]['specification'] = json_decode($res['specification'], true);
            $result[$key]['color'] = json_decode($res['color'], true);
        }
        return is_null($product_id) ? $result : $result[0];
    }
    /************************************************
     * ### 新增商品 ###
     * 注意建立後的商品默認都為未啟用，這是設計不是BUG  
     * 
     * @param int $productId 商品id
     * @param string $name 商品名稱  
     * ```
     * [
     * "name" => String,
     * "description" => String,
     * "types" => Json,
     * "tags" => Json),
     * "price" => Int,
     * "specification" => Json,
     * "color" => Json,
     * "quantity" => Float,
     * ]
     * ```
     ************************************************/
    public function addProduct($product_id, $pInformation = []): bool
    {
        if (isset($pInformation['images'])) $pInformation['images'] = array_values(array_filter($pInformation['images']));
        $checkExist = $this->getProductInformation($product_id);
        if (!empty($checkExist)) return false;
        if (!preg_match('/^[.0-9]+$/', $pInformation['price']) || !is_int($pInformation['quantity']) || $pInformation['price'] < 0 || $pInformation['quantity'] < -2) return false;
        $params = [
            $product_id,
            $this->wid,
            empty($pInformation['name']) ? "未命名商品" : htmlspecialchars($pInformation['name']),
            empty($pInformation['description']) ? "無介紹" : htmlspecialchars($pInformation['description']),
            empty($pInformation['types']) ? "[]" : json_encode($pInformation['types']),
            empty($pInformation['tags']) ? "[]" : json_encode($pInformation['tags']),
            empty($pInformation['specification']) ? "[]" : json_encode($pInformation['specification']),
            empty($pInformation['color']) ? "[]" : json_encode($pInformation['color']),
            empty($pInformation['images']) ? "[]" : json_encode($pInformation['images']),
            empty($pInformation['price']) ? 29 : (($pInformation['price'] < 29) ? 29 : $pInformation['price']),
            empty($pInformation['quantity']) ? -1 : $pInformation['quantity'],
            empty($pInformation['status']) ? 0 : $pInformation['status'],
        ];
        // --------------------------------------------------------------------------------
        $sql = "INSERT INTO {$this->products} (`id`, `wid`, `name`, `description`, `types`, `tags`, `specification`, `color`, `images`, `price`, `quantity`, `status`) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);";
        return empty($this->conn->prepare($sql, $params));
    }
    /************************************************
     * ### 編輯商品 ###
     * 注意建立後的商品默認都為未啟用，這是設計不是BUG  
     * 
     * @param int $productId 商品id
     * ```
     * [
     * "name" => String,
     * "description" => String,
     * "types" => Json,
     * "tags" => Json),
     * "price" => Int,
     * "specification" => Json,
     * "color" => Json,
     * "quantity" => Float,
     * "status" => Int
     * ]
     * ```
     ************************************************/
    public function updateProductInformation($product_id, $pInformation = []): bool
    {
        $result = true;
        $pOriginalInformation = $this->getProductInformation($product_id);
        if (empty($pOriginalInformation)) return false;
        // if (!preg_match('/^[.0-9]+$/', $pInformation['price']) || !is_int($pInformation['quantity']) || $pInformation['price'] < 0 || $pInformation['quantity'] < -2) return false;
        $newProductImage = $pOriginalInformation['images'];
        if (isset($pInformation['images'])) {
            foreach ($pInformation['images'] as $key => $image) {
                // 刪除圖片
                if (isset($pInformation['deletedimage']) && $pInformation['deletedimage'][$key]) {
                    $newProductImage[$key] = '';
                }
                // 變更圖片
                if (!empty($pInformation['images'][$key])) {
                    $newProductImage[$key] = $pInformation['images'][$key];
                }
            }
        }
        $newProductImage = array_values(array_filter($newProductImage));

        $params = [
            empty($pInformation['name']) ? $pOriginalInformation['name'] : htmlspecialchars($pInformation['name']),
            empty($pInformation['description']) ? $pOriginalInformation['description'] : htmlspecialchars($pInformation['description']),
            json_encode(empty($pInformation['types']) ? $pOriginalInformation['types'] : $pInformation['types']),
            json_encode(empty($pInformation['tags']) ? $pOriginalInformation['tags'] : $pInformation['tags']),
            json_encode(empty($pInformation['specification']) ? $pOriginalInformation['specification'] : $pInformation['specification']),
            json_encode(empty($pInformation['color']) ? $pOriginalInformation['color'] : $pInformation['color']),
            json_encode($newProductImage),
            empty($pInformation['price']) ? $pOriginalInformation['price'] : (($pInformation['price'] < 29) ? 29 : $pInformation['price']),
            empty($pInformation['quantity']) ? $pOriginalInformation['quantity'] : $pInformation['quantity'],
            // empty($pInformation['status']) ? $pOriginalInformation['status'] : $pInformation['status'],
            $product_id,
            $this->wid
        ];
        // --------------------------------------------------------------------------------
        $sql = "UPDATE `{$this->products}` SET `name` = ?, `description` = ?, `types` = ?, `tags` = ?, 
        `specification` = ?, `color` = ?, `images`=?, `price` = ?, `quantity` = ? WHERE `id` = ? AND `wid` = ?;";
        $result &= empty($this->conn->prepare($sql, $params));
        return $result;
    }
    /************************************************
     * ### 變更商品狀態 ###
     * @param int $productId 商品id
     ************************************************/
    public function changeProductStatus($product_id): bool
    {
        $sql = "UPDATE `{$this->products}` SET `status` = NOT `status` WHERE `wid` = ? AND `id` = ?;";
        $result = empty($this->conn->prepare($sql, [$this->wid, $product_id]));
        return $result;
    }
    /************************************************
     * ### 變更商品狀態 ###
     * @param int $productId 商品id
     ************************************************/
    public function deleteProduct($product_id): bool
    {
        $sql = "UPDATE `{$this->products}` SET `status` = -1 WHERE `wid` = ? AND `id` = ?;";
        $result = empty($this->conn->prepare($sql, [$this->wid, $product_id]));
        return $result;
    }
    /************************************************
     * ### 查找商品 ###
     * @param obj $db 資料庫
     ************************************************/
    // public function searchProduct($name): array
    // {
    //     $sql = "SELECT `id` FROM {$this->products} WHERE `wid` = ? AND `name` LIKE ?;";
    //     $result = $this->conn->prepare($sql, [$this->wid, "%{$name}%"]);
    //     return empty($result) ? [] : $result;
    // }
    /************************************************
     * ### 透過orderId取得訂單 ###
     * @param int $orderId 訂單編號，也就是MerchantOrderNo
     ************************************************/
    public function getOrderByTime($startTime = "0000-01-01", $endTime = "9999-12-31"): array
    {
        if (!preg_match($this->timeRegex, $startTime) || !preg_match($this->timeRegex, $endTime)) return [];
        $sql = "SELECT * FROM {$this->orders} WHERE `wid` = ? AND `created_at` BETWEEN ? AND ? ORDER BY `created_at` ASC;";
        $result = $this->conn->prepare($sql, [$this->wid, $startTime, $endTime]);
        return empty($result) ? [] : $result;
    }
    /************************************************
     * ### 透過orderId取得訂單 ###
     * @param int $orderId 訂單編號，也就是MerchantOrderNo
     ************************************************/
    public function getOrder($orderId): array
    {
        $sql = "SELECT * FROM {$this->orders} WHERE `wid` = ? AND `id` = ?;";
        $result = $this->conn->prepare($sql, [$this->wid, $orderId]);
        return empty($result) ? [] : $result;
    }
    /************************************************
     * ### 透過orderId取得訂單 ###
     * @param int $orderId 訂單編號，也就是MerchantOrderNo
     ************************************************/
    public function getOrderInformation($orderId): array
    {
        // $sql = "SELECT `order_item`.* FROM `p_order_items` AS `order_item`
        // LEFT JOIN `p_orders` AS `order` ON `order_item`.`order_id` = `order`.`id`
        // WHERE `order_i`.`order_id` = ?;";
        $sql = "SELECT * FROM {$this->order_item} WHERE `order_id` = ?;";
        $order_items =  $this->conn->prepare($sql, [$orderId]);
        return empty($order_items) ? [] : $order_items;
    }
    /************************************************
     * ### 透過orderId取得訂單 ###
     * @param int $mid 用戶編號
     ************************************************/
    public function getOrderByUser($mid): array
    {
        $sql = "SELECT * FROM {$this->orders} WHERE `mid` = ?;";
        $result = $this->conn->prepare($sql, [$mid]);
        return empty($result) ? [] : $result;
    }
    /************************************************
     * ### 新增訂單 ###
     * @param array $orderInformation 訂單資訊
     * @param array $orderItemInformation 訂單物品資訊
     ************************************************/
    public function addOrder($orderInformation, $orderItemInformation): bool
    {
        if (!empty($this->getOrder($orderInformation[2], $orderInformation[0]))) return false;
        $result = true;
        // 商品訂單成立
        $sql = "INSERT INTO `{$this->orders}` (`id`, `mid`, `wid`, `amount`, `hash`) VALUES (?, ?, ?, ?, ?);";
        $result &= empty($this->conn->prepare($sql, $orderInformation));
        // 商品訂單資訊
        $sql = "INSERT INTO `{$this->order_item}` (`order_id`, `product_id`, `quantity`, `price`) VALUES ";
        foreach ($orderItemInformation as $oItem) {
            $sql .= "('{$oItem['oid']}', {$oItem['pid']}, {$oItem['quantity']}, {$oItem['price']}),";
        }
        $sql = rtrim($sql, ',') . ';';
        $result &= empty($this->conn->each($sql));
        return $result;
    }
    /************************************************
     * ### 變更訂單狀態 ###
     * @param int $orderId 訂單編號
     * @param bool $status 狀態碼
     ************************************************/
    public function setOrderStatus($orderId, $status = '0|NO'): bool
    {
        // if (!preg_match('/^\d\|[a-zA-Z]{2}$/', $status)) return false;
        $sql = "UPDATE `{$this->orders}` SET `status` = ? WHERE `id` = ?;";
        $result = empty($this->conn->prepare($sql, [$status, $orderId]));
        return $result;
    }
    /************************************************
     * ### 確認訂單付款狀態 ###
     * @param int $orderPaid 訂單編號
     ************************************************/
    public function getOrderPaid($orderId): array
    {
        $sql = "SELECT * FROM `{$this->paid}` WHERE `order_id` = ?;";
        $result = $this->conn->prepare($sql, [$orderId]);
        return empty($result) ? [] : $result[0];
    }
    /************************************************
     * ### 新增訂單付款狀態 ###
     * @param array $orderPaid 訂單資訊
     ************************************************/
    public function addOrderPaid($orderPaid): bool
    {
        if (!empty($this->getOrderPaid($orderPaid[0]))) return false;
        $sql = "INSERT INTO `{$this->paid}` (`order_id`, `trade_no`, `message`, `amount`, `payment_type`, `ip_address`, `status`, `pay_time`) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?);";
        $result = empty($this->conn->prepare($sql, $orderPaid));
        return $result;
    }
    /************************************************
     * ### 取得商品ID與虛擬商品對應商品 ### 
     * @param int $productId 商品ID
     ************************************************/
    public function getVirtualProduct($productId)
    {
        $sql = "SELECT `product`,`action` FROM {$this->virtual_product} WHERE `product_id` = ?;";
        $result = $this->conn->prepare($sql, [$productId]);
        return empty($result) ? [] : ["action" => $result[0]['action'], "product" => json_decode($result[0]['product'], true)];
    }
    /************************************************
     * ### 透過wId取得訂單付款狀態 ###
     * @param string $status 狀態(1|OK付款成功)
     ************************************************/
    public function getOrderPaidByTime($status = '1|OK', $startTime = "0000-01-01", $endTime = "9999-12-31"): array
    {
        if (!preg_match($this->timeRegex, $startTime) || !preg_match($this->timeRegex, $endTime)) return [];
        $sql = "SELECT * FROM {$this->paid} AS `paid`
        JOIN `{$this->orders}` AS `order` ON `paid`.`order_id` = `order`.`id`
        WHERE `order`.`wid` = ? AND `paid`.`status` = ? AND `order`.`status` = ? AND `paid`.`pay_time` BETWEEN ? AND ? 
        ORDER BY `paid`.`pay_time` ASC;";
        $result = $this->conn->prepare($sql, [$this->wid, $status, $status, $startTime, $endTime]);
        return empty($result) ? [] : $result;
    }
}
