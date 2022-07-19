(function($){
    // StockLocation table
    const stockLocationTable = $('#stockLocationTable').DataTable({
        "lengthChange": false,
        ajax: {
            'url': '/stock_location_ajax',
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
            {"data": "stock_location_id"},
            {"data": "stock_location_ln"},
            {"data": "stock_location_fb"},
            {"data": "stock_location_sp"},
            {"data": "stock_location_tf"},
            {"data": "stock_location_coordinate"},
            {"data": "stock_location_desc"},
            {"data": "stock_location_width"},
            {"data": "stock_location_depth"},
            {"data": "stock_location_height"},
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
        const stockLocationCoordinate = stockLocationTable.row(row).data().stock_location_coordinate;
        window.location.href = '/lagerplatz_bearbeiten/koordinate/' + stockLocationCoordinate;
    });

    // Save edit stock location
    $(document).on('click','button#edit_stock_location_save',function(event) {
        const stockLocationCoordinate = $('#edit_stock_location_stock_location_coordinate').val();
        const $form = $('form[name="edit_stock_location"]');
        event.preventDefault();

        $.ajax({ // Process the form using $.ajax()
            type        : 'POST',
            url         : `{{ path("edit_stock_location",{'stock_location_coordinate' : 'stock_location_coordinate' }) }}`.replace('stock_location_coordinate', stockLocationCoordinate),
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
                        'title': 'Lagerplatz konnte nicht gespeichert werden',
                        'content': error,
                        'theme': 'red',
                        'size': 'md',
                        'showAnimation': 'fadeInUp',
                        'hideAnimation': 'fadeOutDown',
                        'autoClose': 5000
                    });
                } else {
                    $.jAlert({
                        'title': 'Lagerplatz erfolgreich gespeichert',
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
    $(document).on('click','#edit_stock_location_back_to_stock_location_overview',function() {
        window.location.href = '/lagerplatz'
    });
})(jQuery);
