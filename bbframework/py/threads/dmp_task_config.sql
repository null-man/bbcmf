/*
 Navicat Premium Data Transfer

 Source Server         : bbframework_db
 Source Server Type    : MySQL
 Source Server Version : 50549
 Source Host           : 10.1.14.16
 Source Database       : bbframework_dmp

 Target Server Type    : MySQL
 Target Server Version : 50549
 File Encoding         : utf-8

 Date: 05/30/2016 20:47:09 PM
*/

SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Table structure for `dmp_task_config`
-- ----------------------------
DROP TABLE IF EXISTS `dmp_task_config`;
CREATE TABLE `dmp_task_config` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) DEFAULT NULL,
  `config` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Records of `dmp_task_config`
-- ----------------------------
BEGIN;
INSERT INTO `dmp_task_config` VALUES ('1', 'url', 'http://www.bbframework.com/index/index/database_backup'), ('2', 'threads', '10'), ('3', 'switch', '1'), ('4', 'cron_url', 'http://www.bbframework.com/index/index/task/id/');
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;
