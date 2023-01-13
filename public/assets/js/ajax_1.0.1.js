 $.ajaxSetup({
    cache: false
});
/**
 * Loads content into the modal via get
 * @param {string} url
 * @param {string} title
 * @param {type} successFunc
 */
function getContentForModal(url, title, successFunc) {
    const modalLoader = '<div class="modal-body"></div>';
    const modalContentAjax = $("#modal-content-ajax");
    title = title || "";
    successFunc = successFunc || function(){};
    $("#modalCenter .modal-title").text(title);
    modalContentAjax.html(modalLoader);
    modalContentAjax.load(url, function (response, status, xhr) {
        successFunc();
    });
}

/**
 * Performs a post request with data from the given form id
 * if a validation error occurs the warning will be displayed in the modal itself
 * if success a reload is performed
 * @param {string} formId
 * @param {string} url
 * @param {string} successUrl
 * @param type
 * @param successFunc
 * @returns {Boolean}
 */
function _doPost(formId, url, successUrl, type, successFunc) {
    const flashMessage = $("#flash-message-overlay");
    successUrl = successUrl || "";
    type = type || "POST";
    successFunc = successFunc || null;

    $.ajax({
        url: url,
        type: type,
        data: $(formId).serialize(),
        error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.status);
        },
        success: function (data) {
            if(successFunc !== null) {
                successFunc(data);
            } else {
                // if the whole modal content is returned
                if($(data).filter('.modal-body').length > 0 || $(data).find('.modal-body').length > 0) {
                    $("#modal-content-ajax").html(data);
                // if only flash messages are returned
                } else if (data && data.length > 0) {
                    flashMessage.empty();
                    flashMessage.append(data);
                } else if(successUrl.length > 0 ) {
                    location.href = successUrl;
                } else {
                    location.reload();
                }
            }
        }
    });
    return false;
}

function _doDelete(formId, url, successUrl, successFunc) {
    return _doPost(formId, url, successUrl, "DELETE", successFunc);
}

function _doPut(formId, url, successUrl, successFunc) {
    return _doPost(formId, url, successUrl, "PUT", successFunc);
}