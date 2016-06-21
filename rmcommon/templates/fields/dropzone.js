Dropzone.options.imagesUploader = {
    url: '<?php echo RMCURL; ?>/include/upload.php',
    acceptedFiles: '.png, .jpg, .gif, .jpeg, .svg',
    maxFileSize: <?php echo ($cat->getVar('filesize') * $cat->getVar('sizeunit')) / 1000000; ?>,
    parallelUpload: 1,
    autoProcessQueue: true,
    dictDefaultMessage: cuLanguage.dzDefault,

    init: function(){
        this.on('success', function(first, second){
            alert(first);
            alert(second);
        });
    }
};