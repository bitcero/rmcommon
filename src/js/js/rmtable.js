/*
 * Dynamic Table from Red México (designed for SIA)
 * Copyright: Eduardo Cortés <yo@eduardocortes.mx>
 * URL: http://www.eduardocortes.mx
 * Version: 1.0
 */

(function(factory) {
    if (typeof define == 'function' && define.amd) {
        // AMD. Register as an anonymous module
        define(['jquery'], factory);
    }
    else if (typeof exports == 'object') {
        // Node/CommonJS
        module.exports = factory(require('jquery'));
    }
    else {
        // Browser globals
        factory(jQuery);
    }
})(function ($) {

    function DynamicTable(element, options) {

        this.defaults = {
            url: '',
            secure: true,
            page: 1
        };

        this.settings = options;
        this.table = $(element);
        this.columns = $(element).find('thead > tr >  th').length;


        var plugin = this;

        // Overwite URL option if data value is present
        if($(plugin.table).data('url') != undefined){
            plugin.settings.url = $(plugin.table).data('url');
        }

    }

    DynamicTable.prototype = {

        init: function(){

            // Prepare table
            this.table.addClass('dynamic-table');

            var sorters = $(this.table).find("thead th[data-sort]");
            $(sorters).each(function(){
                var caption = $(this).html();
                $(this).html('<button type="button">' + caption + '</button>');
            });

            var _this = this;

            var sorters = $(sorters).find("> button");

            sorters.bind('click', function(){

                _this.resortColumn(_this, $(this));

            });

            // Load data
            var params = {
                CUTOKEN_REQUEST: $("#cu-token").val(),
                page: this.settings.page
            };
            this.render({});
        },

        resortColumn: function(_this, sorter){

            if(undefined == sorter){
                return false;
            }

            if(undefined == $(sorter).parent().data('sort')){
                return false;
            }

            var sort = $(sorter).data('direction');

            sort = undefined == sort ? 'ASC' : sort.toUpperCase();

            if(undefined == sort || 'DESC' == sort){
                sort = 'ASC';
            } else {
                sort = 'DESC';
            }

            var params = {
                orderBy: $(sorter).parent().data('sort'),
                sort: sort
            };

            _this.render(params);

        },

        render: function(params){

            params.CUTOKEN_REQUEST = $("#cu-token").val();
            cuHandler.showLoader();

            var plugin = this;

            $.get(this.settings.url, params, function(response){

                if(!cuHandler.retrieveAjax(response)){
                    return false;
                }

                if(response.tableData == undefined){
                    return false;
                }

                plugin.clear();

                var i = 0;
                for(i = 0; i<response.tableData.length; i++){
                    plugin.addRow(response.tableData[i], undefined == response.cellSettings ? '' : response.cellSettings);
                }

                plugin.table.data('order', response.pagination.order);
                plugin.table.data('sort', response.pagination.sort);
                plugin.setOrderedColumn(plugin, response.pagination.order, response.pagination.sort);

            }, 'json');

        },

        setOrderedColumn: function(_this, orderBy, sort){

            if(undefined == orderBy){
                return false;
            }

            $(_this.table).find('thead th[data-sort] > button').removeClass('dtable-sorted');
            $(_this.table).find('thead th[data-sort="' + orderBy + '"] > button')
                .addClass('dtable-sorted')
                .attr('data-direction', sort.toLowerCase())
                .data('direction', sort.toLowerCase());

        },

        addRow: function(data, attrs){

            var keys = [];
            for (var key in data){
                if(data.hasOwnProperty(key)){
                    keys.push(key);
                }
            }

            var tr = $('<tr/>');

            for(var i = 0; i < this.columns; i++){
                var td = $('<td/>').append(data[keys[i]]);
                applyAttributes(td, attrs[i])
                $(tr).append(td);
            }

            $(this.table).append(tr);

        },

        clear: function(){
            $(this.table).find("tbody > tr").remove();
        }

    }

    function applyAttributes(element, attrs){

        for(var key in attrs){
            if(attrs.hasOwnProperty(key)){
                $(element).attr(key, attrs[key]);
            }
        }

    }

    function Plugin(option, _relatedTarget){
        return this.each(function(){
            var $this = $(this);
            var data = $this.data('dyn.table');
            var options = $.extend({}, DynamicTable.defaults, $this.data(), typeof option == 'object' && option);

            if(!data) $this.data('dyn.table', (data = new DynamicTable($this, options)))
            if(typeof option == 'string'){
                data[option](_relatedTarget);
            } else {
                data.init();
            }
        });
    }

    $.fn.dynamicTable = Plugin;
    $.fn.dynamicTable.Constructor = DynamicTable;

    return $.fn.dynamicTable;

});

(function($){
    $("table[data-dtable='dynamic']").dynamicTable();
}(jQuery));