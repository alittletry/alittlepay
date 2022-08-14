-- MySQL dump 10.13  Distrib 5.7.38, for Linux (x86_64)
--
-- Host: localhost    Database: int
-- ------------------------------------------------------
-- Server version	5.7.38-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `pay_admin`
--

DROP TABLE IF EXISTS `pay_admin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pay_admin` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `uid` int(10) NOT NULL DEFAULT '0' COMMENT '前台用户ID',
  `name` varchar(32) NOT NULL COMMENT '用户名',
  `nickname` varchar(32) NOT NULL COMMENT '昵称',
  `pwd` varchar(64) NOT NULL COMMENT '密码',
  `realname` varchar(32) DEFAULT NULL COMMENT '真实姓名',
  `avatar` varchar(255) DEFAULT NULL COMMENT '头像',
  `role_id` int(4) NOT NULL DEFAULT '0' COMMENT '角色id',
  `tel` varchar(20) DEFAULT NULL COMMENT '电话',
  `mail` varchar(50) DEFAULT NULL COMMENT '邮箱',
  `remark` varchar(255) DEFAULT NULL COMMENT '简介',
  `status` tinyint(1) NOT NULL COMMENT '状态1:正常0冻结',
  `ip` varchar(20) DEFAULT NULL COMMENT '注册ip',
  `create_user` varchar(32) DEFAULT NULL COMMENT '添加人',
  `create_time` varchar(20) DEFAULT NULL COMMENT '添加时间',
  `update_user` varchar(32) DEFAULT NULL COMMENT '修改时间',
  `update_time` varchar(20) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='后台人员列表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pay_admin`
--

LOCK TABLES `pay_admin` WRITE;
/*!40000 ALTER TABLE `pay_admin` DISABLE KEYS */;
INSERT INTO `pay_admin` VALUES (1,0,'admin','admin','e10adc3949ba59abbe56e057f20f883e',NULL,'/upload/image/20220728/ca37178a72d251c4fa7d3660d10d5d39.jpeg',1,'111111211','admin@admin.com','这家伙很懒，什么也没留下',1,NULL,'1','1658965195',NULL,'1658968419');
/*!40000 ALTER TABLE `pay_admin` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pay_admin_auth`
--

DROP TABLE IF EXISTS `pay_admin_auth`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pay_admin_auth` (
  `id` int(4) NOT NULL AUTO_INCREMENT COMMENT '权限id',
  `name` varchar(64) NOT NULL COMMENT '权限名称',
  `icon` varchar(50) DEFAULT NULL COMMENT '图标',
  `pid` int(4) NOT NULL DEFAULT '0' COMMENT '父id',
  `module` varchar(32) NOT NULL COMMENT '模块名',
  `controller` varchar(32) NOT NULL COMMENT '控制器名称',
  `action` varchar(32) NOT NULL COMMENT '方法名名称',
  `params` varchar(128) DEFAULT NULL COMMENT '参数',
  `font_family` varchar(20) DEFAULT NULL COMMENT '字体',
  `spreed` tinyint(1) DEFAULT '0' COMMENT 'spreed',
  `is_check` tinyint(1) DEFAULT '0' COMMENT '是否选中',
  `is_menu` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否菜单',
  `path` varchar(64) NOT NULL COMMENT '路径',
  `rank` int(2) NOT NULL DEFAULT '0' COMMENT '排序',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态 1可用',
  `create_user` varchar(32) DEFAULT NULL COMMENT '添加人',
  `create_time` varchar(20) DEFAULT NULL COMMENT '添加时间',
  `update_user` varchar(32) DEFAULT NULL COMMENT '修改时间',
  `update_time` varchar(20) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=76 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='权限表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pay_admin_auth`
--

LOCK TABLES `pay_admin_auth` WRITE;
/*!40000 ALTER TABLE `pay_admin_auth` DISABLE KEYS */;
INSERT INTO `pay_admin_auth` VALUES (1,'查看日志','',8,'admin','admin.admin_log','index','','ok-icon',0,0,1,'/admin/admin.admin_log/index',0,1,NULL,NULL,NULL,'1581664102'),(2,'控制台','mdi mdi-home',0,'admin','index','main','','ok-icon',0,1,1,'/admin/index/main',99,1,NULL,NULL,'1','1605358475'),(4,'账号管理','mdi mdi-account',0,'admin','admin.admin','index','','ok-icon',0,0,1,'/admin/admin.admin/index',0,1,NULL,NULL,NULL,NULL),(5,'用户管理','',4,'admin','admin.admin','index','',NULL,0,0,1,'/admin/admin.admin/index',0,1,NULL,NULL,NULL,NULL),(6,'权限管理','',4,'admin','admin.admin_auth','index','',NULL,0,0,1,'/admin/admin.admin_auth/index',0,1,NULL,NULL,'1','1582263685'),(7,'角色管理','',4,'admin','admin.admin_role','index','',NULL,0,0,1,'/admin/admin.admin_role/index',0,1,NULL,NULL,'1','1597655581'),(8,'系统管理','mdi mdi-settings',0,'admin','admin.admin_log','index','','ok-icon',1,1,1,'/admin/admin.admin_log/index',0,1,NULL,NULL,NULL,'1606295821'),(14,'系统图标','',8,'admin','admin.admin_icon','index','',NULL,0,0,1,'/admin/admin.admin_icon/index',99,1,'1','1581668876','1','1659257646'),(20,'修改密码','',8,'admin','admin.admin','pwd','',NULL,0,0,1,'/admin/admin.admin/pwd',0,1,'1','1582093161',NULL,'1597398791'),(21,'清理缓存','',8,'admin','index','clearCache','',NULL,0,0,0,'/admin/index/clearCache',0,1,'1','1582093658',NULL,'1582093666'),(26,'网站配置','',8,'admin','system.system_config','base','',NULL,0,0,1,'/admin/system.system_config/base',0,1,'1','1582266348','1','1582781624'),(27,'开发者配置','',8,'admin','system.system_config_tab','index','',NULL,0,0,0,'/admin/system.system_config_tab/index',0,1,'1','1582266439','1','1659181809'),(35,'后台登录','',8,'admin','login','login','',NULL,0,0,0,'/admin/login/login',0,1,'1','1582707133',NULL,NULL),(36,'上传配置','',8,'admin','system.system_config','upload','',NULL,0,0,0,'/admin/system.system_config/upload',0,1,'1','1582781658',NULL,'1582781667'),(38,'邮件配置','',8,'admin','system.system_config','email','',NULL,0,0,0,'/admin/system.system_config/email',0,1,'1','1582781787',NULL,NULL),(53,'留言反馈','',50,'admin','user.user_message','index','',NULL,0,0,1,'/admin/user.user_message/index',0,1,'1','1583558491','1','1583558582'),(56,'附件管理','',8,'admin','widget','index','',NULL,0,0,0,'/admin/widget/index',0,1,'1','1584758583','1','1584758865'),(57,'选择图标','',56,'admin','widget_icon','index','',NULL,0,0,0,'/admin/widget_icon/index',0,1,'1','1584758637',NULL,'1584758874'),(58,'单图片上传1','',56,'admin','widget.files','image','',NULL,0,0,0,'/admin/widget.files/image',0,1,'1','1584758709',NULL,'1584758878'),(59,'bs64上传转图片','',56,'admin','widget_files','baseToImage','',NULL,0,0,0,'/admin/widget_files/baseToImage',0,1,'1','1584758783',NULL,'1584758881'),(60,'tinymce图片上传','',56,'admin','widget.files','tinymce','',NULL,0,0,0,'/admin/widget.files/tinymce',0,1,'1','1584758813',NULL,'1584758870'),(67,'删除用户','',5,'admin','admin.admin','del','',NULL,0,0,0,'/admin/admin.admin/del',0,1,'1','1591411284','1','1591411303'),(68,'删除权限','',6,'admin','admin.admin_auth','del','',NULL,0,0,0,'/admin/admin.admin_auth/del',0,1,'1','1591411848',NULL,NULL),(72,'通道管理','mdi mdi-message-reply',0,'admin','payment.index','index','',NULL,0,0,1,'/admin/payment.index/index',98,1,'1','1658968924','1','1658970663'),(73,'订单管理','mdi mdi-subway',0,'admin','order.index','index','',NULL,0,0,1,'/admin/order.index/index',97,1,'1','1658968964','1','1658975060'),(74,'监听日志','mdi mdi-cloud-braces',0,'admin','listen.index','index','',NULL,0,0,1,'/admin/listen.index/index',96,1,'1','1659170848',NULL,NULL),(75,'对接文档','mdi mdi-file-document',0,'admin','doc.index','index','',NULL,0,0,1,'/admin/doc.index/index',95,1,'1','1659179795',NULL,NULL);
/*!40000 ALTER TABLE `pay_admin_auth` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pay_admin_log`
--

DROP TABLE IF EXISTS `pay_admin_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pay_admin_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '编号',
  `admin_id` int(10) NOT NULL COMMENT '操作人id',
  `admin_name` varchar(50) DEFAULT NULL COMMENT '操作人名字',
  `module` varchar(50) NOT NULL COMMENT '模块名',
  `controller` varchar(50) NOT NULL COMMENT '控制器名',
  `action` varchar(50) NOT NULL COMMENT '方法名',
  `ip` varchar(255) DEFAULT NULL COMMENT 'ip',
  `create_time` varchar(20) DEFAULT NULL COMMENT '操作时间',
  `user_agent` varchar(255) DEFAULT NULL COMMENT 'User-Agent',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='操作日志表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pay_admin_log`
--

LOCK TABLES `pay_admin_log` WRITE;
/*!40000 ALTER TABLE `pay_admin_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `pay_admin_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pay_admin_notify`
--

DROP TABLE IF EXISTS `pay_admin_notify`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pay_admin_notify` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '消息ID',
  `aid` int(10) NOT NULL COMMENT '管理员ID',
  `title` varchar(255) NOT NULL COMMENT '标题',
  `content` text NOT NULL COMMENT '内容',
  `from` varchar(10) DEFAULT NULL COMMENT '消息来源 谁发的',
  `type` varchar(10) NOT NULL COMMENT '消息类型 timer:定时器 system:系统',
  `url` varchar(255) DEFAULT NULL COMMENT '跳转路径 不填写时自动判断',
  `is_read` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否已读',
  `add_time` varchar(20) DEFAULT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='后台信息表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pay_admin_notify`
--

LOCK TABLES `pay_admin_notify` WRITE;
/*!40000 ALTER TABLE `pay_admin_notify` DISABLE KEYS */;
/*!40000 ALTER TABLE `pay_admin_notify` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pay_admin_role`
--

DROP TABLE IF EXISTS `pay_admin_role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pay_admin_role` (
  `id` int(4) NOT NULL AUTO_INCREMENT COMMENT '角色状态',
  `pid` int(4) NOT NULL DEFAULT '0' COMMENT '上级id',
  `name` varchar(32) NOT NULL COMMENT '角色名称',
  `auth` text NOT NULL COMMENT '权限',
  `tree_data` text COMMENT 'treedata',
  `rank` tinyint(2) NOT NULL DEFAULT '0' COMMENT '排序',
  `status` tinyint(1) NOT NULL COMMENT '角色状态1可用0不用',
  `create_user` varchar(32) DEFAULT NULL COMMENT '添加人',
  `create_time` varchar(20) DEFAULT NULL COMMENT '添加时间',
  `update_user` varchar(32) DEFAULT NULL COMMENT '修改时间',
  `update_time` varchar(20) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='后台角色表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pay_admin_role`
--

LOCK TABLES `pay_admin_role` WRITE;
/*!40000 ALTER TABLE `pay_admin_role` DISABLE KEYS */;
INSERT INTO `pay_admin_role` VALUES (1,0,'超级管理员','2,72,73,74,75,4,5,67,6,68,7,8,14,1,20,21,26,27,35,36,38,56,57,58,59,60','2,72,73,74,75,4,5,67,6,68,7,8,14,1,20,21,26,27,35,36,38,56,57,58,59,60',0,1,'1','1581734943','1','1659257670');
/*!40000 ALTER TABLE `pay_admin_role` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pay_attachment`
--

DROP TABLE IF EXISTS `pay_attachment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pay_attachment` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '附件ID',
  `cid` int(2) NOT NULL COMMENT '所属目录',
  `name` varchar(128) NOT NULL COMMENT '附件名称',
  `path` varchar(255) NOT NULL COMMENT '附件地址',
  `type` varchar(10) NOT NULL COMMENT '类型',
  `mime` varchar(20) DEFAULT NULL COMMENT 'mime',
  `size` varchar(20) DEFAULT NULL COMMENT '大小',
  `storage` int(2) DEFAULT NULL COMMENT '存储方式1本地2腾讯云',
  `upload_time` varchar(20) DEFAULT NULL COMMENT '上传时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='附件表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pay_attachment`
--

LOCK TABLES `pay_attachment` WRITE;
/*!40000 ALTER TABLE `pay_attachment` DISABLE KEYS */;
INSERT INTO `pay_attachment` VALUES (2,1,'image/20220728/8b7d83adfaf0b5a0ad529975d349a3aa.png','/upload/image/20220728/8b7d83adfaf0b5a0ad529975d349a3aa.png','image','image/png','10354',1,'1658972486');
/*!40000 ALTER TABLE `pay_attachment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pay_attachment_category`
--

DROP TABLE IF EXISTS `pay_attachment_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pay_attachment_category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '目录ID',
  `pid` int(10) NOT NULL DEFAULT '0' COMMENT '上级分类',
  `name` varchar(255) NOT NULL COMMENT '目录名称',
  `type` varchar(10) NOT NULL COMMENT '所属附件类型',
  `create_user` varchar(32) DEFAULT NULL COMMENT '添加人',
  `create_time` varchar(20) DEFAULT NULL COMMENT '添加时间',
  `update_user` varchar(32) DEFAULT NULL COMMENT '修改时间',
  `update_time` varchar(20) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='附件分类';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pay_attachment_category`
--

LOCK TABLES `pay_attachment_category` WRITE;
/*!40000 ALTER TABLE `pay_attachment_category` DISABLE KEYS */;
INSERT INTO `pay_attachment_category` VALUES (1,0,'二维码','image','1','1658971546',NULL,NULL);
/*!40000 ALTER TABLE `pay_attachment_category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pay_callback`
--

DROP TABLE IF EXISTS `pay_callback`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pay_callback` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `param` text NOT NULL,
  `return` text NOT NULL,
  `create_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pay_callback`
--

LOCK TABLES `pay_callback` WRITE;
/*!40000 ALTER TABLE `pay_callback` DISABLE KEYS */;
/*!40000 ALTER TABLE `pay_callback` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pay_listen`
--

DROP TABLE IF EXISTS `pay_listen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pay_listen` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `payment_id` int(11) DEFAULT NULL,
  `device` varchar(64) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` varchar(255) NOT NULL,
  `pkg` varchar(255) NOT NULL,
  `remarks` varchar(255) DEFAULT NULL,
  `create_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pay_listen`
--

LOCK TABLES `pay_listen` WRITE;
/*!40000 ALTER TABLE `pay_listen` DISABLE KEYS */;
/*!40000 ALTER TABLE `pay_listen` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pay_order`
--

DROP TABLE IF EXISTS `pay_order`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pay_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `payment_id` int(20) NOT NULL COMMENT '通道ID',
  `trade_no` varchar(64) NOT NULL COMMENT '系统订单号',
  `out_trade_no` varchar(64) NOT NULL COMMENT '商户订单号',
  `type` varchar(64) NOT NULL DEFAULT 'alipay' COMMENT '支付方式',
  `name` varchar(255) DEFAULT NULL COMMENT '商品名称',
  `money` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '商品金额',
  `real_money` varchar(64) NOT NULL,
  `notify_url` text COMMENT '异步通知地址',
  `return_url` text COMMENT '跳转通知地址',
  `param` text COMMENT '业务扩展参数',
  `sign_type` varchar(64) DEFAULT 'MD5' COMMENT '签名类型',
  `sign` varchar(64) NOT NULL COMMENT '签名字符串',
  `trade_status` varchar(64) NOT NULL DEFAULT 'TRADE_FAIL' COMMENT '支付状态',
  `notify_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '通知状态',
  `notify_count` int(10) NOT NULL DEFAULT '0' COMMENT '通知次数',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  `pay_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(11) DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pay_order`
--

LOCK TABLES `pay_order` WRITE;
/*!40000 ALTER TABLE `pay_order` DISABLE KEYS */;
/*!40000 ALTER TABLE `pay_order` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pay_payment`
--

DROP TABLE IF EXISTS `pay_payment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pay_payment` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `name` varchar(64) NOT NULL COMMENT '通道名称',
  `type` varchar(64) NOT NULL DEFAULT 'alipay' COMMENT '通道类型wxpzy alipay',
  `image` varchar(255) NOT NULL COMMENT '收款码图片',
  `limit` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '每日限额',
  `float_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1上下 2上 3下',
  `float_quantity` int(10) NOT NULL DEFAULT '10' COMMENT '浮动次数',
  `float_unit` float(10,2) NOT NULL DEFAULT '0.01' COMMENT '浮动值',
  `rotation` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否参与轮训 1参与 2不参与',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1正常 2暂停 3限额',
  `create_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pay_payment`
--

LOCK TABLES `pay_payment` WRITE;
/*!40000 ALTER TABLE `pay_payment` DISABLE KEYS */;
/*!40000 ALTER TABLE `pay_payment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pay_system_config`
--

DROP TABLE IF EXISTS `pay_system_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pay_system_config` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `tab_id` int(8) DEFAULT NULL COMMENT '分组id',
  `name` varchar(32) NOT NULL COMMENT '标题名称',
  `form_name` varchar(32) DEFAULT NULL COMMENT '表单名称',
  `form_type` varchar(16) NOT NULL COMMENT '表单类型',
  `tag_type` varchar(16) NOT NULL COMMENT '标签类型',
  `upload_type` tinyint(1) DEFAULT NULL COMMENT '上传配置',
  `param` varchar(255) DEFAULT NULL COMMENT '参数',
  `value` text COMMENT '内容',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注',
  `rank` tinyint(2) NOT NULL DEFAULT '0' COMMENT '排序',
  `is_show` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否显示',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '角色状态1可用0不用',
  `create_user` varchar(32) DEFAULT NULL COMMENT '添加人',
  `create_time` varchar(20) DEFAULT NULL COMMENT '添加时间',
  `update_user` varchar(32) DEFAULT NULL COMMENT '修改时间',
  `update_time` varchar(20) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=74 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='系统配置表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pay_system_config`
--

LOCK TABLES `pay_system_config` WRITE;
/*!40000 ALTER TABLE `pay_system_config` DISABLE KEYS */;
INSERT INTO `pay_system_config` VALUES (1,1,'网站标题','title','text','input',0,'','ALittleAdmin','systemConfig(\"title\")',90,1,1,'1','1582792265','1','1583855342'),(2,1,'网站图标','favicon','file','input',0,'','','',89,1,1,'1','1582793160',NULL,NULL),(6,1,'后台LOGO','admin_logo','file','input',0,'','','',85,1,1,'1','1582793393','1','1582793700'),(7,1,'版权信息','copyright','text','textarea',0,'','Power by ALittleAdmin.','',84,1,1,'1','1582793470','1','1582793495'),(33,2,'存储方式','storage_type','radio','input',0,'1=>本地储存\n2=>腾讯云COS','1','',0,1,1,'1','1588819285',NULL,NULL),(34,2,'CDN域名','storage_domain','text','input',0,'','','',0,1,1,'1','1588819651','1','1588828871'),(35,2,'SecretId','storage_secretid','text','input',0,'','','',0,1,1,'1','1588820386','1','1608110715'),(36,2,'SecretKey','storage_secretkey','text','input',0,'','','',0,1,1,'1','1588820426','1','1608110724'),(37,2,'存储位置','storage_region','text','input',0,'','','腾讯云COS填写',0,1,1,'1','1588821134','1','1588828897'),(38,2,'存储桶名称','storage_bucket','text','input',0,'','','',0,1,1,'1','1588821538','1','1608110744'),(39,4,'SMTP服务器','mail_host','text','input',0,'','smtp.qq.com','',0,1,1,'1','1588835717',NULL,NULL),(40,4,'邮箱用户名','mail_username','text','input',0,'','','',0,1,1,'1','1588835775','1','1588836096'),(41,4,'授权码','mail_password','text','input',0,'','','',0,1,1,'1','1588835807',NULL,NULL),(42,4,'服务器端口','mail_port','text','input',0,'','465','',0,1,1,'1','1588836004',NULL,NULL),(43,4,'发件人','mail_from','text','input',0,'','','',0,1,1,'1','1588836080',NULL,NULL),(44,4,'发件人签名','mail_from_name','text','input',0,'','只能支付一点点','',0,1,1,'1','1588844572','1','1588845488'),(52,1,'网站域名','domain','text','input',0,'','','',0,1,1,'1','1588858018',NULL,NULL),(64,4,'邮件开关','mail_type','radio','input',0,'0=>关闭\r\n1=>开启','0','邮件功能总开关',0,1,1,'1','1589507116',NULL,NULL),(68,1,' 商户ID','appid','text','input',0,'','10001','默认商户id，无需修改',110,1,1,'1','1658967427',NULL,NULL),(69,1,'商户秘钥','appkey','text','input',0,'','bU3WfCaAAyjsrfNbEXL3km9kUbvaGvxs','自行修改,建议使用32位非规则字符串',109,1,1,'1','1658967538',NULL,NULL),(70,1,'监听秘钥','listenkey','text','input',0,'','a123456','建议16位长度不规则秘钥，与监听端APP保持一致',108,1,1,'1','1658967624',NULL,NULL),(71,4,' 限额通知','limit_notify','radio','input',0,'0=>关闭\n1=>开启','1','当某通道限额时当天通知一次',0,1,1,'1','1658968109',NULL,NULL),(72,4,'无通道通知','empty_notify','radio','input',0,'0=>关闭\n1=>开启','1','所有通道限额且触发支付时通知,每拉起一次支付就通知一次',0,1,1,'1','1658968217',NULL,NULL),(73,4,'通知邮箱','notify_email','text','input',0,'','','接收通知的邮箱,建议使用app随时接收，并添加发送邮箱作为白名单',0,1,1,'1','1658968316',NULL,NULL);
/*!40000 ALTER TABLE `pay_system_config` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pay_system_config_tab`
--

DROP TABLE IF EXISTS `pay_system_config_tab`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pay_system_config_tab` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '分类ID',
  `name` varchar(64) NOT NULL COMMENT '分类名称',
  `rank` tinyint(2) NOT NULL DEFAULT '0' COMMENT '排序',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '角色状态1可用0不用',
  `create_user` varchar(32) DEFAULT NULL COMMENT '添加人',
  `create_time` varchar(20) DEFAULT NULL COMMENT '添加时间',
  `update_user` varchar(32) DEFAULT NULL COMMENT '修改时间',
  `update_time` varchar(20) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='系统配置分类';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pay_system_config_tab`
--

LOCK TABLES `pay_system_config_tab` WRITE;
/*!40000 ALTER TABLE `pay_system_config_tab` DISABLE KEYS */;
INSERT INTO `pay_system_config_tab` VALUES (1,'基础配置',99,1,'1','1582784937','1','1583385482'),(2,'上传配置',98,1,'1','1582785701','1','1594175937'),(4,'邮件配置',96,1,'1','1582785719','1','1583385506');
/*!40000 ALTER TABLE `pay_system_config_tab` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pay_user`
--

DROP TABLE IF EXISTS `pay_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pay_user` (
  `uid` int(10) NOT NULL AUTO_INCREMENT COMMENT '用户ID',
  `nickname` varbinary(64) NOT NULL COMMENT '用户昵称',
  `avatar` varchar(255) NOT NULL COMMENT '头像',
  `tel` varchar(20) DEFAULT NULL COMMENT '电话',
  `password` varchar(64) DEFAULT NULL COMMENT '登录密码',
  `email` varchar(64) DEFAULT NULL COMMENT '邮箱',
  `sex` tinyint(2) NOT NULL DEFAULT '0' COMMENT '性别',
  `money` decimal(8,2) NOT NULL COMMENT '钱数',
  `integral` int(8) NOT NULL DEFAULT '0' COMMENT '积分',
  `level` tinyint(2) NOT NULL DEFAULT '1' COMMENT '用户等级',
  `last_ip` varchar(32) DEFAULT NULL COMMENT '上次登录IP',
  `remark` varchar(255) DEFAULT NULL COMMENT '介绍',
  `register_ip` varchar(32) DEFAULT NULL COMMENT '注册IP',
  `register_type` tinyint(1) NOT NULL COMMENT '注册类型 1微信 2手机号 3 小程序',
  `register_time` varchar(20) DEFAULT NULL COMMENT '注册时间',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态',
  PRIMARY KEY (`uid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='系统用户表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pay_user`
--

LOCK TABLES `pay_user` WRITE;
/*!40000 ALTER TABLE `pay_user` DISABLE KEYS */;
/*!40000 ALTER TABLE `pay_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pay_user_bill`
--

DROP TABLE IF EXISTS `pay_user_bill`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pay_user_bill` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '账单ID',
  `uid` int(10) NOT NULL COMMENT '用户ID',
  `source` varchar(10) NOT NULL COMMENT '来源',
  `oid` varchar(32) NOT NULL COMMENT '订单ID',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态',
  `cost` decimal(8,2) NOT NULL COMMENT '金额',
  `io` tinyint(1) NOT NULL COMMENT '1收入2支出',
  `remark` varchar(255) DEFAULT NULL COMMENT '订单备注',
  `add_time` varchar(20) NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='用户订单表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pay_user_bill`
--

LOCK TABLES `pay_user_bill` WRITE;
/*!40000 ALTER TABLE `pay_user_bill` DISABLE KEYS */;
INSERT INTO `pay_user_bill` VALUES (5,1,'1','1',1,1.00,1,NULL,'');
/*!40000 ALTER TABLE `pay_user_bill` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pay_user_message`
--

DROP TABLE IF EXISTS `pay_user_message`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pay_user_message` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '留言ID',
  `type` tinyint(2) NOT NULL COMMENT '留言来源 1CMS 2小程序',
  `uid` int(8) NOT NULL DEFAULT '0' COMMENT '用户ID 0为游客',
  `email` varchar(64) DEFAULT NULL COMMENT '邮件',
  `tel` varchar(20) DEFAULT NULL COMMENT '电话',
  `content` text COMMENT '留言内容',
  `add_time` varchar(20) DEFAULT NULL COMMENT '添加时间',
  `ip` varchar(32) DEFAULT NULL COMMENT 'IP',
  `user_agent` varchar(255) DEFAULT NULL COMMENT 'user_agent',
  `is_read` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否已读',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='用户留言表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pay_user_message`
--

LOCK TABLES `pay_user_message` WRITE;
/*!40000 ALTER TABLE `pay_user_message` DISABLE KEYS */;
/*!40000 ALTER TABLE `pay_user_message` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pay_user_order`
--

DROP TABLE IF EXISTS `pay_user_order`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pay_user_order` (
  `id` int(9) unsigned NOT NULL AUTO_INCREMENT COMMENT '订单ID',
  `oid` varchar(32) NOT NULL COMMENT '订单编号',
  `uid` int(9) NOT NULL COMMENT '用户ID',
  `source` int(8) NOT NULL COMMENT '订单来源 1 视频小程序',
  `pay_price` decimal(10,2) NOT NULL COMMENT '支付金额',
  `pay_time` varchar(20) DEFAULT NULL COMMENT '支付时间',
  `paid` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否支付',
  `add_time` varchar(20) DEFAULT NULL COMMENT '支付时间',
  `is_refund` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否退款 1 退款',
  `refund_price` decimal(10,2) DEFAULT NULL COMMENT '退款金额',
  `refund_reason` varchar(255) DEFAULT NULL COMMENT '退款原因',
  `refund_time` varchar(20) DEFAULT NULL COMMENT '退款时间',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注',
  `status` tinyint(255) NOT NULL DEFAULT '0' COMMENT '状态 0付款中 1 已付款 2 已退款',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='用户订单表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pay_user_order`
--

LOCK TABLES `pay_user_order` WRITE;
/*!40000 ALTER TABLE `pay_user_order` DISABLE KEYS */;
/*!40000 ALTER TABLE `pay_user_order` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2022-07-31 23:06:38
