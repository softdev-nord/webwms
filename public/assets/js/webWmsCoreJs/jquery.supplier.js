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
            { data: 'supplier_nr' },
            { data: 'supplier_name' },
            { data: 'supplier_address_addition' },
            { data: 'supplier_address_street' },
            { data: 'supplier_address_street_nr' },
            { data: 'supplier_address_country_code' },
            { data: 'supplier_address_zipcode' },
            { data: 'supplier_address_city' },
        ],
        columnDefs: [
            {
                className: 'text-center',
                targets: '_all'
            },
        ],
        dom: 'Bfrtip',
        buttons: [
            {
                extend:    'copyHtml5',
                text:      'Kopieren',
                title:     'Export',
                titleAttr: 'Copy'
            },
            {
                extend:    'excelHtml5',
                text:      'Excel',
                title:     'Export',
                titleAttr: 'Excel'
            },
            {
                extend:    'csvHtml5',
                text:      'CSV',
                title:     'Export',
                titleAttr: 'CSV'
            },
            {
                extend:    'pdfHtml5',
                text:      'PDF',
                title:     'Export',
                titleAttr: 'PDF'
            },
            {
                extend: 'print',
                text: 'Drucken',
                autoPrint: false
            },
            {
                text: 'Lieferant anlegen',
                className: 'btn-add-new',
                action: function(e, dt, node, config) {
                    addSupplier();
                }
            }
        ]
    });

    $.contextMenu({
        selector: 'tr',
        trigger: 'right',
        callback: function(key, options, event) {
            const row = supplierTable.row(options.$trigger),
                supplierId = row.data().supplier_id;

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
