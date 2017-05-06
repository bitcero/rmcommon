(function($){

    $("body").on('click', 'button[data-repeater="button"]', function(){

        var $parent = $(this).parents("[data-adv-field='repeater']");
        var $items = $parent.find('[data-repeater="repeater"] > [data-repeater="item"]');
        var $container = $parent.find('[data-repeater="container"]');
        var $fields = $parent.find('[data-repeater="container"] > [data-repeater="row"]');

        if($items.length <= 0){
            cuHandler.notify({
                text: advLang.noRepeatItems,
                icon: 'svg-rmcommon-alert',
                type: 'alert-warning'
            });
            return false;
        }

        var name = $parent.data('name');

        var idNumber = 0;
        var found = true;

        while(found){

            if($parent.find(".repeater-row[data-id='" + idNumber + "']").length <= 0){
                found = false;
            } else {
                idNumber++;
            }

        }


        var $row = $('<div data-repeater="row" class="repeater-row" data-id="' + idNumber + '" style="display: none;">');

        $items.each(function(){
            var field = $(this).clone();
            field.html(field.html().replace(/x-repeat/g, name));
            field.html(field.html().replace(/repeat-num/g, idNumber));
            var width = undefined != $(this).data('size') ? $(this).data('size') : 0;
            $row.append('<div data-repeater="field" class="repeater-field"' + (width > 0 ? ' style="width: ' + width + '%;"' : '') + '>' + field.html() + '</div>');
        });

        // Color for controls
        var color = $parent.data('color');
        color = undefined == 'color' ? 'default' : color;

        var placeholder = $parent.data('placeholder') != undefined ? $parent.data('placeholder') : advLang.item;

        var controls = '<div class="repeater-field-controls ' + color + '">' +
            '<span class="caption">' + (placeholder.replace('%u', idNumber + 1)) + '</span>' +
            '<button class="open" type="button" title="' + advLang.open + '"></button>' +
            '<button class="delete" type="button" title="' + advLang.delete + '"></button>' +
            '</div>';
        $row.append(controls);

        cuHandler.loadIcon('svg-rmcommon-trash', $row.find('button.delete'));
        cuHandler.loadIcon('svg-rmcommon-double-arrow-up', $row.find('button.open'));

        $container.append($row);
        $row.slideDown(250);

        if($container.find('[data-type="countries-' + idNumber + '"]').length > 0){
            $('[data-type="countries-' + idNumber + '"]').chosen();
        }

        if($container.find('[data-type="color-' + idNumber + '"]').length > 0){
            setColorPicker($('[data-type="color-' + idNumber + '"] span.chooser'));
        }

        if($container.find('[data-type="states-' + idNumber + '"]').length > 0){
            $('[data-type="states-' + idNumber + '"]').chosen();
        }

    });

    $('body').on('click', '.repeater-field-controls .open', function(){
        $(this).parents('.repeater-row').toggleClass('collapsed');
    });

    $('body').on('click', '.repeater-field-controls .delete', function(){
        if(confirm(advLang.confirmDeletion)){
            $(this).parents('.repeater-row').remove();
        }
    });

}(jQuery));