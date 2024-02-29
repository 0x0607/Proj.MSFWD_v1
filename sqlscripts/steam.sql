-- -------------------------------------------------------
-- lastUpdate 11/21/2023
-- Steam串接服務
-- By MaizuRoad
-- -------------------------------------------------------
-- CREATE DATABASE IF NOT EXISTS `TwilightMart`;
USE `TwilightMart`;
CREATE TABLE `api_steamauth` (
    `steam_id` VARCHAR(17) NOT NULL COMMENT 'STEAM帳戶編號',
    `mid` BIGINT(19) UNSIGNED NOT NULL COMMENT '使用者帳戶編號',
    `timecreated` INT(10) NULL DEFAULT NULL COMMENT 'STEAM帳戶創建時間',
    `avatar` VARCHAR(255) NULL DEFAULT NULL COMMENT 'STEAM頭貼',
    `profileurl` VARCHAR(255) NULL DEFAULT NULL COMMENT 'STEAM頁面連結',
    PRIMARY KEY (`steam_id`),
    INDEX `mid` (`mid`),
    CONSTRAINT `FK_api_steam_m_members`
        FOREIGN KEY (`mid`) 
        REFERENCES `m_members` (`id`) 
        ON UPDATE CASCADE 
        ON DELETE NO ACTION
) COMMENT 'STEAM互動資料表';
