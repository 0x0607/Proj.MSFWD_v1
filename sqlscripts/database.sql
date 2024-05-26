-- --------------------------------------------------------
-- 伺服器版本:                10.3.39-MariaDB - MariaDB Server
-- 伺服器操作系統:            Linux
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `api_steamauth` (
  `steam_id` char(18) NOT NULL COMMENT 'SteamId',
  `mid` bigint(19) unsigned NOT NULL COMMENT 'ID',
  PRIMARY KEY (`steam_id`),
  KEY `mid` (`mid`),
  CONSTRAINT `FK_api_steam_m_members` FOREIGN KEY (`mid`) REFERENCES `m_members` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `api_virtual_product` (
  `product_id` bigint(19) unsigned NOT NULL COMMENT '商品ID',
  `action` char(128) NOT NULL DEFAULT 'unknown',
  `product` text NOT NULL DEFAULT '{"GroupID":"default","Balance":0}',
  PRIMARY KEY (`product_id`),
  CONSTRAINT `FK_api_unturned_product_i_products` FOREIGN KEY (`product_id`) REFERENCES `i_products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='虛擬商品';

CREATE TABLE IF NOT EXISTS `bak_page_component` (
  `id` bigint(19) unsigned NOT NULL COMMENT 'ID',
  `pid` bigint(19) unsigned NOT NULL COMMENT '頁面ID',
  `cid` int(11) NOT NULL COMMENT '元件ID',
  `displayname` varchar(128) NOT NULL COMMENT '名稱',
  `position` int(11) NOT NULL COMMENT '位置',
  `params` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT '元件參數',
  `status` int(3) NOT NULL DEFAULT 1 COMMENT '啟用狀態1啟用 0關閉',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT '創建時間',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT '更新時間',
  PRIMARY KEY (`id`),
  KEY `fk_component_id` (`cid`),
  KEY `fk_page_id` (`pid`),
  CONSTRAINT `fk_component_id` FOREIGN KEY (`cid`) REFERENCES `s_components` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_page_id` FOREIGN KEY (`pid`) REFERENCES `w_pages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='元件與頁面關聯表';

CREATE TABLE IF NOT EXISTS `c_msgboard` (
  `id` bigint(19) unsigned NOT NULL COMMENT 'ID',
  `wid` bigint(19) unsigned NOT NULL,
  `tid` bigint(19) unsigned DEFAULT NULL,
  `mid` bigint(19) unsigned NOT NULL,
  `author` varchar(64) NOT NULL DEFAULT 'Anonymous',
  `title` varchar(64) NOT NULL DEFAULT '未命名標題',
  `message` text DEFAULT NULL,
  `weight` int(16) NOT NULL DEFAULT 0 COMMENT '越大排序越前面',
  `status` int(3) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT '創建時間',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT '更新時間',
  PRIMARY KEY (`id`),
  KEY `wid` (`wid`),
  KEY `tid` (`tid`),
  KEY `mid` (`mid`),
  CONSTRAINT `FK_c_msgboard_c_msgboard` FOREIGN KEY (`mid`) REFERENCES `m_members` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_c_msgboard_s_website` FOREIGN KEY (`wid`) REFERENCES `s_website` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `c_msgboard_type` (
  `id` bigint(19) unsigned NOT NULL COMMENT 'ID',
  `wid` bigint(19) unsigned NOT NULL,
  `type` varchar(64) NOT NULL DEFAULT '未命名分類',
  `status` int(3) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT '創建時間',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT '更新時間',
  PRIMARY KEY (`id`),
  KEY `wid` (`wid`),
  CONSTRAINT `FK_c_msgboard_type_s_website` FOREIGN KEY (`wid`) REFERENCES `s_website` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `i_cart` (
  `id` bigint(19) unsigned NOT NULL COMMENT '購物車ID',
  `product_id` bigint(19) unsigned NOT NULL COMMENT '商品ID',
  `wid` bigint(19) unsigned NOT NULL COMMENT '網站ID',
  `mid` bigint(19) unsigned NOT NULL COMMENT '會員ID',
  `specification` longtext NOT NULL DEFAULT '[]' COMMENT '商品規格',
  `color` longtext NOT NULL DEFAULT '[]' COMMENT '商品顏色',
  `quantity` int(11) NOT NULL DEFAULT 1 COMMENT '商品數量',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT '創建時間',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT '更新時間',
  PRIMARY KEY (`id`),
  KEY `FK_i_cart_s_website` (`wid`),
  KEY `FK_i_cart_m_members` (`mid`),
  KEY `FK_i_cart_i_products` (`product_id`),
  CONSTRAINT `FK_i_cart_i_products` FOREIGN KEY (`product_id`) REFERENCES `i_products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_i_cart_m_members` FOREIGN KEY (`mid`) REFERENCES `m_members` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_i_cart_s_website` FOREIGN KEY (`wid`) REFERENCES `s_website` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='購物車資料表';

CREATE TABLE IF NOT EXISTS `i_products` (
  `id` bigint(19) unsigned NOT NULL COMMENT '商品ID',
  `wid` bigint(19) unsigned NOT NULL COMMENT '網站ID',
  `name` varchar(255) NOT NULL COMMENT '商品名稱',
  `description` text DEFAULT NULL COMMENT '商品描述',
  `types` longtext NOT NULL DEFAULT '[]' COMMENT '商品分類',
  `tags` longtext NOT NULL DEFAULT '[]' COMMENT '商品標籤',
  `specification` longtext NOT NULL DEFAULT '[]' COMMENT '商品規格',
  `color` longtext NOT NULL DEFAULT '[]' COMMENT '商品顏色',
  `images` longtext NOT NULL DEFAULT '["/assets/images/quaso.png"]' COMMENT '商品圖片',
  `price` decimal(10,2) NOT NULL COMMENT '商品價格',
  `quantity` int(11) NOT NULL DEFAULT 0 COMMENT '商品數量',
  `status` tinyint(3) NOT NULL DEFAULT 0 COMMENT '商品狀態：0-刪除;1-上架;2-下架',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT '創建時間',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT '更新時間',
  PRIMARY KEY (`id`),
  KEY `fk_i_products_websiteId_id` (`wid`),
  CONSTRAINT `fk_i_products_websiteId_id` FOREIGN KEY (`wid`) REFERENCES `s_website` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='商品資料表';

CREATE TABLE IF NOT EXISTS `log_page_views` (
  `id` bigint(19) unsigned NOT NULL COMMENT '編號',
  `pid` bigint(19) unsigned NOT NULL COMMENT '頁面編號',
  `operator` bigint(19) NOT NULL COMMENT '訪問者',
  `ip_address` varchar(39) NOT NULL DEFAULT '0.0.0.0' COMMENT 'IP位址',
  `member_agent` varchar(255) NOT NULL DEFAULT 'unknown' COMMENT '會員使用裝置',
  `referrer_url` varchar(2048) DEFAULT NULL COMMENT '來源網址',
  `duration` int(11) NOT NULL COMMENT '紀錄時間(秒)',
  `view_end` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT '結束瀏覽時間',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT '創建/瀏覽時間',
  PRIMARY KEY (`id`),
  KEY `FK_log_page_views_log_i_products` (`pid`),
  CONSTRAINT `FK_log_page_views_log_w_pages` FOREIGN KEY (`pid`) REFERENCES `w_pages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='頁面瀏覽紀錄';

CREATE TABLE IF NOT EXISTS `log_product_views` (
  `id` bigint(19) unsigned NOT NULL COMMENT '編號',
  `vid` bigint(19) unsigned NOT NULL COMMENT '瀏覽頁面紀錄編號',
  `product_id` bigint(19) unsigned NOT NULL COMMENT '瀏覽頁面紀錄編號',
  `operator` bigint(19) NOT NULL COMMENT '訪問者',
  `duration` int(11) NOT NULL COMMENT '紀錄時間(秒)',
  `view_end` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT '結束瀏覽時間',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT '創建時間',
  PRIMARY KEY (`id`),
  KEY `FK_log_page_views_log_page_views` (`vid`),
  KEY `FK_s_product_page_views_i_products` (`product_id`),
  CONSTRAINT `FK_log_product_views_log_page_views` FOREIGN KEY (`vid`) REFERENCES `log_page_views` (`id`),
  CONSTRAINT `FK_s_product_page_views_i_products` FOREIGN KEY (`product_id`) REFERENCES `i_products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='商品瀏覽紀錄';

CREATE TABLE IF NOT EXISTS `log_search` (
  `id` bigint(19) unsigned NOT NULL COMMENT '編號',
  `wid` bigint(19) unsigned NOT NULL COMMENT '網站編號',
  `operator` bigint(19) NOT NULL COMMENT '訪問者',
  `keyword` bigint(19) unsigned NOT NULL COMMENT '關鍵字',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT '創建時間',
  `related_products` longtext NOT NULL DEFAULT '[]' COMMENT '被推薦的商品',
  PRIMARY KEY (`id`),
  KEY `FK_log_search_s_website` (`wid`),
  CONSTRAINT `FK_log_search_s_website` FOREIGN KEY (`wid`) REFERENCES `s_website` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='搜尋紀錄';

CREATE TABLE IF NOT EXISTS `log_system` (
  `id` bigint(19) unsigned NOT NULL COMMENT '操作編號',
  `wid` bigint(19) unsigned NOT NULL COMMENT '網站編號',
  `operator` bigint(19) NOT NULL COMMENT '操作者',
  `ip_address` varchar(39) NOT NULL DEFAULT '0.0.0.0' COMMENT 'IP位址',
  `status` char(4) NOT NULL DEFAULT '0|NO' COMMENT '類型 0|NO: 無效操作 1|OK: 存取成功',
  `action` char(64) NOT NULL DEFAULT 'UNKNOW' COMMENT '操作',
  `reason` varchar(1024) NOT NULL COMMENT '原因',
  `hash` char(64) NOT NULL COMMENT '雜湊',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT '創建時間',
  PRIMARY KEY (`id`),
  KEY `FK_s_system_log_s_website` (`wid`),
  CONSTRAINT `FK_s_system_log_s_website` FOREIGN KEY (`wid`) REFERENCES `s_website` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='系統操作紀錄';

CREATE TABLE IF NOT EXISTS `m_attendance` (
  `wid` bigint(19) unsigned NOT NULL COMMENT '網站編號',
  `mid` bigint(19) unsigned NOT NULL COMMENT '會員編號',
  `day` int(3) NOT NULL DEFAULT 0 COMMENT '簽到天數',
  `nonstop_day` int(3) NOT NULL DEFAULT 0 COMMENT '連續簽到天數',
  `last_sign_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT '最後簽到日期',
  KEY `wid` (`wid`),
  KEY `mid` (`mid`),
  CONSTRAINT `FK_m_attendance_m_members` FOREIGN KEY (`mid`) REFERENCES `m_members` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_m_attendance_s_website` FOREIGN KEY (`wid`) REFERENCES `s_website` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `m_members` (
  `id` bigint(19) unsigned NOT NULL COMMENT 'ID',
  `account` varchar(20) NOT NULL COMMENT '唯一識別項帳號',
  `nickname` varchar(18) NOT NULL COMMENT '暱稱',
  `password` varchar(255) DEFAULT NULL COMMENT '密碼',
  `last_ip_address` varchar(45) NOT NULL DEFAULT '0.0.0.0' COMMENT '使用者最後登入IP位址',
  `status` int(3) NOT NULL DEFAULT 1 COMMENT '帳號狀態1啟用 0停用',
  `hashkey` char(32) DEFAULT NULL,
  `hashiv` char(16) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT '創建時間',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT '更新時間',
  PRIMARY KEY (`id`),
  KEY `account` (`account`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='會員資料表';

CREATE TABLE IF NOT EXISTS `m_members_profile` (
  `mid` bigint(19) unsigned NOT NULL COMMENT 'ID',
  `introduction` text NOT NULL DEFAULT '這個人很懶，什麼都沒寫' COMMENT '個人介紹',
  `theme` varchar(7) DEFAULT NULL COMMENT '顏色風格',
  `avatar` varchar(255) NOT NULL DEFAULT '/assets/images/quaso.png' COMMENT '頭貼',
  `background` varchar(255) DEFAULT NULL COMMENT '自訂背景',
  PRIMARY KEY (`mid`),
  CONSTRAINT `fk_m_members` FOREIGN KEY (`mid`) REFERENCES `m_members` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='會員資料表';

CREATE TABLE IF NOT EXISTS `m_member_roles` (
  `mid` bigint(19) unsigned NOT NULL COMMENT '會員ID',
  `rid` bigint(19) unsigned NOT NULL COMMENT '身份組',
  KEY `member_roles_fk_role_id` (`rid`),
  KEY `member_roles_fk_member_id` (`mid`),
  CONSTRAINT `member_roles_fk_member_id` FOREIGN KEY (`mid`) REFERENCES `m_members` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `member_roles_fk_role_id` FOREIGN KEY (`rid`) REFERENCES `m_roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='會員資料表';

CREATE TABLE IF NOT EXISTS `m_permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `name` varchar(100) NOT NULL COMMENT '權限名稱',
  `displayname` varchar(100) NOT NULL COMMENT '權限顯示名稱',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='權限資料表';

CREATE TABLE IF NOT EXISTS `m_roles` (
  `id` bigint(19) unsigned NOT NULL COMMENT 'ID',
  `wid` bigint(19) unsigned NOT NULL COMMENT '網站ID',
  `displayname` varchar(100) NOT NULL COMMENT '身份組顯示名稱',
  `parent_id` bigint(19) unsigned DEFAULT NULL COMMENT '父身份組',
  `permissions` int(50) NOT NULL DEFAULT 0 COMMENT '權限表每個01對應著是否具有權限0x1代表擁有所有權限',
  `status` int(3) NOT NULL DEFAULT 1 COMMENT '啟用狀態1啟用 0關閉 2系統用戶(無法刪除)',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT '創建時間',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT '更新時間',
  PRIMARY KEY (`id`),
  KEY `FK_m_roles_s_website` (`wid`),
  CONSTRAINT `FK_m_roles_s_website` FOREIGN KEY (`wid`) REFERENCES `s_website` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='角色資料表';

CREATE TABLE IF NOT EXISTS `p_orders` (
  `id` char(64) NOT NULL COMMENT '訂單ID',
  `mid` bigint(19) unsigned NOT NULL COMMENT '會員ID',
  `wid` bigint(19) unsigned NOT NULL COMMENT '網站ID',
  `amount` decimal(10,2) NOT NULL COMMENT '交易金額',
  `hash` char(40) NOT NULL COMMENT '雜湊驗證碼',
  `status` varchar(8) NOT NULL DEFAULT '0|NO' COMMENT '狀態碼',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT '創建時間',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT '更新時間',
  PRIMARY KEY (`id`),
  KEY `fk_p_orders_member_id` (`mid`),
  KEY `fk_p_orders_websiteId_id` (`wid`),
  CONSTRAINT `fk_p_orders_member_id` FOREIGN KEY (`mid`) REFERENCES `m_members` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_p_orders_websiteId_id` FOREIGN KEY (`wid`) REFERENCES `s_website` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='訂單資訊表';

CREATE TABLE IF NOT EXISTS `p_order_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `order_id` char(64) NOT NULL COMMENT '訂單ID',
  `product_id` bigint(19) unsigned NOT NULL COMMENT '商品ID',
  `types` longtext NOT NULL DEFAULT '[]' COMMENT '商品分類',
  `tags` longtext NOT NULL DEFAULT '[]' COMMENT '商品標籤',
  `specification` longtext NOT NULL DEFAULT '[]' COMMENT '商品規格',
  `color` longtext NOT NULL DEFAULT '[]' COMMENT '商品顏色',
  `quantity` int(11) NOT NULL COMMENT '商品數量',
  `price` decimal(10,2) NOT NULL COMMENT '商品價格',
  PRIMARY KEY (`id`),
  KEY `fk_p_order_items_order_id` (`order_id`),
  KEY `fk_p_order_items_product_id` (`product_id`),
  CONSTRAINT `fk_p_order_items_order_id` FOREIGN KEY (`order_id`) REFERENCES `p_orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_p_order_items_product_id` FOREIGN KEY (`product_id`) REFERENCES `i_products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=82 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='訂單商品明細表';

CREATE TABLE IF NOT EXISTS `p_paid` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `order_id` char(64) NOT NULL COMMENT '訂單ID',
  `trade_no` char(20) NOT NULL COMMENT '交易流水號',
  `message` char(255) NOT NULL COMMENT '付款訊息',
  `amount` int(16) NOT NULL COMMENT '交易金額',
  `payment_type` char(10) NOT NULL DEFAULT 'NaN' COMMENT '支付方式',
  `ip_address` varchar(45) NOT NULL COMMENT '交易IP位址',
  `status` varchar(8) NOT NULL DEFAULT '0|NO' COMMENT '狀態碼',
  `pay_time` datetime NOT NULL COMMENT '交易時間',
  PRIMARY KEY (`id`),
  KEY `fk_p_paid_order_id` (`order_id`),
  CONSTRAINT `fk_p_paid_order_id` FOREIGN KEY (`order_id`) REFERENCES `p_orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='訂單交易資訊';

CREATE TABLE IF NOT EXISTS `s_components` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '元件ID',
  `name` varchar(50) NOT NULL COMMENT '元件名稱',
  `description` text DEFAULT NULL COMMENT '元件描述',
  `params` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT '元件預設參數',
  `size_x` int(4) NOT NULL DEFAULT 0 COMMENT '元件大小_X (0代表滿版)',
  `fill_y` int(1) NOT NULL DEFAULT 0 COMMENT '元件大小_Y (1代表滿版)',
  `is_absolute_position` int(1) NOT NULL DEFAULT 0 COMMENT '是否是絕對位址',
  `template` longtext NOT NULL DEFAULT 'unknown' COMMENT '元件樣板',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT '創建時間',
  PRIMARY KEY (`id`),
  KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='元件資料表';

CREATE TABLE IF NOT EXISTS `s_newebpay` (
  `wid` bigint(19) unsigned NOT NULL COMMENT '網站ID',
  `store_id` varchar(12) NOT NULL COMMENT '商店ID',
  `store_hash_key` varchar(32) NOT NULL COMMENT '商店雜湊KEY',
  `store_hash_iv` varchar(16) NOT NULL COMMENT '商店雜湊IV',
  `store_return_url` varchar(255) NOT NULL COMMENT '交易完成返回網址',
  `store_client_back_url` varchar(255) NOT NULL DEFAULT 'https://www.youtube.com/watch?v=dQw4w9WgXcQ' COMMENT '交易完成客端返回網址',
  PRIMARY KEY (`wid`),
  CONSTRAINT `fk_s_website` FOREIGN KEY (`wid`) REFERENCES `s_website` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='藍新支付';

CREATE TABLE IF NOT EXISTS `s_website` (
  `id` bigint(19) unsigned NOT NULL,
  `prefix` varchar(8) NOT NULL COMMENT '網站代號',
  `domain` varchar(64) NOT NULL COMMENT '網域名稱',
  `displayname` varchar(20) NOT NULL COMMENT '網站別名',
  `distribution` varchar(32) NOT NULL DEFAULT 'Taiwan' COMMENT '地理位置',
  `icon` varchar(255) DEFAULT NULL COMMENT '圖示',
  `nav` text NOT NULL DEFAULT '{}' COMMENT '導覽列設置',
  `theme_version` int(3) NOT NULL DEFAULT 1 COMMENT '色系版本',
  `status` int(3) NOT NULL DEFAULT 1 COMMENT '啟用狀態1啟用 0關閉',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT '創建時間',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT '更新時間',
  PRIMARY KEY (`id`),
  KEY `prefix` (`prefix`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='網站資訊';

CREATE TABLE IF NOT EXISTS `w_files` (
  `id` bigint(19) unsigned NOT NULL COMMENT '檔案名稱及編號',
  `original_name` varchar(255) NOT NULL COMMENT '檔案原始名稱',
  `path` varchar(255) NOT NULL COMMENT '檔案路徑',
  `size` int(10) unsigned NOT NULL COMMENT '檔案大小',
  `type` varchar(100) NOT NULL COMMENT '檔案類型jpg, jepg, png...etc',
  `uploaded_at` datetime NOT NULL DEFAULT current_timestamp() COMMENT '檔案上傳時間',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='檔案管理系統';

CREATE TABLE IF NOT EXISTS `w_pages` (
  `id` bigint(19) unsigned NOT NULL COMMENT '頁面ID',
  `wid` bigint(19) unsigned NOT NULL COMMENT '網站編號ID',
  `name` varchar(64) NOT NULL COMMENT '連結名稱',
  `displayname` varchar(64) NOT NULL DEFAULT '未命名頁面' COMMENT '頁面標題',
  `description` text DEFAULT NULL COMMENT '頁面描述',
  `nav` int(3) NOT NULL DEFAULT 1 COMMENT '啟用導覽列款式 0代表無',
  `status` int(3) NOT NULL DEFAULT 1 COMMENT '啟用狀態2系統頁面 1啟用 0關閉 -1刪除',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT '創建時間',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT '更新時間',
  PRIMARY KEY (`id`),
  KEY `FK_w_pages_s_website` (`wid`),
  CONSTRAINT `FK_w_pages_s_website` FOREIGN KEY (`wid`) REFERENCES `s_website` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='頁面資料表';