/**
 * @class ModalHandler
 * @param {string} modalId - ID des Modals (Standard: 'modalCenter').
 * @param {string} title - Titel des Modals.
 * @param {string} url - URL für den AJAX-Request.
 * @param {string} size - Größe des Modals (z.B. 'modal-sm', 'modal-lg', 'modal-xl' oder '%').
 * @param {function} onSuccess - Callback-Funktion bei erfolgreichem Laden.
 * @param {function} onError - Callback-Funktion bei Fehlern.
 *
 * @description Eine Klasse zur Handhabung von Bootstrap-Modalen mit AJAX-Inhalten.
 * @example
 * const modalHandler = new ModalHandler('modalCenter');
 * modalHandler.open({
 *   title: 'Mein Modal',
 *   url: '/mein-url',
 *   size: 'modal-lg', // oder '80%' für 80% Breite
 *   onSuccess: () => console.log('Erfolgreich geladen'),
 *   onError: (error) => console.error('Fehler:', error)
 * });
 */
export class ModalHandler {
    constructor(modalId = 'modalCenter') {
        this.modal = document.getElementById(modalId);
        this.title = this.modal.querySelector('.modal-title');
        this.body = this.modal.querySelector('.modal-body');
        this.dialog = this.modal.querySelector('.modal-dialog');
        this.bsModal = new bootstrap.Modal(this.modal);
        this.defaultLoader = '<div class="modal-body text-center py-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Lade...</span></div></div>';
    }

    open({ title = '', url = '', size = 'modal-lg', onSuccess = null, onError = null }) {
        this.setTitle(title);
        this.setSize(size);
        this.setLoading();
        this.show();

        fetch(url)
            .then(response => {
                if (!response.ok) throw new Error('Netzwerkfehler');
                return response.text();
            })
            .then(html => {
                if (!this.body) {
                    throw new Error('Modal body nicht gefunden');
                }
                this.body.innerHTML = html;
                if (typeof onSuccess === 'function') onSuccess();
            })
            .catch(error => {
                console.error('Fehler beim Laden:', error.message);
                this.setError('Fehler beim Laden der Daten: ' + error.message);
                if (typeof onError === 'function') onError(error);
            });
    }

    /**
     * Setzt den Titel des Modals.
     * @param title
     */
    setTitle(title) {
        this.title.textContent = title;
    }

    /**
     * Setzt die Größe des Modals.
     * @param {string} size - Größe des Modals (z.B. 'modal-sm', 'modal-lg', 'modal-xl' oder '%')
     * @example: setSize('modal-lg') oder setSize('80%')
    */
    setSize(size) {
        if (!this.dialog) {
            console.warn('.modal-dialog nicht gefunden – Größe kann nicht gesetzt werden.');
            return;
        }

        this.dialog.classList.remove('modal-sm', 'modal-md', 'modal-lg', 'modal-xl');
        this.dialog.style.width = '';
        this.dialog.style.maxWidth = '';

        if (typeof size === 'string') {
            if (size.endsWith('%')) {
                this.dialog.style.maxWidth = size;
            } else if (['modal-sm', 'modal-md', 'modal-lg', 'modal-xl'].includes(size)) {
                this.dialog.classList.add(size);
            } else {
                console.warn(`Unbekanntes Größenformat: "${size}"`);
            }
        }
    }

    /**
     * Setzt den Inhalt des Modals auf einen Ladeindikator.
     */
    setLoading() {
        this.body.innerHTML = this.defaultLoader;
    }

    /**
     * Setzt den Inhalt des Modals auf eine Fehlermeldung.
     * @param {string} message - Fehlermeldung
     */
    setError(message) {
        this.body.innerHTML = `<div class="modal-body text-danger text-center py-4">${message}</div>`;
    }


    show() {
        this.bsModal.show();
    }

    hide() {
        this.bsModal.hide();
    }
}