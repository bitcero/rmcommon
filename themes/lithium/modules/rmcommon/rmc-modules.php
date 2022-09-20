<h1 class="cu-section-title" xmlns="http://www.w3.org/1999/html"><?php _e('Modules Management', 'rmcommon'); ?></h1>
<script type="text/javascript">
  <!--
  var message = "<?php _e('Do you really want to uninstall selected module?', 'rmcommon'); ?>";
  var message_upd = "<?php _e('Do you really want to update selected module?', 'rmcommon'); ?>";
  var message_dis = "<?php _e('Do you really want to disable selected module?', 'rmcommon'); ?>";
  var message_name = "<?php _e('New name must be different from current name!', 'rmcommon'); ?>";
  var message_wname = "<?php _e('You must provide a new name!', 'rmcommon'); ?>";
  -->
</script>

<form action="modules.php" method="post" id="form-modules">
    <input type="hidden" name="action" id="mod-action" value="">
    <input type="hidden" name="module" id="mod-dir" value="">
  <?php echo $xoopsSecurity->getTokenHTML(); ?>
</form>

<div class="cu-box modules-page">

    <div class="box-content no-padding" role="tablist">
        <ul class="nav nav-tabs">
            <li class="nav-item" role="presentation">
                <button id="installed-tab" class="nav-link active" data-bs-toggle="tab" data-bs-target="#installed"
                        type="button" aria-controls="installed" aria-selected="true">
                  <?php echo $cuIcons->getIcon('svg-lithium-floppy-disk', [], false); ?>
                    <span class="caption">Installed</span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button id="available-tab" class="nav-link" data-bs-toggle="tab" data-bs-target="#available"
                        type="button" aria-controls="available" aria-selected="false">
                  <?php echo $cuIcons->getIcon('svg-lithium-downloads', [], false); ?>
                    <span class="caption">Available</span>
                </button>
            </li>
        </ul>

        <div class="tab-content no-padding" id="des-mods-container">
            <div class="tab-pane fade show active" id="installed" role="tabpanel" aria-labelledby="installed-tab"
                 tabindex="0">
                <div class="table-responsive">
                    <table class="table table-hover m-0">
                        <thead>
                        <tr>
                            <th class="logo">&nbsp;</th>
                            <th><?php _e('Name', 'rmcommon'); ?></th>
                            <th class="text-center"><?php _e('Version', 'rmcommon'); ?></th>
                            <th class="text-center"><?php _e('Author', 'rmcommon'); ?></th>
                            <th colspan="4" class="text-center"><?php _e('Options', 'rmcommon'); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($modules as $mod): ?>
                            <tr class="<?php echo tpl_cycle('even,odd'); ?><?php echo $mod['active'] ? '' : ' inactive'; ?>"
                                id="module-<?php echo $mod['dirname']; ?>" valign="middle" align="center">
                                <td class="logo">
                                    <a href="<?php if ($mod['active']): ?><?php echo $mod['admin_link']; ?><?php else: ?>#<?php endif; ?>"
                                       title="<?php echo $mod['realname']; ?>">
                                      <?php if ('<span' == mb_substr($mod['icon'], 0, 5)): ?>
                                        <?php echo str_replace('cu-icon', '', $mod['icon']); ?>
                                      <?php else: ?>
                                          <img src="<?php echo $mod['icon']; ?>" alt="<?php echo $mod['name']; ?>">
                                      <?php endif; ?>
                                    </a>
                                </td>
                                <td class="name text-start">
                                    <span class="the_name text-nowrap">
                                    <?php if ($mod['active']): ?>
                                        <a href="<?php echo $mod['admin_link']; ?>"><?php echo $mod['name']; ?></a>
                                    <?php else: ?>
                                      <?php echo $mod['name']; ?>
                                    <?php endif; ?>
                                    </span>
                                    <a href="#" class="rename text-info"><span
                                                class="fa fa-edit"></span> <?php _e('Edit', 'rmcommon'); ?></a>
                                  <?php if ('' != $mod['help']): ?>
                                      <a href="<?php echo preg_match("/(http|\.{2})/i", $mod['help']) ? $mod['help'] : '../' . $mod['dirname'] . '/' . $mod['help']; ?>"
                                         class="help cu-help-button text-success"
                                         title="<?php echo sprintf(__('%s Help', 'rmcommon'), $mod['name']); ?>"><span
                                                  class="fa fa-question-circle"></span> <?php _e('Help', 'rmcommon'); ?>
                                      </a>
                                  <?php endif; ?>
                                    <small class="text-muted d-block"><?php echo $mod['description']; ?></small>
                                </td>
                                <td class="text-center" nowrap>
                                  <?php echo $mod['version']; ?>
                                </td>
                                <td class="author" nowrap>
                                  <?php foreach ($mod['authors'] as $author): ?>
                                    <?php if ('' != $author['url']): ?>
                                          <a href="<?php echo $author['url']; ?>" target="_blank"
                                             title="<?php echo $author['name']; ?>">
                                              <img src="http://www.gravatar.com/avatar/<?php echo md5($author['email']); ?>?s=40"
                                                   alt="<?php echo $author['aka']; ?>">
                                          </a>
                                    <?php else: ?>
                                          <img src="http://www.gravatar.com/avatar/<?php echo md5($author['email']); ?>?s=40"
                                               title="<?php echo $author['name']; ?>"
                                               alt="<?php echo $author['aka']; ?>">
                                    <?php endif; ?>
                                  <?php endforeach; ?>
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
                        <span class="author">
                            <?php foreach ($mod['authors'] as $author): ?>
                                <a href="<?php echo $author['url']; ?>" target="_blank"
                                   title="<?php echo $author['name']; ?>">
                                    <img src="http://www.gravatar.com/avatar/<?php echo md5($author['email']); ?>?s=60"
                                         alt="<?php echo $author['aka']; ?>">
                                </a>
                            <?php endforeach; ?>
                        </span>
                        <span class="url"><?php echo $mod['url']; ?></span>
                        <span class="license"><?php echo $mod['license']; ?></span>
                        <span class="help"><?php echo preg_match("/(http|\.{2})/i", $mod['help']) ? $mod['help'] : '../' . $mod['dirname'] . '/' . $mod['help']; ?></span>
                        <span class="active"><?php echo $mod['active']; ?></span>
                        <span class="social">
                            <?php if ($mod['social']): ?>
                              <?php foreach ($mod['social'] as $social): ?>
                                    <a href="<?php echo $social['url']; ?>" target="_blank"
                                       title="<?php echo $social['title']; ?>">
                                        <span class="fa fa-<?php echo $social['type']; ?>"></span>
                                    </a>
                              <?php endforeach; ?>
                            <?php endif; ?>
                        </span>
                    </span>
                                </td>
                                <td class="actions" nowrap>
                                    <div class="d-flex align-items-center justify-content-center">
                                      <?php if ('' != $mod['url']): ?>
                                          <a href="<?php echo $mod['url']; ?>" target="_blank" class="btn btn-link"
                                             title="<?php _e('Visit module website', 'rmcommon'); ?>">
                                              <span class="text-dark">
                                                <?php echo $common->icons()->getIcon('svg-lithium-globe', [], false); ?>
                                            </span>
                                          </a>
                                      <?php else: ?>
                                          <a href="#" class="btn btn-link" onclick="return false;">
                                              <span class="text-muted">
                                                <?php echo $common->icons()->getIcon('svg-lithium-globe', [], false); ?>
                                            </span>
                                          </a>
                                      <?php endif; ?>
                                        <a href="#" class="btn btn-link data_button"
                                           title="<?php _e('Show information', 'rmcommon'); ?>">
                                            <span class="text-info">
                                                <?php echo $common->icons()->getIcon('svg-lithium-info', [], false); ?>
                                            </span>
                                        </a>
                                        <a href="#" class="btn btn-default update_button"
                                           title="<?php _e('Update', 'rmcommon'); ?>">
                                            <span class="text-success">
                                                <?php echo $common->icons()->getIcon('svg-lithium-refresh', [], false); ?>
                                            </span>
                                        </a>
                                      <?php if ($mod['active']): ?>
                                        <?php if ('system' != $mod['dirname']): ?>
                                              <a href="#" class="btn btn-default disable_button"
                                                 title="<?php _e('Disable', 'rmcommon'); ?>">
                                                  <span class="text-success">
                                                <?php echo $common->icons()->getIcon('svg-lithium-enable', [], false); ?>
                                            </span>
                                              </a>
                                        <?php else: ?>
                                              <a href="#" class="btn btn-default" aria-disabled="true" disabled>
                                                  <span class="text-muted">
                                                <?php echo $common->icons()->getIcon('svg-lithium-enable', [], false); ?>
                                            </span>
                                              </a>
                                        <?php endif; ?>
                                      <?php endif; ?>
                                      <?php if (!$mod['active']): ?>
                                          <a href="#" class="btn btn-default enable_button"
                                             title="<?php _e('Enable', 'rmcommon'); ?>">
                                              <span class="text-muted">
                                                <?php echo $common->icons()->getIcon('svg-lithium-disable', [], false); ?>
                                            </span>
                                          </a>
                                      <?php endif; ?>
                                      <?php if ('system' != $mod['dirname']): ?>
                                          <a href="#" class="btn btn-default uninstall_button"
                                             title="<?php _e('Uninstall', 'rmcommon'); ?>"
                                             data-dir="<?php echo $mod['dirname']; ?>">
                                              <span class="text-danger">
                                                <?php echo $common->icons()->getIcon('svg-lithium-remove', [], false); ?>
                                            </span>
                                          </a>
                                      <?php else: ?>
                                          <a href="#" class="btn btn-default" aria-disabled="true" disabled>
                                              <span class="text-muted">
                                                <?php echo $common->icons()->getIcon('svg-lithium-remove', [], false); ?>
                                            </span>
                                          </a>
                                      <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="tab-pane fade" id="available" role="tabpanel" aria-labelledby="available-tab" tabindex="0">
                <div class="table-responsive">
                    <table class="table table-hover m-0">
                        <thead>
                        <tr>
                            <th class="logo">&nbsp;</th>
                            <th><?php _e('Name', 'rmcommon'); ?></th>
                            <th class="text-center"><?php _e('Version', 'rmcommon'); ?></th>
                            <th class="text-center"><?php _e('Author', 'rmcommon'); ?></th>
                            <th colspan="4" class="text-center"><?php _e('Options', 'rmcommon'); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($available_mods as $mod): ?>
                            <tr id="module-<?php echo $mod['dirname']; ?>">
                                <td class="logo">
                                    <img src="<?php echo $mod['image']; ?>" alt="<?php echo $mod['name']; ?>">
                                </td>
                                <td class="name" align="left">
                                    <strong class="the_name text-nowrap">
                                        <?php echo $mod['name']; ?>
                                    </strong>
                                  <?php if ('' != $mod['help']): ?>
                                      <a href="<?php echo preg_match("/(http|\.{2})/i", $mod['help']) ? $mod['help'] : '../' . $mod['dirname'] . '/' . $mod['help']; ?>"
                                         class="help cu-help-button text-success"
                                         title="<?php echo sprintf(__('%s Help', 'rmcommon'), $mod['name']); ?>"><span
                                                  class="fa fa-question-circle"></span> <?php _e('Help', 'rmcommon'); ?>
                                      </a>
                                  <?php endif; ?>
                                    <small class="text-muted d-block"><?php echo $mod['description']; ?></small>
                                </td>
                                <td class="text-center text-nowrap">
                                  <?php echo $mod['version']; ?>
                                </td>
                                <td class="author text-center" nowrap>
                                  <?php foreach ($mod['authors'] as $author): ?>
                                    <?php if ('' != $author['url']): ?>
                                          <a href="<?php echo $author['url']; ?>" target="_blank"
                                             title="<?php echo $author['name']; ?>">
                                              <img src="http://www.gravatar.com/avatar/<?php echo md5($author['email']); ?>?s=40"
                                                   alt="<?php echo $author['aka']; ?>">
                                          </a>
                                    <?php else: ?>
                                          <img src="http://www.gravatar.com/avatar/<?php echo md5($author['email']); ?>?s=40"
                                               title="<?php echo $author['name']; ?>"
                                               alt="<?php echo $author['aka']; ?>">
                                    <?php endif; ?>
                                  <?php endforeach; ?>
                                    <span class="hidden_data">
                        <span class="adminlink"></span>
                        <span class="link"></span>
                        <span class="name"><?php echo $mod['name']; ?></span>
                        <span class="id"></span>
                        <span class="icon"></span>
                        <span class="image"><?php echo $mod['image']; ?></span>
                        <span class="oname"><?php echo $mod['name']; ?></span>
                        <span class="version"><?php echo $mod['version']; ?></span>
                        <span class="dirname"><?php echo $mod['dirname']; ?></span>
                        <span class="author">
                            <?php foreach ($mod['authors'] as $author): ?>
                                <a href="<?php echo $author['url']; ?>" target="_blank"
                                   title="<?php echo $author['name']; ?>">
                                    <img src="http://www.gravatar.com/avatar/<?php echo md5($author['email']); ?>?s=60"
                                         alt="<?php echo $author['aka']; ?>">
                                </a>
                            <?php endforeach; ?>
                        </span>
                        <span class="url"><?php echo isset($mod['url']) ? $mod['url'] : ''; ?></span>
                        <span class="license"><?php echo $mod['license']; ?></span>
                        <span class="help"><?php echo preg_match("/(http|\.{2})/i", $mod['help']) ? $mod['help'] : '../' . $mod['dirname'] . '/' . $mod['help']; ?></span>
                        <span class="active"></span>
                        <span class="social">
                            <?php if ($mod['social']): ?>
                              <?php foreach ($mod['social'] as $social): ?>
                                    <a href="<?php echo $social['url']; ?>" target="_blank"
                                       title="<?php echo $social['title']; ?>">
                                        <span class="fa fa-<?php echo $social['type']; ?>"></span>
                                    </a>
                              <?php endforeach; ?>
                            <?php endif; ?>
                        </span>
                    </span>
                                </td>
                                <td class="actions text-center" nowrap>
                                    <div class="d-flex align-items-center justify-content-center">
                                        <a href="modules.php?action=install&amp;dir=<?php echo $mod['dirname']; ?>"
                                           title="<?php _e('Install', 'rmcommon'); ?>" class="btn btn-link text-success">
                                      <span class="text-success">
                                                <?php echo $common->icons()->getIcon('svg-lithium-floppy-disk', [], false); ?>
                                            </span>
                                        </a>
                                        <a href="#" class="btn btn-link text-info data_button" data-bs-toggle="modal" data-bs-target="#info-module">
                                          <span class="text-info">
                                                <?php echo $common->icons()->getIcon('svg-lithium-info', [], false); ?>
                                            </span>
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

</div>

<div class="modal fade" id="info-module">
    <div class="modal-dialog xlarge">
        <div class="modal-content">
            <div class="modal-header cu-titlebar">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><?php _e('Module Information', 'rmcommon'); ?></h4>
            </div>
            <div class="modal-body info-container">
                <div class="row header">
                    <div class="col-xs-4 col-sm-4 col-md-3 col-lg-3">
                        <img src="#" alt="#" class="img-responsive">
                    </div>
                    <div class="col-xs-8 col-sm-8 col-md-9 col-lg-9 text-right">
                        <h3></h3>
                        <span class="help-block desc"></span>
                    </div>
                </div>
                <hr>

                <div class="row form-group">
                    <div class="col-sm-6">
                        <label><?php _e('Current Version:', 'rmcommon'); ?></label>
                        <span class="form-control version"></span>
                    </div>
                    <div class="col-sm-6">
                        <label><?php _e('Directory:', 'rmcommon'); ?></label>
                        <span class="form-control dirname"></span>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-sm-6">
                        <label><?php _e('Author(s):', 'rmcommon'); ?></label>
                        <span class="author"><?php _e('Not provided', 'rmcommon'); ?></span>
                    </div>
                    <div class="col-sm-6">
                        <label><?php _e('Module web site:', 'rmcommon'); ?></label>
                        <span class="form-control web"><?php _e('Not provided', 'rmcommon'); ?></span>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-sm-6">
                        <label><?php _e('License:', 'rmcommon'); ?></label>
                        <span class="form-control license"></span>
                    </div>
                    <div class="col-sm-6">
                        <label><?php _e('Help:', 'rmcommon'); ?></label>
                        <span class="form-control help"><?php _e('Not provided', 'rmcommon'); ?></span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12 text-center">
                        <span class="social"></span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary"
                        data-dismiss="modal"><?php _e('Close', 'rmcommon'); ?></button>
            </div>
        </div>
    </div>
</div>
