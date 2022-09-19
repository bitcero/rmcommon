<div <?php echo $attributes?>>
  <div class="box-content">
      <div class="widget-titles d-flex justify-content-between align-items-center">
        <?php foreach($titles as $title): ?>
            <div class="widget-title title-<?php echo $title->size; ?>">
              <?php echo $title->title; ?>
            </div>
        <?php endforeach; ?>
      </div>
      <div class="widget-content row">
        <?php foreach($statistics as $item): ?>
            <div class="data-item <?php echo $item->css_classes; ?> <?php echo $item->color; ?>">
                <div class="d-flex justify-content-start align-items-center">
                    <div class="icon">
                        <span>
                            <?php echo $common->icons()->getIcon($item->icon, [], false); ?>
                        </span>
                    </div>
                    <div class="data">
                        <h4 class="value">
                            <?php echo $item->value; ?>
                        </h4>
                        <p class="title">
                            <?php echo $item->title; ?>
                        </p>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
      </div>
  </div>
</div>