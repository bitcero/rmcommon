<div id="li-content" class="<?php echo $common->breadcrumb()->count() > 0 ? 'with-breadcrumb' : ''; ?>">

  <?php if (!empty($right_widgets)): ?>
      <div class="row">
          <div class="col-md-8 col-lg-9">
            <?php echo $content; ?>
          </div>
          <div class="col-md-4 col-lg-3">
            <?php foreach ($right_widgets as $widget): ?>
                <div class="cu-box box-primary">
                  <?php if ('' != $widget['title']): ?>
                      <div class="box-header">
                          <span class="box-handler"><span class="fa fa-caret-down"></span></span>
                          <h4 class="box-title"><?php echo $widget['title']; ?></h4>
                      </div>
                  <?php endif; ?>
                    <div class="box-content">
                      <?php echo $widget['content']; ?>
                    </div>
                </div>
            <?php endforeach; ?>
          </div>
      </div>
  <?php else: ?>
    <?php echo $content; ?>
  <?php endif; ?>

    <div id="he-footer">
      <?php echo sprintf(
        __('Powered by %s and %s', 'rmcommon'),
        '<strong><a href="https://rmcommon.bitcero.dev" target="_blank">' . RMModules::get_module_version('rmcommon') . '</a></strong>',
        '<a href="http://xoops.org" target="_blank">' . XOOPS_VERSION . '</a>'
      ); ?>.
    </div>
</div>