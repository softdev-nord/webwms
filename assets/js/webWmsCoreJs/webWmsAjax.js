/**
 * Lädt Inhalte in das Modal via GET und zeigt sie an.
 * @param {string} url - Ziel-URL
 * @param {string} title - Modaltitel
 * @param {HTMLFormElement|null} form - Optional: HTML-Formular, um Query-Parameter zu übergeben
 */
function getContentForModal(url, title, form = null) {
    const modal = document.getElementById('modalCenter');
    const modalTitle = modal.querySelector('.modal-title');
    const modalContent = document.getElementById('modal-content-ajax');

    modalTitle.textContent = title;
    modalContent.innerHTML = '<div class="modal-body">Lade Inhalt...</div>';

    const bsModal = bootstrap.Modal.getOrCreateInstance(modal);
    bsModal.show();

    // Falls ein Formular übergeben wurde, serialisiere es
    let query = '';
    if (form instanceof HTMLFormElement) {
        query = '?' + new URLSearchParams(new FormData(form)).toString();
    }

    fetch(url + query, {
        method: 'GET',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        cache: 'no-store'
    })
        .then(res => res.text())
        .then(html => modalContent.innerHTML = html)
        .catch(err => {
            console.error('Fehler beim Laden des Modalinhalts:', err);
            modalContent.innerHTML = '<div class="modal-body text-danger">Fehler beim Laden des Inhalts.</div>';
        });
}

/**
 * Führt ein Ajax-Formular-Request durch, zeigt SweetAlert-Feedback, aktualisiert Tabelle oder redirectet.
 * @param {string} method - HTTP-Methode (z. B. 'POST')
 * @param {string} url - Ziel-URL
 * @param {HTMLFormElement} form - Formular für die Daten
 * @param {string} errorMessage - Fehlermeldung für SweetAlert
 * @param {string} successMessage - Erfolgsmeldung für SweetAlert
 * @param {object|null} table - Optional: DataTable für Reload (wird aufgerufen mit `table.ajax.reload()`)
 * @param {boolean} isRedirect - Ob nach Erfolg redirectet werden soll
 * @param {string|null} redirectUrl - Ziel-URL für Redirect
 */
function _doRequest(method, url, form, errorMessage, successMessage, table = null, isRedirect = false, redirectUrl = null) {
    const formData = new URLSearchParams(new FormData(form));

    fetch(url, {
        method: method,
        body: formData,
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        cache: 'no-store'
    })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                const errorList = Object.values(data.error).map(e => `<div>${e}</div>`).join('');
                Swal.fire({
                    title: errorMessage,
                    html: errorList,
                    icon: 'error',
                    timer: 5000
                });
            } else {
                Swal.fire({
                    title: successMessage,
                    text: data.message,
                    icon: 'success',
                    timer: 5000
                });

                const modal = document.getElementById('modalCenter');
                const modalInstance = bootstrap.Modal.getInstance(modal);
                if (modalInstance) {
                    modalInstance.hide();
                }

                if (table && typeof table.ajax?.reload === 'function') {
                    table.ajax.reload();
                }

                if (isRedirect && redirectUrl) {
                    window.location.href = redirectUrl;
                }
            }
        })
        .catch(err => {
            console.error('Fehler im Request:', err);
            Swal.fire({
                title: errorMessage,
                text: "Ein Fehler ist aufgetreten.",
                icon: 'error',
                timer: 5000
            });
        });
}