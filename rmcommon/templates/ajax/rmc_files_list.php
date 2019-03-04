<div class="table-responsive">
    <table class="table table-condensed table-hover">
        <thead>
        <tr>
            <th align="right"><?php _e('#', 'rmcommon'); ?></th>
            <th><?php _e('File', 'rmcommon'); ?></th>
            <th align="center"><?php _e('Mode', 'rmcommon'); ?></th>
            <th align="center"><?php _e('Size', 'rmcommon'); ?></th>
            <th align="center"><?php _e('Action', 'rmcommon'); ?></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($files as $i => $file): ?>
            <tr>
                <td align="right"><?php echo $i + 1; ?></td>
                <td>
                    <strong><?php echo $file['name']; ?></strong>
                    <span class="path"><?php echo sprintf(__('Path: %s', 'rmcommon'), $file['path']); ?></span>
                </td>
                <td align="center"><?php echo $file['mode']; ?></td>
                <td align="center"><?php echo $file['size'] > 0 ? $rmUtil->formatBytesSize($file['size']) : $file['size']; ?></td>
                <td align="center"><?php echo $file['action']; ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
