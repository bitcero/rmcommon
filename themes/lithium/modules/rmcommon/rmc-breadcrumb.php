<?php global $xoopsModule; ?>
<?php $total_crumbs = count($this->crumbs); ?>
<?php if ($total_crumbs > 0): ?>
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="d-none d-md-block ps-3">
        <ol class="breadcrumb rmc-breadcrumb align-items-center">
          <?php if (defined('XOOPS_CPFUNC_LOADED')): ?>
              <li class="breadcrumb-item"><a href="<?php echo RMCURL; ?>" title="<?php _e('Dashboard', 'rmcommon'); ?>">
                  <?php echo $common->icons()->getIcon('svg-lithium-home', [], false); ?>
                  </a></li>
            <?php if ('rmcommon' != $xoopsModule->dirname()): ?>
                  <li class="breadcrumb-item">
                      <a class="text-truncate" href="<?php echo $xoopsModule->hasadmin() ? XOOPS_URL . '/modules/' . $xoopsModule->getVar('dirname') . '/' . $xoopsModule->getInfo('adminindex') : ''; ?>">
                          <strong><?php echo $xoopsModule->name(); ?></strong>
                      </a>
                  </li>
            <?php endif; ?>
          <?php else: ?>
              <li><a href="<?php echo XOOPS_URL; ?>" title="<?php _e('Home', 'rmcommon'); ?>"><span
                              class="fa fa-home"></span></a></li>
            <?php if ($xoopsModule && $xoopsModule->getInfo('hasMain')): ?>
                  <li>
                      <a class="text-truncate" href="<?php echo false === $controller ? XOOPS_URL . '/modules/' . $xoopsModule->getVar('dirname') . '/' : $controller->get_main_link(); ?>">
                        <?php echo $xoopsModule->name(); ?>
                      </a>
                  </li>
            <?php endif; ?>
          <?php endif; ?>
          <?php foreach ($this->crumbs as $i => $item): ?>
            <?php if ('' != $item['link']): ?>
                  <li class="breadcrumb-item">
                      <a class="text-truncate" href="<?php echo $item['link']; ?>">
                        <?php if ('' != $item['icon']): ?>
                            <span class="<?php echo $item['icon']; ?>"></span>
                        <?php endif; ?>
                        <?php echo $item['caption']; ?>
                      </a>
                  </li>
            <?php else: ?>
                  <li class="breadcrumb-item active text-truncate" aria-current="page">
                    <?php if ('' != $item['icon']): ?>
                        <span class="<?php echo $item['icon']; ?>"></span>
                    <?php endif; ?>
                    <?php echo $item['caption']; ?>
                  </li>
            <?php endif; ?>
          <?php endforeach; ?>
        </ol>
    </nav>
    <!--// ENd breadcrumb -->
<?php endif; ?>
