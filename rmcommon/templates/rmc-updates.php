<h1 class="cu-section-title"><?php _e('Available Updates','rmcommon'); ?></h1>

<div class="row">
    <div class="col-md-8 col-lg-8">
        <button type="button" class="btn btn-warning" id="refresh-updates"><span class="fa fa-refresh"></span> <?php _e('Check for Updates','rmcommon'); ?></button>
        <button type="button" class="btn btn-info" id="upds-ftp"><span class="icon icon-settings"></span> <?php _e('FTP Settings','rmcommon'); ?></button>
    </div>
    <div class="col-md-4 col-lg-4 text-right">
        <span class="label label-success"><?php echo sprintf(__('Last comprobation: %s','rmcommon'), '<strong>'.$tf->format($updates['date']).'</strong>'); ?></span>
    </div>
</div>
<br>
<div id="ftp-settings" class="panel panel-default">

    <div class="panel-body">
        <div class="row">

            <form id="ftp-form">

                <div class="col-md-2 col-lg-2">
                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon" title="<?php _e('Server:','rmcommon'); ?>"><i class="fa fa-desktop"></i></span>
                            <input type="text" class="form-control" name="ftp_server" id="ftp-server" value="<?php echo $ftpserver; ?>" />
                        </div>
                    </div>
                </div>

                <div class="col-md-2 col-lg-2">
                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon"><?php _e('Port:','rmcommon'); ?></span>
                            <input type="text" class="form-control" name="ftp_port" id="ftp-port" value="21" />
                        </div>
                    </div>
                </div>

                <div class="col-md-2 col-lg-2">
                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon" title="<?php _e('User:','rmcommon'); ?>"><i class="fa fa-user"></i></span>
                            <input type="text" class="form-control" name="ftp_user" id="ftp-user" value="" />
                        </div>
                    </div>
                </div>

                <div class="col-md-2 col-lg-2">
                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon" title="<?php _e('Password:','rmcommon'); ?>"><i class="fa fa-keyboard-o"></i></span>
                            <input type="password" class="form-control" name="ftp_pass" id="ftp-pass" value="" />
                        </div>
                    </div>
                </div>

                <div class="col-md-2 col-lg-2">
                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon" title="<?php _e('XOOPS Directory:','rmcommon'); ?>"><i class="fa fa-folder-open"></i></span>
                            <input type="text" class="form-control" name="ftp_dir" id="ftp-dir" value="<?php echo $ftpdir; ?>" />
                        </div>
                    </div>
                </div>

                <div class="col-md-2 col-lg-2">
                    <button type="button" class="btn btn-warning btn-block"><i class="fa fa-check"></i> <?php _e('Save','rmcommon'); ?></button>
                </div>

            </form>

        </div>

        <div class="row">
            <div class="col-lg-12">
            <small class="help-block">
                <?php _e('XOOPS directory must match with this XOOPS installation, but only the relative patch to server root.','rmcommon'); ?>
            </small>
            </div>
        </div>
    </div>

</div>

<div class="alert alert-info">
    <button class="close" data-dismiss="alert">&times;</button>
    <p><strong><?php _e('Important:','rmcommon'); ?></strong> <?php _e('Before to install updates be sure that target folders have writting permissions for web server. If you wish, you can configure the internal FTP Client in order to update without assign writting permissions.','rmcommon'); ?></p>
</div>

<div class="row">
    <div class="col-lg-12">
        <span class="rm-loading text-primary"><span class="fa fa-refresh fa-spin"></span> <?php _e('Searching for updates...','rmcommon'); ?></span>
        <div id="rmc-updates">

        </div>
    </div>
</div>

<div id="upd-info" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header cu-titlebar">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><?php _e('Update Details','rmcommon'); ?></h4>
            </div>
            <div class="modal-body">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#details" data-toggle="tab"><?php _e('Details','rmcommon'); ?></a></li>
                    <li><a href="#files" data-toggle="tab"><?php _e('Files','rmcommon'); ?></a></li>
                </ul>
                <div class="tab-content tab-container">
                    <div class="tab-item tab-pane fade active in" id="details">

                    </div>
                    <div class="tab-item tab-pane fade" id="files">

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="upd-warning">
    <h4></h4>
    <p></p>
    <hr>
    <div class="pull-right">
    <button type="button" class="btn cancel-warning"><?php _e('Cancel','rmcommon'); ?></button>
    <button type="button" class="btn btn-primary continue-update"><?php _e('Continue &raquo;','rmcommon'); ?></button>
    </div>
</div>

<div id="login-blocker"></div>
<div id="upd-login" data-next="" class="modal-content">
    <div class="title cu-titlebar">
        <button class="close" type="button">&times;</button>
        <h4><i class="fa fa-user"></i> <?php _e('User Credentials','rmcommon'); ?></h4>
    </div>
    <div class="controls">
        <p class="help-block"><?php _e('Please provide your login credentials for site %site%.','rmcommon'); ?></p>
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    <label><?php _e('Username:','rmcommon'); ?></label>
                    <input type="text" name="uname" id="uname" value="" class="form-control" placeholder="<?php _e('Username','rmcommon'); ?>" />
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label><?php _e('Password:', 'rmcommon'); ?></label>
                    <input type="password" name="uname" id="upass" class="form-control" placeholder="<?php _e('Password','rmcommon'); ?>" />
                </div>
            </div>
        </div>
        <div class="form-group">
            <button type="button" class="btn btn-primary ok-login"><?php _e('Login','rmcommon'); ?></button> &nbsp;
            <button type="button" class="btn btn-default cancel-login"><?php _e('Cancel','rmcommon'); ?></button>
        </div>
    </div>
</div>

<div id="files-blocker"></div>
<div id="upd-run">
    <div class="title xo-bluebar">
        <button class="close" type="button">&times;</button>
        <?php _e('Executing Files...','rmcommon'); ?>
    </div>
    <iframe border="0"></iframe>
</div> 
