$(document).ready(function(){

    var <?php echo $name; ?>DropZone = new Dropzone("#<?php echo $id; ?>", {
        paramName: '<?php echo $name; ?>',
    <?php foreach($parameters as $key => $value): ?>
        <?php echo $key; ?>: '<?php echo str_replace('\'', '\\\'', $value); ?>',
    <?php endforeach; ?>
    });
});