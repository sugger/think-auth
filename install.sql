

DROP TABLE IF EXISTS `auth_model_act`;
CREATE TABLE `auth_model_act` (
  `map_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `model_class` varchar(256) NOT NULL COMMENT '模型类',
  `model_name` varchar(256) DEFAULT NULL COMMENT '模型名称',
  `model_act` text COMMENT '操作路由',
  PRIMARY KEY (`map_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `auth_role`;
CREATE TABLE `auth_role` (
  `role_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '权限组ID',
  `role_name` varchar(256) NOT NULL COMMENT '权限组名称',
  `role_remark` varchar(512) DEFAULT NULL COMMENT '权限组说明',
  PRIMARY KEY (`role_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='权限组';
INSERT INTO `auth_role` VALUES (1,'超管','权限最大'),(2,'未授权用户组','未登录的给该组权限'),(3,'测试',NULL),(4,'运营','日常运作');



DROP TABLE IF EXISTS `auth_role_route`;
CREATE TABLE `auth_role_route` (
  `role_route_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '权限组权限节点ID',
  `role_id` int(10) unsigned NOT NULL COMMENT '权限组ID',
  `route_id` int(10) unsigned NOT NULL COMMENT '路由节点ID',
  `data_scope` text COMMENT '数据范围',
  `data_route_access` text COMMENT '数据的路由权限',
  `show_fields` text COMMENT '显示字段',
  PRIMARY KEY (`role_route_id`)
) ENGINE=InnoDB AUTO_INCREMENT=77 DEFAULT CHARSET=utf8 COMMENT='权限组路由表';





DROP TABLE IF EXISTS `auth_route`;
CREATE TABLE `auth_route` (
  `route_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '路由ID',
  `module` varchar(128) NOT NULL COMMENT '模块',
  `controller` varchar(128) DEFAULT NULL COMMENT '控制器',
  `action` varchar(128) DEFAULT NULL COMMENT '动作',
  `route_name` varchar(128) DEFAULT NULL COMMENT '路由名称',
  `route_level` enum('1','2','3') DEFAULT '1' COMMENT '路由等级',
  `route_url` varchar(255) DEFAULT NULL COMMENT '路由完整URL',
  `show_fields` text COMMENT '允许显示的字段',
  `pid` int(10) unsigned DEFAULT NULL COMMENT '父级ID',
  PRIMARY KEY (`route_id`),
  UNIQUE KEY `route_url_unique` (`route_url`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 COMMENT='路由表';





DROP TABLE IF EXISTS `auth_table`;
CREATE TABLE `auth_table` (
  `table_name` varchar(255) NOT NULL COMMENT '表名',
  `comment` varchar(255) NOT NULL COMMENT '表备注',
  `fields` text COMMENT '字段',
  `engine` enum('InnoDB','MyISAM') DEFAULT NULL COMMENT '存储引擎',
  `last_modify` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '修改时间',
  PRIMARY KEY (`table_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='数据库表数据';


DROP TABLE IF EXISTS `auth_user_role`;
CREATE TABLE `auth_user_role` (
  `authorize_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '授权ID',
  `user_id` varchar(32) NOT NULL COMMENT '用户账号ID',
  `role_id` int(10) unsigned NOT NULL COMMENT '分组ID',
  `end_timestamp` datetime NOT NULL COMMENT '授权结束日期',
  `extra` text COMMENT '授权拓展信息，例如存部门等信息',
  PRIMARY KEY (`authorize_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='用户权限分组表';


CREATE
OR REPLACE
VIEW `auth_role_route_view` AS select
    `arr`.`role_route_id` AS `role_route_id`,
    `arr`.`role_id` AS `role_id`,
    `ar`.`route_id` AS `route_id`,
    `ar`.`route_name` AS `route_name`,
    `ar`.`route_url` AS `route_url`,
    `arr`.`data_scope` AS `data_scope`,
    `arr`.`data_route_access` AS `data_route_access`,
    (case
        when isnull(`arr`.`show_fields`) then `ar`.`show_fields`
        else `arr`.`show_fields`
    end) AS `show_fields`,
    `ar`.`action` AS `action`,
    `ar`.`controller` AS `controller`,
    `ar`.`module` AS `module`,
    `ar`.`pid` AS `route_pid`,
    `ar`.`route_level` AS `route_level`
from
    (`auth_role_route` `arr`
join `auth_route` `ar` on
    ((`arr`.`route_id` = `ar`.`route_id`)));


CREATE
OR REPLACE
VIEW `auth_user_role_view` AS select
    `ur`.`authorize_id` AS `authorize_id`,
    `ur`.`user_id` AS `user_id`,
    `ur`.`extra` AS `extra`,
    `ur`.`role_id` AS `role_id`,
    `r`.`role_name` AS `role_name`,
    `ur`.`end_timestamp` AS `end_timestamp`
from
    (`auth_user_role` `ur`
join `auth_role` `r` on
    ((`ur`.`role_id` = `r`.`role_id`)))