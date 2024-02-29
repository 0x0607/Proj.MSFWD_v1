<?php
// include "./class.connectDatabase.php";

use function PHPSTORM_META\type;

/****************************************************************************************
 * 
 * 商品系統
 * @param obj $db 資料庫
 * 
 ****************************************************************************************/
class productManage
{
    private $conn;
    private $products = "i_products";
    private $product_views = "log_product_views";
    private $orders = "p_orders";
    private $order_item = "p_order_items";
    private $paid = "p_paid";
    private $virtual_product = "api_virtual_product";
    public function __construct($db)
    {
        $this->conn = $db;
    }
    /************************************************
     * ### 取得商品資訊 ###
     * @param int $wid 網站編號
     * @param int $product_id 商品編號
     ************************************************/
    public function getProductInformation($wid, $product_id = null, $autoJsonDecode = true): array
    {
        $field = '';
        $params[] = $wid;
        if (!is_null($product_id)) {
            $field = "AND `id` = ?";
            $params[] = $product_id;
        }
        $sql = "SELECT * FROM `{$this->products}` WHERE `wid` = ? {$field} ORDER BY `status` DESC;";
        $result = $this->conn->prepare($sql, $params);
        if (empty($result)) return [];
        foreach ($result as $key => $res) {
            if ($autoJsonDecode) {
                $result[$key]['tags'] = json_decode($res['tags'], true);
                $result[$key]['images'] = json_decode($res['images'], true);
                $result[$key]['specification'] = json_decode($res['specification'], true);
                $result[$key]['color'] = json_decode($res['color'], true);
            }
            $result[$key]['cover_photo'] = json_decode($res['images'], true)[0];
        }
        return is_null($product_id) ? $result : $result[0];
    }
    /************************************************
     * ### 新增商品 ###
     * 注意建立後的商品默認都為未啟用，這是設計不是BUG  
     * 
     * @param int $wid 網站id
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
    public function addProduct($wid, $product_id, $pInformation = []): bool
    {
        $checkExist = $this->getProductInformation($wid, $product_id);
        if (!empty($checkExist)) return false;
        $result = true;
        if (!preg_match('/^[.0-9]+$/', $pInformation['price']) || !is_int($pInformation['quantity']) || $pInformation['price'] < 0 || $pInformation['quantity'] < -2) return false;
        $params = [
            $product_id,
            $wid,
            empty($pInformation['name']) ? "未命名商品" : htmlspecialchars($pInformation['name']),
            empty($pInformation['description']) ? "無介紹" : htmlspecialchars($pInformation['description']),
            empty($pInformation['types']) ? "[]" : json_encode(htmlspecialchars($pInformation['types'])),
            empty($pInformation['tags']) ? "[]" : json_encode(htmlspecialchars($pInformation['tags'])),
            empty($pInformation['price']) ? 1 : $pInformation['price'],
            empty($pInformation['specification']) ? "[]" : json_encode(htmlspecialchars($pInformation['specification'])),
            empty($pInformation['color']) ? "[]" : json_encode(htmlspecialchars($pInformation['color'])),
            empty($pInformation['quantity']) ? -1 : $pInformation['quantity'],
        ];
        // --------------------------------------------------------------------------------
        $sql = "INSERT INTO {$this->products} (`id`, `wid`, `name`, `description`, `types`, `tags`, `price`, `specification`, `color`, `quantity`) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?);";
        $result &= empty($this->conn->prepare($sql, $params));
        return $result;
    }
    /************************************************
     * ### 編輯商品 ###
     * 注意建立後的商品默認都為未啟用，這是設計不是BUG  
     * 
     * @param int $wid 網站id
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
    public function updateProductInformation($wid, $product_id, $pInformation = []): bool
    {
        $result = true;
        $pOriginalInformation = $this->getProductInformation($wid, $product_id, false);
        if (!empty($checkExist)) return false;
        // if (!preg_match('/^[.0-9]+$/', $pInformation['price']) || !is_int($pInformation['quantity']) || $pInformation['price'] < 0 || $pInformation['quantity'] < -2) return false;
        $params = [
            empty($pInformation['name']) ? $pOriginalInformation['name'] : htmlspecialchars($pInformation['name']),
            empty($pInformation['description']) ? $pOriginalInformation['description'] : htmlspecialchars($pInformation['description']),
            empty($pInformation['types']) ? $pOriginalInformation['types'] : json_encode(htmlspecialchars($pInformation['types'])),
            empty($pInformation['tags']) ? $pOriginalInformation['tags'] : json_encode(htmlspecialchars($pInformation['tags'])),
            empty($pInformation['specification']) ? $pOriginalInformation['specification'] : json_encode(htmlspecialchars($pInformation['specification'])),
            empty($pInformation['color']) ? $pOriginalInformation['color'] : json_encode(htmlspecialchars($pInformation['color'])),
            empty($pInformation['images']) ? $pOriginalInformation['images'] : json_encode(htmlspecialchars($pInformation['images'])),
            empty($pInformation['price']) ? $pOriginalInformation['price'] : $pInformation['price'],
            empty($pInformation['quantity']) ? $pOriginalInformation['quantity'] : $pInformation['quantity'],
            empty($pInformation['status']) ? $pOriginalInformation['status'] : $pInformation['status'],
            $product_id,
            $wid
        ];
        // --------------------------------------------------------------------------------
        $sql = "UPDATE `{$this->products}` SET `name` = ?, `description` = ?, `types` = ?, `tags` = ?, 
        `specification` = ?, `color` = ?, `price` = ?, `quantity` = ?, `status` = `status` ^ ? WHERE `id` = ? AND `wid` = ?;";
        $result &= empty($this->conn->prepare($sql, $params));
        return $result;
    }
    /************************************************
     * ### 查找商品 ###
     * @param obj $db 資料庫
     ************************************************/
    public function searchProduct($wid, $name): array
    {
        $sql = "SELECT `id` FROM {$this->products} WHERE `wid` = ? AND `name` LIKE ?;";
        $result = $this->conn->prepare($sql, [$wid, "%{$name}%"]);
        return empty($result) ? [] : $result;
    }
    /************************************************
     * ### 透過orderId取得訂單 ###
     * @param int $orderId 訂單編號，也就是MerchantOrderNo
     ************************************************/
    public function getOrder($orderId): array
    {
        $sql = "SELECT * FROM {$this->orders} WHERE `id` = ?;";
        $result = $this->conn->prepare($sql, [$orderId]);
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
        if (!empty($this->getOrder($orderInformation[0]))) return false;
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
        $sql = "SELECT * FROM `{$this->paid}` WHERE `order_id` = ? LIMIT 1;";
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
        return empty($result) ? [] : ["action" => $result[0]['action'],"product"=> json_decode($result[0]['product'], true)];
    }
}
