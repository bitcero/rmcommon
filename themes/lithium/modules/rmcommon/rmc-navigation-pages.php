<div class="pagination-container">
    <nav aria-label="<?php _e('Page navigation', 'rmcommon'); ?>">
        <ul class="pagination justify-content-center">
          <?php if ($total_pages > $steps && $current_page > $steps - 1): ?>
              <li class="page-item">
                  <a href="<?php echo str_replace('{PAGE_NUM}', 1, $url); ?>"
                     title="<?php _e('First Page', 'rmcommon'); ?>" class="page-link">
                    <?php _e('First', 'rmcommon'); ?>
                  </a>
              </li>
          <?php endif; ?>

          <?php if ($current_page > 1): ?>
              <li class="page-item">
                  <a href="<?php echo str_replace('{PAGE_NUM}', $current_page - 1, $url); ?>"
                     title="<?php _e('Previous Page', 'rmcommon'); ?>" class="page-link">
                      &laquo; <?php _e('Previous', 'rmcommon'); ?>
                  </a>
              </li>
          <?php endif; ?>

          <?php
          for ($i = $start; $i <= $end; $i++):
            if ($i == $current_page):
              ?>
                <li class="page-item disabled"><a class="page-link"><?php echo $i; ?></a></li>
            <?php else: ?>
                <li class="page-item">
                    <a href="<?php echo str_replace('{PAGE_NUM}', $i, $url); ?>"
                       title="<?php echo sprintf(__('Page %u', 'rmcommon'), $i); ?>" class="page-link">
                      <?php echo $i; ?>
                    </a>
                </li>
            <?php
            endif;
          endfor;
          ?>

          <?php if ($current_page < $total_pages): ?>
              <li class="page-item">
                  <a href="<?php echo str_replace('{PAGE_NUM}', $current_page + 1, $url); ?>"
                     title="<?php _e('Next Page', 'rmcommon'); ?>" class="page-link">
                    <?php _e('Next', 'rmcommon'); ?> &raquo;
                  </a>
              </li>
          <?php endif; ?>

          <?php if ($total_pages > $steps && $current_page < ($total_pages - $steps) + 2): ?>
              <li class="page-item">
                  <a href="<?php echo str_replace('{PAGE_NUM}', $total_pages, $url); ?>"
                     title="<?php _e('Last Page', 'rmcommon'); ?>" class="page-link">
                    <?php _e('Last', 'rmcommon'); ?>
                  </a>
              </li>
          <?php endif; ?>
        </ul>
    </nav>
</div>
