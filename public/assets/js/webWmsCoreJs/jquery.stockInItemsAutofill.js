const stockInItemsAutofill = (function(){
    function getNumOfBoxArt(type) {
        let numOfBoxArt;
        switch (type) {
            case 'article_nr':
                numOfBoxArt = 1;
                break;
            default:
                break;
        }
        return numOfBoxArt;
    }

    function autocompleteHandleArt() {
        let type, numOfBoxArt;
        type = $(this).data('type');
        numOfBoxArt = getNumOfBoxArt(type);

        if (typeof numOfBoxArt === 'undefined') {
            return false;
        }

        $(this).autocomplete({
            source: function (data, cb) {
                $.ajax({
                    url: '/article_order_ajax',
                    method: 'GET',
                    dataType: 'json',
                    data: {
                        name_art: data.term,
                        numOfBoxArt: numOfBoxArt
                    },
                    success: function (res) {
                        let result;
                        result = [
                            {
                                label: 'Keine Ergebnisse für ' + data.term,
                                value: ''
                            }
                        ];

                        if (res.length) {
                            result = $.map(res, function (obj) {
                                const arr = obj.split("|");
                                return {
                                    label: arr[numOfBoxArt],
                                    value: arr[numOfBoxArt],
                                    data: obj
                                };
                            });
                        }
                        cb(result);
                    }
                });
            },
            autoFocus: true,
            minLength: 1,
            select: function (event, ui) {
                let resArr;

                resArr = ui.item.data.split("|");

                $('#stock_in_le_quantity').val(resArr[11]);
                $('#article_nr_right').val(resArr[1]);
                $('#article_name_right').val(resArr[2]);
                $('#article_le_quantity_right').val(resArr[11]);
                $('#article_unit_right').val(resArr[6]);
                $('#standard_loading_equipment_right').val(resArr[12]);
            }
        });
    }

    // Events registrieren
    function registerEventsArt() {
        $(document).on('focus', '.autocomplete_items', autocompleteHandleArt);
    }

    // Events initiieren
    function init() {
        registerEventsArt();
    }

    return {
        init: init
    };
})();

$(document).ready(function(){
    stockInItemsAutofill.init();

    /*$('#stock_in_post_final').on( 'click', function (e) {
        e.preventDefault();
        const form = $(this).closest('form');
        const formData = form.serialize();
        alert("save button clicked");
        $.ajax({
            method:'POST',
            url:'/stock_in_final',
            data: formData,
            success: function(data){
                console.log(formData);
            }
        });
    });*/
});