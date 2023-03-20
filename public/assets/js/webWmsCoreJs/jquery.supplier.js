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
                action: function (e, dt, node, config) {
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
        $('#modalCenter .modal-dialog').css('max-width', '90%');
    });

    $.ajaxSetup({
        cache: false
    });

    // Modal für Lieferanten anlegen
    function addSupplier() {
        const url = '/lieferant_anlegen';
        const content = '<div class="modal-body"></div>';

        $('#modalCenter .modal-title').text('Lieferanten anlegen');
        $('#modal-content-ajax').html(content);
        $('#modalCenter').modal('show');

        $.ajax({
            url: url,
            type: 'GET',
            data: ($('#supplier-form-new').serialize()),
            error: function (xhr, ajaxOptions, thrownError) {
                alert(xhr.status);
            },
            success: function (data) {
                $('#modal-content-ajax').html(data);
            }
        });

        return false;
    }

    // Modal für Lieferanten bearbeiten
    function editSupplier(supplierId) {
        const url = '/lieferant_bearbeiten/supplierId/' + supplierId;
        const content = '<div class="modal-body"></div>';

        $('#modalCenter .modal-title').text('Lieferanten bearbeiten');
        $('#modal-content-ajax').html(content);
        $('#modalCenter').modal('show');

        $.ajax({
            url: url,
            type: 'GET',
            data: ($('#supplier-form-edit').serialize()),
            error: function (xhr, ajaxOptions, thrownError) {
                alert(xhr.status);
            },
            success: function (data) {
                $('#modal-content-ajax').html(data);
            }
        });

        return false;
    }

    // Modal für Lieferanten löschen
    function deleteSupplier(supplierId) {
        const url = '/lieferant_löschen/supplierId/' + supplierId;
        const content = '<div class="modal-body"></div>';

        $('#modalCenter .modal-dialog').css('max-width', '30%');
        $('#modalCenter .modal-title').text('Lieferanten löschen');
        $('#modal-content-ajax').html(content);
        $('#modalCenter').modal('show');

        $.ajax({
            url: url,
            type: 'GET',
            error: function (xhr, ajaxOptions, thrownError) {
                alert(xhr.status);
            },
            success: function (data) {
                $('#modal-content-ajax').html(data);
            }
        });

        return false;
    }

    // Neuen Lieferanten speichern
    $(document).on('click','button#add_supplier_save',function(event) {
        const $form = $('form#supplier-form-new');
        const url = '/lieferant_anlegen';
        event.preventDefault();

        $.ajax({
            type: 'POST',
            url: url,
            data: $form.serialize(),
            success: function(data) {
                if (data.error) {
                    const errors = [];
                    let i = 0;
                    $.each(data.error, function(key, value) {
                        errors[i++] = value + '</br>';
                    });
                    const arrayString = errors.join();
                    const error = arrayString.replace(/,/g, ' ');
                    $.jAlert({
                        'title': 'Lieferantendaten konnten nicht gespeichert werden',
                        'content': error,
                        'theme': 'red',
                        'size': 'md',
                        'showAnimation': 'fadeInUp',
                        'hideAnimation': 'fadeOutDown',
                        'autoClose': 5000
                    });
                } else {
                    $.jAlert({
                        'title': 'Lieferantendaten erfolgreich gespeichert',
                        'content': data.message,
                        'theme': 'green',
                        'size': 'md',
                        'showAnimation': 'fadeInUp',
                        'hideAnimation': 'fadeOutDown',
                        'autoClose': 5000
                    });
                    $('#modalCenter').modal('hide');
                    supplierTable.ajax.reload();
                }
            }
        });
    });

    // Geänderten Lieferanten speichern
    $(document).on('click','button#edit_supplier_save',function(event) {
        const supplierId = $('#edit_supplier_supplierId').val();
        const $form = $('form#supplier-form-edit');
        const url = '/lieferant_bearbeiten/supplierId/' + supplierId;
        event.preventDefault();

        $.ajax({
            type: 'POST',
            url: url,
            data: $form.serialize(),
            success: function(data) {
                if (data.error) {
                    const errors = [];
                    let i = 0;
                    $.each(data.error, function(key, value) {
                        errors[i++] = value + '</br>';
                    });
                    const arrayString = errors.join();
                    const error = arrayString.replace(/,/g, ' ');
                    $.jAlert({
                        'title': 'Lieferantendaten konnten nicht gespeichert werden',
                        'content': error,
                        'theme': 'red',
                        'size': 'md',
                        'showAnimation': 'fadeInUp',
                        'hideAnimation': 'fadeOutDown',
                        'autoClose': 5000
                    });
                } else {
                    $.jAlert({
                        'title': 'Lieferantendaten erfolgreich gespeichert',
                        'content': data.message,
                        'theme': 'green',
                        'size': 'md',
                        'showAnimation': 'fadeInUp',
                        'hideAnimation': 'fadeOutDown',
                        'autoClose': 5000
                    });
                    $('#modalCenter').modal('hide');
                    supplierTable.ajax.reload();
                }
            }
        });
    });

    // Lieferanten löschen
    $(document).on('click','button#delete_supplier_delete',function(event) {
        const supplierId = $('#delete_supplier_supplierId').val();
        const $form = $('form#supplier-modal-delete-ask');
        const url = '/lieferant_löschen/supplierId/' + supplierId;
        event.preventDefault();

        $.ajax({
            type: 'POST',
            url: url,
            data: $form.serialize(),
            success: function(data) {
                if (data.error) {
                    const errors = [];
                    let i = 0;
                    $.each(data.error, function(key, value) {
                        errors[i++] = value + '</br>';
                    });
                    const arrayString = errors.join();
                    const error = arrayString.replace(/,/g, ' ');
                    $.jAlert({
                        'title': 'Lieferant konnten nicht gelöscht werden',
                        'content': error,
                        'theme': 'red',
                        'size': 'md',
                        'showAnimation': 'fadeInUp',
                        'hideAnimation': 'fadeOutDown',
                        'autoClose': 5000
                    });
                } else {
                    $.jAlert({
                        'title': 'Lieferant erfolgreich gelöscht',
                        'content': data.message,
                        'theme': 'green',
                        'size': 'md',
                        'showAnimation': 'fadeInUp',
                        'hideAnimation': 'fadeOutDown',
                        'autoClose': 5000
                    });
                    $('#modalCenter').modal('hide');
                    supplierTable.ajax.reload();
                }
            }
        });
    });

    $(document).on('click','.abort',function() {
        $('#modalCenter').modal('hide');
    });

})(jQuery);
