SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;
-- ----------------------------
-- Table structure for `bbcmf_group`
-- ----------------------------
DROP TABLE IF EXISTS `bbcmf_group`;
CREATE TABLE `bbcmf_group` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`group` varchar(128) DEFAULT NULL,
`parentid` int(11) DEFAULT NULL,
`state` int(11) DEFAULT NULL,
PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
-- ----------------------------
--  Records of `bbcmf_group`
-- ----------------------------
BEGIN;
INSERT INTO `bbcmf_group` VALUES ('1', '工具组', '0', '1'), ('2', '服务端', '0', '1'), ('3', '前端', '2', '0'), ('4', '后端', '2', '0'), ('8', '数据组', '1', '0');
COMMIT;
SET FOREIGN_KEY_CHECKS = 1;