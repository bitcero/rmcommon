<h1 class="cu-section-title" xmlns="http://www.w3.org/1999/html"><?php _e('Modules Management','rmcommon'); ?></h1>
<script type="text/javascript">
    <!--
    var message = "<?php _e('Do you really want to uninstall selected module?','rmcommon'); ?>";
    var message_upd = "<?php _e('Do you really want to update selected module?','rmcommon'); ?>";
    var message_dis = "<?php _e('Do you really want to disable selected module?','rmcommon'); ?>";
    var message_name = "<?php _e('New name must be different from current name!','rmcommon'); ?>";
    var message_wname = "<?php _e('You must provide a new name!','twop6'); ?>";
    -->
</script>

<form action="modules.php" method="post" id="form-modules">
    <input type="hidden" name="action" id="mod-action" value="" />
    <input type="hidden" name="module" id="mod-dir" value="" />
    <?php echo $xoopsSecurity->getTokenHTML(); ?>
</form>

<div class="table-responsive">
    <table class="table table-bordered table-hover" id="des-mods-container">
        <thead>
        <tr>
            <th class="logo"><?php _e('Image','twop6'); ?></th>
            <th><?php _e('Name','twop6'); ?></th>
            <th class="text-center"><?php _e('Version','twop6'); ?></th>
            <th class="text-center"><?php _e('Author','twop6'); ?></th>
            <th colspan="4" class="text-center"><?php _e('Options','twop6'); ?></th>
        </tr>
        </thead>
        <tfoot>
        <tr>
            <th class="logo"><?php _e('Image','twop6'); ?></th>
            <th><?php _e('Name','twop6'); ?></th>
            <th><?php _e('Version','twop6'); ?></th>
            <th><?php _e('Author','twop6'); ?></th>
            <th><?php _e('Options','twop6'); ?></th>
        </tr>
        </tfoot>
        <tbody>
        <?php foreach($modules as $mod): ?>
            <tr class="<?php echo tpl_cycle("even,odd"); ?><?php echo $mod['active'] ? '' : ' inactive'; ?>" id="module-<?php echo $mod['dirname']; ?>" valign="middle" align="center">
                <td class="logo">
                    <a href="<?php if($mod['active']): ?><?php echo $mod['admin_link']; ?><?php else: ?>#<?php endif; ?>" title="<?php echo $mod['realname']; ?>"><img src="<?php echo $mod['icon']; ?>" alt="<?php echo $mod['name']; ?>" /></a>
                </td>
                <td class="name" align="left">
                    <span class="the_name">
                    <?php if($mod['active']): ?>
                        <a href="<?php echo $mod['admin_link']; ?>"><?php echo $mod['name']; ?></a>
                    <?php else: ?>
                        <?php echo $mod['name']; ?>
                    <?php endif; ?>
                    </span>
                    <a href="#" class="rename"><span class="fa fa-edit"></span> <?php _e('Edit','rmcommon'); ?></a>
                    <span class="descriptions"><?php echo $mod['description']; ?></span>
                </td>
                <td align="center">
                    <?php echo $mod['version']; ?>
                </td>
                <td class="author">
                    <?php if($mod['author_mail']!=''): ?>
                        <a href="mailto:<?php echo $mod['author_mail']; ?>"><?php echo $mod['author']; ?></a>
                    <?php else: ?>
                        <?php echo $mod['author']; ?>
                    <?php endif; ?>
                    <span class="hidden_data">
                        <span class="adminlink"><?php echo $mod['admin_link']; ?></span>
                        <span class="link"><?php echo $mod['link']; ?></span>
                        <span class="name"><?php echo $mod['name']; ?></span>
                        <span class="id"><?php echo $mod['id']; ?></span>
                        <span class="icon"><?php echo $mod['icon']; ?></span>
                        <span class="image"><?php echo $mod['image']; ?></span>
                        <span class="oname"><?php echo $mod['realname']; ?></span>
                        <span class="version"><?php echo $mod['version']; ?></span>
                        <span class="dirname"><?php echo $mod['dirname']; ?></span>
                        <span class="author"><?php echo $mod['author']; ?></span>
                        <span class="mail"><?php echo $mod['author_mail']; ?></span>
                        <span class="web"><?php echo $mod['author_web']; ?></span>
                        <span class="url"><?php echo $mod['author_url']; ?></span>
                        <span class="license"><?php echo $mod['license']; ?></span>
                        <span class="help"><?php echo $mod['help']; ?></span>
                        <span class="active"><?php echo $mod['active']; ?></span>
                        <span class="social">
                            <?php if( $mod['social'] ): ?>
                                <?php foreach( $mod['social'] as $social ): ?>
                                <a href="<?php echo $social['url']; ?>" target="_blank" title="<?php echo $social['title']; ?>">
                                    <span class="fa <?php echo RMFormat::social_icon( $social['type'] ); ?>"></span>
                                </a>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </span>
                    </span>
                </td>
                <td class="actions">
                    <div class="btn-group">
                        <a href="#" class="btn btn-default data_button" title="<?php _e('Show Information','twop6'); ?>">
                            <span class="fa fa-info-circle"></span>
                        </a>
                        <a href="#" class="btn btn-default update_button" title="<?php _e('Update','rmcommon'); ?>">
                            <span class="fa fa-refresh"></span>
                        </a>
                        <?php if($mod['active']): ?>
                                <?php if($mod['dirname']!='system'): ?>
                                    <a href="#" class="btn btn-default disable_button" title="<?php _e('Disable','rmcommon'); ?>">
                                        <span class="fa fa-lock"></span>
                                    </a>
                                <?php else: ?>
                                    <a href="#" class="btn btn-default">
                                        <span class="fa fa-lock"></span>
                                    </a>
                                <?php endif; ?>
                        <?php endif; ?>
                        <?php if(!$mod['active']): ?>
                                <a href="#" class="btn btn-default enable_button" title="<?php _e('Enable','rmcommon'); ?>">
                                    <span class="fa fa-unlock"></span>
                                </a>
                        <?php endif; ?>
                        <?php if($mod['dirname']!='system'): ?>
                            <a href="#" class="btn btn-danger uninstall_button" title="<?php _e('Uninstall','rmcommon'); ?>" data-dir="<?php echo $mod['dirname']; ?>">
                                <span class="fa fa-minus-circle"></span>
                            </a>
                        <?php else: ?>
                            <a href="#" class="btn btn-danger">
                                <span class="fa fa-minus-circle"></span>
                            </a>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div class="modal fade" id="info-module">
    <div class="modal-dialog xlarge">
        <div class="modal-content">
            <div class="modal-header cu-titlebar">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><?php _e('Module Information','twop6'); ?></h4>
            </div>
            <div class="modal-body info-container">
                <div class="row header">
                    <div class="col-xs-4 col-sm-4 col-md-3 col-lg-3">
                        <img src="#" alt="#" class="img-responsive" />
                    </div>
                    <div class="col-xs-8 col-sm-8 col-md-9 col-lg-9 text-right">
                        <h3></h3>
                        <span class="help-block desc"></span>
                    </div>
                </div>
                <hr>

                <div class="row form-group">
                    <div class="col-sm-6">
                        <label><?php _e('Current Version:','twop6'); ?></label>
                        <span class="form-control version"></span>
                    </div>
                    <div class="col-sm-6">
                        <label><?php _e('Directory:','twop6'); ?></label>
                        <span class="form-control dirname"></span>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-sm-6">
                        <label><?php _e('Author(s):','twop6'); ?></label>
                        <span class="form-control author"><?php _e('Not provided','twop6'); ?></span>
                    </div>
                    <div class="col-sm-6">
                        <label><?php _e('Web site:','twop6'); ?></label>
                        <span class="form-control web"><?php _e('Not provided','twop6'); ?></span>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-sm-6">
                        <label><?php _e('License:','twop6'); ?></label>
                        <span class="form-control license"></span>
                    </div>
                    <div class="col-sm-6">
                        <label><?php _e('Help:','twop6'); ?></label>
                        <span class="form-control help"><?php _e('Not provided','twop6'); ?></span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12 text-center">
                        <span class="social"></span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal"><?php _e('Close', 'rmcommon'); ?></button>
            </div>
        </div>
    </div>
</div>
