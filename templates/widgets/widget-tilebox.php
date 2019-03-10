<div <?php echo $attributes; ?>>
    <?php if ('user' == $settings->type): ?>

        <div class="avatar">
            <?php echo $common->services()->service('avatar')->getAvatar($user, 150); ?>
        </div>
        <div class="name">
            <?php echo '' != $user->getVar('name') ? $user->getVar('name') : $user->getVar('uname'); ?>
        </div>
        <div class="email">
            <?php echo $user->getVar('email'); ?>
        </div>

    <?php else: ?>

        <!-- Counter -->
        <?php if ('progress' != $settings->style && '' == $settings->link): ?>
        <div class="icon"><?php echo $cuIcons->getIcon($settings->icon . (true != $settings->solid && '' != $settings->color ? ' text-' . $settings->color : '')); ?></div>
        <?php endif; ?>
        <?php if ('' != $settings->link): ?>
        <div class="link">
            <a href="<?php echo $settings->link; ?>" class="btn btn-<?php echo '' != $settings->linkColor ? $settings->linkColor : 'primary'; ?>"><?php _e('View', 'rmcommon'); ?></a>
        </div>
        <?php endif; ?>
        <div class="caption <?php if (true != $settings->solid && '' != $settings->color): ?>text-<?php echo $settings->color; ?><?php endif; ?>"><?php echo $settings->caption; ?></div>
        <div class="counter"><?php echo $settings->counter; ?></div>

        <?php if ('progress' == $settings->style): ?>
        <div class="progress">
            <div class="progress-bar progress-bar-<?php echo $settings->color; ?> progress-bar-striped" role="progressbar" aria-valuenow="<?php echo $settings->progressValue; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $settings->progressValue; ?>%">
                <span class="sr-only"><?php echo sprintf(__('%u% complete', 'rmcommon'), $settings->progressValue); ?></span>
            </div>
        </div>
        <?php endif; ?>

        <?php if ('' != $settings->footer): ?>
        <div class="footer"><?php echo $settings->footer; ?></div>
        <?php endif; ?>
    <?php endif; ?>
</div>