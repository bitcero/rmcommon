<?php if('solid' == $type): ?>
    <div class="widget-counters-row">
        <?php foreach($counters as $counter): ?>
            <div class="row-widget solid counter-<?php echo count($counters); ?> bg-<?php echo $counter->color; ?>">
                <div>
                    <div class="col-xs-5 the-caption">
                        <?php echo $cuIcons->getIcon($counter->icon); ?>
                        <span class="caption">
                        <?php echo $counter->caption; ?>
                    </span>
                    </div>
                    <div class="col-xs-7 the-counter">
                        <?php echo $counter->counter; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="widget-counters-row">
        <?php foreach($counters as $counter): ?>
            <div class="row-widget counter-<?php echo count($counters); ?>">
                <div>
                    <div class="col-xs-5 the-caption">
                        <?php echo $cuIcons->getIcon($counter->icon); ?>
                        <span class="caption">
                        <?php echo $counter->caption; ?>
                    </span>
                    </div>
                    <div class="col-xs-7 the-counter text-<?php echo $counter->color; ?>">
                        <?php echo $counter->counter; ?>
                    </div>
                <span class="border">
                    <span class="bg-<?php echo $counter->color; ?>"></span>
                </span>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
