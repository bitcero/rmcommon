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
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,700;1,300;1,400;1,500;1,700&display=swap" rel="stylesheet">
        <?php echo $lithiumStyles; ?>

        <!-- Scripts -->
        <?php echo $lithiumScripts['header']; ?>
        <?php echo RMTemplate::get()->inline_scripts(); ?>

        <?php if ($showXoopsMetas) {
    require_once __DIR__ . '/include/xoops_metas.php';
} ?>
    </head>
    <body <?php echo RMTemplate::getInstance()->render_attributes('body'); ?>>
        <?php require __DIR__ . '/tpls/sidebar.php'; ?>

        <?php require __DIR__ . '/tpls/topbar.php'; ?>

        <main id="li-main-content">
            <!-- Top bar -->

            <!-- Content -->
          <?php require __DIR__ . '/tpls/content.php'; ?>
        </main>

        <!-- System messages -->
        <?php require __DIR__ . '/tpls/messages.php'; ?>

        <div id="he-context-help">
            <span class="fa fa-question-circle help-switch"></span>
            <span class="fa fa-times help-close"></span>
        </div>

        <input type="hidden" name="cu_token" id="cu-token" value="<?php echo $xoopsSecurity->createToken(0, 'CUTOKEN'); ?>">

        <?php echo $lithiumScripts['footer']; ?>
        <?php echo RMTemplate::get()->inline_scripts(1); ?>
        <?php echo $lithiumScripts['heads']; ?>

        <?php if (1 == $xoopsConfig['debug_mode']): ?>
        <div id="he-logger-output">
            <a href="#" class="close-logger"><?php echo $cuIcons->getIcon('svg-rmcommon-double-arrow-up'); ?> <?php _e('Debug Log', 'rmcommon'); ?></a>
            <!--{xo-logger-output}-->
        </div>
        <?php endif; ?>

    </body>
</html>
