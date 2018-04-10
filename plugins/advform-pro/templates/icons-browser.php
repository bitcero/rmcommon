<div class="adv-icons-browser" data-parent="<?php echo $parent; ?>">
    <ul class="nav nav-tabs nav-tabs-color" role="tablist">
        <li role="presentation" class="nav-item active">
            <a href="#svg-icons" aria-controls="svg-icons" role="tab" data-toggle="tab" class="nav-link">
                <?php _e('SVG Icons', 'advform-pro'); ?>
            </a>
        </li>
        <li role="presentation" class="nav-item">
            <a href="#fa-icons" aria-controls="fa-icons" role="tab" data-toggle="tab" class="nav-link">
                <?php _e('FontAwesome', 'advform-pro'); ?>
            </a>
        </li>
        <li role="presentation" class="nav-item">
            <a href="#glyph-icons" aria-controls="glyph-icons" role="tab" data-toggle="tab" class="nav-link">
                <?php _e('Glyphicons', 'advform-pro'); ?>
            </a>
        </li>
        <li role="presentation" class="nav-item">
            <a href="#moon-icons" aria-controls="moon-icons" role="tab" data-toggle="tab" class="nav-link">
                <?php _e('Icomoon Icons', 'advform-pro'); ?>
            </a>
        </li>
    </ul>

    <div class="adv-icons-browser-controls">
        <div class="row">
            <div class="control-search">
                <input type="text" class="form-control" name="search-icon" id="adv-control-search" placeholder="<?php _e('Search icon...', 'advform-pro'); ?>">
            </div>
        </div>
    </div>

    <div class="tab-content">
        <div role="tabpanel" class="tab-pane fade in active" id="svg-icons">
            <ul class="adv-icons-list" data-type="svg">
                <?php foreach($svgIcons as $icon): ?>
                    <li<?php echo $icon == $selectedIcon ? ' class="selected"' : ''; ?>>
                        <a href="#" data-icon="<?php echo $icon; ?>" title="<?php echo $icon; ?>">
                            <?php echo $cuIcons->getIcon($icon); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div role="tabpanel" class="tab-pane fade" id="fa-icons">
            <ul class="adv-icons-list" data-type="font">
                <?php foreach($faIcons as $icon): ?>
                    <li<?php echo 'fa ' . $icon == $selectedIcon ? ' class="selected"' : ''; ?>>
                        <a href="#" data-icon="<?php echo 'fa ' . $icon; ?>" title="<?php echo 'fa ' . $icon; ?>">
                            <?php echo $cuIcons->getIcon('fa ' . $icon); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div role="tabpanel" class="tab-pane fade" id="glyph-icons">
            <ul class="adv-icons-list" data-type="font">
                <?php foreach($glyphIcons as $icon): ?>
                    <li<?php echo 'glyphicon ' . $icon == $selectedIcon ? ' class="selected"' : ''; ?>>
                        <a href="#" data-icon="<?php echo 'glyphicon ' . $icon; ?>" title="<?php echo 'glyphicon ' . $icon; ?>">
                            <?php echo $cuIcons->getIcon('glyphicon ' . $icon); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div role="tabpanel" class="tab-pane fade" id="moon-icons">
            <ul class="adv-icons-list" data-type="font">
                <?php foreach($moonIcons as $icon): ?>
                    <li<?php echo 'icon ' . $icon == $selectedIcon ? ' class="selected"' : ''; ?>>
                        <a href="#" data-icon="<?php echo 'icon ' . $icon; ?>" title="<?php echo 'icon ' . $icon; ?>">
                            <?php echo $cuIcons->getIcon('icon ' . $icon); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>