const jqueryCustomersAutofill = (function () {
    function getNumOfBoxCustomer(type) {
        let numOfBoxCustomer;
        switch (type) {
            case 'customer_nr':
                numOfBoxCustomer = 0;
                break;
            case 'customer_name':
                numOfBoxCustomer = 1;
                break;
            case 'customer_address_addition':
                numOfBoxCustomer = 2;
                break;
            case 'customer_address_street':
                numOfBoxCustomer = 3;
                break;
            case 'customer_address_street_nr':
                numOfBoxCustomer = 4;
                break;
            case 'customer_country_code':
                numOfBoxCustomer = 5;
                break;
            case 'customer_zip_code':
                numOfBoxCustomer = 6;
                break;
            case 'customer_city':
                numOfBoxCustomer = 7;
                break;
            case 'customer_id':
                numOfBoxCustomer = 8;
                break;
            default:
                break;
        }
        return numOfBoxCustomer;
    }

    // Handling der eingegebenen Daten
    function autocompleteHandleKd() {
        let type, numOfBoxCustomer;
        type = $(this).data('type');
        numOfBoxCustomer = getNumOfBoxCustomer(type);

        if (typeof numOfBoxCustomer === 'undefined') {
            return false;
        }

        $(this).autocomplete({
            source: function (data, cb) {
                $.ajax({
                    url: '/order_customer_ajax',
                    method: 'GET',
                    data: {
                        name_customer: data.term,
                        numOfBoxCustomer: numOfBoxCustomer
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
                                    label: arr[numOfBoxCustomer],
                                    value: arr[numOfBoxCustomer],
                                    data: obj
                                };
                            });
                        }
                        cb(result.slice(0, 5));
                    }
                });
            },
            autoFocus: true,
            minLength: 1,
            select: function (event, ui) {
                let resArrCustomer;
                //Splittung des resArrKd Arrays
                resArrCustomer = ui.item.data.split("|");

                $('#customer_nr_1').val(resArrCustomer[0]);
                $('#customer_name_1').val(resArrCustomer[1]);
                $('#customer_address_addition_1').val(resArrCustomer[2]);
                $('#customer_address_street_1').val(resArrCustomer[3]);
                $('#customer_address_street_nr_1').val(resArrCustomer[4]);
                $('#customer_country_code_1').val(resArrCustomer[5]);
                $('#customer_zip_code_1').val(resArrCustomer[6]);
                $('#customer_city_1').val(resArrCustomer[7]);
                $('#customer_order_customer_id').val(resArrCustomer[8]);
            }
        });
    }

    // Funktion Events registrieren
    function registerEventKd() {
        //Registrierung der Autocomplete events
        $(document).on('focus', '.autocomplete_customers', autocompleteHandleKd);
    }

    // Events iniziieren
    function init() {
        registerEventKd();
    }

    return {
        init: init
    };
})();

$(document).ready(function(){
    jqueryCustomersAutofill.init();
});
