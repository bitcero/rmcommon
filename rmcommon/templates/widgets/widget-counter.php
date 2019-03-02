<div <?php echo $attributes?>>
    <div class="row">
        <div class="col-xs-4 text-center">
            <?php echo $icon; ?>
        </div>
        <div class="col-xs-8 first-cell">
            <?php if (isset($cells[0])): ?>
            <small><?php echo $cells[0]->caption; ?></small>
            <em><?php echo $cells[0]->value; ?></em>
            <?php endif; ?>
        </div>
    </div>
    <div class="row other-cells">
        <?php
        array_shift($cells);
        foreach ($cells as $cell): ?>
        <div class="col-xs-<?php echo 12 / count($cells); ?>">
            <small><?php echo $cell->caption; ?></small>
            <em><?php echo $cell->value; ?></em>
        </div>
        <?php endforeach; ?>
    </div>
</div>