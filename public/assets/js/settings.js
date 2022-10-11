/**
 * Replaces an occurrence in a string
 * @param {string} text to look into
 * @param {string} replacement
 * @param {string} placeholderString
 * @returns {string}
 */
function replacePlaceholder(text, replacement, placeholderString) {
    placeholderString = placeholderString || 'placeholder';
    return text.replace(placeholderString, replacement);
}

/**
 * Show or hide the delete row in a table when clicking on the delete icon for an entry
 * @param {int} id
 * @returns {Boolean}
 */
function collapseEntry(id) {
    const row = "#entry-" + id;
    const cell = "#entry-cell-" + id;
    if ($(row).is(':hidden')) {
        $(row).removeClass('d-none');
        return true;
    } else {
        $(row).addClass('d-none');
        $(cell).html(loader);
        return false;
    }
}

/**
 * Enables the edit form when clicking on edit button
 * @param {int} id
 */
function enableEditForm(id) {
    const formFieldsetPrimary = "#entry-form-fieldset-" + id;
    const editButtonArea = "#edit-button-area";
    const saveButton = "#entry-submit-" + id;
    $(editButtonArea).addClass('d-none');
    $(saveButton).removeClass('d-none');
    $(formFieldsetPrimary).removeAttr('disabled');
}

/**
 * Initiates the delete process and shows the question whether to delete the entry or not
 * @param {int} id
 * @param {string} url
 * @returns {Boolean}
 */
function _deleteEntry(id, url) {
    if (collapseEntry(id)) {
        const cell = "#entry-cell-" + id;
        $(cell).load(url, function (response, status, xhr) {
            //if(status == "success") location.reload();
        });
    }
    return false;
}
