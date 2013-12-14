<?php
$form = new RMActiveForm(array(
    'id'            => 'form-add-group',
    'submit-via'    => 'ajax',
    'validation'    => 'local',
    'action'        => RMCURL.'/groups.php'
));
?>
<div class="cu-content-with-footer">
    <?php $form->open(); ?>

    <div class="row">
        <div class="col-lg-4">

            <div class="form-group">
                <label for="group-name"><?php _e('Group Name','rmcommon') ; ?></label>
                <input type="text" name="name" id="group-name" class="form-control required" value="<?php echo $group->getVar('name'); ?>">
            </div>

            <div class="form-group">
                <label for="group-description"><?php _e('Description','rmcommon') ; ?></label>
                <textarea type="text" name="description" id="group-description" class="form-control required" rows="4"><?php echo $group->getVar('description', 'e'); ?></textarea>
            </div>

            <p class="help-block"><?php _e("Add <strong>admin rights</strong> and <strong>access rights</strong> to new group. Remember that specifix permissions allows to control with more detail the operation sthat users can do according to the groups that they belongs.",'rmcommon'); ?></p>

        </div>
        <div class="col-lg-8">

            <ul class="nav nav-tabs">
                <li class="active"><a href="#admin" data-toggle="tab"><?php _e('Basic Permissions','rmcommon'); ?></a></li>
                <li><a href="#read" data-toggle="tab"><?php _e('Specific Permissions','rmcommon'); ?></a></li>
            </ul>

            <div class="tab-content">

                <div class="tab-pane active" id="admin">

                    <div class="row">

                        <div class="col-lg-6">

                            <h5><?php _e('Admin Rights','rmcommon'); ?></h5>

                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="admin_check" value="0" class="check-all" data-checkbox="admin-module">
                                    <strong><?php _e("Todos los módulos",'rmcommon'); ?></strong>
                                </label>
                            </div>

                            <?php foreach ( $modules as $module ): ?>
                                <?php if ( !$module->hasadmin ) continue; ?>
                                <div class="checkbox">
                                    <label>
                                        <input
                                            data-oncheck="admin-module"
                                            type="checkbox"
                                            name="admin_modules[<?php echo $module->mid; ?>]"
                                            value="<?php echo $module->mid; ?>"
                                            <?php echo $module->admin ? ' checked' : ''; ?>>
                                        <?php echo $module->name; ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>

                        </div>

                        <div class="col-lg-6">

                            <h5><?php _e('Access Rights','rmcommon'); ?></h5>

                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="read_check" value="0" class="check-all" data-checkbox="read-module">
                                    <strong><?php _e("Todos los módulos",'rmcommon'); ?></strong>
                                </label>
                            </div>
                            <?php foreach ( $modules as $module ): ?>
                                <?php if ( !$module->hasmain ) continue; ?>
                                <div class="checkbox">
                                    <label>
                                        <input
                                            data-oncheck="read-module"
                                            type="checkbox"
                                            name="read_modules[<?php echo $module->mid; ?>]"
                                            value="<?php echo $module->mid; ?>"
                                            <?php echo $module->read ? ' checked' : ''; ?>>
                                        <?php echo $module->name; ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>

                        </div>

                    </div>

                </div>

                <div class="tab-pane" id="read">

                    <?php foreach ( $modules as $item ): ?>
                        <?php if( empty( $item->permissions ) ) continue; ?>
                        <h4><?php echo $item->name; ?></h4>

                        <div class="users-columns-permissions">
                            <div class="checkbox">
                                <label>
                                    <input class="check-all" type="checkbox" value="0" name="<?php echo $item->dirname; ?>_perms[]" data-checkbox="<?php echo $item->dirname; ?>-perms">
                                    <strong><?php _e('Todos','rmcommon'); ?></strong>
                                </label>
                            </div>
                            <?php foreach ($item->permissions as $id => $perm): ?>
                                <div class="checkbox">
                                    <label>
                                        <input data-oncheck="<?php echo $item->dirname; ?>-perms" type="checkbox" name="specific_perms[<?php echo $item->dirname; ?>][]" value="<?php echo $id; ?>"<?php echo isset($item->privileges->$id) ? ' checked' : ($perm['default']=='allow' ? ' checked' : ''); ?>>
                                        <?php echo $perm['caption']; ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <hr>
                    <?php endforeach; ?>

                </div>

            </div>

        </div>
    </div>

    <div class="cu-content-footer">

        <button type="button" class="btn btn-default" data-dismiss="modal"><?php _e('Cerrar','rmcommon'); ?></button>
        <button type="submit" class="btn btn-primary"><?php _e('Guardar Datos','rmcommon'); ?></button>

    </div>
    <input type="hidden" name="id" id="group-id" value="<?php echo $group->id(); ?>">
    <input type="hidden" name="action" value="save-group">
    <?php $form->close(); ?>
</div>
