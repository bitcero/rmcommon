<h1 class="cu-section-title"><i class="glyphicon glyphicon-user"></i> <?php _e('Users Management','rmcommon'); ?></h1>
<div id="users-filter-options">
    <form name="filterForm" id="filter-form" method="get" action="users.php">
    <div class="basic_options">
	<span class="sections">
		<label for="search-key"><?php _e('Search:','rmcommon'); ?></label>
		<input class="form-control" type="text" name="keyw" id="search-key" size="15" value="<?php echo rmc_server_var($_REQUEST, 'keyword', ''); ?>" />
	</span>
	<span class="sections">
		<label for="users-number"><?php _e('Show:','rmcommon'); ?></label>
		<input class="form-control" type="text" name="limit" id="users-number" size="6" value="<?php echo $limit; ?>" />
	</span>
	<span class="sections">
        <label>&nbsp;</label>
		<button type="submit" class="btn btn-primary form-control"><?php _e('Go Now!','rmcommon'); ?></button>
	</span>
	<span class="sections_right pull-right">
        <label>&nbsp;</label>
		<a href="javascript:;" id="show-search" class="btn btn-link"><?php _e('Advanced Search','rmcommon'); ?></a>
	</span>
    </div>

        <div id="users-advanced-options">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="row">

                        <div class="col-md-4 col-lg-4">

                            <div class="form-group">
                                <label for="user-email"><?php _e('Email:','rmcommon'); ?></label>
                                <input type="text" class="form-control" name="email" id="user-email" value="<?php echo RMTemplate::get()->get_var('srhemail'); ?>" size="20">
                            </div>

                        </div>

                        <div class="col-md-4 col-lg-4">
                            <div class="form-group">
                                <label for="user-url"><?php _e('Web site:','rmcommon'); ?></label>
                                <input type="text" class="form-control" name="url" id="user-url" size="20" value="">
                            </div>
                        </div>

                        <div class="col-md-4 col-lg-4">
                            <div class="form-group">
                                <label for="user-from"><?php _e('Country/Location:','rmcommon'); ?></label>
                                <input type="text" class="form-control" name="from" id="user-from" size="20" value="">
                            </div>
                        </div>

                    </div>

                    <div class="row">

                        <div class="col-md-4 col-lg-4">
                            <div class="form-group">
                                <label for="registered1"><?php _e('Registered between:','rmcommon'); ?></label><br>
                                <?php echo $register1->render(); ?> <?php _e('and','rmcommon'); ?> <?php echo $register2->render(); ?>
                            </div>
                        </div>

                        <div class="col-md-4 col-lg-4">
                            <div class="form-group">
                                <label for="login1"><?php _e('Last login between:','rmcommon'); ?></label><br>
                                <?php echo $login1->render(); ?> <?php _e('and','rmcommon'); ?> <?php echo $login2->render(); ?>
                            </div>
                        </div>

                        <div class="col-md-4 col-lg-4">
                            <div class="form-group">
                                <label for="<?php _e('Posts between:','rmcommon'); ?>"><?php _e('Posts between:','rmcommon'); ?></label><br>
                                <input type="text" class="form-control inline" name="posts1" id="users-posts1" value="0" size="5" />
                                <?php _e('and','rmcommon'); ?>
                                <input type="text" class="form-control inline" name="posts2" id="users-posts2" value="" size="5" />
                            </div>
                        </div>

                    </div>

                    <div class="row">

                        <div class="col-md-4 col-lg-4">
                            <div class="form-group">
                                <label for="users-mailok"><?php _e('Mail:','rmcommon'); ?></label>
                                <select name="mailok" id="users-mailok" class="form-control">
                                    <option value="-1"><?php _e('All users','rmcommon'); ?></option>
                                    <option value="1"><?php _e('Users that accept mail','rmcommon'); ?></option>
                                    <option value="0"><?php _e('Users that do\'nt accept mail','rmcommon'); ?></option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4 col-lg-4">
                            <div class="form-group">
                                <label for="users-actives"><?php _e('Status:','rmcommon'); ?></label>
                                <select name="actives" id="users-actives" class="form-control">
                                    <option value="-1"><?php _e('All users','rmcommon'); ?></option>
                                    <option value="1"><?php _e('Active users','rmcommon'); ?></option>
                                    <option value="0"><?php _e('Inactive users','rmcommon'); ?></option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4 col-lg-4">
                            <div class="form-group">
                                <label><?php _e('Search method:','rmcommon'); ?></label><br>
                                <div class="radio-inline">
                                    <label><input name="srhmethod" value="OR"  checked="checked" type="radio" />Coincident</label>
                                </div>
                                <div class="radio-inline">
                                    <label><input name="srhmethod" value="AND" type="radio" />Exact</label>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="row">

                        <div class="col-md-4 col-lg-4">

                <span class="help-block">
                    <?php _e('All these options are optional and will be additional to basic search keyword.','rmcommon'); ?>
                </span>

                        </div>

                        <div class="col-md-8 col-lg-8 text-right">

                            <div class="form-group">

                                <button type="button" onclick="$('#users-advanced-options').slideUp('slow');" class="btn btn-default"><?php _e('Cancel','rmcommon'); ?></button>
                                <button type="submit" class="btn btn-primary"><?php _e('Search Now!','rmcommon'); ?></button>

                            </div>

                        </div>

                    </div>
                </div>
            </div>
        </div>



	</form>
</div>
<form name="frmUsers" id="form-users" method="post" action="users.php" class="form-inline">
<!-- Navigation Options -->
<div class="cu-bulk-actions">

    <div class="row">
        <div class="col-sm-6 col-md-3">
            <select name="order" id="user-order" class="form-control">
                <option value=""<?php echo $order=='' ? ' selected="selected"' : ''; ?>><?php _e('Order by...', 'rmcommon'); ?></option>
                <option value="uid"<?php echo $order=='uid' ? ' selected="selected"' : ''; ?>><?php _e('ID','rmcommon'); ?></option>
                <option value="uname"<?php echo $order=='uname' ? ' selected="selected"' : ''; ?>><?php _e('Username','rmcommon'); ?></option>
                <option value="name"<?php echo $order=='name' ? ' selected="selected"' : ''; ?>><?php _e('Name','rmcommon'); ?></option>
                <option value="email"<?php echo $order=='email' ? ' selected="selected"' : ''; ?>><?php _e('Email','rmcommon'); ?></option>
            </select>
            <button type="button" class="btn btn-default" onclick="$('#order').val($('#user-order').val()); submit();"><?php _e('Sort','rmcommon'); ?></button>
        </div>

        <div class="col-sm-6 col-md-9">
            <select name="action" id="bulk-top" class="form-control">
                <option value=""><?php _e('Bulk Actions...','rmcommon'); ?></option>
                <option value="activate"><?php _e('Activate','rmcommon'); ?></option>
                <option value="deactivate"><?php _e('Deactivate','rmcommon'); ?></option>
                <option value="mailer"><?php _e('Send email','rmcommon'); ?></option>
                <option value="delete"><?php _e('Delete','rmcommon'); ?></option>
            </select>
            <button type="button" onclick="before_submit('form-users');" class="btn btn-default" id="the-op-top"><?php _e('Apply','rmcommon'); ?></button>
        </div>

    </div>

</div>
<!-- Navigation Options -->

    <div class="panel panel-default">
        <div class="table-responsive">
            <table class="table table-striped" cellspacing="0" width="100%">
                <thead>
                <tr>
                    <th width="20" class="text-center"><input type="checkbox" class="checkall" id="checkall-top" onclick="$('#form-users').toggleCheckboxes(':not(#checkall-top)');" /></th>
                    <th class="text-center"><?php _e('ID','rmcommon'); ?></th>
                    <th><?php _e('Username','rmcommon'); ?></th>
                    <th><?php _e('Name','rmcommon'); ?></th>
                    <th class="text-center"><?php _e('Email','rmcommon'); ?></th>
                    <th class="text-center"><?php _e('Registered','rmcommon'); ?></th>
                    <th class="text-center"><?php _e('Groups','rmcommon'); ?></th>
                    <th class="text-center"><?php _e('Status','rmcommon'); ?></th>
                </tr>
                </thead>
                <tbody>
                <?php if(count($users)<=0): ?>
                    <tr class="even">
                        <td colspan="8" class="text-center">
                            <span class="label label-important"><?php _e('There are not any user registered with for this filter.','rmcommon'); ?></span>
                        </td>
                    </tr>
                <?php endif; ?>
                <?php
                $class = 'odd';

                $qstring = '';
                foreach (RMTemplate::get()->get_vars() as $var => $value) {
                    $qstring .= $qstring=='' ? $var.'='.$value : '&amp;'.$var.'='.$value;
                }

                foreach($users as $user):
                    ?>
                    <tr class="<?php echo tpl_cycle('even,odd'); ?><?php echo $user['level']<=0 ? ' user_inactive' : '' ?>" valign="top">
                        <td class="text-center"><input type="checkbox" name="ids[]" id="item-<?php echo $user['uid']; ?>" value="<?php echo $user['uid']; ?>" /></td>
                        <td class="text-center"><?php echo $user['uid']; ?></td>
                        <td nowrap="nowrap">
                            <strong><?php echo $user['uname']; ?></strong>
                                <span class="cu-item-options">
                                    <a href="users.php?action=edit&amp;uid=<?php echo $user['uid']; ?>&amp;query=<?php echo base64_encode($qstring); ?>"><?php _e('Edit','rmcommon'); ?></a>
                                    <a href="users.php?action=mailer&amp;uid=<?php echo $user['uid']; ?>&amp;query=<?php echo base64_encode($qstring); ?>"><?php _e('Email','rmcommon'); ?></a>
                                    <a href="#" onclick="select_option(<?php echo $user['uid']; ?>,'delete','form-users');"><?php _e('Delete','rmcommon'); ?></a>
                                </span>
                        </td>
                        <td><?php echo $user['name']; ?></td>
                        <td class="text-center"><a href="javascript:;" title="<?php echo sprintf(__('Send email to %s','rmcommon'), $user['uname']); ?>"><?php echo $user['email']; ?></a></td>
                        <td class="text-center"><?php echo formatTimestamp($user['user_regdate'], 'c'); ?></td>
                        <td class="text-center" class="users_cell_groups">
                            <?php
                            $str = '';
                            foreach ($user['groups'] as $group):
                                $str = $str=='' ? $xgh->get($group)->name() : ', '.$xgh->get($group)->name();
                                echo $str;
                            endforeach; ?>
                        </td>
                        <td class="text-center <?php echo $user['level']<=0 ? ' text-danger' : ' text-success'; ?>">
                            <?php echo $user['level']<=0 ? $cuIcons->getIcon('svg-rmcommon-close') : $cuIcons->getIcon('svg-rmcommon-ok-circle'); ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="panel-footer">
            <div class="row">
                <div class="col-md-6">
                    <?php $nav->render(); echo $nav->get_showing(); ?>
                </div>
                <div class="col-md-6">
                    <?php $nav->display(false); ?>
                </div>
            </div>
        </div>
    </div>

<!-- Navigation Options -->
    <div class="cu-bulk-actions visible-md visible-lg">

        <div class="row">
            <div class="col-md-3">
                <select name="order" id="user-order-bottom" class="form-control">
                    <option value=""<?php echo $order=='' ? ' selected="selected"' : ''; ?>><?php _e('Order by...', 'rmcommon'); ?></option>
                    <option value="uid"<?php echo $order=='uid' ? ' selected="selected"' : ''; ?>><?php _e('ID','rmcommon'); ?></option>
                    <option value="uname"<?php echo $order=='uname' ? ' selected="selected"' : ''; ?>><?php _e('Username','rmcommon'); ?></option>
                    <option value="name"<?php echo $order=='name' ? ' selected="selected"' : ''; ?>><?php _e('Name','rmcommon'); ?></option>
                    <option value="email"<?php echo $order=='email' ? ' selected="selected"' : ''; ?>><?php _e('Email','rmcommon'); ?></option>
                </select>
                <button type="button" class="btn btn-default" onclick="$('#order').val($('#user-order').val()); submit();"><?php _e('Sort','rmcommon'); ?></button>
            </div>

            <div class="col-md-9">
                <select name="actionb" id="bulk-bottom" class="form-control">
                    <option value=""><?php _e('Bulk Actions...','rmcommon'); ?></option>
                    <option value="activate"><?php _e('Activate','rmcommon'); ?></option>
                    <option value="deactivate"><?php _e('Deactivate','rmcommon'); ?></option>
                    <option value="mailer"><?php _e('Send email','rmcommon'); ?></option>
                    <option value="delete"><?php _e('Delete','rmcommon'); ?></option>
                </select>
                <button type="button" onclick="before_submit('form-users');" class="btn btn-default" id="the-op-bottom"><?php _e('Apply','rmcommon'); ?></button>
            </div>
        </div>

    </div>
<?php echo $xoopsSecurity->getTokenHTML(); ?>
<!-- Navigation Options -->
</form>
