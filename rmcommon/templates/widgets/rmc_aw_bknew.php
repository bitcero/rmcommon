<div id="bks-wnew" class="bkbk_forms">
    <label><?php _e('Block', 'rmcommon'); ?></label>
    <select name="bk_module" id="bk-module">
        <option value=""><?php _e('Select block...', 'rmcommon'); ?></option>
        <?php foreach ($blocks as $dir => $block): ?>
            <?php if (!empty($block['blocks'])): ?><option value="" style="font-weight: bold;" disabled="disabled" class="disabled"><?php echo $block['name']; ?></option><?php endif; ?>
            <?php foreach ($block['blocks'] as $id => $bk): ?>
            <option value="<?php echo $dir; ?>-<?php echo $id; ?>">&mdash; <?php echo $bk['name']; ?></option>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </select>
    <input type="button" id="add-now-btn" value="<?php _e('Add Block', 'rmcommon'); ?>" />
</div>
