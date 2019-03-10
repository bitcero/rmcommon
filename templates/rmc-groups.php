<h1 class="cu-section-title"><?php _e('Existing Groups', 'rmcommon'); ?></h1>

<div class="panel panel-blue-grey">
    <div class="panel-heading">
        <h3 class="panel-title">
            <?php _e('Existing Groups', 'rmcommon'); ?>
        </h3>
    </div>
        <div class="table-responsive">

            <table class="table table-hover table-striped activator-container" id="groups-list">
                <thead>
                <tr>
                    <th class="text-center" style="width: 40px;">&nbsp;</th>
                    <th class="text-center"><?php _e('ID', 'rmcommon'); ?></th>
                    <th><?php _e('Name', 'rmcommon'); ?></th>
                    <th><?php _e('Description', 'rmcommon'); ?></th>
                    <th class="text-center"><?php _e('Users', 'rmcommon'); ?></th>
                </tr>
                </thead>
                <tbody>
                <?php if (empty($groups)): ?>
                    <tr>
                        <td class="text-center">
                            <span class="label label-info"><?php _e('There are not groups registered yet.', 'rmcommon'); ?></span>
                        </td>
                    </tr>
                <?php endif; ?>
                <?php foreach ($groups as $group): ?>
                    <tr>
                        <td class="text-center">
                            <label class="rdbox rdbox-default">
                                <input type="radio" name="ids[]" value="<?php echo
                                $group->groupid; ?>"
                                       data-switch>
                                <span></span>
                            </label>
                        </td>
                        <td class="text-center">
                            <?php echo $group->groupid; ?>
                        </td>
                        <td>
                            <strong><?php echo $group->name; ?></strong>
                        </td>
                        <td>
                            <?php echo $group->description; ?>
                        </td>
                        <td class="text-center">
                            <?php echo $group->total_users; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>

        </div>

    <div class="panel-footer">
        <div class="row">
            <div class="col-md-5 col-lg-5">
                <?php echo $navigation->display(false); ?>
            </div>
            <div class="col-md-7 col-lg-7 text-right">
                <?php echo $navigation->get_showing(); ?>
            </div>
        </div>
    </div>
</div>
