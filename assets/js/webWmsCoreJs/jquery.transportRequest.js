;(function($){
    // Transportauftrag Tabelle
    const transportRequestTable = $('#transportRequestTable').DataTable({
        lengthChange: false,
        paging: false,
        retrieve: true,
        ordering: false,

        ajax: {
            url: '/transport_request_ajax',
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
            { data: 'su_id' },
            { data: 'tr_nr' },
            { data: 'tr_pos' },
            { data: 'tr_prio' },
            { data: 'article_nr' },
            {
                data: null,
                render: function(data, type, row) {
                    if (row.tr_type === 1) {
                        return 'Zugang';
                    } else {
                        return 'Abgang';
                    }
                },
            },
            {
                data: null,
                render: function(data, type, row) {
                    if (row.tr_type === 1) {
                        return ' +'+ row.tr_quantity;
                    } else {
                        return ' -'+ row.tr_quantity;
                    }
                },
            },
            { data: 'stock_coordinate' },
            { data: 'stock_location' },
            { data: 'tr_state' },
            { data: 'order_username' },
            { data: 'booking_method' },
            { data: 'order_nr' },
            { data: 'loading_equipment' },
            { data: 'tr_username' },
            { data: 'tr_computer_ip' },
            {
                data: null,
                render: function(data, type, row) {
                    if (row.tr_blocked !== true) {
                        return 'Nein';
                    } else {
                        return 'Ja';
                    }
                },
            },
            { data: 'tr_start_date' },
            {
                data: null,
                render: function(data, type, row) {
                    if (row.tr_edited !== true) {
                        return 'Nein';
                    } else {
                        return 'Ja';
                    }
                },
            },
        ],
        columnDefs: [
            {
                className: 'text-center',
                targets: '_all'
            },
            {
                render: $.fn.dataTable.render.number('.'),
                'targets': [6],
            },
        ],
        dom: 'Bfrtip',
        buttons: {
            buttons: [
                {
                    extend:    'copyHtml5',
                    text:      'Kopieren',
                    title:     'Export',
                    titleAttr: 'Copy',
                    className: 'btn btn-primary btn-xm btn3d'
                },
                {
                    extend:    'csvHtml5',
                    text:      'CSV',
                    title:     'Export',
                    titleAttr: 'CSV',
                    className: 'btn btn-primary btn-xm btn3d'
                },
                {
                    text:       'JSON',
                    title:      'Export',
                    action: function (e, dt, button, config) {
                        console.log(dt.row().data());
                        DataTable.fileSave(
                            new Blob([JSON.stringify(convertTransportRequestTableToJson())]),
                            'export_transport_request_overview.json'
                        );
                    },
                    className: 'btn btn-primary btn-xm btn3d'
                },
                {
                    extend:    'pdfHtml5',
                    text:      'PDF',
                    title:     'Export',
                    titleAttr: 'PDF',
                    className: 'btn btn-primary btn-xm btn3d'
                },
                {
                    extend: 'print',
                    text: 'Drucken',
                    autoPrint: false,
                    className: 'btn btn-primary btn-xm btn3d'
                }
            ]
        }
    });

    $.contextMenu({
        selector: 'tr',
        trigger: 'right',
        callback: function(key, options, event) {
            const row = transportRequestTable.row(options.$trigger),
                id = row.data().id;

            switch (key) {
                case 'edit' :
                    editTransportRequestPos(id);
                    break;
                case 'delete' :
                    deleteTransportRequestPos(id);
                    break;
                case 'export' :
                    convertTableToJson();
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
            export: {
                name: 'Exportieren (Json)',
                icon: 'mdi mdi-access-point-minus'
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

    // Übersicht als Json exportieren
    function convertTransportRequestTableToJson() {
        const objects = [];
        const data = transportRequestTable.rows().data();

        for (let i = 0; i < data.length; i++) {
            objects.push(data[i]);
        }

        return objects;
    }

    // Transportauftrag Position als Json exportieren
    function convertTransportRequestPosToJson(id) {
        const objects = [];
        const data = transportRequestTable.rows().data();

        for (let i = 0; i < data.length; i++) {
            objects.push(data[i]);
        }

        return objects;
    }

    // Modal für Transportauftrag Position bearbeiten
    function editTransportRequestPos(id) {
        const url = 'ta_position_bearbeiten/id/' + id,
            $form = $('form#transport-request-form-edit'),
            title = 'Transportauftrag bearbeiten';

        getContentForModal(url, title, $form);
    }

    // Modal für Transportauftrag Position löschen
    function deleteTransportRequestPos(id) {
        const url = '/ta_position_löschen/id/' + id,
            $form = $('form#transport-request-modal-delete-ask'),
            title = 'Transportauftrag löschen';

        $('#modalCenter .modal-dialog').css('max-width', '30%');

        getContentForModal(url, title, $form);
    }

    // Geänderte Transportauftrag Position speichern
    $(document).on('click', 'button#edit_transport_request_save', function(event) {
        const id = $('#edit_transport_request_id').val(),
            $form = $('form#article-form-edit'),
            url = '/ta_position_bearbeiten/id/' + id,
            errorMessage = 'Transportauftrag Position konnte nicht gespeichert werden',
            successMessage = 'Transportauftrag Position erfolgreich gespeichert',
            redirectUrl = '/stock_in';
        event.preventDefault();

        _doRequest('POST', url, $form, errorMessage, successMessage, transportRequestTable, true, redirectUrl);
    });

    // Transportauftrag Position löschen
    $(document).on('click', 'button#delete_transport_request_delete', function(event) {
        const id = $('#delete_transport_request_id').val(),
            $form = $('form#article-modal-delete-ask'),
            url = '/ta_position_löschen/id/' + id,
            errorMessage = 'Transportauftrag Position konnte nicht gelöscht werden',
            successMessage = 'Transportauftrag Position erfolgreich gelöscht';
        event.preventDefault();

        _doRequest('POST', url, $form, errorMessage, successMessage, transportRequestTable, false, null);
    });

    $(document).on('click', '.abort', function() {
        $('#modalCenter').modal('hide');
    });

    $(document.body).on("keydown", ".context-menu-item",
        function(e){
            alert("key:", e.keyCode);
        }
    );

})(jQuery);
