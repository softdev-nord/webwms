(function($){
    // Lieferanten Tabelle
    const supplierTable = $('#supplierTable').DataTable({
        "lengthChange": false,
        ajax: {
            'url': '/supplier_ajax',
            'dataSrc': ''
        },
        // Seitenlänge max. 10 Einträge
        pageLength: 10,
        "language": {
            "url": "./resources/dataTable.German.json"
        },
        // Initialisierung der DataTables Select-Erweiterung
        select: {
            style: 'single'
        },
        columns: [
            {"data": "supplier_nr"},
            {"data": "supplier_name"},
            {"data": "supplier_address_addition"},
            {"data": "supplier_address_street"},
            {"data": "supplier_address_street_nr"},
            {"data": "supplier_address_country_code"},
            {"data": "supplier_address_zipcode"},
            {"data": "supplier_address_city"},
        ],
        columnDefs: [
            {className: 'text-center', targets: "_all"},
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
                action: function ( e, dt, node, config ) {
                    addSupplier();
                }
            }
        ]
    });

    $.contextMenu({
        selector: 'tr',
        trigger: 'right',
        callback: function(key, options, event) {
            const row = supplierTable.row(options.$trigger);

            switch (key) {
                case 'edit' :
                    editSupplier(row.data().supplier_nr);
                    break;
                case 'delete' :
                    deleteSupplier(row.data().supplier_nr);
                    break;
                default :
                    break
            }
        },
        items: {
            'edit': {name: 'Bearbeiten', icon: 'edit'},
            'delete': {name: 'Löschen', icon: 'delete'}
        }
    });

    $(function(){
        // Ändern der Standardbreite des Modals
        $('#modalCenter .modal-dialog').css('max-width', '90%');
    });

    $.ajaxSetup({
        cache: false
    });

    function editSupplier(supplierNr) {
        const url = '/lieferant_bearbeiten/lieferantenNr/' + supplierNr;
        const content = '<div class="modal-body"></div>';

        $('#modalCenter .modal-title').text('Lieferanten bearbeiten');
        $('#modal-content-ajax').html(content);
        $('#modalCenter').modal('show');

        $.ajax({
            url: url,
            type: 'get',
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

    function addSupplier() {
        const url = '/lieferant_anlegen';
        const content = '<div class="modal-body"></div>';

        $('#modalCenter .modal-title').text('Lieferanten anlegen');
        $('#modal-content-ajax').html(content);
        $('#modalCenter').modal('show');

        $.ajax({
            url: url,
            type: 'get',
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

    function deleteSupplier(supplierNr) {
        const url = '/lieferant_löschen/lieferantenNr/' + supplierNr;
        const content = '<div class="modal-body"></div>';

        $('#modalCenter .modal-dialog').css('max-width', '30%');
        $('#modalCenter .modal-title').text('Lieferanten löschen');
        $('#modal-content-ajax').html(content);
        $('#modalCenter').modal('show');

        $.ajax({
            url: url,
            type: 'get',
            error: function (xhr, ajaxOptions, thrownError) {
                alert(xhr.status);
            },
            success: function (data) {
                $('#modal-content-ajax').html(data);
            }
        });

        return false;
    }

    // Geänderten Lieferanten speichern
    $(document).on('click','button#edit_supplier_save',function(event) {
        const supplierNr = $('#edit_supplier_supplierNr').val();
        const $form = $('form#supplier-form-edit');
        const url = '/lieferant_bearbeiten/lieferantenNr/' + supplierNr;
        event.preventDefault();

        $.ajax({
            type: 'POST',
            url: url,
            data: $form.serialize(),
            success: function(data) {
                if (data.error) {
                    let errors = [];
                    let i = 0;
                    $.each(data.error, function(key, value) {
                        errors[i++] = value + '</br>';
                    });
                    let arrayString = errors.join();
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
                    let errors = [];
                    let i = 0;
                    $.each(data.error, function(key, value) {
                        errors[i++] = value + '</br>';
                    });
                    let arrayString = errors.join();
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
        const supplierNr = $('#delete_supplier_supplierNr').val();
        const $form = $('form#supplier-modal-delete-ask');
        const url = '/lieferant_löschen/lieferantenNr/' + supplierNr;
        event.preventDefault();

        $.ajax({
            type: 'POST',
            url: url,
            data: $form.serialize(),
            success: function(data) {
                if (data.error) {
                    let errors = [];
                    let i = 0;
                    $.each(data.error, function(key, value) {
                        errors[i++] = value + '</br>';
                    });
                    let arrayString = errors.join();
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

    // Back to stock location overview
    $(document).on('click','#edit_supplier_back_to_supplier_overview',function() {
        window.location.href = '/lieferanten'
    });
    $(document).on('click','button#delete_supplier_abort',function() {
        $('#modalCenter').modal('hide');
    });
})(jQuery);
