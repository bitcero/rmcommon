<div class="pagination-container">
    <ul class="pagination">
        <?php if ($caption): ?><span class="pages_caption"><?php _e('Pages:', 'rmcommon'); ?></span><?php endif; ?>
        <?php if ($total_pages > $steps && $current_page > $steps - 1): ?>
            <li><a href="<?php echo str_replace('{PAGE_NUM}', 1, $url); ?>" title="<?php _e('First Page', 'rmcommon'); ?>"><?php _e('First', 'rmcommon'); ?></a></li>
        <?php endif; ?>

        <?php if ($current_page > 1): ?>
            <li><a href="<?php echo str_replace('{PAGE_NUM}', $current_page - 1, $url); ?>" title="<?php _e('Previous Page', 'rmcommon'); ?>"><?php _e('&laquo;', 'rmcommon'); ?></a></li>
        <?php endif; ?>

        <?php
        for ($i = $start; $i <= $end; $i++):
            if ($i == $current_page):
                ?>
                <li class="active"><span><?php echo $i; ?></span></li>
            <?php else: ?>
                <li><a href="<?php echo str_replace('{PAGE_NUM}', $i, $url); ?>" title="<?php echo sprintf(__('Page %u', 'rmcommon'), $i); ?>"><?php echo $i; ?></a></li>
            <?php
            endif;
        endfor;
        ?>

        <?php if ($current_page < $total_pages): ?>
            <li><a href="<?php echo str_replace('{PAGE_NUM}', $current_page + 1, $url); ?>" title="<?php _e('Next Page', 'rmcommon'); ?>"><?php _e('&raquo;', 'rmcommon'); ?></a></li>
        <?php endif; ?>

        <?php if ($total_pages > $steps && $current_page < ($total_pages - $steps) + 2): ?>
            <li><a href="<?php echo str_replace('{PAGE_NUM}', $total_pages, $url); ?>" title="<?php _e('Last Page', 'rmcommon'); ?>"><?php _e('Last', 'rmcommon'); ?></a></li>
        <?php endif; ?>
    </ul>
</div>
