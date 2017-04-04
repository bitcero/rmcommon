<?php if( empty( $modules ) ): ?>
    <span class="label label-default"><?php _e('There are not modules that support rewrite feature.', 'rmcommon'); ?></span>
<?php endif; ?>

<?php foreach( $modules as $module ): ?>

    <div class="form-group<?php echo $this->getClass(); ?>">
        <label for="<?php echo $this->id(); ?>-<?php echo $module->getVar('dirname'); ?>"><strong><?php echo $module->getVar('name'); ?></strong></label>
        <div class="input-group">
            <span class="input-group-addon"><?php echo XOOPS_URL; ?></span>
            <input
                class="form-control" id="<?php echo $this->id(); ?>-<?php echo $module->getVar('dirname'); ?>"
                name="<?php echo $this->getName(); ?>[<?php echo $module->getVar('dirname'); ?>]"
                value="<?php echo isset($this->default[$module->getVar('dirname')]) ? $this->default[$module->getVar('dirname')] : '/modules/' . $module->getVar('dirname'); ?>">
        </div>
    </div>

<?php endforeach; ?>
