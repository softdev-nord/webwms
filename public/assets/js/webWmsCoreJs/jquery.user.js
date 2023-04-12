(function($){
    // Benutzer Tabelle
    const userTable = $('#userTable').DataTable({
        lengthChange: false,
        ajax: {
            url: '/user_ajax',
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
            { data: 'username' },
            { data: 'firstname' },
            { data: 'lastname' },
            { data: 'created_at' },
            { data: 'updated_at' }
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
                text: 'Benutzer anlegen',
                className: 'btn-add-new',
                action: function (e, dt, node, config) {
                    addUser();
                }
            }
        ]
    });

    if (user_role === 'ROLE_SUPER_ADMIN') {
        $.contextMenu({
            selector: 'tr',
            trigger: 'right',
            callback: function(key, options, event) {
                const row = userTable.row(options.$trigger);

                switch (key) {
                    case 'edit' :
                        editUser(row.data().username);
                        break;
                    case 'editPassword' :
                        editUserPassword(row.data().username);
                        break;
                    case 'delete' :
                        deleteUser(row.data().username);
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
                editPassword: {
                    name: 'Passwort ändern',
                    icon: 'edit'
                },
                delete: {
                    name: 'Löschen',
                    icon: 'delete'
                }
            }
        });
    }

    $(function(){
        // Ändern der Standardbreite des Modals
        $('#modalCenter .modal-dialog').css('max-width', '50%');
    });

    $.ajaxSetup({
        cache: false
    });

    function addUser() {
        const url = '/benutzer_anlegen',
            $form = $('form#user-form-new'),
            title = 'Benutzer anlegen';

        getContentForModal(url, title, $form);
    }

    function editUser(username) {
        const url = 'benutzer_bearbeiten/benutzername/' + username,
            $form = $('form#user-form-edit'),
            title = 'Benutzer bearbeiten';

        getContentForModal(url, title, $form);
    }

    function editUserPassword(username) {
        const url = 'benutzer_passwort_aendern/benutzername/' + username,
            $form = $('form#user-password-form-edit'),
            title = 'Benutzerpasswort ändern';

        getContentForModal(url, title, $form);
    }

    function deleteUser(username) {
        const url = '/benutzer_löschen/benutzername/' + username,
            $form = $('form#user-modal-delete-ask'),
            title = 'Benutzerpasswort ändern';

        $('#modalCenter .modal-dialog').css('max-width', '30%');

        getContentForModal(url, title, $form);
    }

    // Neuen Benutzer speichern
    $(document).on('click', 'button#add_user_save', function(event) {
        const $form = $('form#user-form-new'),
            url = '/benutzer_anlegen',
            errorMessage = 'Benutzer konnte nicht gespeichert werden',
            successMessage = 'Benutzer erfolgreich gespeichert';
        event.preventDefault();

        _doRequest('POST', url, $form, errorMessage, successMessage, userTable);
    });

    // Geänderten Benutzer speichern
    $(document).on('click', 'button#edit_user_save', function(event) {
        const username = $('#edit_user_username').val(),
            $form = $('form#user-form-edit'),
            url = '/benutzer_bearbeiten/benutzername/' + username,
            errorMessage = 'Benutzer konnte nicht gespeichert werden',
            successMessage = 'Benutzer erfolgreich gespeichert';
        event.preventDefault();

        _doRequest('POST', url, $form, errorMessage, successMessage, userTable);
    });

    // Geändertes Benutzerpasswort speichern
    $(document).on('click', 'button#change_password_save', function(event) {
        const username = $('#change_password_username').val(),
            $form = $('form#user-password-form-edit'),
            url = '/benutzer_passwort_bearbeiten/benutzername/' + username,
            errorMessage = 'Benutzerpasswort konnte nicht gespeichert werden',
            successMessage = 'Benutzerpasswort erfolgreich gespeichert';
        event.preventDefault();

        _doRequest('POST', url, $form, errorMessage, successMessage, userTable);
    });

    // Benutzer löschen
    $(document).on('click', 'button#delete_user_delete', function(event) {
        const username = $('#delete_user_username').val(),
            $form = $('form#user-modal-delete-ask'),
            url = '/benutzer_löschen/benutzername/' + username,
            errorMessage = 'Benutzer konnte nicht gelöscht werden',
            successMessage = 'Benutzer erfolgreich gelöscht';
        event.preventDefault();

        _doRequest('POST', url, $form, errorMessage, successMessage, userTable);
    });

    $(document).on('click', '.abort', function() {
        $('#modalCenter').modal('hide');
    });

})(jQuery);
