<h1 class="rmc_titles"><?php _e('Available Updates','rmcommon'); ?></h1>

<div class="row-fluid" style="margin-bottom: 10px;">
    <div class="span8">
        <button type="button" class="btn btn-inverse" id="refresh-updates"><i class="icon-refresh icon-white"></i> <?php _e('Check for Updates','rmcommon'); ?></button>
        <button type="button" class="btn btn-info" id="upds-ftp"><i class="icon-upload"></i> <?php _e('FTP Settings','rmcommon'); ?></button>
    </div>
    <div class="span4 rm-comprobation-legend text-success" align="right">
        <?php echo sprintf(__('Last comprobation: %s','rmcommon'), '<strong>'.$tf->format($updates['date']).'</strong>'); ?>
    </div>
</div>

<div id="ftp-settings">
    <form class="form-inline" id="ftp-form">
        <div class="input-prepend">
            <span class="add-on"><?php _e('Server:','rmcommon'); ?></span>
            <input type="text" class="input input-small" name="ftp_server" id="ftp-server" value="<?php echo $ftpserver; ?>" />
        </div>
        <div class="input-prepend">
            <span class="add-on"><?php _e('Port:','rmcommon'); ?></span>
            <input type="text" class="input input-small" name="ftp_port" id="ftp-port" value="21" />
        </div>
        <div class="input-prepend">
            <span class="add-on"><?php _e('User:','rmcommon'); ?></span>
            <input type="text" class="input input-small" name="ftp_user" id="ftp-user" value="" />
        </div>
        <div class="input-prepend">
            <span class="add-on"><?php _e('Password:','rmcommon'); ?></span>
            <input type="password" class="input input-small" name="ftp_pass" id="ftp-pass" value="" />
        </div>
        <div class="input-prepend">
            <span class="add-on"><?php _e('XOOPS Directory:','rmcommon'); ?></span>
            <input type="text" class="input input-small" name="ftp_dir" id="ftp-dir" value="<?php echo $ftpdir; ?>" />
        </div>
        <button type="button" class="btn btn-primary"><?php _e('Save','rmcommon'); ?></button>
    </form>
    <p>
    <?php _e('XOOPS directory must match with this XOOPS installation, but only the relative patch to server root.','rmcommon'); ?>
    </p>
</div>


<div class="alert alert-info alert-block">
    <h4><?php _e('Notice','rmcommon'); ?></h4>
    <p><?php _e('Before to install updates be sure that target folders have writting permissions for web server. If you wish, you can configure the internal FTP Client in order to update without assign writting permissions.','rmcommon'); ?></p>
</div>

<span class="rm-loading text text-info"><?php _e('Searching for updates...','rmcommon'); ?></span>
<div id="rmc-updates">
    
</div>

<div id="upd-info-blocker"></div>
<div id="upd-info">
    <div class="xo-bluebar title window">
        <button type="button" class="close" onclick="$('#upd-info-blocker').click();">&times;</button>
        <?php _e('Update Details','rmcommon'); ?>
    </div>
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
<div id="upd-login" data-next="">
    <div class="title xo-bluebar">
        <button class="close" type="button">&times;</button>
        <i class="icon-user icon-white"></i> <?php _e('User Credentials','rmcommon'); ?>
    </div>
    <div class=" controls">
        <p><?php _e('Please provide your login credentials for site %site%.','rmcommon'); ?></p>
        <input type="text" name="uname" id="uname" value="" class="input-block-level" placeholder="<?php _e('Username','rmcommon'); ?>" />
        <input type="password" name="uname" id="upass" value="" class="input-block-level" placeholder="<?php _e('Password','rmcommon'); ?>" />
        <button type="button" class="btn btn-primary ok-login"><?php _e('Login','rmcommon'); ?></button>
        <button type="button" class="btn cancel-login"><?php _e('Cancel','rmcommon'); ?></button>
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
