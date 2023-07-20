const jqueryCustomersAutofill = (function () {
    function getNumOfBoxCustomer(type) {
        let numOfBoxCustomer;
        switch (type) {
            case 'customerNr':
                numOfBoxCustomer = 0;
                break;
            case 'customerName':
                numOfBoxCustomer = 1;
                break;
            case 'customerAddressAddition':
                numOfBoxCustomer = 2;
                break;
            case 'customerAddressStreet':
                numOfBoxCustomer = 3;
                break;
            case 'customerAddressStreetNr':
                numOfBoxCustomer = 4;
                break;
            case 'customerCountryCode':
                numOfBoxCustomer = 5;
                break;
            case 'customerZipCode':
                numOfBoxCustomer = 6;
                break;
            case 'customerCity':
                numOfBoxCustomer = 7;
                break;
            case 'customerId':
                numOfBoxCustomer = 8;
                break;
            default:
                break;
        }
        return numOfBoxCustomer;
    }

    // Handling der eingegebenen Daten
    function autocompleteHandleCustomer() {
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
                                const arr = obj.split(' | ');
                                return {
                                    label: arr[numOfBoxCustomer],
                                    value: arr[numOfBoxCustomer],
                                    data: obj,
                                    itemDetail: obj.split(' | ', 2)
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

                $('#customer_customerNr').val(resArrCustomer[0]);
                $('#customer_customerName').val(resArrCustomer[1]);
                $('#customer_customerAddressAddition').val(resArrCustomer[2]);
                $('#customer_customerAddressStreet').val(resArrCustomer[3]);
                $('#customer_customerAddressStreetNr').val(resArrCustomer[4]);
                $('#customer_customerAddressCountryCode').val(resArrCustomer[5]);
                $('#customer_customerAddressZipcode').val(resArrCustomer[6]);
                $('#customer_customerAddressCity').val(resArrCustomer[7]);
                $('#customer_customerId').val(resArrCustomer[8]);
            }
        });
    }

    // Funktion Events registrieren
    function registerEventCustomer() {
        //Registrierung der Autocomplete events
        $(document).on('focus', '.autocomplete_customers', autocompleteHandleCustomer);
    }

    // Events iniziieren
    function init() {
        registerEventCustomer();
    }

    return {
        init: init
    };
})();

$(document).ready(function(){
    jqueryCustomersAutofill.init();
});
