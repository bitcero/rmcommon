<div class="adv-icons-browser" data-parent="<?php echo $parent; ?>">
    <ul class="nav nav-tabs" role="tablist" id="browser-icons-tabs">
        <li role="presentation" class="nav-item">
            <button role="tab" type="button" data-bs-target="#svg-icons" aria-controls="svg-icons" role="tab" data-bs-toggle="tab" class="nav-link active">
                <?php _e('SVG Icons', 'advform-pro'); ?>
            </button>
        </li>
        <li role="presentation" class="nav-item">
            <button data-bs-target="#moon-icons" type="button" aria-controls="moon-icons" role="tab" data-bs-toggle="tab" class="nav-link">
                <?php _e('Icomoon Icons', 'advform-pro'); ?>
            </button>
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
        <div role="tabpanel" class="tab-pane fade in show active" id="svg-icons">
            <?php foreach ($svgIcons as $id => $provider): ?>
            <h4><?php echo $provider['name']; ?></h4>

                <ul class="adv-icons-list" data-type="svg">
                  <?php foreach($provider['icons'] as $icon): ?>
                      <li<?php echo $icon == $selectedIcon ? ' class="selected"' : ''; ?>>
                          <a href="#" data-icon="<?php echo $icon; ?>" title="<?php echo $icon; ?>">
                            <?php echo $cuIcons->getIcon($icon); ?>
                          </a>
                      </li>
                  <?php endforeach; ?>
                </ul>
            <?php endforeach; ?>
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