/*
 Navicat Premium Data Transfer

 Source Server         : test
 Source Server Type    : MySQL
 Source Server Version : 80027 (8.0.27-0ubuntu0.20.04.1)
 Source Host           : 192.168.6.202:3306
 Source Schema         : short-link-api

 Target Server Type    : MySQL
 Target Server Version : 80027 (8.0.27-0ubuntu0.20.04.1)
 File Encoding         : 65001

 Date: 22/05/2024 16:39:23
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;


-- ----------------------------
-- Records of packages
-- ----------------------------
BEGIN;
INSERT INTO `vip_packages` (`id`, `name`, `price`, `level`, `config`, `created_at`, `updated_at`) VALUES (1, '体验套餐', 0, 0, '{\"pre_min\": false, \"support\": true, \"uv_limit\": 9, \"cur_index\": false, \"allow_type\": {\"CLI_QR\": false, \"KING_DOC\": false, \"WORK_WECHAT\": false, \"LANDING_MINI\": false, \"MINI_PROGRAM\": true}, \"count_limit\": 1, \"min_count_limit\": 1, \"min_disabled_check\": true}', '2024-04-11 12:49:52', '2024-09-29 08:28:55');
INSERT INTO `vip_packages` (`id`, `name`, `price`, `level`, `config`, `created_at`, `updated_at`) VALUES (2, '初级会员', 4300, 1, '{\"pre_min\": true, \"support\": true, \"uv_limit\": 50, \"cur_index\": true, \"allow_type\": {\"CLI_QR\": true, \"KING_DOC\": true, \"WORK_WECHAT\": true, \"LANDING_MINI\": true, \"MINI_PROGRAM\": true}, \"count_limit\": 5, \"min_count_limit\": 5, \"min_disabled_check\": true}', '2024-04-17 14:27:19', '2024-09-29 08:29:32');
INSERT INTO `vip_packages` (`id`, `name`, `price`, `level`, `config`, `created_at`, `updated_at`) VALUES (3, '高级会员', 29800, 2, '{\"pre_min\": true, \"support\": true, \"uv_limit\": 100000, \"cur_index\": true, \"allow_type\": {\"CLI_QR\": true, \"KING_DOC\": true, \"WORK_WECHAT\": true, \"LANDING_MINI\": true, \"MINI_PROGRAM\": true}, \"count_limit\": 50, \"min_count_limit\": 10, \"min_disabled_check\": true}', '2024-04-22 17:40:28', '2024-09-29 08:29:49');
INSERT INTO `material_categories` (`id`, `name`, `sort`, `type`, `created_at`, `updated_at`) VALUES (1, '系统', 1, 1, '2024-09-29 09:42:37', '2024-09-29 09:42:37');
INSERT INTO `material_categories` (`id`, `name`, `sort`, `type`, `created_at`, `updated_at`) VALUES (2, '其他', 0, 0, '2024-09-29 09:42:44', '2024-09-29 09:42:44');
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;
