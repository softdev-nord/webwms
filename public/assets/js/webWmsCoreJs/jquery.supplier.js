(function($){
    // StockLocation table
    const supplierTable = $('#supplierTable').DataTable({
        "lengthChange": false,
        ajax: {
            'url': '/supplier_ajax',
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
            {"data": "supplier_nr"},
            {"data": "supplier_name"},
            {"data": "supplier_address_addition"},
            {"data": "supplier_address_street"},
            {"data": "supplier_address_street_nr"},
            {"data": "supplier_address_country_code"},
            {"data": "supplier_address_zipcode"},
            {"data": "supplier_address_city"},
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
        const supplierNr = supplierTable.row(row).data().supplier_nr;
        window.location.href = '/lieferanten_bearbeiten/LieferantenNr/' + supplierNr;
    });

    // Save edit stock location
    $(document).on('click','button#edit_supplier_save',function(event) {
        const supplierNr = $('#edit_supplier_supplier_nr').val();
        const $form = $('form[name="edit_supplier"]');
        event.preventDefault();

        $.ajax({ // Process the form using $.ajax()
            type        : 'POST',
            url         : `{{ path("edit_supplier",{'supplier_nr' : 'supplier_nr' }) }}`.replace('supplier_nr', supplierNr),
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
                        'title': 'Lieferant konnte nicht gespeichert werden',
                        'content': error,
                        'theme': 'red',
                        'size': 'md',
                        'showAnimation': 'fadeInUp',
                        'hideAnimation': 'fadeOutDown',
                        'autoClose': 5000
                    });
                } else {
                    $.jAlert({
                        'title': 'Lieferant erfolgreich gespeichert',
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
    $(document).on('click','#edit_supplier_back_to_supplier_overview',function() {
        window.location.href = '/lieferanten'
    });
})(jQuery);
