const stockInItemsAutofill = (function(){
    function getNumOfBoxArt(type) {
        let numOfBoxArt;
        switch (type) {
            case 'articleNr':
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
                                const arr = obj.split('|');
                                return {
                                    label: arr[numOfBoxArt],
                                    value: arr[numOfBoxArt],
                                    data: obj,
                                    itemDetail: obj.split('|', 3)
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

                resArr = ui.item.data.split('|');

                console.log(resArr);

                $('#stock_in_articleId').val(resArr[0]);
                $('#stock_in_leQuantity').val(resArr[12]);
                $('#article_nr_right').val(resArr[1]);
                $('#article_name_right').val(resArr[2]);
                $('#article_le_quantity_right').val(resArr[12]);
                $('#article_unit_right').val(resArr[6]);
                $('#standard_loading_equipment_right').val(resArr[11]);
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

(function($){
    stockInItemsAutofill.init();

    $('#stock_in_post_final').on( 'click', function (e) {
        e.preventDefault();
        const form = $(this).closest('form'),
            errorMessage = 'Der Transportauftrag konnte nicht erstellt werden.',
            successMessage = 'Der Transportauftrag wurde erfolgreich erstellt.';
        $.ajax({
            type: 'POST',
            url: '/stock_in_final',
            data: form.serialize(),
            success: function(data) {
                console.log(data);
                if (data.error) {
                    const errors = [];
                    let i = 0;
                    $.each(data.error, function(key, value) {
                        errors[i++] = value + '</br>';
                    });
                    const arrayString = errors.join();
                    const error = arrayString.replace(/,/g, ' ');
                    Swal.fire({
                        title: errorMessage,
                        text: error,
                        icon: "error",
                        timer: 5000
                    });
                } else {
                    Swal.fire({
                        title: successMessage,
                        text: data.message,
                        icon: "success",
                        timer: 5000
                    });
                    $('#modalCenter').modal('hide');
                    redirectTo();
                }
            }
        });
    });

    function redirectTo() {
        setTimeout(function(){
            window.location.assign("/stock_in")
        },4000)
    }

    // // Neuen Artikel speichern
    // $(document).on('click', 'button#stock_in_post_final', function(event) {
    //     const $form = $('form#article-form-new'),
    //         url = '/artikel_anlegen',
    //         errorMessage = 'Artikel konnte nicht gespeichert werden',
    //         successMessage = 'Artikel erfolgreich gespeichert';
    //     event.preventDefault();
    //
    //     _doRequest('POST', url, $form, errorMessage, successMessage);
    // });
    //
})(jQuery);
