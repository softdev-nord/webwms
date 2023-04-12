;(function($){
    // Artikel Tabelle
    const artTable = $('#artTable').DataTable({
        lengthChange: false,
        paging: false,
        retrieve: true,

        ajax: {
            url: '/article_ajax',
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
            { data: 'article_nr' },
            { data: 'article_name' },
            { data: 'article_category' },
            { data: 'article_weight' },
            { data: 'article_ean' },
            { data: 'article_unit' },
            { data: 'article_depth' },
            { data: 'article_width' },
            { data: 'article_height' },
            {
                data: 'lbw_menge',
                defaultContent: 0
            },
            {
                data: null,
                render: function(data, type, row) {
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
                className: 'text-center',
                targets: '_all'
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
                action: function(e, dt, node, config) {
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

    // Modal für Artikel anlegen
    function addArticle() {
        const url = '/artikel_anlegen',
            $form = $('form#article-form-new'),
            title = 'Artikel anlegen';

        getContentForModal(url, title, $form);
    }

    // Modal für Artikel bearbeiten
    function editArticle(id) {
        const url = '/artikel_bearbeiten/articleId/' + id,
            $form = $('form#article-form-edit'),
            title = 'Artikel bearbeiten';

        getContentForModal(url, title, $form);
    }

    // Modal für Artikel löschen
    function deleteArticle(articleId) {
        const url = '/artikel_löschen/articleId/' + articleId,
            $form = $('form#article-modal-delete-ask'),
            title = 'Artikel löschen';

        $('#modalCenter .modal-dialog').css('max-width', '30%');

        getContentForModal(url, title, $form);
    }

    // Neuen Artikel speichern
    $(document).on('click', 'button#add_article_save', function(event) {
        const $form = $('form#article-form-new'),
            url = '/artikel_anlegen',
            errorMessage = 'Artikel konnte nicht gespeichert werden',
            successMessage = 'Artikel erfolgreich gespeichert';
        event.preventDefault();

        _doRequest('POST', url, $form, errorMessage, successMessage, artTable);
    });

    // Geänderten Artikel speichern
    $(document).on('click', 'button#edit_article_save', function(event) {
        const articleId = $('#edit_article_articleId').val(),
            $form = $('form#article-form-edit'),
            url = '/artikel_bearbeiten/articleId/' + articleId,
            errorMessage = 'Artikel konnte nicht gespeichert werden',
            successMessage = 'Artikel erfolgreich gespeichert';
        event.preventDefault();

        _doRequest('POST', url, $form, errorMessage, successMessage, artTable);
    });

    // Artikel löschen
    $(document).on('click', 'button#delete_article_delete', function(event) {
        const articleId = $('#delete_article_articleId').val(),
            $form = $('form#article-modal-delete-ask'),
            url = '/artikel_löschen/articleId/' + articleId,
            errorMessage = 'Artikel konnte nicht gelöscht werden',
            successMessage = 'Artikel erfolgreich gelöscht';
        event.preventDefault();

        _doRequest('POST', url, $form, errorMessage, successMessage, artTable);
    });

    $(document).on('click', '.abort', function() {
        $('#modalCenter').modal('hide');
    });

})(jQuery);
