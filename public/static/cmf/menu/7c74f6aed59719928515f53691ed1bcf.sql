SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;
-- ----------------------------
-- Table structure for `bbcmf_rule`
-- ----------------------------
DROP TABLE IF EXISTS `bbcmf_rule`;
CREATE TABLE `bbcmf_rule` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`name` varchar(128) DEFAULT NULL,
`parentid` int(128) DEFAULT NULL,
`src` varchar(128) DEFAULT NULL,
`show` int(11) DEFAULT NULL,
`icon` varchar(128) DEFAULT NULL,
PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=utf8;
-- ----------------------------
--  Records of `bbcmf_rule`
-- ----------------------------
BEGIN;
INSERT INTO `bbcmf_rule` VALUES ('1', '设置', '0', '#', '1', 'user'), ('2', '网站配置', '1', '/admin/index/siteConfig', '1', 'user'), ('3', '个人信息', '1', '/admin/index/userInfo', '1', 'user'), ('11', '用户管理', '0', '/admin/index/siteConfig', '1', 'user'), ('12', '用户管理列表', '11', '/admin/Rbac/index', '1', 'user'), ('13', '角色管理', '0', '/admin/index/siteConfig', '0', 'user'), ('14', '角色管理', '13', '/admin/role/index', '1', 'user'), ('15', '用户组管理', '0', '/admin/index/siteConfig', '0', 'user'), ('17', '用户组管理', '15', '/admin/Group/index', '1', ''), ('28', '菜单管理', '0', '#', '1', ''), ('30', '菜单管理', '28', '/admin/Menu/index', '1', ''), ('34', '网站设置', '1', '/admin/index/siteSet', '1', '');
COMMIT;
SET FOREIGN_KEY_CHECKS = 1;