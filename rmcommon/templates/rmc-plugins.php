<h1 class="cu-section-title"><?php _e('Plugins Manager','rmcommon'); ?></h1>

<ul class="nav nav-tabs plugins-nav">
	<li class="active"><a href="#plugins-installed" data-toggle="tab"><?php _e('Installed Plugins','rmcommon'); ?></a></li>
	<li><a href="#plugins-noinstalled" data-toggle="tab"><?php _e('Available Plugins','rmcommon'); ?></a></li>
</ul>

<div class="tab-content">
	<div class="page-header" style="margin: 0 0 5px 0;">
		<span class="help-block">
			<?php _e('Plugins allows to Common Utilities to improve its features and capabilities. Following is a list with existing plugins, installed and available to install.','rmcommon'); ?>
		</span>
	</div>
	<div class="tab-pane active" id="plugins-installed">

		<h3><?php _e('Installed Plugins', 'rmcommon'); ?></h3>

		<table class="table" cellspacing="0" cellpadding="0">
			<thead>
			<tr>
				<th align="left"><?php _e('Name','rmcommon'); ?></th>
				<th align="left"><?php _e('Description', 'rmcommon'); ?></th>
				<th><?php _e('Version','rmcommon'); ?></th>
				<th><?php _e('Author','rmcommon'); ?></th>
				<th><?php _e('Status','rmcommon'); ?></th>
			</tr>
			</thead>
			<tfoot>
			<tr>
				<th align="left"><?php _e('Name','rmcommon'); ?></th>
				<th align="left"><?php _e('Description', 'rmcommon'); ?></th>
				<th><?php _e('Version','rmcommon'); ?></th>
				<th><?php _e('Author','rmcommon'); ?></th>
				<th><?php _e('Status','rmcommon'); ?></th>
			</tr>
			</tfoot>
			<tbody>
			<?php if(empty($installed_plugins)): ?>
				<tr class="even">
					<td class="error" colspan="5" align="center"><?php _e('There are not plugins installed yet!','rmcommon'); ?></td>
				</tr>
			<?php endif; ?>
			<?php foreach ($installed_plugins as $plugin): ?>
				<tr class="<?php echo tpl_cycle("even,odd"); ?>" valign="top">
					<td>
						<?php if($plugin->get_info('hasmain')): ?>
							<strong><a href="plugins.php?p=<?php echo $plugin->get_info('dir'); ?>"><?php echo $plugin->getVar('name'); ?></a></strong>
						<?php else: ?>
							<strong><?php echo $plugin->getVar('name'); ?></strong>
						<?php endif; ?>
						<span class="cu-item-options">
                <a href="plugins.php?action=uninstall&amp;plugin=<?php echo $plugin->get_info('dir'); ?>"><?php _e('Uninstall','rmcommon'); ?></a> |
							<?php if($plugin->getVar('status')): ?>
								<a href="plugins.php?action=disable&amp;plugin=<?php echo $plugin->get_info('dir'); ?>"><?php _e('Disable','rmcommon'); ?></a> |
							<?php else: ?>
								<a href="plugins.php?action=enable&amp;plugin=<?php echo $plugin->get_info('dir'); ?>"><?php _e('Enable','rmcommon'); ?></a> |
							<?php endif; ?>
							<a href="plugins.php?action=update&amp;plugin=<?php echo $plugin->get_info('dir'); ?>"><?php _e('Update','rmcommon'); ?></a>
							<?php if($plugin->options()): ?>
								| <a href="plugins.php?action=configure&amp;plugin=<?php echo $plugin->get_info('dir'); ?>"><?php _e('Settings','rmcommon'); ?></a>
							<?php endif; ?>
            </span>
					</td>
					<td>
						<span class="descriptions"><?php echo $plugin->get_info('description'); ?></span>
					</td>
					<td align="center">
						<strong><?php echo is_array($plugin->get_info('version')) ? RMModules::format_module_version($plugin->get_info('version')) : $plugin->get_info('version'); ?></strong>
					</td>
					<td align="center">
						<?php if($plugin->get_info('web')!=''): ?>
							<strong><a href="<?php echo $plugin->get_info('web'); ?>"><?php echo $plugin->get_info('author'); ?></a></strong>
						<?php else: ?>
							<strong><?php echo $plugin->get_info('author'); ?></strong>
						<?php endif; ?><br />
						<?php echo $plugin->get_info('email'); ?>
					</td>
					<td align="center">
						<?php echo $plugin->getVar('status')?__('Active','rmcommon'):__('Inactive','rmcommon'); ?>
					</td>
				</tr>
			<?php endforeach; ?>
			</tbody>

		</table>
	</div>

	<div class="tab-pane" id="plugins-noinstalled">
		<h3><?php _e('Available Plugins'); ?></h3>
		<table class="table" cellspacing="0" cellpadding="0">
			<thead>
			<tr>
				<th align="left"><?php _e('Name','rmcommon'); ?></th>
				<th align="left"><?php _e('Description', 'rmcommon'); ?></th>
				<th><?php _e('Version','rmcommon'); ?></th>
				<th><?php _e('Author','rmcommon'); ?></th>
			</tr>
			</thead>
			<tfoot>
			<tr>
				<th align="left"><?php _e('Name and Description','rmcommon'); ?></th>
				<th align="left"><?php _e('Description', 'rmcommon'); ?></th>
				<th><?php _e('Version','rmcommon'); ?></th>
				<th><?php _e('Author','rmcommon'); ?></th>
			</tr>
			</tfoot>
			<tbody>
			<?php if(empty($available_plugins)): ?>
				<tr class="even">
					<td class="error" colspan="4" align="center"><?php _e('There are not available plugins yet!','rmcommon'); ?></td>
				</tr>
			<?php endif; ?>
			<?php foreach ($available_plugins as $plugin): ?>
				<tr class="<?php echo tpl_cycle("even,odd"); ?>" valign="top">
					<td>
						<strong><?php echo $plugin->get_info('name'); ?></strong>
			<span class="cu-item-options">
				<a href="plugins.php?action=install&amp;plugin=<?php echo $plugin->get_info('dir'); ?>"><?php _e('Install','rmcommon'); ?></a> |
				<a href="<?php echo $plugin->get_info('web'); ?>"><?php _e('Visit Web site','rmcommon'); ?></a>
			</span>
					</td>
					<td>
						<span class="descriptions"><?php echo $plugin->get_info('description'); ?></span>
					</td>
					<td align="center">
						<strong><?php echo $plugin->get_info('version'); ?></strong>
					</td>
					<td align="center">
						<?php if($plugin->get_info('web')!=''): ?>
							<strong><a href="<?php echo $plugin->get_info('web'); ?>"><?php echo $plugin->get_info('author'); ?></a></strong>
						<?php else: ?>
							<strong><?php echo $plugin->get_info('author'); ?></strong>
						<?php endif; ?><br />
						<?php echo $plugin->get_info('email'); ?>
					</td>
				</tr>
			<?php endforeach; ?>
			</tbody>

		</table>
	</div>

</div>
