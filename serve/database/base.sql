/*
 Navicat Premium Dump SQL

 Source Server         : test
 Source Server Type    : MySQL
 Source Server Version : 80027 (8.0.27-0ubuntu0.20.04.1)
 Source Host           : 192.168.6.202:3306
 Source Schema         : huoxing_link_test

 Target Server Type    : MySQL
 Target Server Version : 80027 (8.0.27-0ubuntu0.20.04.1)
 File Encoding         : 65001

 Date: 02/01/2025 16:27:20
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for cache
-- ----------------------------
DROP TABLE IF EXISTS `cache`;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of cache
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for cache_locks
-- ----------------------------
DROP TABLE IF EXISTS `cache_locks`;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of cache_locks
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for cards
-- ----------------------------
DROP TABLE IF EXISTS `cards`;
CREATE TABLE `cards` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态',
  `secret` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '卡密',
  `vip_id` int unsigned NOT NULL COMMENT '会员套餐',
  `month` int unsigned NOT NULL COMMENT '有效期/月',
  `used` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否已使用',
  `user_id` bigint unsigned DEFAULT NULL COMMENT '使用人ID',
  `exchange_time` timestamp NULL DEFAULT NULL COMMENT '兑换时间',
  `expiry_time` timestamp NOT NULL COMMENT '到效期时间',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cards_secret_unique` (`secret`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='兑换卡密';

-- ----------------------------
-- Records of cards
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for commission_logs
-- ----------------------------
DROP TABLE IF EXISTS `commission_logs`;
CREATE TABLE `commission_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `type` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '类型',
  `user_id` bigint unsigned NOT NULL,
  `children_user_id` bigint unsigned DEFAULT NULL,
  `amount` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_withdraw` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否提现',
  `payee` json DEFAULT NULL COMMENT '收款人信息',
  `status` tinyint unsigned NOT NULL COMMENT '状态',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `commission_logs_type_index` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='佣金记录';

-- ----------------------------
-- Records of commission_logs
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for domains
-- ----------------------------
DROP TABLE IF EXISTS `domains`;
CREATE TABLE `domains` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '链接',
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '标题',
  `enable` tinyint(1) NOT NULL COMMENT '是否启用',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='无风险域名';

-- ----------------------------
-- Records of domains
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for failed_jobs
-- ----------------------------
DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of failed_jobs
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for job_batches
-- ----------------------------
DROP TABLE IF EXISTS `job_batches`;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of job_batches
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for jobs
-- ----------------------------
DROP TABLE IF EXISTS `jobs`;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of jobs
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for link_visit_logs
-- ----------------------------
DROP TABLE IF EXISTS `link_visit_logs`;
CREATE TABLE `link_visit_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `link_id` bigint unsigned NOT NULL COMMENT '卡片链接id',
  `user_id` bigint unsigned NOT NULL COMMENT '卡片链接所属用户ID',
  `ip` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '访问IP',
  `device_uid` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '设备唯一标识',
  `cache` json DEFAULT NULL COMMENT '缓存链接数据',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='链接访问记录表';

-- ----------------------------
-- Records of link_visit_logs
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for links
-- ----------------------------
DROP TABLE IF EXISTS `links`;
CREATE TABLE `links` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL COMMENT '创建人id',
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '标题',
  `type` tinyint unsigned NOT NULL COMMENT '类型',
  `status` tinyint unsigned NOT NULL DEFAULT '0' COMMENT 'url状态',
  `icon` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '缩略图',
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '描述',
  `remark` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '备注',
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '短链接代码',
  `config` json NOT NULL COMMENT '链接配置',
  `price` bigint unsigned NOT NULL DEFAULT '0' COMMENT '价格',
  `expired_at` timestamp NULL DEFAULT NULL COMMENT '有效期',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `links_code_unique` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='链接表';

-- ----------------------------
-- Records of links
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for material_categories
-- ----------------------------
DROP TABLE IF EXISTS `material_categories`;
CREATE TABLE `material_categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '名称',
  `sort` int unsigned NOT NULL DEFAULT '0' COMMENT '权重',
  `type` tinyint NOT NULL DEFAULT '0' COMMENT '类型1系统内置',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='素材库分类';

-- ----------------------------
-- Records of material_categories
-- ----------------------------
BEGIN;
INSERT INTO `material_categories` (`id`, `name`, `sort`, `type`, `created_at`, `updated_at`) VALUES (1, '系统', 1, 1, '2024-09-29 09:42:37', '2024-09-29 09:42:37');
INSERT INTO `material_categories` (`id`, `name`, `sort`, `type`, `created_at`, `updated_at`) VALUES (2, '其他', 0, 0, '2024-09-29 09:42:44', '2024-09-29 09:42:44');
COMMIT;

-- ----------------------------
-- Table structure for materials
-- ----------------------------
DROP TABLE IF EXISTS `materials`;
CREATE TABLE `materials` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL COMMENT '用户ID',
  `cate_id` bigint unsigned NOT NULL DEFAULT '0' COMMENT '分类ID',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '名称',
  `path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '路径',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `materials_cate_id_index` (`cate_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='素材库';

-- ----------------------------
-- Records of materials
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for migrations
-- ----------------------------
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of migrations
-- ----------------------------
BEGIN;
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (1, '0001_01_01_000000_create_users_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (2, '0001_01_01_000001_create_cache_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (3, '0001_01_01_000002_create_jobs_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (4, '2019_12_14_000001_create_personal_access_tokens_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (5, '2024_04_10_095245_create_vip_packages_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (6, '2024_04_10_100349_create_commission_logs_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (7, '2024_04_10_101023_create_notices_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (8, '2024_04_10_101140_create_domains_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (9, '2024_04_10_101217_create_mini_programs_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (10, '2024_04_10_101507_create_links_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (11, '2024_04_10_103926_create_link_visit_logs_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (12, '2024_04_10_104736_create_payments_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (13, '2024_04_10_104736_create_sys_configs_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (14, '2024_04_15_140100_create_materials_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (15, '2024_04_15_143134_create_cards_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (16, '2024_04_16_084221_create_vip_logs_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (17, '2024_05_08_101122_create_material_categories_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (18, '2025_01_08_011255_create_versions_table', 1);
COMMIT;

-- ----------------------------
-- Table structure for mini_programs
-- ----------------------------
DROP TABLE IF EXISTS `mini_programs`;
CREATE TABLE `mini_programs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '小程序名称',
  `app_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'app_id',
  `secret` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'secret',
  `url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '首页',
  `type` bigint unsigned NOT NULL COMMENT '小程序类型',
  `user_id` bigint unsigned NOT NULL COMMENT '用户id',
  `is_pre_min` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否为平台小程序池小程序',
  `is_enable` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否启用',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='小程序配置';

-- ----------------------------
-- Records of mini_programs
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for notices
-- ----------------------------
DROP TABLE IF EXISTS `notices`;
CREATE TABLE `notices` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '标题',
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '内容',
  `show` tinyint(1) NOT NULL COMMENT '是否显示',
  `sort` int unsigned NOT NULL DEFAULT '0' COMMENT '权重',
  `type` bigint unsigned DEFAULT NULL COMMENT '类型',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='公告';

-- ----------------------------
-- Records of notices
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for payments
-- ----------------------------
DROP TABLE IF EXISTS `payments`;
CREATE TABLE `payments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `no` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '支付编号',
  `order_no` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '内部订单号',
  `channel` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '支付渠道',
  `amount` bigint unsigned NOT NULL COMMENT '支付金额/分',
  `type` tinyint unsigned NOT NULL COMMENT '支付类型: 1付款 2退款 3转账',
  `status` tinyint unsigned NOT NULL COMMENT '状态: 1处理中 2成功 3失败',
  `success_at` timestamp NULL DEFAULT NULL COMMENT '成功时间',
  `fail_at` timestamp NULL DEFAULT NULL COMMENT '失败时间',
  `expired_at` timestamp NULL DEFAULT NULL COMMENT '过期时间',
  `remark` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '备注',
  `notification_no` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '第三方支付通知单号',
  `notification_data` json DEFAULT NULL COMMENT '第三方支付通知原始数据',
  `job` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '回调任务',
  `attach` json DEFAULT NULL COMMENT '附加信息',
  `payment_id` bigint unsigned DEFAULT NULL COMMENT '退款时关联支付单ID',
  `merchant_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `merchant_id` bigint unsigned DEFAULT NULL,
  `payer_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payer_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `payments_no_unique` (`no`),
  KEY `payments_merchant_type_merchant_id_index` (`merchant_type`,`merchant_id`),
  KEY `payments_payer_type_payer_id_index` (`payer_type`,`payer_id`),
  KEY `payments_type_index` (`type`),
  KEY `payments_status_index` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='支付记录表';

-- ----------------------------
-- Records of payments
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for personal_access_tokens
-- ----------------------------
DROP TABLE IF EXISTS `personal_access_tokens`;
CREATE TABLE `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of personal_access_tokens
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for sys_configs
-- ----------------------------
DROP TABLE IF EXISTS `sys_configs`;
CREATE TABLE `sys_configs` (
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '唯一标识符',
  `value` longtext COLLATE utf8mb4_unicode_ci COMMENT '值',
  `desc` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '描述',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='系统配置表';

-- ----------------------------
-- Records of sys_configs
-- ----------------------------
BEGIN;
INSERT INTO `sys_configs` (`slug`, `value`, `desc`, `created_at`, `updated_at`) VALUES ('ali_sms_key', '', NULL, '2025-01-02 00:04:23', '2025-01-02 00:04:23');
INSERT INTO `sys_configs` (`slug`, `value`, `desc`, `created_at`, `updated_at`) VALUES ('ali_sms_secret', '', NULL, '2025-01-02 00:04:23', '2025-01-02 00:04:23');
INSERT INTO `sys_configs` (`slug`, `value`, `desc`, `created_at`, `updated_at`) VALUES ('ali_sms_sign_name', '', NULL, '2025-01-02 00:04:23', '2025-01-02 00:04:23');
INSERT INTO `sys_configs` (`slug`, `value`, `desc`, `created_at`, `updated_at`) VALUES ('give_vip_days', '7', '注册赠送会员有效期/天', NULL, NULL);
INSERT INTO `sys_configs` (`slug`, `value`, `desc`, `created_at`, `updated_at`) VALUES ('give_vip_id', '1', '赠送套餐', NULL, '2025-01-02 00:04:51');
INSERT INTO `sys_configs` (`slug`, `value`, `desc`, `created_at`, `updated_at`) VALUES ('is_give_vip', '1', '开启注册赠送会员', NULL, NULL);
INSERT INTO `sys_configs` (`slug`, `value`, `desc`, `created_at`, `updated_at`) VALUES ('mail_from_address', 'mayi@mayilink.cn', NULL, '2025-01-02 00:04:23', '2025-01-02 00:04:23');
INSERT INTO `sys_configs` (`slug`, `value`, `desc`, `created_at`, `updated_at`) VALUES ('mail_from_name', '火星块链', NULL, '2025-01-02 00:04:23', '2025-01-02 00:04:23');
INSERT INTO `sys_configs` (`slug`, `value`, `desc`, `created_at`, `updated_at`) VALUES ('mail_host', 'smtp.qq.com', NULL, '2025-01-02 00:04:23', '2025-01-02 00:04:23');
INSERT INTO `sys_configs` (`slug`, `value`, `desc`, `created_at`, `updated_at`) VALUES ('mail_password', 'mayi@mayilink.cn', NULL, '2025-01-02 00:04:23', '2025-01-02 00:04:23');
INSERT INTO `sys_configs` (`slug`, `value`, `desc`, `created_at`, `updated_at`) VALUES ('mail_port', '465', NULL, '2025-01-02 00:04:23', '2025-01-02 00:04:23');
INSERT INTO `sys_configs` (`slug`, `value`, `desc`, `created_at`, `updated_at`) VALUES ('mail_username', 'mayi@mayilink.cn', NULL, '2025-01-02 00:04:23', '2025-01-02 00:04:23');
INSERT INTO `sys_configs` (`slug`, `value`, `desc`, `created_at`, `updated_at`) VALUES ('member_pay_commission_1', '2', '一级分销消费返现', NULL, '2025-01-02 00:04:51');
INSERT INTO `sys_configs` (`slug`, `value`, `desc`, `created_at`, `updated_at`) VALUES ('member_pay_commission_2', '1', '二级分销消费返现', NULL, '2025-01-02 00:04:51');
INSERT INTO `sys_configs` (`slug`, `value`, `desc`, `created_at`, `updated_at`) VALUES ('recommend_commission', '', '邀请注册返现', NULL, NULL);
INSERT INTO `sys_configs` (`slug`, `value`, `desc`, `created_at`, `updated_at`) VALUES ('recommend_commission_1', '2', NULL, '2025-01-02 00:04:51', '2025-01-02 00:04:51');
INSERT INTO `sys_configs` (`slug`, `value`, `desc`, `created_at`, `updated_at`) VALUES ('recommend_commission_2', '1', NULL, '2025-01-02 00:04:51', '2025-01-02 00:04:51');
INSERT INTO `sys_configs` (`slug`, `value`, `desc`, `created_at`, `updated_at`) VALUES ('send_code_mode', '2', '发送验证码类型', NULL, '2025-01-02 00:04:23');
INSERT INTO `sys_configs` (`slug`, `value`, `desc`, `created_at`, `updated_at`) VALUES ('verify_code_is_open', '0', NULL, '2025-01-02 00:04:23', '2025-01-02 00:04:27');
INSERT INTO `sys_configs` (`slug`, `value`, `desc`, `created_at`, `updated_at`) VALUES ('web_site_bottom_logo', '2025/01/02/Z56tK9MA6ERvcHGmLDdLifSPyE8P2Ii7PQQOa5oI.png', NULL, '2025-01-02 00:04:23', '2025-01-02 00:07:20');
INSERT INTO `sys_configs` (`slug`, `value`, `desc`, `created_at`, `updated_at`) VALUES ('web_site_customer_service', '2025/01/02/oNnRBPp2TYBDDXNYttXEXxwCtG5a4T1odsHvoJez.png', NULL, '2025-01-02 00:04:23', '2025-01-02 00:07:20');
INSERT INTO `sys_configs` (`slug`, `value`, `desc`, `created_at`, `updated_at`) VALUES ('web_site_logo', '2025/01/02/xiKQ4jkl45ccYfbCBEXInSmxwqJmDqJ7OQHkdaz4.png', NULL, '2025-01-02 00:04:23', '2025-01-02 00:22:39');
INSERT INTO `sys_configs` (`slug`, `value`, `desc`, `created_at`, `updated_at`) VALUES ('web_site_title', '火星块链', NULL, '2025-01-02 00:04:23', '2025-01-02 00:04:23');
INSERT INTO `sys_configs` (`slug`, `value`, `desc`, `created_at`, `updated_at`) VALUES ('wechat_pay_app_id', '', NULL, '2025-01-02 00:04:23', '2025-01-02 00:04:23');
INSERT INTO `sys_configs` (`slug`, `value`, `desc`, `created_at`, `updated_at`) VALUES ('wechat_pay_certificate', '', NULL, '2025-01-02 00:04:23', '2025-01-02 00:04:23');
INSERT INTO `sys_configs` (`slug`, `value`, `desc`, `created_at`, `updated_at`) VALUES ('wechat_pay_mch_id', '', NULL, '2025-01-02 00:04:23', '2025-01-02 00:04:23');
INSERT INTO `sys_configs` (`slug`, `value`, `desc`, `created_at`, `updated_at`) VALUES ('wechat_pay_private_cert', '', NULL, '2025-01-02 00:04:23', '2025-01-02 00:04:23');
INSERT INTO `sys_configs` (`slug`, `value`, `desc`, `created_at`, `updated_at`) VALUES ('wechat_pay_secret_key', '', NULL, '2025-01-02 00:04:23', '2025-01-02 00:04:23');
INSERT INTO `sys_configs` (`slug`, `value`, `desc`, `created_at`, `updated_at`) VALUES ('system_version', '{\"version_number\":\"1.0.1\",\"version\":1}', NULL, NULL, '2025-01-08 01:53:06');
INSERT INTO `sys_configs` (`slug`, `value`, `desc`, `created_at`, `updated_at`) VALUES ('upgrade_domain', 'https://huoxing-test.o2.sapuai.com', NULL, NULL, '2025-01-08 01:53:06');
COMMIT;

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '账号',
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '密码',
  `status` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '状态',
  `type` bigint unsigned NOT NULL COMMENT '账号类型',
  `credit` bigint unsigned NOT NULL DEFAULT '0' COMMENT '账户剩余积分',
  `accumulate_credit` bigint unsigned NOT NULL DEFAULT '0' COMMENT '累计充值积分',
  `commission` bigint unsigned NOT NULL DEFAULT '0' COMMENT '未提现佣金',
  `accumulate_commission` bigint unsigned NOT NULL DEFAULT '0' COMMENT '累计佣金',
  `vip_id` bigint unsigned DEFAULT NULL COMMENT 'vip套餐ID',
  `agent_id` bigint unsigned DEFAULT NULL COMMENT '代理套餐ID',
  `level_id` bigint unsigned DEFAULT NULL COMMENT '代理级别ID',
  `start_at` timestamp NULL DEFAULT NULL COMMENT '套餐生效时间',
  `end_at` timestamp NULL DEFAULT NULL COMMENT '套餐过期时间',
  `parent_id` bigint unsigned DEFAULT NULL COMMENT '上级ID',
  `referral_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '推荐码',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_username_unique` (`username`),
  UNIQUE KEY `users_referral_code_unique` (`referral_code`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table structure for vip_logs
-- ----------------------------
DROP TABLE IF EXISTS `vip_logs`;
CREATE TABLE `vip_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL COMMENT '会员id',
  `payment_id` bigint unsigned DEFAULT NULL COMMENT '支付ID',
  `vip_id` bigint unsigned DEFAULT NULL COMMENT '会员ID',
  `status` tinyint unsigned NOT NULL COMMENT '状态',
  `start_at` timestamp NOT NULL COMMENT '开始时间',
  `end_at` timestamp NOT NULL COMMENT '结束时间',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='VIP购买记录表';

-- ----------------------------
-- Records of vip_logs
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for versions
-- ----------------------------
DROP TABLE IF EXISTS `versions`;
CREATE TABLE `versions` (
    `id` bigint unsigned NOT NULL AUTO_INCREMENT,
    `version_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '版本号',
    `version` int NOT NULL COMMENT '版本',
    `path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '安装包',
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of versions
-- ----------------------------
BEGIN;
INSERT INTO `versions` (`id`, `version_number`, `version`, `path`, `created_at`, `updated_at`) VALUES (1, '1.0.1', 1, 'zip.zip', '2025-01-08 01:32:12', '2025-01-08 01:32:12');
COMMIT;

-- ----------------------------
-- Table structure for vip_packages
-- ----------------------------
DROP TABLE IF EXISTS `vip_packages`;
CREATE TABLE `vip_packages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '套餐名称',
  `price` bigint unsigned NOT NULL COMMENT '价格/月',
  `level` tinyint unsigned NOT NULL COMMENT '等级',
  `config` json NOT NULL COMMENT '权益配置',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='vip套餐配置';

-- ----------------------------
-- Records of vip_packages
-- ----------------------------
BEGIN;
INSERT INTO `vip_packages` (`id`, `name`, `price`, `level`, `config`, `created_at`, `updated_at`) VALUES (1, '体验套餐', 0, 0, '{\"pre_min\": false, \"support\": true, \"uv_limit\": 9, \"cur_index\": false, \"allow_type\": {\"CLI_QR\": false, \"KING_DOC\": false, \"WORK_WECHAT\": false, \"LANDING_MINI\": false, \"MINI_PROGRAM\": true}, \"count_limit\": 1, \"min_count_limit\": 1, \"min_disabled_check\": true}', '2024-04-11 12:49:52', '2024-09-29 08:28:55');
INSERT INTO `vip_packages` (`id`, `name`, `price`, `level`, `config`, `created_at`, `updated_at`) VALUES (2, '初级会员', 4300, 1, '{\"pre_min\": true, \"support\": true, \"uv_limit\": 50, \"cur_index\": true, \"allow_type\": {\"QR_QQ\": true, \"CLI_QR\": true, \"KING_DOC\": true, \"WORK_WECHAT\": true, \"LANDING_MINI\": true, \"MINI_PROGRAM\": true}, \"count_limit\": 5, \"min_count_limit\": 5, \"min_disabled_check\": true}', '2024-04-17 14:27:19', '2025-01-04 02:59:09');
INSERT INTO `vip_packages` (`id`, `name`, `price`, `level`, `config`, `created_at`, `updated_at`) VALUES (3, '高级会员', 29800, 2, '{\"pre_min\": true, \"support\": true, \"uv_limit\": 100000, \"cur_index\": true, \"allow_type\": {\"CLI_QR\": true, \"KING_DOC\": true, \"WORK_WECHAT\": true, \"LANDING_MINI\": true, \"MINI_PROGRAM\": true}, \"count_limit\": 50, \"min_count_limit\": 10, \"min_disabled_check\": true}', '2024-04-22 17:40:28', '2024-09-29 08:29:49');
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;
