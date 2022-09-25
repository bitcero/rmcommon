<h1 class="cu-section-title"><?php _e('Dashboard', 'rmcommon'); ?></h1>

<!-- Top Widgets -->
<div class="top-widgets row">
    <div class="col-12 col-md-6 col-xl-4 mb-3">
      <?php echo $counterSystem->getHtml(); ?>
    </div>

    <div class="col-12 col-md-6 col-xl-8">
      <?php $widget_statistics->display(); ?>
    </div>

    <div class="col-sm-6 col-md-4 mb-3">
      <?php echo $counterModules->getHtml(); ?>
    </div>

    <div class="col-sm-6 col-md-4 mb-3">
        <?php echo $counterUsers->getHtml(); ?>
    </div>

    <div class="col-sm-6 col-md-4 mb-3">
        <?php echo $counterComments->getHtml(); ?>
    </div>
</div>

<div class="clearfix visible-xs"></div>

<div data-news="load" data-boxes="load" data-module="rmcommon" data-target="#rmc-recent-news" data-box="rmcommon-dashboard" data-container="dashboard">

    <div class="size-1" data-dashboard="item">
        <div class="cu-box box-primary" id="recent-comments">
            <div class="box-header">
                <span class="fa fa-caret-up box-handler"></span>
                <h3 class="box-title"><?php _e('Recent Comments', 'rmcommon'); ?></h3>
            </div>
            <div class="box-content">
                <ul class="comments">
                    <?php foreach ($comments as $comment): ?>
                        <li>
                            <p><?php echo $comment->text; ?></p>
                            <cite><a href="<?php echo RMUris::relative_url('/userinfo.php?uid=' . $comment->poster->id); ?>">&mdash; <?php echo $comment->poster->name; ?></a></cite>
                            <time><?php echo $comment->date; ?></time>
                            <span><a href="<?php echo $comment->item_url; ?>"><?php _e('View', 'rmcommon'); ?></a></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="box-footer text-right">
                <a href="comments.php" class="btn btn-link"><?php _e('View all comments', 'rmcommon'); ?></a>
            </div>
        </div>
    </div>

    <div class="size-1" data-dashboard="item" style="display: none;" id="updater-info">
        <div class="cu-box box-warning">
            <div class="box-header">
                <h3 class="box-title"><?php echo sprintf(__('%s Updates Available!', 'rmcommon'), '<span class="badge badge-warning">%s</span>'); ?></h3>
            </div>
            <div class="box-content">
                <p class="text-warning"><?php echo sprintf(__('Please %s to view available updates.', 'rmcommon'), '<a href="updates.php">' . __('click here', 'rmcommon') . '</a>'); ?></p>
            </div>
        </div>
    </div>

    <!-- Management Tools -->
    <div class="size-2" data-dashboard="item">
        <div class="cu-box box-teal" id="management-box">
            <div class="box-header">
                <span class="fa fa-caret-up box-handler"></span>
                <h3 class="box-title"><?php _e('Management', 'rmcommon'); ?></h3>
            </div>

            <div class="box-content">
                <ul class="management-tools">
                    <?php foreach ($managementTools as $tool): ?>
                        <li>
                            <a href="<?php echo $tool->link; ?>">
                                <?php echo $cuIcons->getIcon($tool->icon . ' text-' . $tool->color); ?>
                                <span class="title"><?php echo $tool->caption; ?></span>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

        </div>
    </div>

    <!-- Recent News -->
    <div class="size-1" data-dashboard="item">
        <div class="cu-box">
          <?php
          $user = $common->widgets()->load('rmcommon', 'UserCard');
          $user->setup([
            'type' => 'large-header',
            'image' => 'https://www.gravatar.com/avatar/a888698732624c0a1d4da48f1e5c6bb4?s=200',
            'name' => 'Eduardo Cortes',
            'link' => 'https://www.eduardocortes.mx',
            'charge' => 'Web Developer (Freelance)',
            'mainButton' => [
              'caption' => __('Website', 'mywords'),
              'link' => 'https://www.eduardocortes.mx',
              'icon' => 'svg-rmcommon-user',
            ],
            'color' => 'gray',
            'highlight' => 'bottom',
            'info' => __('Hello! I\'m the developer of Common Utilities and other modules that you\'ve propabily had used. If you are interested on my work, please visit my website to find more information.', 'mywords'),
            'social' => [
              [
                'icon' => 'svg-rmcommon-world',
                'link' => 'https://www.eduardocortes.mx/',
              ],
              [
                'icon' => 'svg-rmcommon-twitter',
                'link' => 'https://www.twitter.com/bitcero',
              ],
              [
                'icon' => 'svg-rmcommon-instagram',
                'link' => 'https://www.instagram.com/bitcero',
              ],
              [
                'icon' => 'svg-rmcommon-github',
                'link' => 'https://bitcero.dev',
              ],
            ],
          ]);
          $user->display();
          ?>
        </div>
    </div>

    <div class="size-1" data-dashboard="item">
        <div class="cu-box">
            <div class="box-header">
                <span class="fa fa-caret-up box-handler"></span>
                <h3 class="box-title"><?php _e('Recent News', 'rmcommon'); ?></h3>
            </div>
            <div class="box-content collapsable" id="rmc-recent-news">

            </div>
            <div class="box-footer">
                <a href="http://www.eduardocortes.mx" target="_blank" class="btn btn-link">
                  <?php _e('Author\'s Website', 'rmcommon'); ?>
                </a>
                <a href="http://xoops.org" target="_blank" class="btn btn-link pull-right">
                  <?php echo $cuIcons->getIcon('svg-rmcommon-xoops'); ?>
                    Xoops.org
                </a>
            </div>
        </div>
    </div>
    <!--// End recent news -->

    <!-- Third part panels -->
    <?php if ($dashboardPanels): foreach ($dashboardPanels as $panel): ?>
        <?php echo $panel; ?>
    <?php endforeach; endif; ?>

</div>
