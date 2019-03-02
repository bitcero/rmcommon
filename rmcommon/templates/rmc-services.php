<div data-container="dashboard">
<?php
foreach ($allServices as $id => $services):
?>
        <div class="size-1 services-container" data-dashboard="item">
            <div class="cu-box">
                <div class="box-header">
                    <span class="fa fa-caret-up box-handler"></span>
                    <h3 class="box-title"><?php echo sprintf(__('Service: %s', 'rmcommon'), $id); ?></h3>
                </div>
                <div class="box-content" data-service="<?php echo $id; ?>">
                    <?php foreach ($services as $idService => $service): ?>
                    <div class="radio">
                        <label>
                            <input type="radio" data-name="<?php echo $service['name']; ?>" name="services-<?php echo $id; ?>" value="<?php echo $idService; ?>"<?php echo array_key_exists($id, $enabledProviders) && $idService === $enabledProviders[$id] ? ' checked' : ''; ?>>
                            <strong><?php echo $service['name']; ?></strong>
                            <small class="help-block"><?php echo $service['description']; ?></small>
                        </label>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

<?php
endforeach;
?>
</div>