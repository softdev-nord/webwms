/**
 * Fügt zwei Datumseingabefelder ein, wenn eines der Felder leer ist. Es wird z.B. im anderen Feld + 1 Tag hinzugefügt
 * @returns void
 * @param idStart
 * @param idEnd
 * @param addDays
 */
function iniStartOrEndDate(idStart, idEnd, addDays) {
    let end = document.getElementById(idEnd);
    let start = document.getElementById(idStart);
    if (end.value === '' && start.value !== '') {
        let dEnd = new Date(start.value).addDays(addDays);
        let sDate = dEnd.getFullYear() + '-' + ('0' + (dEnd.getMonth() + 1)).slice(-2) + '-' + ('0' + dEnd.getDate()).slice(-2);
        end.value = sDate;
    }

    if (start.value === '' && end.value !== '') {
        let dStart = new Date(end.value).minusDays(addDays);
        let sDate = dStart.getFullYear() + '-' + ('0' + (dStart.getMonth() + 1)).slice(-2) + '-' + ('0' + dStart.getDate()).slice(-2);
        start.value = sDate;
    }
}

/**
 * Kopieren des Textes eines nahen Textfeldes in die Zwischenablage
 * @param {Element} elm
 * @returns {void}
 */
function copyToClipboard(elm) {
    if (elm) {
        let target = elm.closest('div').querySelector('input[type=text]');
        if (target) {
            target.select();
            target.setSelectionRange(0, target.value.lengt);
            let suceed;
            try {
                suceed = navigator.clipboard.writeText(target.value);
            } catch (e) {
                console.warn(e);
                suceed = false;
            }

            if (suceed) {
                var popover = new bootstrap.Popover(target, {
                    'content': elm.dataset.hint,
                    'placement': 'top',
                });
                popover.show();
                setTimeout(function () {
                    popover.dispose();
                }, 1500);
            }
        }
    }
}