<?php
// $Id: install.php 1062 2012-09-14 16:18:41Z i.bitcero $
// --------------------------------------------------------------
// Red México Common Utilities
// A framework for Red México Modules
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

function xoops_module_pre_uninstall_rmcommon($mod){
    
    // Restore previous configurations
    $db = XoopsDatabaseFactory::getDatabaseConnection();
    
    $db->queryF("UPDATE ".$db->prefix("config")." SET conf_value='default' WHERE conf_name='cpanel'");
    
    return true;
    
}

function xoops_module_uninstall_rmcommon($mod){

    $contents = file_get_contents(XOOPS_VAR_PATH.'/configs/xoopsconfig.php');
    $write = "if(file_exists(XOOPS_ROOT_PATH.'/modules/rmcommon/loader.php')) include_once XOOPS_ROOT_PATH.'/modules/rmcommon/loader.php';";
    if(strpos($contents,$write)!==FALSE)
        $contents = str_replace($write, '', $contents);

    file_put_contents(XOOPS_VAR_PATH.'/configs/xoopsconfig.php', $contents);
    
    xoops_setActiveModules();
    return true;
    
}

function xoops_module_install_rmcommon($mod){

    // Prepare welcome screen
    $domain = preg_replace("/http:\/\/|https:\/\//", '', XOOPS_URL);
    setcookie( "rmcwelcome", 1, time() + (365 * 86400), '/', $domain );

    // Restore previous configurations
    $db = XoopsDatabaseFactory::getDatabaseConnection();
    
    $db->queryF("UPDATE ".$db->prefix("config")." SET conf_value='redmexico' WHERE conf_name='cpanel'");
    
    // Temporary solution
    $contents = file_get_contents(XOOPS_VAR_PATH.'/configs/xoopsconfig.php');
    $write = "if(file_exists(XOOPS_ROOT_PATH.'/modules/rmcommon/loader.php')) include_once XOOPS_ROOT_PATH.'/modules/rmcommon/loader.php';";
    if(strpos($contents,$write)!==FALSE) return true;
    
    $pos = strpos($contents, '<?php');
    
    file_put_contents(XOOPS_VAR_PATH.'/configs/xoopsconfig.php', substr($contents, $pos, 5)."\n".$write."\n".substr($contents, $pos+5));
    xoops_setActiveModules();
    
    return true;
}

function xoops_module_update_rmcommon($mod, $prev){

    $db = XoopsDatabaseFactory::getDatabaseConnection();

    $result = $db->query("SHOW TABLES LIKE '" . $db->prefix("rmc_bkmod") . "'");

    if ( $db->getRowsNum($result) > 0 ) {
        /**
         * Update old tables
         */
        $db->queryF('RENAME TABLE `'.$db->prefix("rmc_bkmod").'` TO  `'.$db->prefix("mod_rmcommon_blocks_assignations").'` ;');
        $db->queryF('RENAME TABLE `'.$db->prefix("rmc_blocks").'` TO  `'.$db->prefix("mod_rmcommon_blocks").'` ;');
        $db->queryF('RENAME TABLE `'.$db->prefix("rmc_blocks_positions").'` TO  `'.$db->prefix("mod_rmcommon_blocks_positions").'` ;');
        $db->queryF('RENAME TABLE `'.$db->prefix("rmc_comments").'` TO  `'.$db->prefix("mod_rmcommon_comments").'` ;');
        $db->queryF('RENAME TABLE `'.$db->prefix("rmc_comusers").'` TO  `'.$db->prefix("mod_rmcommon_comments_assignations").'` ;');
        $db->queryF('RENAME TABLE `'.$db->prefix("rmc_images").'` TO  `'.$db->prefix("mod_rmcommon_images").'` ;');
        $db->queryF('RENAME TABLE `'.$db->prefix("rmc_img_cats").'` TO  `'.$db->prefix("mod_rmcommon_images_categories").'` ;');
        $db->queryF('RENAME TABLE `'.$db->prefix("rmc_plugins").'` TO  `'.$db->prefix("mod_rmcommon_plugins").'` ;');
        $db->queryF('RENAME TABLE `'.$db->prefix("rmc_settings").'` TO  `'.$db->prefix("mod_rmcommon_settings").'` ;');

        $db->queryF('ALTER TABLE  `'.$db->prefix("mod_rmcommon_blocks_assignations").'` ENGINE = INNODB');
        $db->queryF('ALTER TABLE  `'.$db->prefix("mod_rmcommon_blocks").'` ENGINE = INNODB');
        $db->queryF('ALTER TABLE  `'.$db->prefix("mod_rmcommon_blocks_positions").'` ENGINE = INNODB');
        $db->queryF('ALTER TABLE  `'.$db->prefix("mod_rmcommon_comments").'` ENGINE = INNODB');
        $db->queryF('ALTER TABLE  `'.$db->prefix("mod_rmcommon_comments_assignations").'` ENGINE = INNODB');
        $db->queryF('ALTER TABLE  `'.$db->prefix("mod_rmcommon_images").'` ENGINE = INNODB');
        $db->queryF('ALTER TABLE  `'.$db->prefix("mod_rmcommon_images_categories").'` ENGINE = INNODB');
        $db->queryF('ALTER TABLE  `'.$db->prefix("mod_rmcommon_plugins").'` ENGINE = INNODB');
        $db->queryF('ALTER TABLE  `'.$db->prefix("mod_rmcommon_settings").'` ENGINE = INNODB');

    }

    $result = $db->query("SHOW TABLES LIKE '" . $db->prefix("mod_rmcommon_notifications") . "'");
    if ( $db->getRowsNum($result) <= 0 ) {
        /**
         * Create notifications table if not exists
         */
        $sql = 'CREATE TABLE `' . $db->prefix("mod_rmcommon_notifications") . '` (
                `id_notification` int(11) NOT NULL AUTO_INCREMENT,
                  `event` varchar(50) NOT NULL,
                  `element` varchar(50) NOT NULL,
                  `params` varchar(50) NOT NULL,
                  `uid` int(11) NOT NULL,
                  `type` varchar(10) NOT NULL DEFAULT \'module\',
                  `date` datetime NOT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;';
        $db->queryF( $sql );

        $sql = 'ALTER TABLE `' . $db->prefix("mod_rmcommon_notifications") . '`
                ADD PRIMARY KEY (`id_notification`), ADD KEY `event` (`event`), ADD KEY `element` (`element`), ADD KEY `uid` (`uid`);';
        $db->queryF( $sql );

        $sql = 'ALTER TABLE `' . $db->prefix("mod_rmcommon_notifications") . '`
                MODIFY `id_notification` int(11) NOT NULL AUTO_INCREMENT;COMMIT;';
        $db->queryF( $sql );

    }

    // Prepare welcome screen
    $domain = preg_replace("/http:\/\/|https:\/\//", '', XOOPS_URL);
    setcookie( "rmcwelcome", 1, time() + (365 * 86400), '/', $domain );

    return true;

}
