<h1><?php _e('Notifications Subscriptions', 'rmcommon'); ?></h1>

<p class="lead">
    <?php _e('Next are the notifications that you are subscriben in our site. To cancel any of these, uncheck the box.', 'rmcommon'); ?>
</p>

<?php foreach ($elements as $index => $element): ?>
    <h3><a href="<?php echo $element['link']; ?>"><?php echo $element['name']; ?></a></h3>

    <div class="panel panel-default">
        <div class="table-responsive">
            <table class="table table-bordered" id="cu-notifications">
                <thead>
                <tr>
                    <th class="text-left"><?php _e('Event', 'rmcommon'); ?></th>
                    <th class="text-left"><?php _e('Item', 'rmcommon'); ?></th>
                    <th class="text-center"><?php _e('Date', 'rmcommon'); ?></th>
                    <th class="text-center">&nbsp;</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($items[$index] as $item): ?>
                    <tr>
                        <td><?php echo $item['caption']; ?></td>
                        <td>
                            <a href="<?php echo $item['object']['link']; ?>"><?php echo $item['object']['name']; ?></a>
                        </td>
                        <td class="text-center"><?php echo $item['date']; ?></td>
                        <td class="text-center">
                            <button type="button"
                                   data-info="<?php echo $item['hash']; ?>"
                                   class="cancel-subscription"><?php _e('Unsubscribe', 'rmcommon'); ?></button>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

<?php endforeach; ?>
