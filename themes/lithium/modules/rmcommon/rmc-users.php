<h1 class="cu-section-title"><i class="glyphicon glyphicon-user"></i> <?php _e('Users Management', 'rmcommon'); ?></h1>

<div class="cu-box">
    <div class="box-content no-padding">
        <div class="controls-toggler d-flex justify-content-between align-items-center d-md-none p-3 pb-0">
            <button type="button" class="btn btn-link" id="show-basic-search">
              <?php _e('Search options', 'rmcommon'); ?>
            </button>
            <button type="button" class="btn btn-link" id="show-other">
              <?php _e('Bulk options', 'rmcommon'); ?>
            </button>
        </div>
        <form name="frmUsers" id="form-users" method="post" action="users.php" class="form-inline">
            <!-- Navigation Options -->
            <div class="d-lg-flex align-items-center justify-content-between p-3">
                <div id="users-filter-options" class="d-md-flex">
                        <div class="sections">
                            <label for="search-key" class="visually-hidden"><?php _e('Search:', 'rmcommon'); ?></label>
                            <input class="form-control" type="text" name="keyw" id="search-key" size="15"
                                   value="<?php echo $srhkeyw; ?>" placeholder="<?php _e('Search', 'rmcommon'); ?>">
                        </div>
                        <div class="sections">
                            <label for="users-number" class="visually-hidden"><?php _e('Show:', 'rmcommon'); ?></label>
                            <input class="form-control" type="text" name="limit" id="users-number" size="6"
                                   value="<?php echo $limit; ?>" title="<?php _e('Results limit', 'rmcommon'); ?>">
                        </div>
                        <div class="sections d-grid">
                            <button type="submit"
                                    class="btn btn-primary btn-block"><?php _e('Go Now!', 'rmcommon'); ?></button>
                        </div>
                        <div class="sections d-grid">
                            <button type="button" id="show-search"
                                    class="btn btn-blue with-icon">
                              <?php echo $common->icons()->svg('lithium-search'); ?>
                              <?php _e('Advanced', 'rmcommon'); ?>
                            </button>
                        </div>
                </div>
                <div id="users-other-options" class="d-md-flex">
                    <div class="d-flex">
                        <select name="order" id="user-order" class="form-select me-md-2"
                                onchange="$('#order').val($('#user-order').val()); submit();">
                            <option value=""<?php echo '' == $order ? ' selected="selected"' : ''; ?>><?php _e('Order by...', 'rmcommon'); ?></option>
                            <option value="uid"<?php echo 'uid' == $order ? ' selected="selected"' : ''; ?>><?php _e('ID', 'rmcommon'); ?></option>
                            <option value="uname"<?php echo 'uname' == $order ? ' selected="selected"' : ''; ?>><?php _e('Username', 'rmcommon'); ?></option>
                            <option value="name"<?php echo 'name' == $order ? ' selected="selected"' : ''; ?>><?php _e('Name', 'rmcommon'); ?></option>
                            <option value="email"<?php echo 'email' == $order ? ' selected="selected"' : ''; ?>><?php _e('Email', 'rmcommon'); ?></option>
                        </select>
                        <button type="button" class="btn btn-default visually-hidden"
                                onclick="$('#order').val($('#user-order').val()); submit();"><?php _e('Sort', 'rmcommon'); ?></button>
                    </div>

                    <div class="d-flex">
                        <select name="action" id="bulk-top" class="form-select me-2">
                            <option value=""><?php _e('Bulk Actions...', 'rmcommon'); ?></option>
                            <option value="activate"><?php _e('Activate', 'rmcommon'); ?></option>
                            <option value="deactivate"><?php _e('Deactivate', 'rmcommon'); ?></option>
                            <option value="mailer"><?php _e('Send email', 'rmcommon'); ?></option>
                            <option value="delete"><?php _e('Delete', 'rmcommon'); ?></option>
                        </select>
                        <button type="button" onclick="before_submit('form-users');" class="btn btn-primary"
                                id="the-op-top"><?php _e('Apply', 'rmcommon'); ?></button>
                    </div>

                </div>

            </div>
            <!-- Navigation Options -->

            <div id="users-advanced-options"<?php echo $display_adv ? ' style="display: block;"' : ''; ?>>
                <div class="filters">
                    <div class="filter-option">
                        <div class="form-floating">
                            <input type="text" class="form-control" name="email" id="user-email"
                                   value="<?php echo $srhemail; ?>" size="20">
                            <label for="user-email"><?php _e('Email:', 'rmcommon'); ?></label>
                        </div>
                    </div>

                    <div class="filter-option">
                        <div class="form-floating">
                            <input type="text" class="form-control" name="url" id="user-url"
                                   size="20"
                                   value="<?php echo $srhurl; ?>">
                            <label for="user-url" class="form-label"><?php _e('Web site:', 'rmcommon'); ?></label>
                        </div>
                    </div>

                    <div class="filter-option">
                        <div class="form-floating">
                            <input type="text" class="form-control" name="from" id="user-from"
                                   size="20"
                                   value="<?php echo $srhfrom; ?>">
                            <label for="user-from"
                                   class="form-label"><?php _e('Country/Location:', 'rmcommon'); ?></label>
                        </div>
                    </div>

                    <div class="filter-option">
                        <label for="registered1"
                               class="form-label"><?php _e('Registered between:', 'rmcommon'); ?></label>
                        <div class="d-flex align-items-center">
                          <?php echo $register1->render(); ?>
                            <span class="ps-2 pe-2"><?php _e('and', 'rmcommon'); ?></span>
                          <?php echo $register2->render(); ?>
                        </div>
                    </div>

                    <div class="filter-option">
                        <label for="login1"
                               class="form-label"><?php _e('Last login between:', 'rmcommon'); ?></label><br>
                        <div class="d-flex align-items-center">
                          <?php echo $login1->render(); ?>
                            <span class="ps-2 pe-2"><?php _e('and', 'rmcommon'); ?></span>
                          <?php echo $login2->render(); ?>
                        </div>
                    </div>

                    <div class="filter-option">
                        <label for="<?php _e('Posts between:', 'rmcommon'); ?>"
                               class="form-label"><?php _e('Posts between:', 'rmcommon'); ?></label><br>
                        <div class="d-flex align-items-center">
                            <input type="text" class="form-control inline" name="posts1"
                                   id="users-posts1"
                                   value="<?php echo (int)$srhposts1; ?>" size="5">
                            <span class="ps-2 pe-2"><?php _e('and', 'rmcommon'); ?></span>
                            <input type="text" class="form-control inline" name="posts2"
                                   id="users-posts2"
                                   value="<?php echo (int)$srhposts2 > 0 ? (int)$posts2 : ''; ?>"
                                   size="5">
                        </div>
                    </div>

                    <div class="filter-option">
                        <div class="form-floating">
                            <select name="mailok" id="users-mailok" class="form-select">
                                <option value="-1"<?php echo -1 == $srhmailok || '' == $srhmailok ? ' selected' : ''; ?>><?php _e('All users', 'rmcommon'); ?></option>
                                <option value="1"<?php echo 1 == $srhmailok ? ' selected' : ''; ?>><?php _e('Users that accept mail', 'rmcommon'); ?></option>
                                <option value="0"<?php echo is_numeric($srhmailok) && 0 == $srhmailok ? ' selected' : ''; ?>><?php _e('Users that do\'nt accept mail', 'rmcommon'); ?></option>
                            </select>
                            <label for="users-mailok" class="form-label"><?php _e('Mail:', 'rmcommon'); ?></label>
                        </div>
                    </div>

                    <div class="filter-option">
                        <div class="form-floating">
                            <select name="actives" id="users-actives" class="form-select">
                                <option value="all"<?php echo 'all' == $srhactives || '' == $srhactives ? ' selected' : ''; ?>><?php _e('All users', 'rmcommon'); ?></option>
                                <option value="active"<?php echo 'active' == $srhactives ? ' selected' : ''; ?>><?php _e('Active users', 'rmcommon'); ?></option>
                                <option value="inactive"<?php echo 'inactive' == $srhactives ? ' selected' : ''; ?>><?php _e('Inactive users', 'rmcommon'); ?></option>
                            </select>
                            <label for="users-actives" class="form-label"><?php _e('Status:', 'rmcommon'); ?></label>
                        </div>
                    </div>

                    <div class="filter-option">
                        <label><?php _e('Search method:', 'rmcommon'); ?></label><br>
                        <div class="mt-2">
                            <div class="form-check form-check-inline">
                                <input name="srhmethod" value="OR" class="form-check-input" id="method-coincident"
                                       type="radio"<?php echo 'OR' == $srhmethod ? ' checked' : ''; ?>>
                                <label class="form-check-label"
                                       for="method-coincident"><?php _e('Coincident', 'rmcommon'); ?></label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input name="srhmethod" value="AND" class="form-check-input" id="method-exact"
                                       type="radio"<?php echo 'AND' == $srhmethod ? ' checked' : ''; ?>>
                                <label class="form-check-label"
                                       for="method-exact"><?php _e('Exact', 'rmcommon'); ?></label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="controls d-md-flex justify-content-between align-items-center">
                    <div class="help-block mb-3 mb-md-0">
                      <?php _e('All these options are optional and will be additional to basic search keyword.', 'rmcommon'); ?>
                    </div>

                    <div class="d-flex" style="white-space: nowrap;">
                        <button type="button"
                                onclick="$('#users-advanced-options').slideUp('slow');"
                                class="btn btn-secondary me-2"><?php _e('Cancel', 'rmcommon'); ?></button>
                        <?php if($display_adv): ?>
                        <button type="button" class="btn btn-success with-icon me-2" onclick="window.location = 'users.php'">
                          <?php echo $common->icons()->svg('lithium-clear'); ?>
                          <?php _e('Clear Filters', 'rmcommon'); ?>
                        </button>
                        <?php endif; ?>
                        <button type="submit"
                                class="btn btn-primary with-icon">
                          <?php echo $common->icons()->svg('lithium-search'); ?>
                          <?php _e('Search Now!', 'rmcommon'); ?>
                        </button>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover no-margin">
                    <thead>
                    <tr>
                        <th class="text-center"><input type="checkbox" class="checkall" id="checkall-top"
                                                                  data-checkbox="users"></th>
                        <th class="text-center"><?php _e('ID', 'rmcommon'); ?></th>
                        <th><?php _e('Username', 'rmcommon'); ?></th>
                        <th><?php _e('Name', 'rmcommon'); ?></th>
                        <th class="text-center"><?php _e('Email', 'rmcommon'); ?></th>
                        <th class="text-center"><?php _e('Registered', 'rmcommon'); ?></th>
                        <th class="text-center"><?php _e('Groups', 'rmcommon'); ?></th>
                        <th class="text-center"><?php _e('Status', 'rmcommon'); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (count($users) <= 0): ?>
                        <tr class="even">
                            <td colspan="8" class="text-center">
                                <span class="text-danger"><?php _e('There are not any user registered.', 'rmcommon'); ?></span>
                            </td>
                        </tr>
                    <?php endif; ?>
                    <?php
                    $class = 'odd';

                    $qstring = '';
                    foreach (RMTemplate::get()->get_vars() as $var => $value) {
                      $qstring .= '' == $qstring ? $var . '=' . $value : '&amp;' . $var . '=' . $value;
                    }

                    foreach ($users as $user):
                      ?>
                        <tr class="<?php echo tpl_cycle('even,odd'); ?><?php echo $user['level'] <= 0 ? ' user_inactive' : '' ?>"
                            valign="top">
                            <td class="text-center"><input type="checkbox" name="ids[]"
                                                           id="item-<?php echo $user['uid']; ?>"
                                                           value="<?php echo $user['uid']; ?>" data-oncheck="users">
                            </td>
                            <td class="text-center"><?php echo $user['uid']; ?></td>
                            <td nowrap="nowrap">
                                <strong><?php echo $user['uname']; ?></strong>
                                <span class="cu-item-options">
                                    <a href="users.php?action=edit&amp;uid=<?php echo $user['uid']; ?>&amp;query=<?php echo base64_encode($qstring); ?>"><?php _e('Edit', 'rmcommon'); ?></a>
                                    <a href="users.php?action=mailer&amp;uid=<?php echo $user['uid']; ?>&amp;query=<?php echo base64_encode($qstring); ?>"><?php _e('Email', 'rmcommon'); ?></a>
                                    <a href="#"
                                       onclick="select_option(<?php echo $user['uid']; ?>,'delete','form-users');"><?php _e('Delete', 'rmcommon'); ?></a>
                                </span>
                            </td>
                            <td><?php echo $user['name']; ?></td>
                            <td class="text-center"><a href="javascript:;"
                                                       title="<?php echo sprintf(__('Send email to %s', 'rmcommon'), $user['uname']); ?>"><?php echo $user['email']; ?></a>
                            </td>
                            <td class="text-center"><?php echo formatTimestamp($user['user_regdate'], 'c'); ?></td>
                            <td class="text-center" class="users_cell_groups">
                              <?php
                              $str = '';
                              foreach ($user['groups'] as $group):
                                $str = '' == $str ? $xgh->get($group)->name() : ', ' . $xgh->get($group)->name();
                                echo $str;
                              endforeach; ?>
                            </td>
                            <td class="text-center <?php echo $user['level'] <= 0 ? ' text-danger' : ' text-success'; ?>">
                              <?php echo $user['level'] <= 0 ? $cuIcons->getIcon('svg-rmcommon-close') : $cuIcons->getIcon('svg-rmcommon-ok-circle'); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Navigation Options -->
          <?php echo $xoopsSecurity->getTokenHTML(); ?>
            <input type="hidden" name="query" value="<?php echo urlencode($query); ?>">
            <!-- Navigation Options -->
        </form>
    </div>
    <div class="box-footer">
        <div class="row">
            <div class="col-md-6">
              <?php $nav->render();
              echo $nav->get_showing(); ?>
            </div>
            <div class="col-md-6">
              <?php $nav->display(false); ?>
            </div>
        </div>
    </div>
</div>
