/**
 * Ersetzt ein Vorkommen in einer Zeichenkette
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
 * Ein- oder Ausblenden der Löschzeile in einer Tabelle, wenn Sie auf das Löschsymbol eines Eintrags klicken
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
 * Aktiviert das Bearbeitungsformular beim Klicken auf die Schaltfläche Bearbeiten
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
 * Initiiert den Löschvorgang und zeigt die Frage an, ob der Eintrag gelöscht werden soll oder nicht
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
