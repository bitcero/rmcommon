<form <?php echo $attributes; ?>>

    <div class="panel panel-default">
        <div class="panel-heading">
            <?php if ($form->has('title')): ?>
            <hi class="panel-title">
                <?php echo $form->get('title'); ?>
            </hi>
            <?php endif; ?>
        </div>
        <div class="panel-body">
            <div class="form_container">
                <?php
                $elements = $form->elements();

                foreach ($elements as $data):
                    //print_r($data);
                    $field = &$data['field'];

                    if ('RMFormHidden' == $data['class']):
                        echo $field->render();
                    elseif ('RMFormSubTitle' == $data['class']):
                        ?>
                        <h3><?php echo $field->render(); ?></h3>
                        <?php if ('' != $field->getDescription()): ?><span class="help-block"><?php echo $field->getDescription(); ?></span><?php endif; ?>

                        <?php
                    else:
                        ?>
                        <div id="row_<?php echo $field->getName(); ?>" class="form-group">
                            <div class="col-md-3">
                                <?php if ('' != $field->getCaption()): ?><label for="<?php echo $field->getName(); ?>"><?php echo $field->getCaption(); ?></label><?php else: ?>&nbsp;<?php endif; ?>
                            </div>
                            <div class="col-md-9">
                                <?php echo $field->render(); ?>
                                <?php
                                switch (get_class($field)) {
                                    case 'RMFormButtonGroup':
                                    case 'RMFormButton':
                                    case 'RMFormHidden':
                                        break;
                                    default: ?>
                                        <label for="<?php echo $field->getName(); ?>" class="error hidden"><?php _e('This is a required field. Please fill it!', 'rmcommon'); ?></label>
                                        <?php
                                }
                                ?>
                                <?php if ('' != $field->getDescription()): ?><span class="help-block"><?php echo $field->getDescription(); ?></span><?php endif; ?>
                            </div>
                        </div>
                        <?php
                    endif;
                endforeach;
                ?>
            </div>
        </div>
    </div>

</form>
