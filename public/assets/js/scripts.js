/**
 * Fügt zwei Datumseingabefelder ein, wenn eines der Felder leer ist. Es wird z.B. im anderen Feld + 1 Tag hinzugefügt
 * @returns void
 * @param idStart
 * @param idEnd
 * @param addDays
 */
function iniStartOrEndDate(idStart, idEnd, addDays) {
    const end = document.getElementById(idEnd);
    const start = document.getElementById(idStart);
    if (end.value === '' && start.value !== '') {
        const dEnd = new Date(start.value).addDays(addDays);
        const sDate = dEnd.getFullYear() + '-' + ('0' + (dEnd.getMonth() + 1)).slice(-2) + '-' + ('0' + dEnd.getDate()).slice(-2);
        end.value = sDate;
    }

    if (start.value === '' && end.value !== '') {
        const dStart = new Date(end.value).minusDays(addDays);
        const sDate = dStart.getFullYear() + '-' + ('0' + (dStart.getMonth() + 1)).slice(-2) + '-' + ('0' + dStart.getDate()).slice(-2);
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
        const target = elm.closest('div').querySelector('input[type=text]');
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
                const popover = new bootstrap.Popover(target, {
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