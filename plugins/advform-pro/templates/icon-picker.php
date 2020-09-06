
<div <?php echo $attributes; ?>>

    <!-- Selected indicator -->
    <span class="btn btn-info <?php echo $this->get('size') != '' ? 'btn-' . $this->get('size') : ''; ?> adv-icon-selected">
        <span><?php echo $cuIcons->getIcon($this->get('default')); ?></span>
        <input type="hidden" id="<?php echo $this->get('id'); ?>" name="<?php echo $this->get('name'); ?>" value="<?php echo $this->get('default'); ?>">
    </span>

    <!-- SVG icons -->
    <?php if($this->get('svg')): ?>
        <div class="btn-group dropdown <?php echo $this->get('size') != '' ? 'button-group-' . $this->get('size') : ''; ?> adv-icons-svg" id="picker-<?php echo $this->get('id'); ?>">
            <button type="button" class="btn btn-default dropdown-toggle<?php echo substr( $this->get('default'), 0, 3)=='svg' ? ' active' : ''; ?>" data-toggle="dropdown">
                <span class="the-icon"><?php _e('SVG Icons', 'advform-pro'); ?></span>
                <span class="caret"></span>
            </button>
            <div class="dropdown-menu" role="menu">
                <div class="icons-container">
                    <?php
                    $icons = $cuIcons->getIconsList();
                    ?>
                    <ul>
                        <?php foreach($icons as $icon): ?>
                            <li><a href="#" title="<?php echo $icon; ?>" data-icon="<?php echo $icon; ?>"><?php echo $cuIcons->getIcon($icon); ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!--- FontAwesome Icons -->
    <?php if ( $this->get('fa') ): ?>
    <div class="btn-group <?php echo $this->get('size') != '' ? 'button-group-' . $this->get('size') : ''; ?> adv-icons-fa" id="picker-<?php echo $this->get('id'); ?>">
        <button type="button" class="btn btn-default dropdown-toggle<?php echo substr( $this->get('default'), 0, 2)=='fa' ? ' active' : ''; ?>" data-toggle="dropdown">
            <span class="the-icon"><?php _e('FontAwesome', 'advform-pro'); ?></span>
            <span class="caret"></span>
        </button>
        <div class="dropdown-menu" role="menu">
            <div class="icons-container">
                <span class="fa fa-spin fa-spinner"></span>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php if ( $this->get('glyph') ): ?>
    <!-- Glyphicons Icons -->
    <div class="btn-group <?php echo $this->get('size') != '' ? 'button-group-' . $this->get('size') : ''; ?> adv-icons-glyph" id="picker-<?php echo $this->get('id'); ?>">
        <button type="button" class="btn btn-default dropdown-toggle<?php echo substr( $this->get('default'), 0, 9)=='glyphicon' ? ' active' : ''; ?>" data-toggle="dropdown">
            <span class="the-icon"><?php _e('Glyphicons', 'advform-pro'); ?></span>
            <span class="caret"></span>
        </button>
        <div class="dropdown-menu" role="menu">
            <div class="icons-container">
                <span class="fa fa-spin fa-spinner"></span>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- IcoMoon Icons -->
    <?php if ( $this->get('moon') ): ?>
        <div class="btn-group <?php echo $this->get('size') != '' ? 'button-group-' . $this->get('size') : ''; ?> adv-icons-icomoon" id="picker-<?php echo $this->get('id'); ?>">
            <button type="button" class="btn btn-default dropdown-toggle<?php echo substr( $this->get('default'), 0, 9)=='icon' ? ' active' : ''; ?>" data-toggle="dropdown">
                <span class="the-icon"><?php _e('IcoMoon', 'advform-pro'); ?></span>
                <span class="caret"></span>
            </button>
            <div class="dropdown-menu" role="menu">
                <div class="icons-container">
                    <span class="fa fa-spin fa-spinner"></span>
                </div>
            </div>
        </div>
    <?php endif; ?>

</div>

