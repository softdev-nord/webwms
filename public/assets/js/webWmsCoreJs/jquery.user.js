(function($){
    // Benutzer Tabelle
    const userTable = $('#userTable').DataTable({
        lengthChange: false,
        ajax: {
            'url': '/user_ajax',
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
            {'data': 'username'},
            {'data': 'firstname'},
            {'data': 'lastname'},
            {'data': 'created_at'},
            {'data': 'updated_at'}
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
                text: 'Benutzer anlegen',
                className: 'btn-add-new',
                action: function ( e, dt, node, config ) {
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
                        break
                }
            },
            items: {
                'edit': {name: 'Bearbeiten', icon: 'edit'},
                'editPassword': {name: 'Passwort ändern', icon: 'edit'},
                'delete': {name: 'Löschen', icon: 'delete'}
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

    function editUser(username) {
        const url = 'benutzer_bearbeiten/benutzername/' + username;
        const content = '<div class="modal-body"></div>';

        $('#modalCenter .modal-title').text('Benutzer bearbeiten');
        $('#modal-content-ajax').html(content);
        $('#modalCenter').modal('show');

        $.ajax({
            url: url,
            type: 'get',
            data: ($('#user-form-edit').serialize()),
            error: function (xhr, ajaxOptions, thrownError) {
                alert(xhr.status);
            },
            success: function (data) {
                $('#modal-content-ajax').html(data);
            }
        });

        return false;
    }

    function editUserPassword(username) {
        const url = 'benutzer_passwort_bearbeiten/benutzername/' + username;
        const content = '<div class="modal-body"></div>';

        $('#modalCenter .modal-title').text('Benutzerpasswort ändern');
        $('#modal-content-ajax').html(content);
        $('#modalCenter').modal('show');

        $.ajax({
            url: url,
            type: 'get',
            data: ($('#user-form-edit').serialize()),
            error: function (xhr, ajaxOptions, thrownError) {
                alert(xhr.status);
            },
            success: function (data) {
                $('#modal-content-ajax').html(data);
            }
        });

        return false;
    }

    function addUser() {
        const url = '/benutzer_anlegen';
        const content = '<div class="modal-body"></div>';

        $('#modalCenter .modal-title').text('Benutzer anlegen');
        $('#modal-content-ajax').html(content);
        $('#modalCenter').modal('show');

        $.ajax({
            url: url,
            type: 'get',
            data: ($('#user-form-new').serialize()),
            error: function (xhr, ajaxOptions, thrownError) {
                alert(xhr.status);
            },
            success: function (data) {
                $('#modal-content-ajax').html(data);
            }
        });

        return false;
    }

    function deleteUser(username) {
        const url = '/benutzer_löschen/benutzername/' + username;
        const content = '<div class="modal-body"></div>';

        $('#modalCenter .modal-dialog').css('max-width', '30%');
        $('#modalCenter .modal-title').text('Benutzer löschen');
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

    // Neuen Benutzer speichern
    $(document).on('click','button#add_user_save',function(event) {
        const $form = $('form#user-form-new');
        const url = '/benutzer_anlegen';
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
                        'title': 'Benutzer konnten nicht gespeichert werden',
                        'content': error,
                        'theme': 'red',
                        'size': 'md',
                        'showAnimation': 'fadeInUp',
                        'hideAnimation': 'fadeOutDown',
                        'autoClose': 5000
                    });
                } else {
                    $.jAlert({
                        'title': 'Benutzer erfolgreich gespeichert',
                        'content': data.message,
                        'theme': 'green',
                        'size': 'md',
                        'showAnimation': 'fadeInUp',
                        'hideAnimation': 'fadeOutDown',
                        'autoClose': 5000
                    });
                    $('#modalCenter').modal('hide');
                    userTable.ajax.reload();
                }
            }
        });
    });

    // Geändertes Benutzerpasswort speichern
    $(document).on('click','button#change_password_save',function(event) {
        const username = $('#change_password_username').val();
        const $form = $('form#user-password-form-edit');
        const url = '/benutzer_passwort_bearbeiten/benutzername/' + username;
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
                        'title': 'Benutzerpasswort konnten nicht gespeichert werden',
                        'content': error,
                        'theme': 'red',
                        'size': 'md',
                        'showAnimation': 'fadeInUp',
                        'hideAnimation': 'fadeOutDown',
                        'autoClose': 5000
                    });
                } else {
                    $.jAlert({
                        'title': 'Benutzerpasswort erfolgreich gespeichert',
                        'content': data.message,
                        'theme': 'green',
                        'size': 'md',
                        'showAnimation': 'fadeInUp',
                        'hideAnimation': 'fadeOutDown',
                        'autoClose': 5000
                    });
                    $('#modalCenter').modal('hide');
                    userTable.ajax.reload();
                }
            }
        });
    });

    // Benutzer löschen
    $(document).on('click','button#delete_user_delete',function(event) {
        const username = $('#delete_user_username').val();
        const $form = $('form#user-modal-delete-ask');
        const url = '/benutzer_löschen/benutzername/' + username;
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
                        'title': 'Benutzer konnten nicht gelöscht werden',
                        'content': error,
                        'theme': 'red',
                        'size': 'md',
                        'showAnimation': 'fadeInUp',
                        'hideAnimation': 'fadeOutDown',
                        'autoClose': 5000
                    });
                } else {
                    $.jAlert({
                        'title': 'Benutzer erfolgreich gelöscht',
                        'content': data.message,
                        'theme': 'green',
                        'size': 'md',
                        'showAnimation': 'fadeInUp',
                        'hideAnimation': 'fadeOutDown',
                        'autoClose': 5000
                    });
                    $('#modalCenter').modal('hide');
                    userTable.ajax.reload();
                }
            }
        });
    });

    // Geänderten Benutzer speichern
    $(document).on('click','button#edit_user_save',function(event) {
        const username = $('#edit_user_username').val();
        const $form = $('form#user-form-edit');
        const url = '/benutzer_bearbeiten/benutzername/' + username;
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
                        'title': 'Benutzer konnte nicht gespeichert werden',
                        'content': error,
                        'theme': 'red',
                        'size': 'md',
                        'showAnimation': 'fadeInUp',
                        'hideAnimation': 'fadeOutDown',
                        'autoClose': 5000
                    });
                } else {
                    $.jAlert({
                        'title': 'Benutzer erfolgreich gespeichert',
                        'content': data.message,
                        'theme': 'green',
                        'size': 'md',
                        'showAnimation': 'fadeInUp',
                        'hideAnimation': 'fadeOutDown',
                        'autoClose': 5000
                    });
                    $('#modalCenter').modal('hide');
                    userTable.ajax.reload();
                }
            }
        });
    });

    // Zurück zur Kundenübersicht
    $(document).on('click','#edit_user_back_to_user_overview',function() {
        window.location.href = '/benutzer'
    });

    $(document).on('click','button#delete_user_abort',function() {
        $('#modalCenter').modal('hide');
    });
})(jQuery);
