-- -------------------------------------------------------
-- lastUpdate 11/21/2023
-- 核心資料表
-- By MaizuRoad
-- -------------------------------------------------------
CREATE DATABASE IF NOT EXISTS `TwilightMart`;
USE `TwilightMart`;

-- 網站資訊
CREATE TABLE IF NOT EXISTS `s_website` (
  `id` BIGINT(19) UNSIGNED NOT NULL,
  `prefix` VARCHAR(8) NOT NULL COMMENT '網站代號',
  `domain` VARCHAR(64) NOT NULL COMMENT '網域名稱',
  `displayname` VARCHAR(20) NOT NULL COMMENT '網站別名',
  `distribution` VARCHAR(32) DEFAULT 'Taiwan' NOT NULL  COMMENT '地理位置',
  -- `dbname` VARCHAR(18) NOT NULL COMMENT '使用資料庫',
  `icon` VARCHAR(255) DEFAULT NULL COMMENT '圖示',
  `background` VARCHAR(255) DEFAULT NULL COMMENT '背景',
  `stylesheet` VARCHAR(64) DEFAULT 'style_default' NOT NULL COMMENT '主題',
  `settings` TEXT NOT NULL DEFAULT '{"general":{"enable_cart":false,"enable_unturned":true,"enable_local_login":false,"enable_steam_login":true},"unturned":{"datebase":{"dbname":"unturned","host":"addr.snkms.org","port":3306,"username":"","password":""},"table":{"uconomy":{"uconomy_itemshop":"i_itemshop","uconomy_vehicleshop":"i_vehicleshop","uconomy_payments":"log_payments","uconomy_uconomy":"m_uconomy"},"permissions":{"permission_groups":"p_groups","permission_members":"p_members","permission_permissions":"p_permissions"},"moderation":{"moderation_expiredplayerbans":"moderation_expiredplayerbans","moderation_ipinfo":"moderation_ipinfo","moderation_playerbans":"moderation_playerbans","moderation_playerhwids":"moderation_playerhwids","moderation_playerinfo":"moderation_playerinfo","moderation_playerwarns":"moderation_playerwarns"},"battlepass":"b_battlepass","ranks":"m_ranks"}},"theme":{"body":"#212121","mainColor":"#212121","secondaryColor":"#2879f6","mainfontColor":"#FFFFFF"}}' COMMENT '設置',
  `status` INT(3) NOT NULL DEFAULT 1 COMMENT '啟用狀態1啟用 0關閉',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '創建時間',
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新時間',
  PRIMARY KEY (`id`),
  INDEX `prefix` (`prefix`)
) COMMENT='網站資訊';

-- 會員資料表
CREATE TABLE IF NOT EXISTS `m_members` (
  `id` BIGINT(19) UNSIGNED NOT NULL COMMENT 'ID',
  `account` VARCHAR(20) NOT NULL COMMENT '唯一識別項帳號',
  `nickname` VARCHAR(18) NOT NULL COMMENT '暱稱',
  `password` VARCHAR(255) NOT NULL COMMENT '密碼',
  `last_ip_address` VARCHAR(45) NOT NULL DEFAULT '0.0.0.0' COMMENT '使用者最後登入IP位址',
  `status` INT(3) NOT NULL DEFAULT 1 COMMENT '帳號狀態1啟用 0停用',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '創建時間',
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新時間',
  PRIMARY KEY (`id`),
  INDEX `account` (`account`)
) COMMENT='會員資料表';

-- 會員頁面資料表
CREATE TABLE IF NOT EXISTS `m_members_profile` (
  `mid`  BIGINT(19) UNSIGNED NOT NULL COMMENT 'ID',
  `introduction` text NOT NULL DEFAULT '這個人很懶，什麼都沒寫' COMMENT '個人介紹',
  `theme` VARCHAR(7) DEFAULT NULL COMMENT '顏色風格',
  `avatar` VARCHAR(255) NOT NULL DEFAULT '/assets/images/quaso.png' COMMENT '頭貼',
  `background` VARCHAR(255) DEFAULT NULL COMMENT '自訂背景',
  PRIMARY KEY (`mid`),
  CONSTRAINT `fk_m_members`
    FOREIGN KEY (`mid`)
    REFERENCES `m_members` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) COMMENT='會員資料表';

-- 藍新支付
CREATE TABLE IF NOT EXISTS `s_newebpay` (
  `wid` BIGINT(19) UNSIGNED NOT NULL COMMENT '網站ID',
  `store_id` VARCHAR(12) NOT NULL COMMENT '商店ID',
  `store_hash_key` VARCHAR(32) NOT NULL COMMENT '商店雜湊KEY',
  `store_hash_iv` VARCHAR(16) NOT NULL COMMENT '商店雜湊IV',
  `store_return_url` VARCHAR(255) NOT NULL COMMENT '交易完成返回網址',
  `store_client_back_url` VARCHAR(255) DEFAULT 'https://www.youtube.com/watch?v=dQw4w9WgXcQ' NOT NULL COMMENT '交易完成客端返回網址',
  `store_notify_url` VARCHAR(255) NOT NULL COMMENT '通知網址',
  PRIMARY KEY (wid),
  CONSTRAINT `fk_s_website`
    FOREIGN KEY (`wid`)
    REFERENCES `s_website` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) COMMENT='藍新支付';

-- 網站元件
CREATE TABLE IF NOT EXISTS `s_components` (
  `id` INT NOT NULL AUTO_INCREMENT COMMENT '元件ID',
  `name` VARCHAR(50) NOT NULL COMMENT '元件名稱',
  `description` TEXT COMMENT '元件描述',
  `params` JSON COMMENT '元件預設參數',
  `template` LONGTEXT NOT NULL DEFAULT 'unknown' COMMENT '元件樣板',
  `permissions` INT(50) NOT NULL DEFAULT '0' COMMENT '瀏覽該元件需求權限0x0代表無須權限，0x2代表需要群組管理權限',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '創建時間',
  PRIMARY KEY (id),
  INDEX `name` (`name`)
) COMMENT='元件資料表';

-- 權限組
-- 這個表只用來標示沒有其他用途
CREATE TABLE IF NOT EXISTS `m_permissions` (
  `id` INT NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `name` VARCHAR(100) NOT NULL COMMENT '權限名稱',
  `displayname` VARCHAR(100) NOT NULL COMMENT '權限顯示名稱',
  PRIMARY KEY (id)
) COMMENT='權限資料表';

-- 建立Demo站台
INSERT INTO `s_website` (`id`, `domain`, `name`, `displayname`, `distribution`, `icon`, `background`) VALUES (589605057335390208, 'localhost', 'Demo', 'DemoWebsite', 'Taiwan', '/assets/images/logo.png', '/assets/images/bg.jpg');

-- 插入Demo藍新
INSERT INTO `s_newebpay` (`wid`,`store_prefix`,`store_id`,`store_hash_key`,`store_hash_iv`,`store_return_url`,`store_client_back_url`,`store_notify_url`) VALUES(589605057335390208,'0','0','0','0','https://personal.snkms.com/projects/steam/','https://personal.snkms.com/projects/steam/','https://personal.snkms.com/projects/steam/api/done.php');

-- 插入權限
INSERT INTO `m_permissions` (`name`, `displayname`) VALUES
  ('*', '所有權限'), 
  ('group', '群組管理'), 
  ('website', '網站基本資料維護'), 
  ('menu', '選單維護'), 
  ('page', '頁面維護'), 
  ('product_create', '商品建檔'), 
  ('product_change', '商品調整'), 
  ('order_view', '訂單查詢'), 
  ('order_manage', '訂單管理'), 
  ('order_customer_service', '訂單客服'), 
  ('sales_report', '銷售統計報表'); 


-- 新增元件
INSERT INTO `s_components` (`id`, `name`, `description`, `params`, `template`, `permissions`) VALUES
(1, 'null', '無', '', 'unknown', 0),
(2, 'navbar', '導覽列', '[{"displayname":"首頁","link":"?route=home","icon":"e88a"},{"displayname":"商店","link":"?route=store","icon":"e051"},{"displayname":"關於","link":[{"displayname":"關於團隊","link":"?route=about","icon":"e7ef"},{"displayname":"免責聲明","link":"?route=disclaimer","icon":"f04c"},{"displayname":"版本訊息","link":"?jump=https://github.com/Sky-Night-Kamhu-Mitor-Seuna/TwilightMart","icon":"f04c"}]}]', 'navbar', 0),
(3, 'banner', '橫幅', '{"style":0,"image":"/assets/images/webdesign.svg","message":["Hello Im MaizuRoad","Freut mich, Sie kennenzulernen","這是一朵美麗的小花"],"button":[{"displayname":"see more>>","link":"?route=home"}]}', 'banner', 0),
(4, 'login', '登入', '', 'login', 0),
(5, 'register', '註冊', '', 'register', 0),
(6, 'logout', '登出', '', 'unknown', 0),
(7, 'profile_card', '個人頁面資訊卡', '', 'profile_card', 0),
(8, 'recommend_product', '推薦商品', '', 'recommend_product', 0),
(9, 'product_list', '商品清單', '', 'product_list', 0),
(10, 'announcement', '公告欄', '[{"title":"公告","content":"如果你看到這行文字代表你具有管理員權限，必須具有對應的權限才能進行操作"},{"title":"說明","content":"網站管理: 一些關於整個網站的操作<br/>商品管理: 跟商品有關的操作<br/>群組管理: 跟權限有關的操作"}]', 'announcement', 0),
(11, 'member_profile_edit', '個人化', '{"method":"member-setting"}', 'information_edit', 0),
(12, 'website_information_edit', '站台資訊編輯', '{"method":"website-management"}', 'information_edit', _binary 0x34),
(13, 'product_edit', '商品編輯', '', 'product_edit', _binary 0x3634),
(14, 'product_details_page', '商品詳細頁面', NULL, 'unknown', 0),
(15, 'data_chart_page', '數據圖表頁面', '[{"theme":"dark1","lineColor":"#566573","axisY":"單位","type":"column","toolTipContent":"{label}<br>{y}單位"},{"theme":"light2","lineColor":"#F1C40F","axisY":"單位","type":"column","toolTipContent":"{label}<br>{y}單位"}]', 'data_chart_page', 0);


-- 插入會員 root 密碼：P@55word
INSERT INTO `m_members` (`id`, `account`, `nickname`, `password`) VALUES (589605057335390208, 'root', 'Administrator', '3fbfeb0ee307127bbd4ef7da33f7b57a9ff3c7357da182c5bfccc2a4f599c6f9');
INSERT INTO `m_members_profile` (`mid`, `avatar`) VALUES (589605057335390208, '/assets/images/demoAvatar1.png');

-- 網站頁面
CREATE TABLE IF NOT EXISTS `w_pages` (
  `id` BIGINT(19) UNSIGNED NOT NULL COMMENT '頁面ID',
  `wid` BIGINT(19) UNSIGNED NOT NULL COMMENT '網站編號ID',
  `name` VARCHAR(100) NOT NULL COMMENT '連結名稱',
  `displayname` VARCHAR(100) NOT NULL DEFAULT "未命名頁面" COMMENT '頁面標題',
  `icon` VARCHAR(100) NOT NULL DEFAULT "/assets/images/logo" COMMENT '圖示',
  `description` TEXT COMMENT '頁面描述',
  `status` INT(3) NOT NULL DEFAULT 1 COMMENT '啟用狀態1啟用 0關閉',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '創建時間',
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新時間',
  PRIMARY KEY (`id`),
  INDEX `FK_w_pages_s_website` (`wid`),
  CONSTRAINT `FK_w_pages_s_website`
  FOREIGN KEY (`wid`)
  REFERENCES `s_website` (`id`)
  ON UPDATE CASCADE
  ON DELETE CASCADE
) COMMENT='頁面資料表';

-- 網站頁面應用元件
CREATE TABLE IF NOT EXISTS `w_page_component` (
  `id` BIGINT(19) UNSIGNED NOT NULL COMMENT 'ID',
  `pid` BIGINT(19) UNSIGNED NOT NULL COMMENT '頁面ID',
  `cid` INT NOT NULL COMMENT '元件ID',
  `displayname` VARCHAR(128) NOT NULL COMMENT '名稱',
  `position` INT(11) NOT NULL COMMENT '位置',
  `params` JSON COMMENT '元件參數',
  `permissions` INT(50) NOT NULL DEFAULT 0 COMMENT '瀏覽該頁面需求權限0x0代表無須權限，0x2代表需要群組管理權限',
  `status` INT(3) NOT NULL DEFAULT 1 COMMENT '啟用狀態1啟用 0關閉',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '創建時間',
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新時間',
  PRIMARY KEY (`id`),
  INDEX `fk_component_id` (`cid`),
  INDEX `fk_page_id` (`pid`),
  CONSTRAINT `fk_component_id` 
    FOREIGN KEY (`cid`)
    REFERENCES `s_components`(`id`)
    ON DELETE CASCADE 
    ON UPDATE CASCADE,
  CONSTRAINT `fk_page_id` 
    FOREIGN KEY (`pid`)
    REFERENCES `w_pages`(`id`)
    ON DELETE CASCADE 
    ON UPDATE CASCADE
) COMMENT='元件與頁面關聯表';

-- 身份組
-- 跟會員帳號一樣獨立
CREATE TABLE IF NOT EXISTS `m_roles` (
  `id` BIGINT(19) UNSIGNED NOT NULL COMMENT 'ID',
  `wid` BIGINT(19) UNSIGNED NOT NULL COMMENT '網站ID',
  `parent_id` BIGINT(19) UNSIGNED NULL COMMENT '父身份組',
  `name` VARCHAR(100) NOT NULL COMMENT '身份組名稱',
  `displayname` VARCHAR(100) NOT NULL COMMENT '身份組顯示名稱',
  `permissions` INT(50) NOT NULL DEFAULT 0 COMMENT '權限表每個01對應著是否具有權限0x1代表擁有所有權限',
  `status` INT(3) NOT NULL DEFAULT 1 COMMENT '啟用狀態1啟用 0關閉 2系統用戶(無法刪除)',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '創建時間',
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新時間',
  PRIMARY KEY (`id`),
  INDEX `FK_m_roles_s_website` (`wid`),
  CONSTRAINT `FK_m_roles_s_website`
  FOREIGN KEY (`wid`)
  REFERENCES `s_website` (`id`)
  ON UPDATE CASCADE
  ON DELETE CASCADE
) COMMENT='角色資料表';

-- 會員擁有的身份組
CREATE TABLE IF NOT EXISTS `m_member_roles` (
  `mid` BIGINT(19) UNSIGNED NOT NULL COMMENT '會員ID',
  `rid` BIGINT(19) UNSIGNED NOT NULL COMMENT '身份組',
  INDEX `member_roles_fk_role_id` (`rid`),
  INDEX `member_roles_fk_member_id` (`mid`),
  INDEX `FK_m_member_roles_s_website` (`wid`),
  CONSTRAINT `member_roles_fk_role_id`
    FOREIGN KEY (`rid`)
    REFERENCES `m_roles` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `member_roles_fk_member_id`
    FOREIGN KEY (`mid`)
    REFERENCES `m_members` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) COMMENT='會員資料表';

-- 商品頁面
CREATE TABLE IF NOT EXISTS `i_products` (
  `id` BIGINT(19) UNSIGNED NOT NULL COMMENT '商品ID',
  `wid` BIGINT(19) UNSIGNED NOT NULL COMMENT '網站ID',
  `name` VARCHAR(255) NOT NULL COMMENT '商品名稱',
  `description` TEXT COMMENT '商品描述',
  `types` LONGTEXT NOT NULL DEFAULT '[]' COMMENT '商品分類',
  `tags` LONGTEXT NOT NULL DEFAULT '[]' COMMENT '商品標籤',
  `specification` LONGTEXT NOT NULL DEFAULT '[]' COMMENT '商品規格',
  `color` LONGTEXT NOT NULL DEFAULT '[]' COMMENT '商品顏色',
  `images` LONGTEXT NOT NULL DEFAULT '["/assets/images/quaso.png"]' COMMENT '商品圖片',
  `price` DECIMAL(10, 2) NOT NULL COMMENT '商品價格',
  `quantity` INT NOT NULL DEFAULT 0 COMMENT '商品數量',
  `status` TINYINT(3) NOT NULL DEFAULT 0 COMMENT '商品狀態：0-刪除;1-上架;2-下架',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '創建時間',
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新時間',
  PRIMARY KEY (`id`),
  INDEX `fk_i_products_websiteId_id` (`wid`),
  CONSTRAINT `fk_i_products_websiteId_id` 
    FOREIGN KEY (`wid`)
    REFERENCES `s_website`(`id`)
    ON DELETE CASCADE 
    ON UPDATE CASCADE
) COMMENT='商品資料表';

-- 購物車資料表
CREATE TABLE `i_cart` (
  `id` BIGINT(19) UNSIGNED NOT NULL COMMENT '購物車ID',
  `product_id` BIGINT(19) UNSIGNED NOT NULL COMMENT '商品ID',
  `wid` BIGINT(19) UNSIGNED NOT NULL COMMENT '網站ID',
  `mid` BIGINT(19) UNSIGNED NOT NULL COMMENT '會員ID',
  `specification` LONGTEXT NOT NULL DEFAULT '[]' COMMENT '商品規格',
  `color` LONGTEXT NOT NULL DEFAULT '[]' COMMENT '商品顏色',
  `quantity` INT(11) NOT NULL DEFAULT 1 COMMENT '商品數量',
  -- `status` TINYINT(3) NOT NULL DEFAULT 1 COMMENT '購買狀態: 0刪除 1未結帳 2已結帳',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '創建時間',
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新時間',
  PRIMARY KEY (`id`),
  INDEX `FK_i_cart_s_website` (`wid`),
  INDEX `FK_i_cart_m_members` (`mid`),
  INDEX `FK_i_cart_i_products` (`product_id`),
  CONSTRAINT `FK_i_cart_i_products` FOREIGN KEY (`product_id`) REFERENCES `i_products` (`id`) ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT `FK_i_cart_m_members` FOREIGN KEY (`mid`) REFERENCES `m_members` (`id`) ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT `FK_i_cart_s_website` FOREIGN KEY (`wid`) REFERENCES `s_website` (`id`) ON UPDATE CASCADE ON DELETE CASCADE
) COMMENT='購物車資料表';


-- 訂單資訊
CREATE TABLE IF NOT EXISTS `p_orders` (
  `id` CHAR(64) NOT NULL COMMENT '訂單ID',
  `mid` BIGINT(19) UNSIGNED NOT NULL COMMENT '會員ID',
  `wid` BIGINT(19) UNSIGNED NOT NULL COMMENT '網站ID',
  `amount` DECIMAL(10, 2) NOT NULL COMMENT '交易金額',
  `hash` char(40) NOT NULL COMMENT '雜湊驗證碼',
  `status` VARCHAR(8) NOT NULL DEFAULT '0|NO' COMMENT '狀態碼',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '創建時間',
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新時間',
  PRIMARY KEY (`id`),
  INDEX `fk_p_orders_member_id` (`mid`),
  INDEX `fk_p_orders_websiteId_id` (`wid`),
  CONSTRAINT `fk_p_orders_member_id` 
    FOREIGN KEY (`mid`)
    REFERENCES `m_members`(`id`)
    ON DELETE CASCADE 
    ON UPDATE CASCADE,
  CONSTRAINT `fk_p_orders_websiteId_id` 
    FOREIGN KEY (`wid`)
    REFERENCES `s_website`(`id`)
    ON DELETE CASCADE 
    ON UPDATE CASCADE
) COMMENT='訂單資訊表';

-- 訂單明細
CREATE TABLE IF NOT EXISTS `p_order_items` (
  `id` INT AUTO_INCREMENT COMMENT 'ID',
  `order_id` CHAR(64) NOT NULL COMMENT '訂單ID',
  `product_id` BIGINT(19) UNSIGNED NOT NULL COMMENT '商品ID',
  `types` LONGTEXT NOT NULL DEFAULT '[]' COMMENT '商品分類',
  `tags` LONGTEXT NOT NULL DEFAULT '[]' COMMENT '商品標籤',
  `specification` LONGTEXT NOT NULL DEFAULT '[]' COMMENT '商品規格',
  `color` LONGTEXT NOT NULL DEFAULT '[]' COMMENT '商品顏色',
  `quantity` INT NOT NULL COMMENT '商品數量',
  `price` DECIMAL(10, 2) NOT NULL COMMENT '商品價格',
  PRIMARY KEY (`id`),
  INDEX `fk_p_order_items_order_id` (`order_id`),
  INDEX `fk_p_order_items_product_id` (`product_id`),
  CONSTRAINT `fk_p_order_items_order_id` 
    FOREIGN KEY (`order_id`)
    REFERENCES `p_orders`(`id`)
    ON DELETE CASCADE 
    ON UPDATE CASCADE,
  CONSTRAINT `fk_p_order_items_product_id` 
    FOREIGN KEY (`product_id`)
    REFERENCES `i_products`(`id`)
    ON DELETE CASCADE 
    ON UPDATE CASCADE
) COMMENT='訂單商品明細表';

-- 訂單交易資訊
CREATE TABLE IF NOT EXISTS `p_paid` (
  `id` INT AUTO_INCREMENT COMMENT 'ID',
  `order_id` CHAR(64) NOT NULL COMMENT '訂單ID',
  `trade_no` CHAR(20) NOT NULL COMMENT '交易流水號',
  `message` CHAR(255) NOT NULL COMMENT '付款訊息',
  `amount` INT(16) NOT NULL COMMENT '交易金額',
  `payment_type` CHAR(10) NOT NULL COMMENT '支付方式',
  `ip_address` VARCHAR(45) NOT NULL COMMENT '交易IP位址',
  `status` VARCHAR(8) NOT NULL DEFAULT '0|NO' COMMENT '狀態碼',
  `pay_time` DATETIME NOT NULL COMMENT '交易時間',
  PRIMARY KEY (`id`),
  INDEX `fk_p_paid_order_id` (`order_id`),
  CONSTRAINT `fk_p_paid_order_id` 
    FOREIGN KEY (`order_id`)
    REFERENCES `p_orders`(`id`)
    ON DELETE CASCADE 
    ON UPDATE CASCADE
) COMMENT='訂單交易資訊';

-- 檔案系統
CREATE TABLE IF NOT EXISTS `w_files` (
    `id` BIGINT(19) UNSIGNED NOT NULL COMMENT "檔案名稱及編號",
    `original_name` VARCHAR(255) NOT NULL COMMENT "檔案原始名稱",
    `path` VARCHAR(255) NOT NULL COMMENT "檔案路徑",
    `size` INT(10) UNSIGNED NOT NULL COMMENT "檔案大小",
    `type` VARCHAR(100) NOT NULL COMMENT "檔案類型jpg, jepg, png...etc",
    `uploaded_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT "檔案上傳時間",
    PRIMARY KEY (`id`)
) COMMENT='檔案管理系統';

-- 系統操作紀錄表
CREATE TABLE IF NOT EXISTS `log_system` (
  `id` BIGINT(19) UNSIGNED NOT NULL COMMENT '操作編號',
  `wid` BIGINT(19) UNSIGNED NOT NULL COMMENT '網站編號',
  `operator` BIGINT(19) NOT NULL COMMENT '操作者',
  `ip_address` VARCHAR(39) NOT NULL DEFAULT '0.0.0.0' COMMENT 'IP位址',
  `status` CHAR(4) NOT NULL DEFAULT '0|NO' COMMENT '類型 0|NO: 無效操作 1|OK: 存取成功',
  `action` CHAR(64) NOT NULL DEFAULT 'UNKNOW' COMMENT '操作',
  `reason` VARCHAR(1024) NOT NULL COMMENT '原因',
  `hash` CHAR(64) NOT NULL COMMENT '雜湊',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '創建時間',
  PRIMARY KEY (`id`),
  INDEX `FK_s_system_log_s_website` (`wid`),
  CONSTRAINT `FK_s_system_log_s_website` 
  FOREIGN KEY (`wid`)
  REFERENCES `s_website` (`id`)
  ON UPDATE CASCADE 
  ON DELETE CASCADE
) COMMENT='系統操作紀錄';

-- 頁面瀏覽紀錄表
CREATE TABLE IF NOT EXISTS `log_page_views` (
  `id` BIGINT(19) UNSIGNED NOT NULL COMMENT '編號',
  `pid` BIGINT(19) UNSIGNED NOT NULL COMMENT '頁面編號',
  `operator` BIGINT(19) NOT NULL COMMENT '訪問者',
  `ip_address` VARCHAR(39) NOT NULL DEFAULT '0.0.0.0' COMMENT 'IP位址',
  `member_agent` VARCHAR(255) NOT NULL DEFAULT 'unknown' COMMENT '會員使用裝置',
  `referrer_url` VARCHAR(2048) NULL DEFAULT NULL COMMENT '來源網址',
  `duration` INT(11) NOT NULL COMMENT '紀錄時間(秒)',
  `view_end` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '結束瀏覽時間',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '創建/瀏覽時間',
  PRIMARY KEY (`id`),
  INDEX `FK_log_page_views_log_i_products` (`pid`),
  CONSTRAINT `FK_log_page_views_log_w_pages` 
  FOREIGN KEY (`pid`)
  REFERENCES `w_pages` (`id`)
  ON UPDATE CASCADE
  ON DELETE CASCADE
) COMMENT='頁面瀏覽紀錄';

-- 商品瀏覽紀錄表
CREATE TABLE IF NOT EXISTS `log_product_views` (
  `id` BIGINT(19) UNSIGNED NOT NULL COMMENT '編號',
  `vid` BIGINT(19) UNSIGNED NOT NULL COMMENT '瀏覽頁面紀錄編號',
  `product_id` BIGINT(19) UNSIGNED NOT NULL COMMENT '瀏覽頁面紀錄編號',
  `operator` BIGINT(19) NOT NULL COMMENT '訪問者',
  `duration` INT(11) NOT NULL COMMENT '紀錄時間(秒)',
  `view_end` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '結束瀏覽時間',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '創建時間',
  PRIMARY KEY (`id`),
  INDEX `FK_log_page_views_log_page_views` (`vid`),
  INDEX `FK_s_product_page_views_i_products` (`product_id`),
  CONSTRAINT `FK_s_product_page_views_i_products` 
    FOREIGN KEY (`product_id`) 
    REFERENCES `i_products` (`id`)
    ON UPDATE CASCADE 
    ON DELETE CASCADE,
  CONSTRAINT `FK_s_page_views_log_page_views` 
    FOREIGN KEY (`vid`) 
    REFERENCES `log_page_views` (`id`) 
    ON UPDATE CASCADE 
    ON DELETE CASCADE
) COMMENT='商品瀏覽紀錄';

-- 搜尋紀錄表
CREATE TABLE IF NOT EXISTS `log_search` (
  `id` BIGINT(19) UNSIGNED NOT NULL COMMENT '編號',
  `wid` BIGINT(19) UNSIGNED NOT NULL COMMENT '網站編號',
  `operator` BIGINT(19) NOT NULL COMMENT '訪問者',
  `keyword` BIGINT(19) UNSIGNED NOT NULL COMMENT '關鍵字',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '創建時間',
  `related_products` LONGTEXT NOT NULL DEFAULT '[]' COMMENT '被推薦的商品',
  PRIMARY KEY (`id`),
  INDEX `FK_log_search_s_website` (`wid`),
  CONSTRAINT `FK_log_search_s_website`
  FOREIGN KEY (`wid`) 
  REFERENCES `s_website` (`id`) 
  ON UPDATE CASCADE 
  ON DELETE CASCADE
) COMMENT='搜尋紀錄';

-- 插入基本身份組
INSERT INTO `m_roles` (`id`, `wid`, `name`, `displayname`, `parent_id`) VALUES 
  (589605057335390208, 589605057335390208, 'everyone', '所有人', 589605057335390208),
  (589605057335390209, 589605057335390208, 'root', '超級管理員', 589605057335390208),
  (589605057335390210, 589605057335390208, 'admin', '管理員', 589605057335390208);

-- 插入最高權限用戶帳戶權限
INSERT INTO `m_member_roles` (`wid`, `mid`, `rid`) VALUES (589605057335390208, 589605057335390208, 589605057335390209);

-- 插入商品
INSERT INTO `i_products` (`id`, `wid`, `name`, `price`, `description`, `types`, `quantity`, `status`) VALUES 
  (589605308993630209, 589605057335390208,  '測試垮鬆', 5.00, '好ㄔ的垮鬆','["範例商品","可口垮鬆"]', -1, 1),
  (589605308993630210, 589605057335390208,  '測試垮鬆(草莓口味)', 6.00, '戀愛的ㄗ味','["範例商品","可口垮鬆"]', 0, 1),
  (589605308993630211, 589605057335390208,  '測試垮鬆(巧克力口味)', 6.00, '開心的港覺','["範例商品","可口垮鬆"]', 13, 1),
  (589605308993630212, 589605057335390208,  '測試垮鬆(芒果口味)', 8.00, '夏天ㄉ味道','["範例商品","可口垮鬆"]', 6, 1),
  (589605308993630213, 589605057335390208,  '測試垮鬆(紅豆口味)', 6.00, '思鄉惹','["範例商品","可口垮鬆"]', 7, 1),
  (589605308993630214, 589605057335390208,  '測試垮鬆(祕密特別版)', 8.00, '不能跟別人縮', '["範例商品","秘制商品"]', 5, 1),
  (589605308993630215, 589605057335390208,  '測試垮鬆(法國正統版本)', 15.00, 'Bonjour', '["範例商品","秘制商品"]', 999, 1);