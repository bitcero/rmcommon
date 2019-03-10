<div class="row" id="icons-options">
    <div class="col-md-4">
        <div class="input-group">
            <span class="input-group-addon">
                <?php _e('Proveedor:', 'rmcommon'); ?>
            </span>
            <select name="provider" id="provider" class="form-control">
                <?php foreach ($providers as $provider): ?>
                    <option value="<?php echo $provider['id']; ?>"><?php echo $provider['name']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <div class="col-sm-6 col-md-4">
        <div class="input-group">
            <span class="input-group-addon">
                <?php _e('Search:', 'rmcommon'); ?>
            </span>
            <input type="text" class="form-control" id="icons-search">
        </div>
    </div>
    <div class="col-sm-6 col-md-4 text-right">
        <div class="btn-group" data-toggle="buttons" id="icons-sizes">
            <label class="btn btn-pink active">
                <input type="radio" name="sizes" autocomplete="off" value="32" checked> Small
            </label>
            <label class="btn btn-pink">
                <input type="radio" name="sizes" autocomplete="off" value="48"> Medium
            </label>
            <label class="btn btn-pink">
                <input type="radio" name="sizes" autocomplete="off" value="64"> Large
            </label>
        </div>
    </div>
</div>

<div class="panel panel-pink icons-container">
    <div class="panel-heading">
        <h3 class="panel-title"><?php echo sprintf(__('Icons from %s', 'rmcommon'), $selectedProvider['name']); ?></h3>
    </div>
    <div class="panel-body">
        <ul class="icons-grid" data-size="32" data-provider="<?php echo $selectedProvider['name']; ?>">
            <?php foreach ($icons as $icon): ?>
                <li data-icon="<?php echo str_replace($providerPrefix, '', $icon); ?>">
                    <a href="#" title="<?php echo str_replace($providerPrefix, '', $icon); ?>" data-icon="<?php echo $icon; ?>"><?php echo $cuIcons->getIcon($icon); ?></a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>

<script id="details-tpl" type="text/x-jsrender">
    <div class="icon-data">
    <button class="close">&times;</button>
        <div class="media">
            <div class="media-left">
                <div class="media-object">{{:icon}}</div>
            </div>
            <div class="media-body">
                <h4 class="media-heading">{{:iconCode}}</h4>
                <table>
                    <tbody>
                    <tr>
                        <td><?php _e('Provider:', 'rmcommon'); ?></td>
                        <td>{{:provider}}</td>
                    </tr>
                    <tr>
                        <td><?php _e('Code:', 'rmcommon'); ?></td>
                        <td><code>{{:iconCode}}</code></td>
                    </tr>
                    <tr>
                        <td><?php _e('PHP Usage:', 'rmcommon'); ?></td>
                        <td>
                            <code>$cuIcons->getIcon('{{:iconCode}}');</code>
                        </td>
                    </tr>
                    <tr>
                        <td><?php _e('Smarty Usage:', 'rmcommon'); ?></td>
                        <td>
                            <code>&lt;{cuIcon icon={{:iconCode}}}&gt;</code>
                        </td>
                    </tr>
                    <tr>
                        <td><?php _e('File name:', 'rmcommon'); ?></td>
                        <td>{{:file}}.svg</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</script>