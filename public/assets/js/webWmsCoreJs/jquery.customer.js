(function($){
    // StockLocation table
    const customerTable = $('#customerTable').DataTable({
        "lengthChange": false,
        ajax: {
            'url': '/customer_ajax',
            'dataSrc': ''
        },
        // Page length max. 10 entries
        pageLength: 10,
        "language": {
            "url": "./resources/dataTable.German.json"
        },
        // Initialisation of the DataTables Select extension
        select: {
            style: 'single'
        },
        columns: [
            {"data": "customer_nr"},
            {"data": "customer_name"},
            {"data": "customer_address_addition"},
            {"data": "customer_address_street"},
            {"data": "customer_address_street_nr"},
            {"data": "customer_country_code"},
            {"data": "customer_zip_code"},
            {"data": "customer_city"},
            {
                data: null,
                className: "editor-edit text-center",
                defaultContent: '<i class="mdi mdi-square-edit-outline"/>',
                orderable: false,
            },
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
            }
        ]
    });

    // Get selected stock location
    $(document).on('click','i.mdi-square-edit-outline',function(event) {
        const row = $(this).parents('tr')[0];
        const customerNr = customerTable.row(row).data().customer_nr;
        window.location.href = '/kunden_bearbeiten/kundenNr/' + customerNr;
    });

    // Save edit stock location
    $(document).on('click','button#edit_customer_save',function(event) {
        const customerNr = $('#edit_customer_customer_nr').val();
        const $form = $('form[name="edit_customer"]');
        event.preventDefault();

        $.ajax({ // Process the form using $.ajax()
            type        : 'POST',
            url         : `{{ path("edit_customer",{'customer_nr' : 'customer_nr' }) }}`.replace('customer_nr', customerNr),
            data        : $form.serialize(),
            success     : function(data) {
                if (data.error) {
                    let errors = [];
                    let i = 0;
                    $.each(data.error, function(key, value) {
                        errors[i++] = value + '</br>';
                    });
                    let arrayString = errors.join();
                    const error = arrayString.replace(/,/g, " ");
                    $.jAlert({
                        'title': 'Kunde konnte nicht gespeichert werden',
                        'content': error,
                        'theme': 'red',
                        'size': 'md',
                        'showAnimation': 'fadeInUp',
                        'hideAnimation': 'fadeOutDown',
                        'autoClose': 5000
                    });
                } else {
                    $.jAlert({
                        'title': 'Kunde erfolgreich gespeichert',
                        'content': data.message,
                        'theme': 'green',
                        'size': 'md',
                        'showAnimation': 'fadeInUp',
                        'hideAnimation': 'fadeOutDown',
                        'autoClose': 5000
                    });
                }
            }
        });

    });

    // Back to stock location overview
    $(document).on('click','#edit_customer_back_to_customer_overview',function() {
        window.location.href = '/kunden'
    });
})(jQuery);
