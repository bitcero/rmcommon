CREATE TABLE `mod_rmcommon_blocks` (
  `bid` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `element` varchar(50) NOT NULL,
  `element_type` varchar(20) NOT NULL,
  `options` text NOT NULL,
  `name` varchar(150) NOT NULL DEFAULT '',
  `description` varchar(255) NOT NULL,
  `canvas` smallint(5) unsigned NOT NULL DEFAULT '0',
  `weight` smallint(5) unsigned NOT NULL DEFAULT '0',
  `visible` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `type` varchar(6) NOT NULL,
  `content_type` varchar(20) NOT NULL,
  `content` text NOT NULL,
  `isactive` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `dirname` varchar(50) NOT NULL DEFAULT '',
  `file` varchar(150) NOT NULL,
  `show_func` varchar(50) NOT NULL DEFAULT '',
  `edit_func` varchar(50) NOT NULL DEFAULT '',
  `template` varchar(150) NOT NULL,
  `bcachetime` int(10) NOT NULL,
  PRIMARY KEY (`bid`),
  KEY `element` (`element`),
  KEY `visible` (`visible`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE `mod_rmcommon_blocks_assignations` (
  `bid` int(11) NOT NULL,
  `mid` int(11) NOT NULL,
  `page` varchar(50) NOT NULL,
  KEY `bid` (`bid`,`mid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `mod_rmcommon_blocks_positions` (
  `id_position` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `tag` varchar(150) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_position`),
  UNIQUE KEY `tag` (`tag`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE `mod_rmcommon_comments` (
  `id_com` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_obj` varchar(50) NOT NULL,
  `type` varchar(50) NOT NULL DEFAULT 'module',
  `parent` bigint(20) NOT NULL DEFAULT '0',
  `params` varchar(200) NOT NULL,
  `content` text NOT NULL,
  `user` int(11) NOT NULL,
  `ip` varchar(40) NOT NULL,
  `posted` int(10) NOT NULL DEFAULT '0',
  `status` varchar(10) NOT NULL DEFAULT 'waiting',
  PRIMARY KEY (`id_com`),
  KEY `id_obj` (`id_obj`,`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `mod_rmcommon_comments_assignations` (
  `id_user` int(11) NOT NULL AUTO_INCREMENT,
  `xuid` int(11) NOT NULL DEFAULT '0',
  `name` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `url` varchar(150) NOT NULL,
  PRIMARY KEY (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `mod_rmcommon_images` (
  `id_img` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `desc` text NOT NULL,
  `date` int(10) NOT NULL,
  `file` varchar(150) NOT NULL,
  `cat` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  PRIMARY KEY (`id_img`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE `mod_rmcommon_images_categories` (
  `id_cat` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `status` varchar(10) NOT NULL DEFAULT 'active',
  `groups` text NOT NULL,
  `filesize` int(11) NOT NULL DEFAULT '0',
  `sizeunit` mediumint(9) NOT NULL DEFAULT '1024',
  `sizes` text NOT NULL,
  PRIMARY KEY (`id_cat`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE `mod_rmcommon_permissions` (
  `group` int(11) NOT NULL,
  `element` varchar(50) NOT NULL,
  `key` varchar(50) NOT NULL,
  KEY `group` (`group`,`element`,`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `mod_rmcommon_plugins` (
  `id_plugin` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `description` text NOT NULL,
  `dir` varchar(100) NOT NULL,
  `version` text NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_plugin`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE `mod_rmcommon_settings` (
  `conf_id` int(11) NOT NULL AUTO_INCREMENT,
  `element` varchar(50) NOT NULL,
  `name` varchar(50) NOT NULL,
  `type` varchar(20) NOT NULL,
  `value` text NOT NULL,
  `valuetype` varchar(20) NOT NULL,
  PRIMARY KEY (`conf_id`),
  KEY `element` (`element`,`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
