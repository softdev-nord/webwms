(function($){
    // Kunden Tabelle
    const customerTable = $('#customerTable').DataTable({
        lengthChange: false,

        ajax: {
            url: '/customer_ajax',
            dataSrc: ''
        },
        // Seitenlänge max. 10 Einträge
        pageLength: 10,
        language: {
            url: './resources/dataTable.German.json'
        },
        // Initialisierung der DataTables Select-Erweiterung
        select: {
            style: 'single'
        },
        columns: [
            { data: 'customerNr' },
            { data: 'customerName' },
            { data: 'customerAddressAddition' },
            { data: 'customerAddressStreet' },
            { data: 'customerAddressStreetNr' },
            { data: 'customerCountryCode' },
            { data: 'customerZipCode' },
            { data: 'customerCity' }
        ],
        columnDefs: [
            {
                className: 'text-center',
                targets: '_all'
            },
        ],
        dom: 'Bfrtip',
        buttons: {
            buttons: [
                {
                    extend:    'copyHtml5',
                    text:      'Kopieren',
                    title:     'Export',
                    titleAttr: 'Copy',
                    className: 'btn btn-primary btn-xm btn3d'
                },
                {
                    extend:    'csvHtml5',
                    text:      'CSV',
                    title:     'Export',
                    titleAttr: 'CSV',
                    className: 'btn btn-primary btn-xm btn3d'
                },
                {
                    text:       'JSON',
                    title:      'Export',
                    action: function (e, dt, button, config) {
                        DataTable.fileSave(
                            new Blob(
                                [
                                    JSON.stringify(convertCustomerOverviewToJson())
                                ]
                            ),
                            'export_customer_overview.json'
                        );
                    },
                    className: 'btn btn-primary btn-xm btn3d'
                },
                {
                    extend:    'pdfHtml5',
                    text:      'PDF',
                    title:     'Export',
                    titleAttr: 'PDF',
                    className: 'btn btn-primary btn-xm btn3d'
                },
                {
                    extend: 'print',
                    text: 'Drucken',
                    autoPrint: false,
                    className: 'btn btn-primary btn-xm btn3d'
                },
                {
                    text: 'Kunde anlegen',
                    className: 'btn btn-primary btn-xm btn3d float-start',
                    action: function(e, dt, node, config) {
                        addCustomer();
                    }
                }
            ],
            dom: {
                button: {
                    className: 'btn'
                },
                buttonLiner: {
                    tag: null
                }
            }
        }
    });

    $.contextMenu({
        selector: 'tr',
        trigger: 'right',
        callback: function(key, options, event) {
            const row = customerTable.row(options.$trigger),
                articleId = row.data().customer_id;

            switch (key) {
                case 'edit' :
                    editCustomer(articleId);
                    break;
                case 'delete' :
                    deleteCustomer(articleId);
                    break;
                default :
                    break;
            }
        },
        items: {
            edit: {
                name: 'Bearbeiten',
                icon: 'edit'
            },
            delete: {
                name: 'Löschen',
                icon: 'delete'
            },
        }
    });

    // Kunden als Json exportieren
    function convertCustomerOverviewToJson() {
        const objects = [];
        const data = customerTable.rows().data();

        for (let i = 0; i < data.length; i++) {
            objects.push(data[i]);
        }

        return objects;
    }

    $(function(){
        // Ändern der Standardbreite des Modals
        $('#modalCenter .modal-dialog').css('max-width', '98%');
    });

    $.ajaxSetup({
        cache: false
    });

    // Modal für Kunden anlegen
    function addCustomer() {
        const url = '/kunden_anlegen',
            $form = $('form#customer-form-new'),
            title = 'Kunden anlegen';

        getContentForModal(url, title, $form);
    }

    // Modal für Kunden bearbeiten
    function editCustomer(customerId) {
        const url = 'kunden_bearbeiten/customerId/' + customerId,
            $form = $('form#customer-form-edit'),
            title = 'Kunden bearbeiten';

        getContentForModal(url, title, $form);
    }

    // Modal für Kunden löschen
    function deleteCustomer(customerId) {
        const url = '/kunden_löschen/customerId/' + customerId,
            $form = $('form#customer-modal-delete-ask'),
            title = 'Kunden löschen';

        $('#modalCenter .modal-dialog').css('max-width', '30%');

        getContentForModal(url, title, $form);
    }

    // Neuen Kunden speichern
    $(document).on('click', 'button#add_customer_save', function(event) {
        const $form = $('form#customer-form-new'),
            url = '/kunden_anlegen',
            errorMessage = 'Kunde konnte nicht gespeichert werden',
            successMessage = 'Kunde erfolgreich gespeichert';
        event.preventDefault();

        _doRequest('POST', url, $form, errorMessage, successMessage, customerTable, false);
    });

    // Geänderten Kunden speichern
    $(document).on('click', 'button#edit_customer_save', function(event) {
        const customer_id = $('#edit_customer_customerId').val(),
            $form = $('form#customer-form-edit'),
            url = '/kunden_bearbeiten/customerId/' + customer_id,
            errorMessage = 'Kunde konnte nicht gespeichert werden',
            successMessage = 'Kunde erfolgreich gespeichert';
        event.preventDefault();

        _doRequest('POST', url, $form, errorMessage, successMessage, customerTable, false);
    });

    // Kunden löschen
    $(document).on('click', 'button#delete_customer_delete', function (event) {
        const customerId = $('#delete_customer_customerId').val(),
            $form = $('form#customer-modal-delete-ask'),
            url = '/kunden_löschen/customerId/' + customerId,
            errorMessage = 'Kunde konnte nicht gelöscht werden',
            successMessage = 'Kunde erfolgreich gelöscht';
        event.preventDefault();

        _doRequest('POST', url, $form, errorMessage, successMessage, customerTable, false);
    });

    $(document).on('click', '.abort', function() {
        $('#modalCenter').modal('hide');
    });

})(jQuery);
