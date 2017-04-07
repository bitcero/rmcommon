<div role="tabpanel" class="tab-pane active" id="languages">
    <div class="row" id="langs-editor">
        <div class="col-xs-6 col-sm-3 col-md-2">
            <label data-field="name"><?php _e('Name','polyglot'); ?></label>
            <a href="https://docs.redmexico.com.mx/docs/bitcero/plugin-polyglot/add-a-language/#field-name" data-action="help" tabindex="-1">
                <?php echo $cuIcons->getIcon('svg-rmcommon-question'); ?>
            </a>
            <input type="text" name="lang_name" data-value="name" class="form-control">
        </div>
        <div class="col-xs-6 col-sm-2">
            <label data-field="code"><?php _e('Code','polyglot'); ?></label>
            <a href="https://docs.redmexico.com.mx/docs/bitcero/plugin-polyglot/add-a-language/#field-code" data-action="help" tabindex="-1">
                <?php echo $cuIcons->getIcon('svg-rmcommon-question'); ?>
            </a>
            <input type="text" name="lang_code" data-value="code" class="form-control" maxlength="2">
        </div>
        <div class="col-xs-6 col-sm-2">
            <label data-field="country"><?php _e('Country', 'polyglot'); ?></label>
            <a href="https://docs.redmexico.com.mx/docs/bitcero/plugin-polyglot/add-a-language/#field-country" data-action="help" tabindex="-1">
                <?php echo $cuIcons->getIcon('svg-rmcommon-question'); ?>
            </a>
            <input type="text" class="form-control field-country" data-value="country" name="lang_country" maxlength="2">
        </div>
        <div class="col-xs-6 col-sm-2">
            <label data-field="file"><?php _e('File', 'polyglot'); ?></label>
            <a href="https://docs.redmexico.com.mx/docs/bitcero/plugin-polyglot/add-a-language/#field-file" data-action="help" tabindex="-1">
                <?php echo $cuIcons->getIcon('svg-rmcommon-question'); ?>
            </a>
            <input type="text" class="form-control" data-value="file" name="lang_file" maxlength="5">
        </div>
        <div class="col-xs-6 col-sm-3 col-md-2">
            <label data-field="directory"><?php _e('Directory', 'polyglot'); ?></label>
            <a href="https://docs.redmexico.com.mx/docs/bitcero/plugin-polyglot/add-a-language/#field-directory" data-action="help" tabindex="-1">
                <?php echo $cuIcons->getIcon('svg-rmcommon-question'); ?>
            </a>
            <input type="text" class="form-control" data-value="directory" name="lang_directory">
        </div>
        <div class="col-xs-6 col-sm-6 col-md-2">
            <label>&nbsp;&nbsp;</label>
            <button class="btn btn-primary btn-block create-lang" type="button">
                <?php echo $cuIcons->getIcon('svg-rmcommon-plus'); ?>
                <?php _e('Language', 'polyglot'); ?>
            </button>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table" id="table-langs">
            <thead>
            <tr>
                <th class="text-center"><?php echo $cuIcons->getIcon('svg-rmcommon-star text-orange'); ?></th>
                <th><?php _e('Name', 'polyglot'); ?></th>
                <th class="text-center"><?php _e('Code', 'polyglot'); ?></th>
                <th class="text-center"><?php _e('Country (flag)', 'polyglot'); ?></th>
                <th class="text-center"><?php _e('File', 'polyglot'); ?></th>
                <th class="text-center"><?php _e('Directory', 'polyglot'); ?></th>
                <th>&nbsp;</th>
            </tr>
            </thead>
            <tbody>
            <?php if(empty($languages)): ?>
                <tr>
                    <td colspan="7" class="text-center text-info">
                        <?php echo $cuIcons->getIcon('svg-rmcommon-info'); ?>
                        <?php _e('Althought there are no languages to handle in Polyglot.', 'polyglot'); ?>
                    </td>
                </tr>
            <?php endif; ?>
            <?php foreach($languages as $code => $lang): ?>
                <tr data-lang="<?php echo $code; ?>">
                    <td class="text-center" data-field="base">
                        <?php if(!$baseAssigned): ?>
                            <input type="radio" name="type" value="<?php echo $code; ?>" class="type-changer"<?php echo $lang['type']=='base' ? ' checked' : ''; ?>>
                        <?php elseif(array_key_exists('type', $lang) && $lang['type']=='base'): ?>
                            <?php echo $cuIcons->getIcon('svg-rmcommon-star text-orange'); ?>
                        <?php endif; ?>
                    </td>
                    <td data-field="name">
                        <?php echo $lang['name']; ?>
                    </td>
                    <td data-field="code" class="text-center">
                        <?php echo $code; ?>
                    </td>
                    <td class="text-center" data-field="country" data-value="<?php echo $lang['country']; ?>">
                        <?php
                        $flag = $plugin->countryFlag($lang['country']);
                        if(null != $flag):
                            ?>
                            <img src="<?php echo $flag; ?>" alt="<?php echo $lang['country']; ?>" title="<?php echo $lang['country']; ?>">
                        <?php else: ?>
                            <?php echo $lang['country']; ?>
                        <?php endif; ?>
                    </td>
                    <td class="text-center" data-field="file">
                        <?php echo $lang['file']; ?>
                    </td>
                    <td class="text-center" data-field="directory">
                        <?php echo $lang['directory']; ?>
                    </td>
                    <td class="text-center cu-options" data-field="options">
                        <a href="#" data-option="edit" data-code="<?php echo $code; ?>" class="info" title="<?php _e('Edit', 'polyglot'); ?>"><?php echo $cuIcons->getIcon('svg-rmcommon-pencil'); ?></a>
                        <?php if($lang['status']=='disabled'): ?>
                            <a href="#" data-option="enable" data-code="<?php echo $code; ?>" class="grey" title="<?php _e('Enable', 'polyglot'); ?>"><?php echo $cuIcons->getIcon('svg-rmcommon-ok'); ?></a>
                        <?php else: ?>
                            <a href="#" data-option="disable" data-code="<?php echo $code; ?>" class="success" title="<?php _e('Disable', 'polyglot'); ?>"><?php echo $cuIcons->getIcon('svg-rmcommon-ok'); ?></a>
                        <?php endif; ?>
                        <a href="#" data-option="delete" data-code="<?php echo $code; ?>" class="danger" title="<?php _e('Delete', 'polyglot'); ?>"><?php echo $cuIcons->getIcon('svg-rmcommon-trash'); ?></a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>