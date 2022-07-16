/*
 Navicat Premium Data Transfer

 Source Server         : local
 Source Server Type    : MySQL
 Source Server Version : 100607
 Source Host           : 127.0.0.1:3306
 Source Schema         : sejong2

 Target Server Type    : MySQL
 Target Server Version : 100607
 File Encoding         : 65001

 Date: 16/07/2022 22:00:39
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for province
-- ----------------------------
-- DROP TABLE IF EXISTS `province`;
-- CREATE TABLE `province` (
--   `id` bigint(20) NOT NULL AUTO_INCREMENT,
--   `name_id` varchar(255) DEFAULT NULL,
--   `name_en` varchar(255) DEFAULT NULL,
--   `created_at` timestamp NULL DEFAULT NULL,
--   `updated_at` timestamp NULL DEFAULT NULL,
--   PRIMARY KEY (`id`)
-- ) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of province
-- ----------------------------
BEGIN;
INSERT INTO `province` VALUES (1, 'Bali', 'Bali', '2022-07-16 21:28:26', '2022-07-16 21:28:26');
INSERT INTO `province` VALUES (2, 'Bangka Belitung', 'Bangka Belitung', '2022-07-16 21:28:26', '2022-07-16 21:28:26');
INSERT INTO `province` VALUES (3, 'Banten', 'Banten', '2022-07-16 21:28:26', '2022-07-16 21:28:26');
INSERT INTO `province` VALUES (4, 'Bengkulu', 'Bengkulu', '2022-07-16 21:28:26', '2022-07-16 21:28:26');
INSERT INTO `province` VALUES (5, 'DI Yogyakarta', 'DI Yogyakarta', '2022-07-16 21:28:26', '2022-07-16 21:28:26');
INSERT INTO `province` VALUES (6, 'DKI Jakarta', 'DKI Jakarta', '2022-07-16 21:28:27', '2022-07-16 21:28:27');
INSERT INTO `province` VALUES (7, 'Gorontalo', 'Gorontalo', '2022-07-16 21:28:27', '2022-07-16 21:28:27');
INSERT INTO `province` VALUES (8, 'Jambi', 'Jambi', '2022-07-16 21:28:27', '2022-07-16 21:28:27');
INSERT INTO `province` VALUES (9, 'Jawa Barat', 'Jawa Barat', '2022-07-16 21:28:27', '2022-07-16 21:28:27');
INSERT INTO `province` VALUES (10, 'Jawa Tengah', 'Jawa Tengah', '2022-07-16 21:28:27', '2022-07-16 21:28:27');
INSERT INTO `province` VALUES (11, 'Jawa Timur', 'Jawa Timur', '2022-07-16 21:28:27', '2022-07-16 21:28:27');
INSERT INTO `province` VALUES (12, 'Kalimantan Barat', 'Kalimantan Barat', '2022-07-16 21:28:27', '2022-07-16 21:28:27');
INSERT INTO `province` VALUES (13, 'Kalimantan Selatan', 'Kalimantan Selatan', '2022-07-16 21:28:27', '2022-07-16 21:28:27');
INSERT INTO `province` VALUES (14, 'Kalimantan Tengah', 'Kalimantan Tengah', '2022-07-16 21:28:27', '2022-07-16 21:28:27');
INSERT INTO `province` VALUES (15, 'Kalimantan Timur', 'Kalimantan Timur', '2022-07-16 21:28:28', '2022-07-16 21:28:28');
INSERT INTO `province` VALUES (16, 'Kalimantan Utara', 'Kalimantan Utara', '2022-07-16 21:28:28', '2022-07-16 21:28:28');
INSERT INTO `province` VALUES (17, 'Kepulauan Riau', 'Kepulauan Riau', '2022-07-16 21:28:28', '2022-07-16 21:28:28');
INSERT INTO `province` VALUES (18, 'Lampung', 'Lampung', '2022-07-16 21:28:28', '2022-07-16 21:28:28');
INSERT INTO `province` VALUES (19, 'Maluku Utara', 'Maluku Utara', '2022-07-16 21:28:28', '2022-07-16 21:28:28');
INSERT INTO `province` VALUES (20, 'Maluku', 'Maluku', '2022-07-16 21:28:28', '2022-07-16 21:28:28');
INSERT INTO `province` VALUES (21, 'Nanggroe Aceh Darussalam', 'Nanggroe Aceh Darussalam', '2022-07-16 21:28:28', '2022-07-16 21:28:28');
INSERT INTO `province` VALUES (22, 'Nusa Tenggara Barat', 'Nusa Tenggara Barat', '2022-07-16 21:28:28', '2022-07-16 21:28:28');
INSERT INTO `province` VALUES (23, 'Nusa Tenggara Timur', 'Nusa Tenggara Timur', '2022-07-16 21:28:28', '2022-07-16 21:28:28');
INSERT INTO `province` VALUES (24, 'Papua Barat', 'Papua Barat', '2022-07-16 21:28:28', '2022-07-16 21:28:28');
INSERT INTO `province` VALUES (25, 'Papua', 'Papua', '2022-07-16 21:28:28', '2022-07-16 21:28:28');
INSERT INTO `province` VALUES (26, 'Riau', 'Riau', '2022-07-16 21:28:28', '2022-07-16 21:28:28');
INSERT INTO `province` VALUES (27, 'Sulawesi Barat', 'Sulawesi Barat', '2022-07-16 21:28:29', '2022-07-16 21:28:29');
INSERT INTO `province` VALUES (28, 'Sulawesi Selatan', 'Sulawesi Selatan', '2022-07-16 21:28:29', '2022-07-16 21:28:29');
INSERT INTO `province` VALUES (29, 'Sulawesi Tengah', 'Sulawesi Tengah', '2022-07-16 21:28:29', '2022-07-16 21:28:29');
INSERT INTO `province` VALUES (30, 'Sulawesi Tenggara', 'Sulawesi Tenggara', '2022-07-16 21:28:29', '2022-07-16 21:28:29');
INSERT INTO `province` VALUES (31, 'Sulawesi Utara', 'Sulawesi Utara', '2022-07-16 21:28:29', '2022-07-16 21:28:29');
INSERT INTO `province` VALUES (32, 'Sumatera Barat', 'Sumatera Barat', '2022-07-16 21:28:29', '2022-07-16 21:28:29');
INSERT INTO `province` VALUES (33, 'Sumatera Selatan', 'Sumatera Selatan', '2022-07-16 21:28:29', '2022-07-16 21:28:29');
INSERT INTO `province` VALUES (34, 'Sumatera Utara', 'Sumatera Utara', '2022-07-16 21:28:29', '2022-07-16 21:28:29');
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;
