const suppliersAutofill = (function () {
//
    function getNumOfBoxSupplier(type) {
        let numOfBoxSupplier;
        switch (type) {
            case 'supplierNr':
                numOfBoxSupplier = 0;
                break;
            case 'supplierName':
                numOfBoxSupplier = 1;
                break;
            case 'supplierAddressAddition':
                numOfBoxSupplier = 2;
                break;
            case 'supplierAddressStreet':
                numOfBoxSupplier = 3;
                break;
            case 'supplierAddressStreetNr':
                numOfBoxSupplier = 4;
                break;
            case 'supplierAddressCountryCode':
                numOfBoxSupplier = 5;
                break;
            case 'supplierAddressZipcode':
                numOfBoxSupplier = 6;
                break;
            case 'supplierAddressCity':
                numOfBoxSupplier = 7;
                break;
            case 'supplierId':
                numOfBoxSupplier = 8;
                break;
            default:
                break;
        }
        return numOfBoxSupplier;
    }

    function autocompleteHandleSupplier() {
        let type, numOfBoxSupplier;
        type = $(this).data('type');
        numOfBoxSupplier = getNumOfBoxSupplier(type);

        if (typeof numOfBoxSupplier === 'undefined') {
            return false;
        }

        $(this).autocomplete({
            source: function (data, cb) {
                $.ajax({
                    url: '/order_supplier_ajax',
                    method: 'GET',
                    data: {
                        name_supplier: data.term,
                        numOfBoxSupplier: numOfBoxSupplier
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
                                    label: arr[numOfBoxSupplier],
                                    value: arr[numOfBoxSupplier],
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
                let resArraySupplier;
                //Splittung des resArraySupplier Arrays
                resArraySupplier = ui.item.data.split(' | ');

                $('#supplier_supplierNr').val(resArraySupplier[0]);
                $('#supplier_supplierName').val(resArraySupplier[1]);
                $('#supplier_supplierAddressAddition').val(resArraySupplier[2]);
                $('#supplier_supplierAddressStreet').val(resArraySupplier[3]);
                $('#supplier_supplierAddressStreetNr').val(resArraySupplier[4]);
                $('#supplier_supplierAddressCountryCode').val(resArraySupplier[5]);
                $('#supplier_supplierAddressZipcode').val(resArraySupplier[6]);
                $('#supplier_supplierAddressCity').val(resArraySupplier[7]);
                $('#supplier_supplierId').val(resArraySupplier[8]);
            }
        });
    }

    // Events registrieren
    function registerEventSupplier() {
        // register autocomplete events
        $(document).on('focus', '.autocomplete_suppliers', autocompleteHandleSupplier);
    }

    // Events iniziieren
    function init() {
        registerEventSupplier();
    }

    return {
        init: init
    };
})();

$(document).ready(function(){
    suppliersAutofill.init();
});
