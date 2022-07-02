/*
 Navicat Premium Data Transfer

 Source Server         : local
 Source Server Type    : MySQL
 Source Server Version : 100607
 Source Host           : 127.0.0.1:3306
 Source Schema         : salihara

 Target Server Type    : MySQL
 Target Server Version : 100607
 File Encoding         : 65001

 Date: 01/07/2022 09:44:48
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for province
-- ----------------------------
-- DROP TABLE IF EXISTS `province`;
-- CREATE TABLE `province` (
--   `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
--   `name_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
--   `name_en` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
--   `created_at` timestamp NULL DEFAULT NULL,
--   `updated_at` timestamp NULL DEFAULT NULL,
--   PRIMARY KEY (`id`)
-- ) ENGINE=InnoDB AUTO_INCREMENT=93 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of province
-- ----------------------------
BEGIN;
INSERT INTO `province` VALUES (11, 'Aceh', 'Aceh', '2022-06-30 07:29:04', '2022-06-30 07:31:30');
INSERT INTO `province` VALUES (12, 'Sumatera Utara', 'North Sumatera', '2022-06-30 07:29:04', '2022-06-30 07:31:30');
INSERT INTO `province` VALUES (13, 'Sumatera Barat', 'West Sumatera', '2022-06-30 07:29:04', '2022-06-30 07:31:30');
INSERT INTO `province` VALUES (14, 'Riau', 'Riau', '2022-06-30 07:29:04', '2022-06-30 07:31:30');
INSERT INTO `province` VALUES (15, 'Jambi', 'Jambi', '2022-06-30 07:29:04', '2022-06-30 07:31:30');
INSERT INTO `province` VALUES (16, 'Sumatera Selatan', 'South Sumatera', '2022-06-30 07:29:04', '2022-06-30 07:31:30');
INSERT INTO `province` VALUES (17, 'Bengkulu', 'Bengkulu', '2022-06-30 07:29:04', '2022-06-30 07:31:30');
INSERT INTO `province` VALUES (18, 'Lampung', 'Lampung', '2022-06-30 07:29:05', '2022-06-30 07:31:30');
INSERT INTO `province` VALUES (19, 'Kepulauan Bangka Belitung', 'Bangka Belitung Islands', '2022-06-30 07:29:05', '2022-06-30 07:31:30');
INSERT INTO `province` VALUES (21, 'Kepulauan Riau', 'Riau Islands', '2022-06-30 07:29:05', '2022-06-30 07:31:30');
INSERT INTO `province` VALUES (31, 'Daerah Khusus Ibukota Jakarta', 'Special Capital Region Of Jakarta', '2022-06-30 07:29:05', '2022-06-30 07:31:30');
INSERT INTO `province` VALUES (32, 'Jawa Barat', 'West Java', '2022-06-30 07:29:05', '2022-06-30 07:31:30');
INSERT INTO `province` VALUES (33, 'Jawa Tengah', 'Central Java', '2022-06-30 07:29:05', '2022-06-30 07:31:30');
INSERT INTO `province` VALUES (34, 'Daerah Istimewa Yogyakarta', 'Sepcial Region Of Yogyakarta', '2022-06-30 07:29:05', '2022-06-30 07:31:31');
INSERT INTO `province` VALUES (35, 'Jawa Timur', 'East Java', '2022-06-30 07:29:05', '2022-06-30 07:31:31');
INSERT INTO `province` VALUES (36, 'Banten', 'Banten', '2022-06-30 07:29:05', '2022-06-30 07:31:31');
INSERT INTO `province` VALUES (51, 'Bali', 'Bali', '2022-06-30 07:29:05', '2022-06-30 07:31:31');
INSERT INTO `province` VALUES (52, 'Nusa Tenggara Barat', 'West Nusa Tenggara', '2022-06-30 07:29:05', '2022-06-30 07:31:31');
INSERT INTO `province` VALUES (53, 'Nusa Tenggara Timur', 'East Nusa Tenggara', '2022-06-30 07:29:05', '2022-06-30 07:31:31');
INSERT INTO `province` VALUES (61, 'Kalimantan Barat', 'West Kalimantan', '2022-06-30 07:29:05', '2022-06-30 07:31:31');
INSERT INTO `province` VALUES (62, 'Kalimantan Tengah', 'Central Kalimantan', '2022-06-30 07:29:05', '2022-06-30 07:31:31');
INSERT INTO `province` VALUES (63, 'Kalimantan Selatan', 'South Kalimantan', '2022-06-30 07:29:05', '2022-06-30 07:31:31');
INSERT INTO `province` VALUES (64, 'Kalimantan Timur', 'East Kalimantan', '2022-06-30 07:29:05', '2022-06-30 07:31:31');
INSERT INTO `province` VALUES (65, 'Kalimantan Utara', 'North Kalimantan', '2022-06-30 07:29:05', '2022-06-30 07:31:31');
INSERT INTO `province` VALUES (71, 'Sulawesi Utara', 'North Sulawesi', '2022-06-30 07:29:05', '2022-06-30 07:31:31');
INSERT INTO `province` VALUES (72, 'Sulawesi Tengah', 'Central Sulawesi', '2022-06-30 07:29:05', '2022-06-30 07:31:31');
INSERT INTO `province` VALUES (73, 'Sulawesi Selatan', 'South Sulawesi', '2022-06-30 07:29:05', '2022-06-30 07:31:31');
INSERT INTO `province` VALUES (74, 'Sulawesi Tenggara', 'Southeast Sulawesi', '2022-06-30 07:29:05', '2022-06-30 07:31:31');
INSERT INTO `province` VALUES (75, 'Gorontalo', 'Gorontalo', '2022-06-30 07:29:05', '2022-06-30 07:31:31');
INSERT INTO `province` VALUES (76, 'Sulawesi Barat', 'West Sulawesi', '2022-06-30 07:29:05', '2022-06-30 07:31:31');
INSERT INTO `province` VALUES (81, 'Maluku', 'Maluku', '2022-06-30 07:29:05', '2022-06-30 07:31:31');
INSERT INTO `province` VALUES (82, 'Maluku Utara', 'North Maluku', '2022-06-30 07:29:05', '2022-06-30 07:31:31');
INSERT INTO `province` VALUES (91, 'Papua', 'Papua', '2022-06-30 07:29:05', '2022-06-30 07:31:31');
INSERT INTO `province` VALUES (92, 'Papua Barat', 'West Papua', '2022-06-30 07:29:05', '2022-06-30 07:31:31');
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;
