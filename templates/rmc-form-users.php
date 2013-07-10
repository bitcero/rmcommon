<script type="text/javascript">
<!--
	$("#XOOPS_TOKEN_REQUEST").val('<?php echo $token; ?>');
    var baseurl = "<?php echo XOOPS_URL; ?>";
-->
</script>
<form name="<?php echo $field; ?>_users_form">
<?php $cols = $field_type=='radio' ? 5 : 3; ?>
<?php if ($field_type=='checkbox'): ?>
<div class="form_users_selected_list">
	<div id="<?php echo $field; ?>-selected-title" class="form_users_selected_title">
		<?php _e('Selected:','rmcommon'); ?> (<strong><span><?php echo count($selected); ?></span></strong>)
	</div>
	<ul id="<?php echo $field; ?>-selected-list">
	<?php foreach($selecteds as $sel): ?>
		<li class="user_<?php echo $sel['id'] ?>">
		<label><input type="checkbox" name="s[]" value="<?php echo $sel['id']; ?>" checked="checked" onchange="usersField.remove_from_list(<?php echo $sel['id']; ?>);" /> 
		<span id="user-<?php echo $field; ?>-caption-<?php echo $sel['id']; ?>"><?php echo $sel['name']; ?></span>
		</label></li>
	<?php endforeach; ?>
	</ul>
	<div align="center"><input type="button" value="<?php echo _e('Insert Users','rmcommon'); ?>" onclick="usersField.insert_users(<?php echo $type; ?>);" /></div>
</div>
<?php endif; ?>
<div<?php if ($field_type=='checkbox'): ?> style="margin-right: 160px;"<?php endif; ?>>
<table class="outer" cellspacing="0">
	<tr>
		<td colspan="<?php echo $cols; ?>" align="center" class="even">
			<?php _e('Search:','rmcommon'); ?> <input type="text" id="<?php echo $field ?>-kw" size="8" value="<?php echo $kw; ?>" /> &nbsp;
			<?php _e('Show:','rmcommon'); ?> <input type="text" id="<?php echo $field ?>-limit" size="5" value="<?php echo $limit; ?>" /> &nbsp;
			<?php _e('Sort by:','rmcommon'); ?>
			<select id="<?php echo $field ?>-ord">
				<option value="2"<?php echo $ord==2 ? ' selected="selected"' : ''; ?>><?php _e('ID','rmcommon'); ?></option>
				<option value="0"<?php echo $ord==0 ? ' selected="selected"' : ''; ?>><?php _e('Registered','rmcommon'); ?></option>
				<option value="1"<?php echo $ord==1 ? ' selected="selected"' : ''; ?>><?php _e('Username','rmcommon'); ?></option>
			</select>
			<input type="button" value="<?php _e('Go!','rmcommon'); ?>" onclick="usersField.submit_search(<?php echo $type; ?>);" />
		</td>
	</tr>
	<tr>
		<th align="left" colspan="<?php echo $cols; ?>">
			<div style="float: right; font-weight: normal; font-size: 0.9em;">
				<?php echo $nav->get_showing(); ?>
			</div>
			<?php _e('Existing Users','system'); ?>
		</th>	
	</tr>
	<tr class="even form_users_list">
	<?php
		$i = 1; //Counter
		foreach($users as $user):
	?>
		<?php
			if($i>$cols):
				$i = 1;
		?>
			</tr><tr class="<?php if (function_exists('cycle')): echo cycle("odd","even"); else: echo "even"; endif; ?>">
		<?php endif; ?>
		<td>
			<label><input <?php if($type): ?>onchange="usersField.add_to_list(<?php echo $user['id']; ?>);"<?php else: ?>onclick="usersField.insert_users(<?php echo $type.','.$user['id']; ?>);"<?php endif; ?> type="<?php echo $field_type ?>" id="<?php echo $field; ?>-user-<?php echo $user['id']; ?>" name="users<?php echo $field_type=='checkbox' ? '[]' : ''; ?>" value="<?php echo $user['id']; ?>"<?php echo $user['check'] ? ' checked="checked"' : ''; ?> />
			<span id="<?php echo $field; ?>-username-<?php echo $user['id']; ?>"><?php echo $user['name']; ?></span></label>
		</td>
	<?php 
			$i++;
		endforeach; 
	?>
	</tr>
	<tr class="foot">
		<td colspan="<?php echo $cols; ?>"><?php $nav->display(); ?></td>
	</tr>
</table>
</div>
<?php if(!$type): ?>
<input type="hidden" name="s" value="<?php echo $selected_string; ?>" id="s" />
<?php endif; ?>
</form>