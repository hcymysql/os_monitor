/*Table structure for table `os_status` */

CREATE TABLE `os_status` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键自增Id',
  `host` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '监控主机IP',
  `tag` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '监控主机名字',
  `is_alive` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '是否存活.online为在线;offline为离线',
  `cpu_idle` tinyint DEFAULT NULL COMMENT 'cpu空闲使用率',
  `cpu_load` tinyint DEFAULT NULL COMMENT 'cpu负载使用率',
  `memory_usage` tinyint DEFAULT NULL COMMENT '内存使用率',
  `disk_free` text CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT '磁盘空间使用率',
  `disk_io` text CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT '磁盘IO使用率',
  `create_time` timestamp NULL DEFAULT NULL COMMENT '监控信息入库时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_h_t` (`host`,`tag`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='系统监控状态表';



/*Table structure for table `os_status_history` */

CREATE TABLE `os_status_history` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键自增Id',
  `host` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '监控主机IP',
  `tag` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '监控主机名字',
  `is_alive` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '是否存活.online为在线;offline为离线',
  `cpu_idle` tinyint DEFAULT NULL COMMENT 'cpu空闲使用率',
  `cpu_load` tinyint DEFAULT NULL COMMENT 'cpu负载使用率',
  `memory_usage` tinyint DEFAULT NULL COMMENT '内存使用率',
  `create_time` timestamp NULL DEFAULT NULL COMMENT '监控信息入库时间',
  PRIMARY KEY (`id`),
  KEY `idx_h_t` (`host`,`tag`),
  KEY `idx_ct` (`create_time`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='系统监控状态历史信息记录表';



/*Table structure for table `os_status_info` */

CREATE TABLE `os_status_info` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT '主键自增Id',
  `host` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '输入被监控的主机IP地址',
  `ssh_port` int DEFAULT NULL COMMENT '输入被监控的主机SSH端口',
  `tag` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '输入被监控的主机名字',
  `monitor` tinyint DEFAULT '1' COMMENT '0为关闭监控;1为开启监控',
  `send_mail` tinyint DEFAULT '1' COMMENT '0为关闭邮件报警;1为开启邮件报警',
  `send_mail_to_list` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '邮件人列表，多个邮箱以,逗号分隔',
  `send_weixin` tinyint DEFAULT '1' COMMENT '0为关闭微信报警;1为开启微信报警',
  `send_weixin_to_list` varchar(100) DEFAULT NULL COMMENT '微信公众号',
  `alarm_alive_status` tinyint DEFAULT NULL COMMENT '记录主机存活的告警信息，1为已记录',
  `alarm_cpu_idle_status` tinyint DEFAULT NULL COMMENT '记录cpu空闲使用率告警信息，1为已记录',
  `threshold_alarm_cpu_idle` tinyint DEFAULT NULL COMMENT '设置cpu空闲使用率阀值',
  `alarm_cpu_load_status` tinyint DEFAULT NULL COMMENT '记录cpu负载使用率告警信息，1为已记录',
  `threshold_alarm_cpu_load` tinyint DEFAULT NULL COMMENT '设置cpu负载使用率阀值',
  `alarm_memory_usage_status` tinyint DEFAULT NULL COMMENT '记录内存使用率告警信息，1为已记录',
  `threshold_alarm_memory_usage` tinyint DEFAULT NULL COMMENT '设置内存使用率阀值',
  `alarm_disk_free_status` tinyint DEFAULT NULL COMMENT '记录剩余磁盘空间使用率告警信息，1为已记录',
  `threshold_alarm_disk_free` tinyint DEFAULT NULL COMMENT '设置磁盘空间使用率阀值',
  `alarm_disk_io_status` tinyint DEFAULT NULL COMMENT '记录磁盘IO使用率告警信息，1为已记录',
  `threshold_alarm_disk_io` tinyint DEFAULT NULL COMMENT '设置磁盘IO使用率阀值',
  PRIMARY KEY (`id`),
  KEY `IX_i_d_p` (`host`,`tag`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='系统信息配置表';



/*Table structure for table `os_disk_history` */

CREATE TABLE `os_disk_history` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键自增Id',
  `host` VARCHAR(30) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '监控主机IP',
  `tag` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '监控主机名字',
  `is_alive` VARCHAR(10) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '是否存活.online为在线;offline为离线',
  `mount` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '挂载目录信息',
  `disk_usage` INT DEFAULT NULL COMMENT '磁盘空间使用率',
  `create_time` TIMESTAMP NULL DEFAULT NULL COMMENT '监控信息入库时间',
  PRIMARY KEY (`id`),
  KEY `idx_ct` (`create_time`)
) ENGINE=INNODB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='磁盘空间使用率历史信息记录表';



/*Table structure for table `os_diskio_history` */

CREATE TABLE `os_diskio_history` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键自增Id',
  `host` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '监控主机IP',
  `tag` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '监控主机名字',
  `is_alive` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '是否存活.online为在线;offline为离线',
  `device` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '磁盘设备信息',
  `diskio_util` int DEFAULT NULL COMMENT '磁盘IO使用率',
  `create_time` timestamp NULL DEFAULT NULL COMMENT '监控信息入库时间',
  PRIMARY KEY (`id`),
  KEY `idx_ct` (`create_time`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='磁盘IO使用率历史信息记录表';



