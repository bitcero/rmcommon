<h1 class="rmc_titles"><i class="icon-users"></i> <?php _e('Users Management','rmcommon'); ?></h1>
<div id="users-filter-options">
    <form name="filterForm" id="filter-form" method="get" action="users.php" class="form-inline">
    <div class="basic_options">
	<span class="sections">
		<label for="search-key"><?php _e('Search:','rmcommon'); ?></label>
		<input type="text" name="keyw" id="search-key" size="15" value="<?php echo rmc_server_var($_REQUEST, 'keyword', ''); ?>" />
	</span>
	<span class="sections">
		<label for="users-number"><?php _e('Show:','rmcommon'); ?></label>
		<input type="text" name="limit" id="users-number" size="6" value="<?php echo $limit; ?>" />
	</span>
	<span class="sections">
		<input type="submit" value="<?php _e('Go Now!','rmcommon'); ?>" />
	</span>
	<span class="sections_right">
		<a href="javascript:;" id="show-search"><?php _e('Advanced Search','rmcommon'); ?></a>
	</span>
    </div>
    <div id="users-advanced-options">
        <table class="table_options" cellspacing="0" border="0" width="100%">
            <tr class="even">
                <td><?php _e('Email:','rmcommon'); ?></td>
                <td><?php _e('Web site:','rmcommon'); ?></td>
                <td><?php _e('Country/Location:','rmcommon'); ?></td>
            </tr>
            <tr class="even">
                <td><input type="text" name="email" id="user-email" value="<?php echo RMTemplate::get()->get_var('srhemail'); ?>" size="20" /></td>
                <td><input type="text" name="url" id="user-url" size="20" value="" /></td>
                <td><input type="text" name="from" id="user-from" size="20" value="" /></td>
            </tr>
            <tr class="even">
                <td><?php _e('Registered between:','rmcommon'); ?></td>
                <td><?php _e('Last login between:','rmcommon'); ?></td>
                <td><?php _e('Posts between:','rmcommon'); ?></td>
            </tr>
            <tr class="even">
                <td><?php echo $register1->render(); ?> <?php _e('and','rmcommon'); ?> <?php echo $register2->render(); ?></td>
                <td><?php echo $login1->render(); ?> <?php _e('and','rmcommon'); ?> <?php echo $login2->render(); ?></td>
                <td>
                    <input type="text" name="posts1" id="users-posts1" value="0" size="5" />
                    <?php _e('and','rmcommon'); ?>
                    <input type="text" name="posts2" id="users-posts2" value="" size="5" />
                </td>
            </tr>
            <tr class="even">
                <td><?php _e('Mail:','rmcommon'); ?></td>
                <td><?php _e('Status:','rmcommon'); ?></td>
                <td><?php _e('Search method:','rmcommon'); ?></td>
            </tr>
            <tr class="even">
                <td>
                    <select name="mailok" id="users-mailok">
                        <option value="-1"><?php _e('All users','rmcommon'); ?></option>
                        <option value="1"><?php _e('Users that accept mail','rmcommon'); ?></option>
                        <option value="0"><?php _e('Users that do\'nt accept mail','rmcommon'); ?></option>
                    </select>
                </td>
                <td>
                    <select name="actives" id="users-actives">
                        <option value="-1"><?php _e('All users','rmcommon'); ?></option>
                        <option value="1"><?php _e('Active users','rmcommon'); ?></option>
                        <option value="0"><?php _e('Inactive users','rmcommon'); ?></option>
                    </select>
                </td>
                <td>
                    <label><input name="srhmethod" value="OR"  checked="checked" type="radio" />Coincident</label>
                    <label><input name="srhmethod" value="AND" type="radio" />Exact</label>
                </td>
            </tr>
            <tr class="even">
                <td colspan="3" align="right">
                    <input type="submit" value="<?php _e('Search Now!','rmcommon'); ?>" class="formButton" />
                    <input type="button" value="<?php _e('Cancel','rmcommon'); ?>" onclick="$('#users-advanced-options').slideUp('slow');" />
                </td>
            </tr>
            <tr class="even no_border_bottom">
                <td colspan="3">
                    <?php _e('All these options are optional and will be additional to basic search keyword.','rmcommon'); ?>
                </td>
            </tr>
        </table>
    </div>
	</form>
</div>
<form name="frmUsers" id="form-users" method="post" action="users.php" class="form-inline">
<!-- Navigation Options -->
<div class="users_navigation">
	<?php $nav->display(false); ?>
	<div class="users_nav_showing"><?php echo $nav->get_showing(); ?></div>
	<div class="users_order_options">
		<?php _e('Order by:', 'system'); ?>
		<select name="order" id="user-order">
			<option value="uid"<?php echo $order=='uid' || $order=='' ? ' selected="selected"' : ''; ?>><?php _e('ID','rmcommon'); ?></option>
			<option value="uname"<?php echo $order=='uname' ? ' selected="selected"' : ''; ?>><?php _e('Username','rmcommon'); ?></option>
			<option value="name"<?php echo $order=='name' ? ' selected="selected"' : ''; ?>><?php _e('Name','rmcommon'); ?></option>
			<option value="email"<?php echo $order=='email' ? ' selected="selected"' : ''; ?>><?php _e('Email','rmcommon'); ?></option>
		</select>
		<input type="button" value="<?php _e('Sort','rmcommon'); ?>" onclick="$('#order').val($('#user-order').val()); submit();" />
	</div>
	<div class="users_bulk">
		<select name="action" id="bulk-top">
			<option value=""><?php _e('Bulk Actions...','rmcommon'); ?></option>
			<option value="activate"><?php _e('Activate','rmcommon'); ?></option>
			<option value="deactivate"><?php _e('Deactivate','rmcommon'); ?></option>
			<option value="mailer"><?php _e('Send email','rmcommon'); ?></option>
			<option value="delete"><?php _e('Delete','rmcommon'); ?></option>
		</select>
		<input type="button" value="<?php _e('Apply','rmcommon'); ?>" id="the-op-top" onclick="before_submit('form-users');" />
	</div>
</div>
<!-- Navigation Options -->

<table class="outer" cellspacing="0" width="100%">
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
    <tfoot>
    <tr>
        <th width="20" class="text-center"><input type="checkbox" id="checkall-bottom" name="checkall" onclick="$('#form-users').toggleCheckboxes(':not(#checkall-bottom)');" /></th>
        <th class="text-center"><?php _e('ID','rmcommon'); ?></th>
        <th><?php _e('Username','rmcommon'); ?></th>
        <th><?php _e('Name','rmcommon'); ?></th>
        <th class="text-center"><?php _e('Email','rmcommon'); ?></th>
        <th class="text-center"><?php _e('Registered','rmcommon'); ?></th>
        <th class="text-center"><?php _e('Groups','rmcommon'); ?></th>
        <th class="text-center"><?php _e('Status','rmcommon'); ?></th>
    </tr>
    </tfoot>
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
    foreach (RMTemplate::get()->get_vars() as $var => $value){
        $qstring .= $qstring=='' ? $var.'='.$value : '&amp;'.$var.'='.$value;
    }
    
    foreach($users as $user):
    ?>
    <tr class="<?php echo tpl_cycle('even,odd'); ?><?php echo $user['level']<=0 ? ' user_inactive' : '' ?>" valign="top">
        <td class="text-center"><input type="checkbox" name="ids[]" id="item-<?php echo $user['uid']; ?>" value="<?php echo $user['uid']; ?>" /></td>
        <td class="text-center"><?php echo $user['uid']; ?></td>
        <td>
            <strong><?php echo $user['uname']; ?></strong>
            <span class="rmc_options">
                <a href="users.php?action=edit&amp;uid=<?php echo $user['uid']; ?>&amp;query=<?php echo base64_encode($qstring); ?>"><?php _e('Edit','rmcommon'); ?></a> | 
                <a href="users.php?action=mailer&amp;uid=<?php echo $user['uid']; ?>&amp;query=<?php echo base64_encode($qstring); ?>"><?php _e('Send Email','rmcommon'); ?></a> | 
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
        <td class="text-center">
            <img src="images/<?php echo $user['level']<=0 ? 'error.png' : 'done.png'; ?>" alt="" />
        </td>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<!-- Navigation Options -->
<div class="users_navigation">
	<?php $nav->display(false); ?>
	<div class="users_nav_showing"><?php echo $nav->get_showing(); ?></div>
	<div class="users_order_options">
		<?php _e('Order by:', 'system'); ?>
		<select name="order" id="user-order-bottom">
			<option value="uid"><?php _e('ID','rmcommon'); ?></option>
			<option value="display_name"><?php _e('Username','rmcommon'); ?></option>
			<option value="name"><?php _e('Name','rmcommon'); ?></option>
			<option value="email"><?php _e('Email','rmcommon'); ?></option>
		</select>
		<input type="button" value="<?php _e('Sort','rmcommon'); ?>" onclick="$('#order').val($('#user-order-bottom').val()); submit();" />
	</div>
	<div class="users_bulk">
		<select name="actionb" id="bulk-bottom">
			<option value=""><?php _e('Bulk Actions...','rmcommon'); ?></option>
			<option value="activate"><?php _e('Activate','rmcommon'); ?></option>
			<option value="deactivate"><?php _e('Deactivate','rmcommon'); ?></option>
			<option value="mailer"><?php _e('Send email','rmcommon'); ?></option>
			<option value="delete"><?php _e('Delete','rmcommon'); ?></option>
		</select>
		<input type="button" value="<?php _e('Apply','rmcommon'); ?>" id="the-op-bottom" onclick="before_submit('form-users');" />
	</div>
</div>
<?php echo $xoopsSecurity->getTokenHTML(); ?>
<!-- Navigation Options -->
</form>