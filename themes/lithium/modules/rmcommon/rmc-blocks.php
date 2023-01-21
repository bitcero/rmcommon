<h1 class="cu-section-title"><?php _e('Blocks Administration', 'rmcommon'); ?></h1>

<div class="cu-box">
    <div class="box-content no-padding">
        <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button
                        class="nav-link <?php echo 'positions' == $from ? 'active' : ''; ?>"
                        id="position-tab"
                        type="button"
                        aria-controls="positions"
                        role="tab"
                        data-bs-toggle="tab"
                        data-bs-target="#positions">
                  <?php _e('Positions', 'rmcommon'); ?>
                </button>
            </li>
            <li class="nav-item">
                <button
                        class="nav-link <?php echo 'positions' != $from ? 'active' : ''; ?>"
                        id="blocks-tab"
                        type="button"
                        aria-controls="blocks"
                        aria-selected="true"
                        role="tab"
                        data-bs-toggle="tab"
                        data-bs-target="#blocks"
                >
                  <?php _e('Blocks', 'rmcommon'); ?>
                </button>
            </li>
        </ul>

        <div class="tab-content no-padding" id="blocks-tab-content">
            <div role="tabpanel" class="tab-pane fade <?php echo 'positions' == $from ? 'show active' : ''; ?>" aria-labelledby="position-tab" id="positions">

                <?php include $common->template()->path('partials/blocks-positions.tpl.php'); ?>

            </div>

            <div role="tabpanel" class="tab-pane fade <?php echo 'positions' != $from ? 'show active' : ''; ?>" aria-labelledby="blocks-tab" id="blocks">

                <!-- Positions Grid -->
              <?php include $common->template()->path('partials/blocks-list.tpl.php'); ?>

            </div>
        </div>
    </div>
</div>


<input type="hidden" value="<?php echo $xoopsSecurity->createToken(); ?>" id="token-positions" name="token_positions">
<!--// End positions grid -->


<!--/ Positions -->
<script type="text/x-jsrender" id="loading-tpl">
    <div class="d-flex justify-content-center align-items-center">
        <?php echo $common->icons()->svg('svg-rmcommon-spinner-02'); ?>
        <?php _e('Loading data...', 'rmcommon'); ?>
    </div>
</script>

<script type="text/x-jsrender" id="console-log-item">
    <li class="console-item {{:type}}">
        {{:message}}
    </li>

</script>

<script type="text/javascript">
  $(document).ready(function () {
    <?php foreach ($positions as $pos): ?>
    $("#position-<?php echo $pos['id']; ?> .box-content").nestable({
      group: 1,
      maxDepth: 1
    }).on('change', blocksAjax.saveOrder);
    <?php endforeach; ?>
  });
</script>
