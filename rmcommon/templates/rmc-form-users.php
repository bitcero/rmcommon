<form name="<?php echo $field; ?>_users_form">
    <div class="form-users-options">
        <div class="col-lg-4 form-group">
            <label for="<?php echo $field ?>-kw"><?php _e('Search user:', 'rmcommon'); ?></label>
            <input type="text" class="form-control" id="<?php echo $field ?>-kw" placeholder="<?php _e('Search user', 'rmcommon'); ?>" value="<?php echo $kw; ?>" />
        </div>
        <div class="col-lg-3 form-group">
            <label for="<?php echo $field ?>-limit"><?php _e('Results:', 'rmcommon'); ?></label>
            <input type="text" class="form-control" id="<?php echo $field ?>-limit" placeholder="<?php _e('Results', 'rmcommon'); ?>" value="<?php echo $limit; ?>" />
        </div>
        <div class="col-lg-3 form-group">
            <label for="<?php echo $field ?>-ord"><?php _e('Sort by:', 'rmcommon'); ?></label>
            <select id="<?php echo $field ?>-ord" class="form-control">
                <option value="2"<?php echo $ord==2 ? ' selected="selected"' : ''; ?>><?php _e('ID', 'rmcommon'); ?></option>
                <option value="0"<?php echo $ord==0 ? ' selected="selected"' : ''; ?>><?php _e('Date of register', 'rmcommon'); ?></option>
                <option value="1"<?php echo $ord==1 ? ' selected="selected"' : ''; ?>><?php _e('Name', 'rmcommon'); ?></option>
            </select>
        </div>
        <div class="col-lg-2 form-group">
            <br>
            <button class="btn btn-primary" type="button" onclick="usersField.submit_search(<?php echo $type; ?>);"><?php _e('Search', 'rmcommon'); ?></button>
        </div>
    </div>

    <div class="row">
        <?php if ($field_type=='checkbox'): ?>
            <div class="col-lg-5">
                <div class="form-users-selected-list">
                    <div id="<?php echo $field; ?>-selected-title" class="form_users_selected_title">
                        <?php _e('Selected:', 'rmcommon'); ?> (<strong><span><?php echo count($selected); ?></span></strong>)
                    </div>
                    <ul id="<?php echo $field; ?>-selected-list">
                        <?php foreach ($selecteds as $sel): ?>
                            <li class="user_<?php echo $sel['id'] ?>">
                                <label><input type="checkbox" name="s[]" value="<?php echo $sel['id']; ?>" checked="checked" onchange="usersField.remove_from_list(<?php echo $sel['id']; ?>);" />
                                    <span id="user-<?php echo $field; ?>-caption-<?php echo $sel['id']; ?>"><?php echo $sel['name']; ?></span>
                                </label></li>
                        <?php endforeach; ?>
                    </ul>
                    <div align="center"><button type="button" class="btn btn-default" onclick="usersField.insert_users(<?php echo $type; ?>);"><?php echo _e('Insert Users', 'rmcommon'); ?></button></div>
                </div>
            </div>
        <?php endif; ?>

        <div class="<?php echo $field_type=='checkbox' ? 'col-lg-7' : 'col-lg-12'; ?>">

            <div class="form-users-fields">

                <div class="col-lg-12">
                    <ul class="list-unstyled">
                    <?php foreach ($users as $user): ?>
                        <li>
                            <div class="<?php echo $field_type; ?>">
                                <label>
                                <input <?php if ($type): ?>onchange="usersField.add_to_list(<?php echo $user['id']; ?>);"<?php else: ?>onclick="usersField.insert_users(<?php echo $type.','.$user['id']; ?>);"<?php endif; ?> type="<?php echo $field_type ?>" id="<?php echo $field; ?>-user-<?php echo $user['id']; ?>" name="users<?php echo $field_type=='checkbox' ? '[]' : ''; ?>" value="<?php echo $user['id']; ?>"<?php echo $user['check'] ? ' checked="checked"' : ''; ?> />
                                <span id="<?php echo $field; ?>-username-<?php echo $user['id']; ?>"><?php echo $user['name']; ?></span>
                                </label>
                            </div>
                        </li>
                    <?php endforeach; ?>
                    </ul>
                    <?php $nav->display(false); ?>
                </div>

            </div>

        </div>
    </div>

<?php $cols = $field_type=='radio' ? 5 : 3; ?>
<?php if (!$type): ?>
<input type="hidden" name="s" value="<?php echo $selected_string; ?>" id="s" />
<?php endif; ?>
</form>
