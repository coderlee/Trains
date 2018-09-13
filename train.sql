/*
SQLyog Enterprise Trial - MySQL GUI v7.11 
MySQL - 5.6.70 : Database - ryb_train
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

CREATE DATABASE /*!32312 IF NOT EXISTS*/`ryb_train` /*!40100 DEFAULT CHARACTER SET utf8mb4 */;

USE `ryb_train`;

/*Table structure for table `admin_settings` */

DROP TABLE IF EXISTS `admin_settings`;

CREATE TABLE `admin_settings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT '名称',
  `code` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT '代码',
  `value` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `describe` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT '描述',
  `tag` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT '分类标签',
  `type` enum('text','textarea','select','date','combodate','datetime','typeahead','checklist','select2','address','wysihtml5') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'text' COMMENT '类型',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC;

/*Table structure for table `article_cate` */

DROP TABLE IF EXISTS `article_cate`;

CREATE TABLE `article_cate` (
  `article_id` int(10) unsigned NOT NULL,
  `article_category_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`article_id`,`article_category_id`),
  KEY `article_cate_article_category_id_foreign` (`article_category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC;

/*Table structure for table `article_categories` */

DROP TABLE IF EXISTS `article_categories`;

CREATE TABLE `article_categories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT '分类名称',
  `describe` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '描述',
  `display_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '分类英文别名',
  `parent_id` int(11) NOT NULL DEFAULT '0' COMMENT '父类ID',
  `order` int(11) NOT NULL DEFAULT '1' COMMENT '排序，asc',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC;

/*Table structure for table `article_tag` */

DROP TABLE IF EXISTS `article_tag`;

CREATE TABLE `article_tag` (
  `article_id` int(10) unsigned NOT NULL,
  `article_tag_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`article_id`,`article_tag_id`),
  KEY `article_tag_article_tag_id_foreign` (`article_tag_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC;

/*Table structure for table `article_tags` */

DROP TABLE IF EXISTS `article_tags`;

CREATE TABLE `article_tags` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT '标签名',
  `display_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '标签英文别名',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC;

/*Table structure for table `articles` */

DROP TABLE IF EXISTS `articles`;

CREATE TABLE `articles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT '文章标题',
  `abstract` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT '文章摘要',
  `content` longtext COLLATE utf8_unicode_ci NOT NULL COMMENT '文章内容',
  `content_md` longtext COLLATE utf8_unicode_ci NOT NULL COMMENT '文章内容MarkDown',
  `article_image` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '文章特色图片',
  `article_status` tinyint(4) NOT NULL COMMENT '文章状态，1：公共，2：私有',
  `comment_status` tinyint(4) NOT NULL COMMENT '评论状态',
  `display_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '文章英文别名',
  `comment_count` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '评论总数',
  `author` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT '作者',
  `user_id` int(11) NOT NULL COMMENT '作者ID',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC;

/*Table structure for table `cache` */

DROP TABLE IF EXISTS `cache`;

CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `value` text COLLATE utf8_unicode_ci NOT NULL,
  `expiration` int(11) NOT NULL,
  UNIQUE KEY `cache_key_unique` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC;

/*Table structure for table `failed_jobs` */

DROP TABLE IF EXISTS `failed_jobs`;

CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `connection` text COLLATE utf8_unicode_ci NOT NULL,
  `queue` text COLLATE utf8_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC;

/*Table structure for table `images` */

DROP TABLE IF EXISTS `images`;

CREATE TABLE `images` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT '用户ID',
  `user_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT '用户名',
  `path` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT '文件路径',
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT '文件名',
  `filename` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT '用户命名文件名',
  `extension` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT '文件尾缀',
  `year_month` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT '上传年月',
  `size` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT '文件大小',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=85 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC;

/*Table structure for table `jobs` */

DROP TABLE IF EXISTS `jobs`;

CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8_unicode_ci NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_reserved_at_index` (`queue`,`reserved_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC;

/*Table structure for table `menus` */

DROP TABLE IF EXISTS `menus`;

CREATE TABLE `menus` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `parent_id` int(11) NOT NULL DEFAULT '0' COMMENT '父级ID',
  `order` int(11) NOT NULL DEFAULT '0' COMMENT '排序，asc',
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL COMMENT '名称',
  `icon` varchar(50) COLLATE utf8_unicode_ci NOT NULL COMMENT '字体图标',
  `uri` varchar(50) COLLATE utf8_unicode_ci NOT NULL COMMENT '路由名',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC;

/*Table structure for table `migrations` */

DROP TABLE IF EXISTS `migrations`;

CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC;

/*Table structure for table `password_resets` */

DROP TABLE IF EXISTS `password_resets`;

CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`),
  KEY `password_resets_token_index` (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC;

/*Table structure for table `permission_role` */

DROP TABLE IF EXISTS `permission_role`;

CREATE TABLE `permission_role` (
  `permission_id` int(10) unsigned NOT NULL,
  `role_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `permission_role_role_id_foreign` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC;

/*Table structure for table `permissions` */

DROP TABLE IF EXISTS `permissions`;

CREATE TABLE `permissions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `display_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `uri` varchar(50) COLLATE utf8_unicode_ci NOT NULL COMMENT '路由名',
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC;

/*Table structure for table `role_user` */

DROP TABLE IF EXISTS `role_user`;

CREATE TABLE `role_user` (
  `user_id` int(10) unsigned NOT NULL,
  `role_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`user_id`,`role_id`),
  KEY `role_user_role_id_foreign` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC;

/*Table structure for table `roles` */

DROP TABLE IF EXISTS `roles`;

CREATE TABLE `roles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `display_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC;

/*Table structure for table `t_apply_students` */

DROP TABLE IF EXISTS `t_apply_students`;

CREATE TABLE `t_apply_students` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `apply_user` int(10) unsigned NOT NULL COMMENT '报名人id',
  `student_id` int(50) unsigned NOT NULL COMMENT '关联nursery_students',
  `contract_no` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '园所合同号',
  `train_id` int(10) unsigned NOT NULL COMMENT '关联trains',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1082 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

/*Table structure for table `t_nursery_students` */

DROP TABLE IF EXISTS `t_nursery_students`;

CREATE TABLE `t_nursery_students` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `apply_user` int(10) unsigned NOT NULL COMMENT '报名人id',
  `contract_no` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '园所合同号',
  `student_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '学员姓名',
  `student_sex` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '1男2女0未知',
  `student_phone` char(11) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '学员性别',
  `student_position` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '学员岗位',
  `school` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '毕业院校',
  `education` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '学历',
  `profession` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '专业',
  `idcard` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '身份证',
  `card_z` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '身份证正面',
  `card_f` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '身份证反面',
  `health_1` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '健康证',
  `health_2` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '健康证',
  `health_3` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '健康证',
  `labor_1` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '劳动合同首页',
  `labor_2` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '劳动合同尾页',
  `learnership` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '培训协议',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=287 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

/*Table structure for table `t_order_students` */

DROP TABLE IF EXISTS `t_order_students`;

CREATE TABLE `t_order_students` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(10) unsigned NOT NULL COMMENT '关联train_order',
  `student_id` int(10) unsigned NOT NULL COMMENT '关联nursery_students',
  `fee` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '费用',
  `is_paid` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1支付0未支付',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '-1退训 0 未审核 1 审核通过未签到 2 审核未通过 3已签到 4已完成',
  `check_recoder` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '操作人',
  `remark` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '备注、退训原因',
  `sign_time` timestamp NULL DEFAULT NULL COMMENT '签到时间',
  `check_time` timestamp NULL DEFAULT NULL COMMENT '审核时间',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=345 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

/*Table structure for table `t_pay_info` */

DROP TABLE IF EXISTS `t_pay_info`;

CREATE TABLE `t_pay_info` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_sn` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '订单号',
  `trade_no` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '微信支付订单号',
  `total_fee` decimal(10,2) DEFAULT NULL COMMENT '支付金额',
  `pay_time` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '支付时间',
  `openid` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '用户标识',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `NewIndex1` (`order_sn`)
) ENGINE=InnoDB AUTO_INCREMENT=198 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

/*Table structure for table `t_phone_code` */

DROP TABLE IF EXISTS `t_phone_code`;

CREATE TABLE `t_phone_code` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `phone` char(11) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '手机号',
  `code` char(6) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '验证码',
  `send_time` int(10) unsigned NOT NULL COMMENT '发送时间',
  `dead_time` int(10) unsigned NOT NULL COMMENT '过期时间',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0有效 1失效',
  `next_time` int(11) NOT NULL COMMENT '重新发送时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=341 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

/*Table structure for table `t_refund` */

DROP TABLE IF EXISTS `t_refund`;

CREATE TABLE `t_refund` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `train_id` int(11) NOT NULL,
  `refund_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0 待退款 1 已审核 2已退款',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

/*Table structure for table `t_refund_log` */

DROP TABLE IF EXISTS `t_refund_log`;

CREATE TABLE `t_refund_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_sn` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '订单号',
  `transaction_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '微信交易号',
  `total_fee` decimal(10,2) unsigned NOT NULL COMMENT '交易金额',
  `refund_no` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '退款号',
  `refund_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '退款交易号',
  `refund_fee` decimal(10,2) unsigned NOT NULL COMMENT '退款金额',
  `refund_desc` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '退款描述',
  `is_refund` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0 未退款 1已退款',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '创建日期',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '更新日期',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

/*Table structure for table `t_train_charge` */

DROP TABLE IF EXISTS `t_train_charge`;

CREATE TABLE `t_train_charge` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `train_id` int(10) unsigned NOT NULL COMMENT '关联t_trains',
  `charge_way` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '付费方式1/2/3',
  `unit` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 人 2园所',
  `max_nursery_num` int(10) unsigned NOT NULL COMMENT '限制园所报名人数',
  `min_num` int(11) DEFAULT NULL COMMENT '团购最低人数',
  `attr1_name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `attr1_value` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `attr1_price` decimal(10,2) DEFAULT '0.00',
  `attr2_name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `attr2_value` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `attr2_price` decimal(10,2) DEFAULT '0.00',
  `attr3_name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `attr3_value` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `attr3_price` decimal(10,2) DEFAULT '0.00',
  `is_card` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否上传身份证',
  `is_health` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否上传健康证',
  `is_labor` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否上传劳动合同',
  `is_learnership` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否上传培训协议',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `NewIndex1` (`train_id`)
) ENGINE=InnoDB AUTO_INCREMENT=94 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

/*Table structure for table `t_train_order` */

DROP TABLE IF EXISTS `t_train_order`;

CREATE TABLE `t_train_order` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_sn` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '订单标识',
  `contract_no` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '园所合同号',
  `park_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '园所名称',
  `apply_user` int(10) unsigned DEFAULT NULL COMMENT '报名人，关联wx_user',
  `apply_user_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '报名人姓名',
  `apply_phone` char(11) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '报名手机号',
  `apply_num` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '报名人数',
  `apply_form` tinyint(1) unsigned NOT NULL COMMENT '1 单人 2团购',
  `train_id` int(11) NOT NULL COMMENT '关联trains',
  `total_fee` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '支付费用',
  `is_paid` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0 未支付 1 已支付',
  `payment` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 微信2 支付宝 3现金 4 汇款',
  `pay_time` timestamp NULL DEFAULT NULL COMMENT '支付时间',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0 正常 1 退款 2 未支付取消 3审核中 4审核失败 5部分审核 6已审核 7已完成 ',
  `from` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '1 线上 2线下',
  `remark` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '备注',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=249 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

/*Table structure for table `t_trains` */

DROP TABLE IF EXISTS `t_trains`;

CREATE TABLE `t_trains` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '培训标题',
  `banner` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'banner图',
  `pre_num` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '预计报名人数',
  `sale_num` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '已报名人数',
  `jia_sale_num` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '虚拟报名数',
  `train_start` date DEFAULT NULL,
  `train_end` date DEFAULT NULL,
  `train_adress` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '培训地址',
  `apply_start` date DEFAULT NULL,
  `apply_end` date DEFAULT NULL,
  `desc` text COLLATE utf8mb4_unicode_ci,
  `desc_md` text COLLATE utf8mb4_unicode_ci,
  `is_free` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '1 收费 0 免费',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '1 待发布 2 已发布 0删除',
  `sort` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `recorder` int(11) DEFAULT NULL,
  `shengming` text COLLATE utf8mb4_unicode_ci COMMENT '声明',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

/*Table structure for table `t_wx_user` */

DROP TABLE IF EXISTS `t_wx_user`;

CREATE TABLE `t_wx_user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `open_id` varchar(100) NOT NULL,
  `contract_no` varchar(50) DEFAULT NULL COMMENT '园所合同号',
  `nick_name` varchar(50) NOT NULL,
  `avatar_url` varchar(255) NOT NULL,
  `city` varchar(50) NOT NULL,
  `country` varchar(50) NOT NULL,
  `mobile` varchar(50) DEFAULT NULL COMMENT '微信绑定手机号',
  `gender` tinyint(1) NOT NULL DEFAULT '0',
  `province` varchar(50) NOT NULL,
  `app_id` varchar(150) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `NewIndex1` (`open_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2693 DEFAULT CHARSET=utf8mb4;

/*Table structure for table `users` */

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
