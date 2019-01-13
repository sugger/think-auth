CREATE TABLE `auth_group` (
  `group_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '权限组ID',
  `group_name` varchar(256) NOT NULL COMMENT '权限组名称',
  `group_remark` varchar(512) DEFAULT NULL COMMENT '权限组说明',
  PRIMARY KEY (`group_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='权限组';

CREATE TABLE `auth_group_route` (
  `group_route_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '权限组权限节点ID',
  `group_id` int(10) unsigned NOT NULL COMMENT '权限组ID',
  `route_id` int(10) unsigned NOT NULL COMMENT '路由节点ID',
  `data_scope` json DEFAULT NULL COMMENT '数据范围',
  `data_route_access` json DEFAULT NULL COMMENT '数据的路由权限',
  `show_fields` json DEFAULT NULL COMMENT '显示字段',
  PRIMARY KEY (`group_route_id`)
) ENGINE=InnoDB AUTO_INCREMENT=77 DEFAULT CHARSET=utf8 COMMENT='权限组路由表';

CREATE TABLE `auth_model_act` (
  `map_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `model_class` varchar(256) NOT NULL COMMENT '模型类',
  `model_name` varchar(256) DEFAULT NULL COMMENT '模型名称',
  `model_act` json DEFAULT NULL COMMENT '操作路由',
  PRIMARY KEY (`map_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;


CREATE TABLE `auth_route` (
  `route_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '路由ID',
  `module` varchar(128) NOT NULL COMMENT '模块',
  `controller` varchar(128) DEFAULT NULL COMMENT '控制器',
  `action` varchar(128) DEFAULT NULL COMMENT '动作',
  `route_name` varchar(128) DEFAULT NULL COMMENT '路由名称',
  `route_level` enum('1','2','3') DEFAULT '1' COMMENT '路由等级',
  `route_url` varchar(386) DEFAULT NULL COMMENT '路由完整URL',
  `show_fields` json DEFAULT NULL COMMENT '允许显示的字段',
  `pid` int(10) unsigned DEFAULT NULL COMMENT '父级ID',
  PRIMARY KEY (`route_id`),
  UNIQUE KEY `route_url_unique` (`route_url`)
) ENGINE=InnoDB AUTO_INCREMENT=579 DEFAULT CHARSET=utf8 COMMENT='路由表'

CREATE TABLE `auth_table` (
  `table_name` varchar(256) NOT NULL COMMENT '表名',
  `comment` varchar(256) NOT NULL COMMENT '表备注',
  `fields` json DEFAULT NULL COMMENT '字段',
  `engine` enum('InnoDB','MyISAM') DEFAULT NULL COMMENT '存储引擎',
  `last_modify` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '修改时间',
  PRIMARY KEY (`table_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='数据库表数据';

CREATE TABLE `auth_user_group` (
  `authorize_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '授权ID',
  `user_id` varchar(32) NOT NULL COMMENT '用户账号ID',
  `group_id` int(10) unsigned NOT NULL COMMENT '分组ID',
  `end_timestamp` datetime NOT NULL COMMENT '授权结束日期',
  `extra` json DEFAULT NULL COMMENT '授权拓展信息，例如存部门等信息',
  PRIMARY KEY (`authorize_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='用户权限分组表';


CREATE OR REPLACE VIEW `auth_group_route_view` AS
select
    `agr`.`group_route_id` AS `group_route_id`,
    `agr`.`group_id` AS `group_id`,
    `ar`.`route_id` AS `route_id`,
    `ar`.`route_name` AS `route_name`,
    `ar`.`route_url` AS `route_url`,
    `agr`.`data_scope` AS `data_scope`,
    `agr`.`data_route_access` AS `data_route_access`,
    (case
        when isnull(`agr`.`show_fields`) then `ar`.`show_fields`
        else `agr`.`show_fields`
    end) AS `show_fields`,
    `ar`.`action` AS `action`,
    `ar`.`controller` AS `controller`,
    `ar`.`module` AS `module`,
    `ar`.`pid` AS `route_pid`,
    `ar`.`route_level` AS `route_level`
from
    (`auth_group_route` `agr`
join `auth_route` `ar` on
    ((`agr`.`route_id` = `ar`.`route_id`)));


CREATE OR REPLACE VIEW `auth_user_group_view` AS
select
    `ug`.`authorize_id` AS `authorize_id`,
    `ug`.`user_id` AS `user_id`,
    `ug`.`extra` AS `extra`,
    `ug`.`group_id` AS `group_id`,
    `g`.`group_name` AS `group_name`,
    `ug`.`end_timestamp` AS `end_timestamp`
from
    (`auth_user_group` `ug`
join `auth_group` `g` on
    ((`ug`.`group_id` = `g`.`group_id`)));