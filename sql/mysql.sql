CREATE TABLE `mod_rmcommon_blocks` (
  `bid` mediumint(8) UNSIGNED NOT NULL,
  `element` varchar(50) NOT NULL,
  `element_type` varchar(20) NOT NULL,
  `options` text NOT NULL,
  `name` varchar(150) NOT NULL DEFAULT '',
  `description` varchar(255) NOT NULL,
  `canvas` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `weight` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `visible` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `type` varchar(6) NOT NULL,
  `content_type` varchar(20) NOT NULL,
  `content` text NOT NULL,
  `isactive` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `dirname` varchar(50) NOT NULL DEFAULT '',
  `file` varchar(150) NOT NULL,
  `show_func` varchar(50) NOT NULL DEFAULT '',
  `edit_func` varchar(50) NOT NULL DEFAULT '',
  `template` varchar(150) NOT NULL,
  `bcachetime` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `mod_rmcommon_blocks_assignations` (
  `bid` int(11) NOT NULL,
  `mid` int(11) NOT NULL,
  `page` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `mod_rmcommon_blocks_positions` (
  `id_position` int(11) NOT NULL,
  `name` varchar(150) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `tag` varchar(150) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `mod_rmcommon_comments` (
  `id_com` bigint(20) NOT NULL,
  `id_obj` varchar(50) NOT NULL,
  `type` varchar(50) NOT NULL DEFAULT 'module',
  `parent` bigint(20) NOT NULL DEFAULT '0',
  `params` varchar(200) NOT NULL,
  `content` text NOT NULL,
  `user` int(11) NOT NULL,
  `ip` varchar(40) NOT NULL,
  `posted` int(10) NOT NULL DEFAULT '0',
  `status` varchar(10) NOT NULL DEFAULT 'waiting'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `mod_rmcommon_comments_assignations` (
  `id_user` int(11) NOT NULL,
  `xuid` int(11) NOT NULL DEFAULT '0',
  `name` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `url` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `mod_rmcommon_images` (
  `id_img` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `desc` text NOT NULL,
  `date` int(10) NOT NULL,
  `file` varchar(150) NOT NULL,
  `cat` int(11) NOT NULL,
  `uid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `mod_rmcommon_images_categories` (
  `id_cat` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `status` varchar(10) NOT NULL DEFAULT 'active',
  `groups` text NOT NULL,
  `filesize` int(11) NOT NULL DEFAULT '0',
  `sizeunit` mediumint(9) NOT NULL DEFAULT '1024',
  `sizes` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `mod_rmcommon_licensing` (
  `id_license` int(11) NOT NULL,
  `identifier` varchar(32) NOT NULL,
  `element` varchar(50) NOT NULL,
  `type` varchar(10) NOT NULL,
  `data` text NOT NULL,
  `date` int(11) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `mod_rmcommon_notifications` (
  `id_notification` int(11) NOT NULL,
  `event` varchar(50) NOT NULL,
  `element` varchar(50) NOT NULL,
  `params` varchar(50) NOT NULL,
  `uid` int(11) NOT NULL,
  `type` varchar(10) NOT NULL DEFAULT 'module',
  `date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `mod_rmcommon_permissions` (
  `group` int(11) NOT NULL,
  `element` varchar(50) NOT NULL,
  `key` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `mod_rmcommon_plugins` (
  `id_plugin` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `description` text NOT NULL,
  `dir` varchar(100) NOT NULL,
  `version` text NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `mod_rmcommon_settings` (
  `conf_id` int(11) NOT NULL,
  `element` varchar(50) NOT NULL,
  `name` varchar(50) NOT NULL,
  `type` varchar(20) NOT NULL,
  `value` text NOT NULL,
  `valuetype` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE `mod_rmcommon_blocks`
  ADD PRIMARY KEY (`bid`),
  ADD KEY `element` (`element`),
  ADD KEY `visible` (`visible`);

ALTER TABLE `mod_rmcommon_blocks_assignations`
  ADD KEY `bid` (`bid`,`mid`);

ALTER TABLE `mod_rmcommon_blocks_positions`
  ADD PRIMARY KEY (`id_position`),
  ADD UNIQUE KEY `tag` (`tag`);

ALTER TABLE `mod_rmcommon_comments`
  ADD PRIMARY KEY (`id_com`),
  ADD KEY `id_obj` (`id_obj`,`type`);

ALTER TABLE `mod_rmcommon_comments_assignations`
  ADD PRIMARY KEY (`id_user`);

ALTER TABLE `mod_rmcommon_images`
  ADD PRIMARY KEY (`id_img`);

ALTER TABLE `mod_rmcommon_images_categories`
  ADD PRIMARY KEY (`id_cat`);

ALTER TABLE `mod_rmcommon_licensing`
  ADD PRIMARY KEY (`id_license`),
  ADD UNIQUE KEY `indentifier` (`identifier`),
  ADD KEY `element` (`element`),
  ADD KEY `type` (`type`);

ALTER TABLE `mod_rmcommon_notifications`
  ADD PRIMARY KEY (`id_notification`),
  ADD KEY `event` (`event`),
  ADD KEY `element` (`element`),
  ADD KEY `uid` (`uid`);

ALTER TABLE `mod_rmcommon_permissions`
  ADD KEY `group` (`group`,`element`,`key`);

ALTER TABLE `mod_rmcommon_plugins`
  ADD PRIMARY KEY (`id_plugin`);

ALTER TABLE `mod_rmcommon_settings`
  ADD PRIMARY KEY (`conf_id`),
  ADD KEY `element` (`element`,`name`);


ALTER TABLE `mod_rmcommon_blocks`
  MODIFY `bid` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `mod_rmcommon_blocks_positions`
  MODIFY `id_position` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `mod_rmcommon_comments`
  MODIFY `id_com` bigint(20) NOT NULL AUTO_INCREMENT;
ALTER TABLE `mod_rmcommon_comments_assignations`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `mod_rmcommon_images`
  MODIFY `id_img` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `mod_rmcommon_images_categories`
  MODIFY `id_cat` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `mod_rmcommon_licensing`
  MODIFY `id_license` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `mod_rmcommon_notifications`
  MODIFY `id_notification` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `mod_rmcommon_plugins`
  MODIFY `id_plugin` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `mod_rmcommon_settings`
  MODIFY `conf_id` int(11) NOT NULL AUTO_INCREMENT;