<div id="icons-options">
    <div class="form-floating mb-3 mb-md-0">
        <select name="provider" id="provider" class="form-select">
          <?php foreach ($providers as $provider): ?>
              <option value="<?php echo $provider['id']; ?>"><?php echo $provider['name']; ?></option>
          <?php endforeach; ?>
        </select>
        <label for="provider"><?php _e('Provider:', 'rmcommon'); ?></label>
    </div>
    <div class="form-floating mb-3 mb-md-0">
        <input type="text" class="form-control" id="icons-search" placeholder="<?php _e('Search:', 'rmcommon'); ?>">
        <label for="icons-search"><?php _e('Search by name:', 'rmcommon'); ?></label>
    </div>
    <div id="icons-sizes">
        <input type="radio" class="btn-check" value="32" name="sizes" id="size-small" autocomplete="off">
        <label class="btn btn-primary" for="size-small"><?php _e('Small', 'rmcommon'); ?></label>

        <input type="radio" class="btn-check" value="64" name="sizes" id="size-medium" autocomplete="off">
        <label class="btn btn-primary" for="size-medium"><?php _e('Medium', 'rmcommon'); ?></label>

        <input type="radio" class="btn-check" name="sizes" value="120" id="size-large" autocomplete="off">
        <label class="btn btn-primary" for="size-large"><?php _e('Large', 'rmcommon'); ?></label>
    </div>
</div>

<div class="cu-box box-primary icons-container">
    <div class="box-header">
        <h3 class="box-title"><?php echo sprintf(__('Icons from %s', 'rmcommon'), $selectedProvider['name']); ?></h3>
    </div>
    <div class="box-content">
        <ul class="icons-grid" data-size="32" data-provider="<?php echo $selectedProvider['name']; ?>">
          <?php foreach ($icons as $icon): ?>
              <li data-icon="<?php echo str_replace($providerPrefix, '', $icon); ?>">
                  <a href="#" title="<?php echo str_replace($providerPrefix, '', $icon); ?>"
                     data-icon="<?php echo $icon; ?>"><?php echo $cuIcons->getIcon($icon); ?></a>
              </li>
          <?php endforeach; ?>
        </ul>
    </div>
</div>

<script id="details-tpl" type="text/x-jsrender">
    <div class="icon-data">
        <button class="close">
            <?php echo $common->icons()->svg('svg-lithium-cross'); ?>
        </button>
        <div class="d-md-flex align-items-stretch">
            <div class="icon-preview d-flex align-items-center justify-content-center me-md-4">
                {{:icon}}
            </div>
            <div class="icon-details">
                <h4>{{:iconCode}}</h4>

                <table class="table">
                    <tr>
                        <th><?php _e('Provider:', 'rmcommon'); ?></th>
                        <td>{{:provider}}</td>
                    </tr>
                    <tr>
                        <th><?php _e('Code:', 'rmcommon'); ?></th>
                        <td class="copy-text"><code>{{:iconCode}}</code></td>
                    </tr>
                    <tr>
                        <th><?php _e('PHP Usage:', 'rmcommon'); ?></th>
                        <td class="copy-text"><code>$common->icons->svg('{{:iconCode}}');</code></td>
                    </tr>
                    <tr>
                        <th><?php _e('Smarty Usage:', 'rmcommon'); ?></th>
                        <td class="copy-text"><code>&lt;{cuIcon icon={{:iconCode}}}&gt;</code></td>
                    </tr>
                    <tr>
                        <th><?php _e('File name:', 'rmcommon'); ?></th>
                        <td class="copy-text"><code>{{:file}}.svg</code></td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="f-copied"><?php _e('Copied to clipboard!', 'rmcommon'); ?></div>
    </div>

</script>