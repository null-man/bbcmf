
CREATE TABLE `bbcmf_admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(128) DEFAULT NULL,
  `password` varchar(128) DEFAULT NULL,
  `nikname` varchar(128) DEFAULT NULL,
  `role_id` int(11) DEFAULT NULL,
  `group_id` int(11) DEFAULT NULL,
  `phone` varchar(128) DEFAULT NULL,
  `mail` varchar(128) DEFAULT NULL,
  `head` varchar(128) DEFAULT NULL,
  `mark` text,
  `create_time` varchar(128) DEFAULT NULL,
  `update_time` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `bbcmf_group`
-- ----------------------------

CREATE TABLE `bbcmf_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group` varchar(128) DEFAULT NULL,
  `parentid` int(11) DEFAULT NULL,
  `state` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;



INSERT INTO `bbcmf_group` VALUES ('1', '工具组', '0', '1'), ('2', '服务端', '0', '1'), ('3', '前端', '2', '0'), ('4', '后端', '2', '0'), ('8', '数据组', '1', '0');


-- ----------------------------
--  Table structure for `bbcmf_role`
-- ----------------------------

CREATE TABLE `bbcmf_role` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role` varchar(128) DEFAULT NULL,
  `state` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;



INSERT INTO `bbcmf_role` VALUES ('1', '超级管理员', '1'), ('2', '普通管理员', '1');


-- ----------------------------
--  Table structure for `bbcmf_role_auth`
-- ----------------------------

CREATE TABLE `bbcmf_role_auth` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_id` int(11) DEFAULT NULL,
  `rule_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=170 DEFAULT CHARSET=utf8;



INSERT INTO `bbcmf_role_auth` VALUES ('106', '5', '1'), ('107', '5', '2'), ('108', '5', '3'), ('109', '5', '34'), ('110', '5', '11'), ('111', '5', '12'), ('112', '5', '13'), ('113', '5', '14'), ('114', '5', '15'), ('115', '5', '17'), ('116', '5', '28'), ('117', '5', '30'), ('151', '6', '1'), ('152', '6', '2'), ('153', '6', '3'), ('154', '6', '34'), ('155', '6', '11'), ('156', '6', '12'), ('157', '6', '13'), ('158', '6', '14'), ('159', '6', '15'), ('160', '6', '17'), ('161', '6', '28'), ('162', '6', '30'), ('163', '3', '1'), ('164', '3', '2'), ('165', '3', '3'), ('166', '7', '1'), ('167', '7', '2'), ('168', '7', '3'), ('169', '7', '34');


-- ----------------------------
--  Table structure for `bbcmf_rule`
-- ----------------------------

CREATE TABLE `bbcmf_rule` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) DEFAULT NULL,
  `parentid` int(128) DEFAULT NULL,
  `src` varchar(128) DEFAULT NULL,
  `show` int(11) DEFAULT NULL,
  `icon` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8;



INSERT INTO `bbcmf_rule` VALUES ('1', '设置', '0', '/default/default/default', '1', 'user'), ('2', '网站配置', '1', '/admin/Index/siteConfig', '1', 'user'), ('3', '个人信息', '1', '/admin/index/userInfo', '1', 'user'), ('11', '用户管理', '0', '/default/default/default', '1', 'user'), ('12', '用户管理列表', '11', '/admin/Rbac/index', '1', 'user'), ('13', '角色管理', '0', '/default/default/default', '1', 'user'), ('14', '角色管理', '13', '/admin/role/index', '1', 'user'), ('15', '用户组管理', '0', '/admin/index/siteConfig', '0', 'user'), ('17', '用户组管理', '15', '/admin/Group/index', '1', ''), ('28', '菜单管理', '0', '/default/default/default', '1', ''), ('30', '菜单管理', '28', '/admin/Menu/index', '1', ''), ('34', '网站设置', '1', '/admin/index/siteSet', '1', '');


-- ----------------------------
--  Table structure for `bbcmf_site_config`
-- ----------------------------

CREATE TABLE `bbcmf_site_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `site_name` varchar(128) DEFAULT NULL,
  `admin_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


-- ----------------------------
--  Table structure for `bbcmf_site_set`
-- ----------------------------

CREATE TABLE `bbcmf_site_set` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `verify_code` int(11) DEFAULT NULL,
  `free_reg` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;



INSERT INTO `bbcmf_site_set` VALUES ('1', '0', '0');


SET FOREIGN_KEY_CHECKS = 1;
