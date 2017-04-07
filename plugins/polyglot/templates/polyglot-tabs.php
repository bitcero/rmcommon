<ul class="nav nav-tabs nav-tabs-color" role="tablist">
    <li role="presentation"<?php echo 'dashboard' == $page ? ' class="active"' : ''; ?>>
        <a href="<?php echo 'dashboard' == $page ? '#' : 'plugins.php?p=polyglot'; ?>">
            <?php echo $cuIcons->getIcon('svg-rmcommon-comments2'); ?>
            <span class="caption"><?php _e('Languages', 'polyglot'); ?></span>
        </a>
    </li>
    <li role="presentation"<?php echo 'adjusts' == $page ? ' class="active"' : ''; ?>>
        <a href="<?php echo 'adjusts' == $page ? '#' : 'plugins.php?p=polyglot&amp;page=adjust'; ?>">
            <?php echo $cuIcons->getIcon('svg-rmcommon-tools'); ?>
            <span class="caption"><?php _e('Adjustments', 'polyglot'); ?></span>
        </a>
    </li>
</ul>