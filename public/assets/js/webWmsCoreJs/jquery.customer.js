(function($){
    // Kunden Tabelle
    const customerTable = $('#customerTable').DataTable({
        lengthChange: false,
        ajax: {
            'url': '/customer_ajax',
            'dataSrc': ''
        },
        // Seitenlänge max. 10 Einträge
        pageLength: 10,
        'language': {
            'url': './resources/dataTable.German.json'
        },
        // Initialisierung der DataTables Select-Erweiterung
        select: {
            style: 'single'
        },
        columns: [
            {'data': 'customer_nr'},
            {'data': 'customer_name'},
            {'data': 'customer_address_addition'},
            {'data': 'customer_address_street'},
            {'data': 'customer_address_street_nr'},
            {'data': 'customer_country_code'},
            {'data': 'customer_zip_code'},
            {'data': 'customer_city'}
        ],
        columnDefs: [
            {className: 'text-center', targets: '_all'},
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
                text: 'Kunde anlegen',
                className: 'btn-add-new',
                action: function ( e, dt, node, config ) {
                    addCustomer();
                }
            }
        ]
    });

    $.contextMenu({
        selector: 'tr',
        trigger: 'right',
        callback: function(key, options, event) {
            const row = customerTable.row(options.$trigger);

            switch (key) {
                case 'edit' :
                    editCustomer(row.data().customer_nr);
                    break;
                default :
                    break
            }
        },
        items: {
            'edit': {name: 'Bearbeiten', icon: 'edit'}
        }
    });

    $(function(){
        // Ändern der Standardbreite des Modals
        $('#modalCenter .modal-dialog').css('max-width', '90%');
    });

    $.ajaxSetup({
        cache: false
    });

    function editCustomer(customerNr) {
        const url = 'kunden_bearbeiten/kundenNr/' + customerNr;
        const content = '<div class="modal-body"></div>';

        $('#modalCenter .modal-title').text('Kunden bearbeiten');
        $('#modal-content-ajax').html(content);
        $('#modalCenter').modal('show');

        $.ajax({
            url: url,
            type: 'get',
            data: ($('#customer-form-edit').serialize()),
            error: function (xhr, ajaxOptions, thrownError) {
                alert(xhr.status);
            },
            success: function (data) {
                $('#modal-content-ajax').html(data);
            }
        });

        return false;
    }

    function addCustomer() {
        const url = '/kunden_anlegen';
        const content = '<div class="modal-body"></div>';

        $('#modalCenter .modal-title').text('Kunden anlegen');
        $('#modal-content-ajax').html(content);
        $('#modalCenter').modal('show');

        $.ajax({
            url: url,
            type: 'get',
            data: ($('#customer-form-new').serialize()),
            error: function (xhr, ajaxOptions, thrownError) {
                alert(xhr.status);
            },
            success: function (data) {
                $('#modal-content-ajax').html(data);
            }
        });

        return false;
    }

    // Geänderten Kunden speichern
    $(document).on('click','button#edit_customer_save',function(event) {
        const customerNr = $('#edit_customer_customerNr').val();
        const $form = $('form#customer-form-edit');
        const url = '/kunden_bearbeiten/kundenNr/' + customerNr;
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
                        'title': 'Kundendaten konnten nicht gespeichert werden',
                        'content': error,
                        'theme': 'red',
                        'size': 'md',
                        'showAnimation': 'fadeInUp',
                        'hideAnimation': 'fadeOutDown',
                        'autoClose': 5000
                    });
                } else {
                    $.jAlert({
                        'title': 'Kundendaten erfolgreich gespeichert',
                        'content': data.message,
                        'theme': 'green',
                        'size': 'md',
                        'showAnimation': 'fadeInUp',
                        'hideAnimation': 'fadeOutDown',
                        'autoClose': 5000
                    });
                    $('#modalCenter').modal('hide');
                    customerTable.ajax.reload();
                }
            }
        });
    });

    // Neuen Kunden speichern
    $(document).on('click','button#add_customer_save',function(event) {
        const $form = $('form#customer-form-new');
        const url = '/kunden_anlegen';
        event.preventDefault();

        console.log($form.serialize());

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
                        'title': 'Kundendaten konnten nicht gespeichert werden',
                        'content': error,
                        'theme': 'red',
                        'size': 'md',
                        'showAnimation': 'fadeInUp',
                        'hideAnimation': 'fadeOutDown',
                        'autoClose': 5000
                    });
                } else {
                    $.jAlert({
                        'title': 'Kundendaten erfolgreich gespeichert',
                        'content': data.message,
                        'theme': 'green',
                        'size': 'md',
                        'showAnimation': 'fadeInUp',
                        'hideAnimation': 'fadeOutDown',
                        'autoClose': 5000
                    });
                    $('#modalCenter').modal('hide');
                    customerTable.ajax.reload();
                }
            }
        });
    });

    // Zurück zur Kundenübersicht
    $(document).on('click','#edit_customer_back_to_customer_overview',function() {
        window.location.href = '/kunden'
    });
})(jQuery);
