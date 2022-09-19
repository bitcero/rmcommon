<div <?php echo $attributes?>>
    <div class="row">
        <div class="col text-center">
            <?php echo $icon; ?>
        </div>
        <div class="col first-cell">
            <?php if (isset($cells[0])): ?>
            <small class="text-truncate"><?php echo $cells[0]->caption; ?></small>
            <em class="text-truncate"><?php echo $cells[0]->value; ?></em>
            <?php endif; ?>
        </div>
    </div>
    <div class="row other-cells">
        <?php
        array_shift($cells);
        foreach ($cells as $cell): ?>
        <div class="col">
            <small><?php echo $cell->caption; ?></small>
            <em class="text-truncate"><?php echo $cell->value; ?></em>
        </div>
        <?php endforeach; ?>
    </div>
</div>