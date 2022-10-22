(function($){
    // Article table
    const artTable = $('#artTable').DataTable({
        "lengthChange": false,
        paging: false,
        retrieve: true,
        //searching: false,

        ajax: {
            'url': '/article_ajax',
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
            {"data": "article_nr"},
            {"data": "article_name"},
            {"data": "article_category"},
            {"data": "article_weight"},
            {"data": "article_ean"},
            {"data": "article_unit"},
            {"data": "article_depth"},
            {"data": "article_width"},
            {"data": "article_height"},
            {
                "data": "lbw_menge",
                "defaultContent": 0
            },
            {
                data: null,
                className: "editor-edit text-center",
                defaultContent: '<i class="mdi mdi-square-edit-outline"/>',
                orderable: false,
            }
        ],
        columnDefs: [
            {
                className: 'text-center', targets: "_all"
            },
            {
                render: $.fn.dataTable.render.number( '.'),
                "targets": [9],
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

    // Get selected article
    $(document).on('click','i.mdi-square-edit-outline',function(event) {
        const row = $(this).parents('tr')[0];
        const articleNr = artTable.row(row).data().article_nr;
        window.location.href = '/artikel_bearbeiten/articleNr/' + articleNr;
    });

    // Save edit article
    $(document).on('click','button#edit_article_save',function(event) {
        const articleNr = $('#edit_article_article_nr').val();
        const $form = $('form[name="edit_article"]');
        event.preventDefault();

        $.ajax({ // Process the form using $.ajax()
            type        : 'POST',
            url         : `{{ path("edit_article",{'article_nr' : 'article_nr' }) }}`.replace('article_nr', articleNr),
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
                        'title': 'Artikel konnte nicht gespeichert werden',
                        'content': error,
                        'theme': 'red',
                        'size': 'md',
                        'showAnimation': 'fadeInUp',
                        'hideAnimation': 'fadeOutDown',
                        'autoClose': 5000
                    });
                } else {
                    $.jAlert({
                        'title': 'Artikel erfolgreich gespeichert',
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

    // Back to article overview
    $(document).on('click','#edit_article_back_to_article_overview',function() {
        window.location.href = '/artikel'
    });
})(jQuery);
