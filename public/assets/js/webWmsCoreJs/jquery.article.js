;(function($){
    // Artikel Tabelle
    const artTable = $('#artTable').DataTable({
        lengthChange: false,
        paging: false,
        retrieve: true,

        ajax: {
            'url': '/article_ajax',
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
            {'data': 'article_nr'},
            {'data': 'article_name'},
            {'data': 'article_category'},
            {'data': 'article_weight'},
            {'data': 'article_ean'},
            {'data': 'article_unit'},
            {'data': 'article_depth'},
            {'data': 'article_width'},
            {'data': 'article_height'},
            {
                'data': 'lbw_menge',
                'defaultContent': 0
            },
            {
                "data": null,
                render: function (data, type, row) {
                    if (row.updated_at != null) {
                        return row.updated_at;
                    } else {
                        return row.created_at;
                    }
                },
            }
        ],
        columnDefs: [
            {
                className: 'text-center', targets: '_all'
            },
            {
                render: $.fn.dataTable.render.number('.'),
                'targets': [9],
            },
        ],
        dom: 'Bfrtip',
        buttons: [
            {
                extend:    'copyHtml5',
                text:      'Kopieren',
                title:     'Export',
            },
            {
                extend:    'csvHtml5',
                text:      'CSV',
                title:     'Export',
            },
            {
                extend:    'pdfHtml5',
                text:      'PDF',
                title:     'Export',
            },
            {
                extend: 'print',
                text: 'Drucken',
                autoPrint: false
            },
            {
                text: 'Artikel anlegen',
                className: 'btn-add-new',
                action: function (e, dt, node, config) {
                    addArticle();
                }
            }
        ],
    });

    $.contextMenu({
        selector: 'tr',
        trigger: 'right',
        callback: function(key, options, event) {
            const row = artTable.row(options.$trigger),
                articleId = row.data().article_id;

            switch (key) {
                case 'edit' :
                    editArticle(articleId);
                    break;
                case 'delete' :
                    deleteArticle(articleId);
                    break;
                default :
                    break;
            }
        },
        items: {
            'edit': {name: 'Bearbeiten', icon: 'edit'},
            'delete': {name: 'Löschen', icon: 'delete'},
        }
    });

    $(function(){
        // Ändern der Standardbreite des Modals
        $("#modalCenter .modal-dialog").css('max-width', '90%');
    });

    $.ajaxSetup({
        cache: false
    });

    // Modal für Artikel anlegen
    function addArticle() {
        const url = '/artikel_anlegen';
        const content = '<div class="modal-body"></div>';

        $('#modalCenter .modal-dialog').css('max-width', '90%');
        $('#modalCenter .modal-title').text('Artikel anlegen');
        $('#modal-content-ajax').html(content);
        $('#modalCenter').modal('show');

        $.ajax({
            url: url,
            type: 'get',
            data: ($('#article-form-new').serialize()),
            error: function (xhr, ajaxOptions, thrownError) {
                alert(xhr.status);
            },
            success: function (data) {
                $('#modal-content-ajax').html(data);
            }
        });

        return false;
    }

    // Modal für Artikel bearbeiten
    function editArticle(id) {
        const url = '/artikel_bearbeiten/articleId/' + id;
        const content = '<div class="modal-body"></div>';

        $('#modalCenter .modal-title').text('Artikel bearbeiten');
        $('#modal-content-ajax').html(content);
        $('#modalCenter').modal('show');

        $.ajax({
            url: url,
            type: 'get',
            data: ($('#article-form-edit').serialize()),
            error: function (xhr, ajaxOptions, thrownError) {
                alert(xhr.status);
            },
            success: function (data) {
                $("#modal-content-ajax").html(data);
            }
        });

        return false;
    }

    // Modal für Artikel löschen
    function deleteArticle(articleId) {
        console.log(articleId);
        const url = '/artikel_löschen/articleId/' + articleId;
        const content = '<div class="modal-body"></div>';

        $('#modalCenter .modal-dialog').css('max-width', '30%');
        $('#modalCenter .modal-title').text('Artikel löschen');
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

    // Neuen Artikel speichern
    $(document).on('click','button#add_article_save',function(event) {
        const $form = $('form#article-form-new');
        const url = '/artikel_anlegen';
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
                        'title': 'Artikeldaten konnten nicht gespeichert werden',
                        'content': error,
                        'theme': 'red',
                        'size': 'md',
                        'showAnimation': 'fadeInUp',
                        'hideAnimation': 'fadeOutDown',
                        'autoClose': 5000
                    });
                } else {
                    $.jAlert({
                        'title': 'Artikeldaten erfolgreich gespeichert',
                        'content': data.message,
                        'theme': 'green',
                        'size': 'md',
                        'showAnimation': 'fadeInUp',
                        'hideAnimation': 'fadeOutDown',
                        'autoClose': 5000
                    });
                    $('#modalCenter').modal('hide');
                    artTable.ajax.reload();
                }
            }
        });
    });

    // Geänderten Artikel speichern
    $(document).on('click','button#edit_article_save',function(event) {
        const articleId = $('#edit_article_articleId').val();
        const $form = $('form#article-form-edit');
        const url = '/artikel_bearbeiten/articleId/' + articleId;
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
                    const error = arrayString.replace(/,/g, " ");
                    $.jAlert({
                        'title': 'Artikeldaten konnten nicht gespeichert werden',
                        'content': error,
                        'theme': 'red',
                        'size': 'md',
                        'showAnimation': 'fadeInUp',
                        'hideAnimation': 'fadeOutDown',
                        'autoClose': 5000
                    });
                } else {
                    $.jAlert({
                        'title': 'Artikeldaten erfolgreich gespeichert',
                        'content': data.message,
                        'theme': 'green',
                        'size': 'md',
                        'showAnimation': 'fadeInUp',
                        'hideAnimation': 'fadeOutDown',
                        'autoClose': 5000
                    });
                    $('#modalCenter').modal('hide');
                    artTable.ajax.reload();
                }
            }
        });
    });

    // Artikel löschen
    $(document).on('click','button#delete_article_delete',function(event) {
        const articleId = $('#delete_article_articleId').val();
        const $form = $('form#article-modal-delete-ask');
        const url = '/artikel_löschen/articleId/' + articleId;
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
                        'title': 'Artikel konnten nicht gelöscht werden',
                        'content': error,
                        'theme': 'red',
                        'size': 'md',
                        'showAnimation': 'fadeInUp',
                        'hideAnimation': 'fadeOutDown',
                        'autoClose': 5000
                    });
                } else {
                    $.jAlert({
                        'title': 'Artikel erfolgreich gelöscht',
                        'content': data.message,
                        'theme': 'green',
                        'size': 'md',
                        'showAnimation': 'fadeInUp',
                        'hideAnimation': 'fadeOutDown',
                        'autoClose': 5000
                    });
                    $('#modalCenter').modal('hide');
                    artTable.ajax.reload();
                }
            }
        });
    });

    // Zurück zur Artikelübersicht
    $(document).on('click','#edit_article_back_to_article_overview',function() {
        $('#modalCenter').modal('hide');
    });
    $(document).on('click','#add_article_back_to_article_overview',function() {
        $('#modalCenter').modal('hide');
    });
    $(document).on('click','button#delete_article_abort',function() {
        $('#modalCenter').modal('hide');
    });
})(jQuery);
