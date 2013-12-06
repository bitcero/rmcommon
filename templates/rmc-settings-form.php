<h1 class="cu-section-title">
    <?php echo sprintf( __('%s Settings', 'rmcommon'), $module->getVar('name') ); ?>
</h1>

<!-- Categories tabs -->
<ul class="nav nav-tabs cu-top-tabs">
    <?php
    $i = 0;
    foreach( $categories as $id => $category): ?>
    <li<?php echo $i==0 ? ' class="active"' : ''; ?>><a href="#category-<?php echo $id; ?>" data-toggle="tab"><?php echo $category['caption']; ?></a></li>
    <?php
    $i++;
    endforeach; ?>
</ul>

<?php
$form = new RMForm('','','');
?>

<div class="tab-content">

    <?php
    $i = 0;
    foreach( $categories as $id => $category ):
    ?>
    <div class="tab-pane<?php echo $i==0 ? ' active' : ''; ?>" id="category-<?php echo $id; ?>">

        <?php if( !isset( $category['fields'] ) ): ?>
            <span class="label label-danger"><?php _e('There are not fields in this category.', 'rmcommon'); ?></span>
        <?php else: ?>
            <?php foreach($category['fields'] as $id => $field): ?>

                <div class="row form-group">
                    <div class="col-md-4 col-lg-4">
                        <label for="<?php echo $id; ?>"><?php echo $field->caption; ?></label>
                        <?php if( $field->description != '' ): ?>
                        <span class="help-block"><?php echo $field->description; ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-8 col-lg-8">
                        <?php echo RMSettings::render_field( $field ); ?>
                    </div>
                </div>

            <?php endforeach; ?>
        <?php endif; ?>

    </div>
    <?php
    $i++;
    endforeach;
    ?>

</div>

<!--// Categories -->

