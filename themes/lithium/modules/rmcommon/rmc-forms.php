<form <?php echo $attributes; ?>>

    <div class="cu-box">
      <?php if ($form->has('title')): ?>
          <div class="box-header">
              <h3 class="box-title">
                <?php echo $form->get('title'); ?>
              </h3>
          </div>
      <?php endif; ?>
        <div class="box-content">
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
                  <?php if ('' != $field->getDescription()): ?><span
                        class="help-block"><?php echo $field->getDescription(); ?></span><?php endif; ?>

                <?php
                else:
                  ?>

                  <?php
                  $to_float = ['rmformtext', 'rmformselect', 'rmformtimezonefield'];
                  $floating = in_array(strtolower($data['class']), $to_float);
                  ?>
                    <div id="row_<?php echo $field->getName(); ?>"
                         class="mb-3 <?php echo $floating ? 'form-floating' : ''; ?>">
                      <?php echo $floating ? $field->render() : ''; ?>
                      <?php if ('' != $field->getCaption()): ?>
                          <label for="<?php echo $field->getName(); ?>" class="form-label">
                            <?php echo $field->getCaption(); ?>
                          </label>
                      <?php endif; ?>
                      <?php echo $floating ? '' : $field->render(); ?>

                      <?php
                      switch (get_class($field)) {
                        case 'RMFormButtonGroup':
                        case 'RMFormButton':
                        case 'RMFormHidden':
                          break;
                        default: ?>
                            <label for="<?php echo $field->getName(); ?>"
                                   class="error hidden"><?php _e('This is a required field. Please fill it!', 'rmcommon'); ?></label>
                        <?php
                      }
                      ?>
                        <div class="form-text">

                          <?php if ('' != $field->getDescription()): ?><span
                                  class="help-block"><?php echo $field->getDescription(); ?></span><?php endif; ?>
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
