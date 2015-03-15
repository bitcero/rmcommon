<?php if ($form->getTitle()!=''): ?><h1 class="cu-section-title"><?php echo $form->getTitle(); ?></h1><?php endif; ?>

<form class="form-horizontal" name="<?php echo $form->getName() ?>" id="<?php echo $form->getName(); ?>" method="<?php echo $form->method(); ?>" action="<?php echo $form->getAction(); ?>"<?php if($form->getExtra()!=''): echo " ".$form->getExtra(); endif;?>>

<div class="form_container">
<?php
    $elements = $form->elements();

    foreach ($elements as $data):
        //print_r($data);
        $field =& $data['field'];

        if ($data['class']=='RMFormHidden'):
            echo $field->render();
        elseif($data['class']=='RMFormSubTitle'):
?>
<h3><?php echo $field->render(); ?></h3>
<?php if($field->getDescription()!=''): ?><span class="help-block"><?php echo $field->getDescription(); ?></span><?php endif; ?>

<?php
        else:
?>
<div id="row_<?php echo $field->getName(); ?>" class="form-group">
	<label class="col-md-2 col-md-offset-1 control-label">
		<?php if($field->getCaption()!=''): ?><label for="<?php echo $field->getName(); ?>"><?php echo $field->getCaption(); ?></label><?php else: ?>&nbsp;<?php endif; ?>
	</label>
	<div class="col-md-8">
		<?php echo $field->render(); ?>
                <?php
                    switch (get_class($field)) {
                        case 'RMFormButtonGroup':
                        case 'RMFormButton':
                        case 'RMFormHidden':
                            break;
                        default: ?>
                        <label for="<?php echo $field->getName(); ?>" class="error hidden"><?php _e('This is a required field. Please fill it!','rmcommon'); ?></label>
            <?php
                    }
                ?>
        <?php if ( $field->getDescription() != '' ): ?><span class="help-block"><?php echo $field->getDescription(); ?></span><?php endif; ?>
    </div>
</div>
<?php
        endif;
    endforeach;
?>
</div>
</form>
