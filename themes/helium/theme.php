<!DOCTYPE HTML>
<html lang="<?php echo $cuSettings->lang; ?>" <?php echo RMTemplate::getInstance()->render_attributes('html'); ?>>
    <head>
        <meta charset="<?php echo $this->get_var('charset'); ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>
            <?php if ('' != $this->get_var('xoops_pagetitle')): ?>
                <?php echo $this->get_var('xoops_pagetitle'); ?> -
            <?php endif; ?>
            <?php echo isset($xoopsModule) ? $xoopsModule->getInfo('name') . ' - ' : ''; ?><?php echo $xoopsConfig['sitename']; ?>
        </title>
        <!-- Styles -->
        <?php echo $heliumStyles; ?>

        <!-- Scripts -->
        <?php echo $heliumScripts['header']; ?>
        <?php echo RMTemplate::get()->inline_scripts(); ?>

        <?php if ($showXoopsMetas) {
    require_once __DIR__ . '/include/xoops_metas.php';
} ?>
    </head>
    <body <?php echo RMTemplate::getInstance()->render_attributes('body'); ?>>

        <!-- Top bar -->
        <?php require __DIR__ . '/tpls/topbar.php'; ?>

        <?php require __DIR__ . '/tpls/sidebar.php'; ?>

        <!-- Content -->
        <?php require __DIR__ . '/tpls/content.php'; ?>

        <!-- System messages -->
        <?php require __DIR__ . '/tpls/messages.php'; ?>

        <div id="he-context-help">
            <span class="fa fa-question-circle help-switch"></span>
            <span class="fa fa-times help-close"></span>
        </div>

        <input type="hidden" name="cu_token" id="cu-token" value="<?php echo $xoopsSecurity->createToken(0, 'CUTOKEN'); ?>">

        <?php echo $heliumScripts['footer']; ?>
        <?php echo RMTemplate::get()->inline_scripts(1); ?>
        <?php echo $heliumScripts['heads']; ?>

        <?php if (1 == $xoopsConfig['debug_mode']): ?>
        <div id="he-logger-output">
            <a href="#" class="close-logger"><?php echo $cuIcons->getIcon('svg-rmcommon-double-arrow-up'); ?> <?php _e('Debug Log', 'rmcommon'); ?></a>
            <!--{xo-logger-output}-->
        </div>
        <?php endif; ?>

    </body>
</html>
