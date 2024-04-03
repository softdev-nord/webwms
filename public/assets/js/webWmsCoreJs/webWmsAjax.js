$.ajaxSetup({
    cache: false
});

/**
 * Lädt Inhalte in das Modal über GET
 * @param {string} url
 * @param {string} title
 * @param $form
 */
function getContentForModal(url, title, $form) {
    $('#modalCenter .modal-title').text(title);
    $('#modal-content-ajax').html('<div class="modal-body"></div>');
    $('#modalCenter').modal('show');

    $.ajax({
        url: url,
        type: 'GET',
        data: $form.serialize(),
        error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.status);
        },
        success: function (data) {
            $('#modal-content-ajax').html(data);
        }
    });
}

/**
 * Führt ein Ajax-Request durch
 * und bei Erfolg wird ein Reload durchgeführt
 * @param {string} type
 * @param {string} url
 * @param $form
 * @param type
 * @param errorMessage
 * @param successMessage
 * @param table
 */
function _doRequest(type, url, $form, errorMessage, successMessage, table) {
    $.ajax({
        type: type,
        url: url,
        data: $form.serialize(),
        success: function(data) {
            console.log(data);
            if (data.error) {
                const errors = [];
                let i = 0;
                $.each(data.error, function(key, value) {
                    errors[i++] = value + '</br>';
                });
                const arrayString = errors.join();
                const error = arrayString.replace(/,/g, ' ');
                Swal.fire({
                    title: errorMessage,
                    text: error,
                    icon: "error",
                    timer: 5000
                });
            } else {
                Swal.fire({
                    title: successMessage,
                    text: data.message,
                    icon: "success",
                    timer: 5000
                });
                $('#modalCenter').modal('hide');
                table.ajax.reload();
            }
        }
    });
}