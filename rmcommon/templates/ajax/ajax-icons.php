<ul class="icons-grid" data-size="<?php echo $size; ?>">
    <?php foreach($icons as $icon): ?>
        <li>
            <a href="#" title="<?php echo str_replace($providerPrefix, '', $icon); ?>" data-icon="<?php echo $icon; ?>"><?php echo $cuIcons->getIcon($icon); ?></a>
        </li>
    <?php endforeach; ?>
</ul>