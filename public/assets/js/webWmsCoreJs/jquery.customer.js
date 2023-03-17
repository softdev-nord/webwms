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
            { data: 'customer_nr' },
            { data: 'customer_name' },
            { data: 'customer_address_addition' },
            { data: 'customer_address_street' },
            { data: 'customer_address_street_nr' },
            { data: 'customer_country_code' },
            { data: 'customer_zip_code' },
            { data: 'customer_city' }
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
                text: 'Kunde anlegen',
                className: 'btn-add-new',
                action: function (e, dt, node, config) {
                    addCustomer();
                }
            }
        ]
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

    $(function(){
        // Ändern der Standardbreite des Modals
        $('#modalCenter .modal-dialog').css('max-width', '90%');
    });

    $.ajaxSetup({
        cache: false
    });

    // Modal für Kunden anlegen
    function addCustomer() {
        const url = '/kunden_anlegen';
        const content = '<div class="modal-body"></div>';

        $('#modalCenter .modal-title').text('Kunden anlegen');
        $('#modal-content-ajax').html(content);
        $('#modalCenter').modal('show');

        $.ajax({
            url: url,
            type: 'GET',
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

    // Modal für Kunden bearbeiten
    function editCustomer(customerId) {
        const url = 'kunden_bearbeiten/customerId/' + customerId;
        const content = '<div class="modal-body"></div>';

        $('#modalCenter .modal-title').text('Kunden bearbeiten');
        $('#modal-content-ajax').html(content);
        $('#modalCenter').modal('show');

        $.ajax({
            url: url,
            type: 'GET',
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

    // Modal für Kunden löschen
    function deleteCustomer(articleId) {
        const url = '/kunden_löschen/customerId/' + articleId;
        const content = '<div class="modal-body"></div>';

        $('#modalCenter .modal-dialog').css('max-width', '30%');
        $('#modalCenter .modal-title').text('Kunden löschen');
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
                    const errors = [];
                    let i = 0;
                    $.each(data.error, function(key, value) {
                        errors[i++] = value + '</br>';
                    });
                    const arrayString = errors.join();
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

    // Geänderten Kunden speichern
    $(document).on('click','button#edit_customer_save',function(event) {
        const customer_id = $('#edit_customer_customerId').val();
        const $form = $('form#customer-form-edit');
        const url = '/kunden_bearbeiten/customerId/' + customer_id;
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

    // Artikel löschen
    $(document).on('click','button#delete_customer_delete',function(event) {
        const customerId = $('#delete_customer_customerId').val();
        const $form = $('form#customer-modal-delete-ask');
        const url = '/kunden_löschen/customerId/' + customerId;
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
                        'title': 'Kunde konnte nicht gelöscht werden',
                        'content': error,
                        'theme': 'red',
                        'size': 'md',
                        'showAnimation': 'fadeInUp',
                        'hideAnimation': 'fadeOutDown',
                        'autoClose': 5000
                    });
                } else {
                    $.jAlert({
                        'title': 'Kunde erfolgreich gelöscht',
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

    $(document).on('click','.abort',function() {
        $('#modalCenter').modal('hide');
    });

})(jQuery);
