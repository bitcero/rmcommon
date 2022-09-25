<h1 class="cu-section-title"><?php _e('Plugins Manager', 'rmcommon'); ?></h1>

<div class="cu-box">
    <div class="box-content no-padding">
        <ul class="nav nav-tabs plugins-nav cu-top-tabs">
            <li class="nav-item" role="presentation">
                <button type="button" data-bs-target="#plugins-installed" data-bs-toggle="tab"
                        class="nav-link active" aria-controls="plugins-installed"
                        aria-selected="true"><?php _e('Installed Plugins', 'rmcommon'); ?></button>
            </li>
            <li class="nav-item">
                <button type="button" data-bs-target="#plugins-noinstalled" data-bs-toggle="tab"
                        class="nav-link" aria-controls="plugins-noinstalled"
                        aria-selected="false"><?php _e('Available Plugins', 'rmcommon'); ?></button>
            </li>
        </ul>

        <div class="p-4 mb-0">
            <p class="mb-0">
              <?php _e('Plugins allows to Common Utilities to improve its features and capabilities. Following is a list with existing plugins, installed and available to install.', 'rmcommon'); ?>
            </p>
        </div>

        <div class="tab-content no-padding pt-0">
            <div class="tab-pane active" id="plugins-installed">
                <table class="table m-0">
                    <thead>
                    <tr>
                        <th><?php _e('Name', 'rmcommon'); ?></th>
                        <th><?php _e('Description', 'rmcommon'); ?></th>
                        <th class="text-center"><?php _e('Version', 'rmcommon'); ?></th>
                        <th class="text-center"><?php _e('Author', 'rmcommon'); ?></th>
                        <th class="text-center"><?php _e('Status', 'rmcommon'); ?></th>
                        <th class="text-center"><?php _e('Actions', 'rmcommon'); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($installed_plugins)): ?>
                        <tr class="even">
                            <td class="text-danger text-center" colspan="6"
                            ><?php _e('There are not plugins installed yet!', 'rmcommon'); ?></td>
                        </tr>
                    <?php endif; ?>
                    <?php foreach ($installed_plugins as $plugin): ?>
                        <tr class="<?php echo tpl_cycle('even,odd'); ?>" valign="top">
                            <td>
                              <?php if ($plugin->get_info('hasmain')): ?>
                                  <strong><a href="plugins.php?p=<?php echo $plugin->get_info('dir'); ?>"><?php echo $plugin->getVar('name'); ?></a></strong>
                              <?php else: ?>
                                  <strong><?php echo $plugin->getVar('name'); ?></strong>
                              <?php endif; ?>
                            </td>
                            <td>
                                <span class="descriptions"><?php echo $plugin->get_info('description'); ?></span>
                            </td>
                            <td align="center">
                                <strong><?php echo is_array($plugin->get_info('version')) ? RMModules::format_module_version($plugin->get_info('version')) : $plugin->get_info('version'); ?></strong>
                            </td>
                            <td align="center">
                              <?php if ('' != $plugin->get_info('web')): ?>
                                  <strong><a href="<?php echo $plugin->get_info('web'); ?>"><?php echo $plugin->get_info('author'); ?></a></strong>
                              <?php else: ?>
                                  <strong><?php echo $plugin->get_info('author'); ?></strong>
                              <?php endif; ?><br>
                              <?php echo $plugin->get_info('email'); ?>
                            </td>
                            <td align="center">
                              <?php echo $plugin->getVar('status') ? __('Active', 'rmcommon') : __('Inactive', 'rmcommon'); ?>
                            </td>
                            <td class="text-center">
                                <div class="cu-options d-flex align-items-center justify-content-center">
                                  <?php if ($plugin->getVar('status')): ?>
                                      <a href="plugins.php?action=disable&amp;plugin=<?php echo $plugin->get_info('dir'); ?>"
                                         class="text-success">
                                        <?php echo $common->icons()->getIcon('svg-lithium-enable', [], false); ?>
                                          <span class="visually-hidden"><?php _e('Disable', 'rmcommon'); ?></span>
                                      </a>
                                  <?php else: ?>
                                      <a href="plugins.php?action=enable&amp;plugin=<?php echo $plugin->get_info('dir'); ?>"
                                         class="text-secondary">
                                        <?php echo $common->icons()->getIcon('svg-lithium-disable', [], false); ?>
                                          <span class="visually-hidden"><?php _e('Enable', 'rmcommon'); ?></span>
                                      </a>
                                  <?php endif; ?>
                                    <a href="plugins.php?action=update&amp;plugin=<?php echo $plugin->get_info('dir'); ?>"
                                       class="text-warning">
                                      <?php echo $common->icons()->getIcon('svg-lithium-refresh', [], false); ?>
                                        <span class="visually-hidden"><?php _e('Update', 'rmcommon'); ?></span>
                                    </a>
                                  <?php if ($plugin->options()): ?>
                                      <a href="plugins.php?action=configure&amp;plugin=<?php echo $plugin->get_info('dir'); ?>">
                                        <?php echo $common->icons()->getIcon('svg-lithium-setting', [], false); ?>
                                          <span class="visually-hidden"><?php _e('Settings', 'rmcommon'); ?></span>
                                      </a>
                                  <?php endif; ?>
                                    <a href="plugins.php?action=uninstall&amp;plugin=<?php echo $plugin->get_info('dir'); ?>"
                                       class="text-danger" title="<?php _e('Uninstall', 'rmcommon'); ?>">
                                      <?php echo $common->icons()->getIcon('svg-lithium-remove', [], false); ?>
                                        <span class="visually-hidden"><?php _e('Uninstall', 'rmcommon'); ?></span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>

                </table>
            </div>

            <div class="tab-pane" id="plugins-noinstalled">
                <table class="table table-hover m-0">
                    <thead>
                    <tr>
                        <th><?php _e('Name', 'rmcommon'); ?></th>
                        <th><?php _e('Description', 'rmcommon'); ?></th>
                        <th class="text-center"><?php _e('Version', 'rmcommon'); ?></th>
                        <th class="text-center"><?php _e('Author', 'rmcommon'); ?></th>
                        <th class="text-center"><?php _e('Actions', 'rmcommon'); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($available_plugins)): ?>
                        <tr class="even">
                            <td class="error"
                                colspan="4"><?php _e('There are not available plugins yet!', 'rmcommon'); ?></td>
                        </tr>
                    <?php endif; ?>
                    <?php foreach ($available_plugins as $plugin): ?>
                        <tr class="<?php echo tpl_cycle('even,odd'); ?>" valign="top">
                            <td>
                                <strong><?php echo $plugin->get_info('name'); ?></strong>
                                <span class="cu-item-options">

			</span>
                            </td>
                            <td>
                                <span class="descriptions"><?php echo $plugin->get_info('description'); ?></span>
                            </td>
                            <td align="center">
                                <strong><?php echo RMFormat::version($plugin->get_info('version')); ?></strong>
                            </td>
                            <td align="center">
                              <?php if ('' != $plugin->get_info('web')): ?>
                                  <strong><a href="<?php echo $plugin->get_info('web'); ?>"><?php echo $plugin->get_info('author'); ?></a></strong>
                              <?php else: ?>
                                  <strong><?php echo $plugin->get_info('author'); ?></strong>
                              <?php endif; ?><br>
                              <?php echo $plugin->get_info('email'); ?>
                            </td>
                            <td class="text-center">
                                <div class="cu-options d-flex align-items-center justify-content-center">
                                    <a href="plugins.php?action=install&amp;plugin=<?php echo $plugin->get_info('dir'); ?>"
                                       class="text-success">
                                      <?php echo $common->icons()->getIcon('svg-lithium-floppy-disk', [], false); ?>
                                        <span class="visually-hidden"><?php _e('Install', 'rmcommon'); ?></span>
                                    </a>
                                    <a href="<?php echo $plugin->get_info('web'); ?>" class="text-info" target="_blank">
                                      <?php echo $common->icons()->getIcon('svg-lithium-info', [], false); ?>
                                        <span class="visually-hidden"><?php _e('Visit Web site', 'rmcommon'); ?></span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>

                </table>
            </div>

        </div>
    </div>
</div>
