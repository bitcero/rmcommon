<?php if ($form->getTitle()!=''): ?><h1 class="cu-section-title"><?php echo $form->getTitle(); ?></h1><?php endif; ?>

<form name="<?php echo $form->getName() ?>" id="<?php echo $form->getName(); ?>" method="<?php echo $form->method(); ?>" action="<?php echo $form->getAction(); ?>"<?php if($form->getExtra()!=''): echo " ".$form->getExtra(); endif;?>>

<div class="form_container">
<table class="table_form" cellspacing="0">
<?php
	$elements = $form->elements();

	foreach ($elements as $data):
		//print_r($data);
		$field =& $data['field'];
		
		if ($data['class']=='RMFormHidden'):
			echo $field->render();
		elseif($data['class']=='RMFormSubTitle'):
?>
<tr>
	<td colspan="2"><?php echo $field->render(); ?>
</tr>
	<?php if($field->getDescription()!=''): ?><tr class="cell_fields"><td colspan="2"><span class="descriptions"><?php echo $field->getDescription(); ?></span></td></tr><?php endif; ?>

<?php
		else:
			$field->addClass("inputForm");
?>
<tr id="row_<?php echo $field->getName(); ?>" class="cell_fields">
	<td width="30%" class="form_captions">
		<?php if($field->getCaption()!=''): ?><label for="<?php echo $field->getName(); ?>"><?php echo $field->getCaption(); ?></label><?php else: ?>&nbsp;<?php endif; ?>
		<?php if($field->getDescription()!=''): ?><span class="descriptions"><?php echo $field->getDescription(); ?></span><?php endif; ?>
	</td>
	<td>
		<?php echo $field->render(); ?>
                <?php
                    switch(get_class($field)){
                        case 'RMFormButtonGroup':
                        case 'RMFormButton':
                        case 'RMFormHidden':
                            break;
                        default: ?>
                        <label for="<?php echo $field->getName(); ?>" class="error hidden"><?php _e('This is a required field. Please fill it!','rmcommon'); ?></label></td>
            <?php
                    }
                ?>
		
</tr>
<?php
		endif;
	endforeach;
?>
</table>
</div>
</form>