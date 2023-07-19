(function($){
    // Lieferanten Tabelle
    const supplierTable = $('#supplierTable').DataTable({
        searchPanes: {
            cascadePanes: true,
            viewTotal: true
        },
        lengthChange: false,

        ajax: {
            url: '/supplier_ajax',
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
            { data: 'supplierNr' },
            { data: 'supplierName' },
            { data: 'supplierAddressAddition' },
            { data: 'supplierAddressStreet' },
            { data: 'supplierAddressStreetNr' },
            { data: 'supplierAddressCountryCode' },
            { data: 'supplierAddressZipcode' },
            { data: 'supplierAddressCity' },
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
                    className: 'btn-primary btn-xs btn3d'
                },
                {
                    extend:    'csvHtml5',
                    text:      'CSV',
                    title:     'Export',
                    titleAttr: 'CSV',
                    className: 'btn-primary btn-xs btn3d'
                },
                {
                    extend:    'pdfHtml5',
                    text:      'PDF',
                    title:     'Export',
                    titleAttr: 'PDF',
                    className: 'btn-primary btn-xs btn3d'
                },
                {
                    extend: 'print',
                    text: 'Drucken',
                    autoPrint: false,
                    className: 'btn-primary btn-xs btn3d'
                },
                {
                    text: 'Lieferant anlegen',
                    className: 'btn-primary btn-xs btn3d float-start',
                    action: function(e, dt, node, config) {
                        addSupplier();
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
            const row = supplierTable.row(options.$trigger),
                supplierId = row.data().supplierId;

            switch (key) {
                case 'edit' :
                    editSupplier(supplierId);
                    break;
                case 'delete' :
                    deleteSupplier(supplierId);
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

    $(function(){
        // Ändern der Standardbreite des Modals
        $('#modalCenter .modal-dialog').css('max-width', '98%');
    });

    $.ajaxSetup({
        cache: false
    });

    // Modal für Lieferanten anlegen
    function addSupplier() {
        const url = '/lieferant_anlegen',
            $form = $('form#supplier-form-new'),
            title = 'Lieferanten anlegen';

        getContentForModal(url, title, $form);
    }

    // Modal für Lieferanten bearbeiten
    function editSupplier(supplierId) {
        const url = '/lieferant_bearbeiten/supplierId/' + supplierId,
            $form = $('form#supplier-form-edit'),
            title = 'Lieferanten bearbeiten';

        getContentForModal(url, title, $form);
    }

    // Modal für Lieferanten löschen
    function deleteSupplier(supplierId) {
        const url = '/lieferant_löschen/supplierId/' + supplierId,
            $form = $('form#supplier-modal-delete-ask'),
            title = 'Lieferanten löschen';

        $('#modalCenter .modal-dialog').css('max-width', '30%');

        getContentForModal(url, title, $form);
    }

    // Neuen Lieferanten speichern
    $(document).on('click', 'button#add_supplier_save', function(event) {
        const $form = $('form#supplier-form-new'),
            url = '/lieferant_anlegen',
            errorMessage = 'Lieferant konnte nicht gespeichert werden',
            successMessage = 'Lieferant erfolgreich gespeichert';
        event.preventDefault();

        _doRequest('POST', url, $form, errorMessage, successMessage, supplierTable);
    });

    // Geänderten Lieferanten speichern
    $(document).on('click', 'button#edit_supplier_save', function(event) {
        const supplierId = $('#edit_supplier_supplierId').val();
        const $form = $('form#supplier-form-edit'),
            url = '/lieferant_bearbeiten/supplierId/' + supplierId,
            errorMessage = 'Lieferant konnte nicht gespeichert werden',
            successMessage = 'Lieferant erfolgreich gespeichert';
        event.preventDefault();

        _doRequest('POST', url, $form, errorMessage, successMessage, supplierTable);
    });

    // Lieferanten löschen
    $(document).on('click', 'button#delete_supplier_delete', function(event) {
        const supplierId = $('#delete_supplier_supplierId').val();
        const $form = $('form#supplier-modal-delete-ask'),
            url = '/lieferant_löschen/supplierId/' + supplierId,
            errorMessage = 'Lieferant konnte nicht gelöscht werden',
            successMessage = 'Lieferant erfolgreich gelöscht';
        event.preventDefault();

        _doRequest('POST', url, $form, errorMessage, successMessage, supplierTable);
    });

    $(document).on('click', '.abort', function() {
        $('#modalCenter').modal('hide');
    });

})(jQuery);
