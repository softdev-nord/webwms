(() => {
    'use strict';

    const endpoints = {
        supplierOrders: '/supplier_orders_ajax',
        supplierOrderPos: '/supplier_order_pos_ajax',
        add: '/bestellung_anlegen',
        edit: (supplierOrderId) => `/bestellung_bearbeiten/supplierOrderId/${supplierOrderId}`,
        remove: (supplierOrderId) => `/bestellung_löschen/supplierOrderId/${supplierOrderId}`,
    };

    const state = {
        supplierOrders: [],
        supplierOrderPositions: [],
        selectedSupplierOrderId: null,
    };

    const supplierOrderTable = document.getElementById('supplierOrderTable');
    const posTable = document.getElementById('posTable');

    if (!supplierOrderTable || !posTable) {
        return;
    }

    const supplierOrderTBody = ensureTBody(supplierOrderTable);
    const posTBody = ensureTBody(posTable);

    init();

    async function init() {
        configureModalDefaults();
        createToolbar();
        attachGlobalHandlers();
        await reloadTables();
    }

    function ensureTBody(table) {
        if (table.tBodies.length > 0) {
            return table.tBodies[0];
        }
        return table.createTBody();
    }

    function configureModalDefaults() {
        const modalDialog = document.querySelector('#modalCenter .modal-dialog');
        if (modalDialog) {
            modalDialog.style.maxWidth = '98%';
        }
    }

    function createToolbar() {
        const cardBody = supplierOrderTable.closest('.card-body');
        if (!cardBody) {
            return;
        }

        const toolbar = document.createElement('div');
        toolbar.className = 'd-flex flex-wrap gap-2 mb-3';
        toolbar.innerHTML = [
            buttonHtml('copy', 'Kopieren'),
            buttonHtml('csv', 'CSV'),
            buttonHtml('pdf', 'PDF'),
            buttonHtml('print', 'Drucken'),
            buttonHtml('add', 'Bestellung anlegen', 'ms-auto'),
        ].join('');

        toolbar.addEventListener('click', (event) => {
            const button = event.target.closest('button[data-action]');
            if (!button) {
                return;
            }

            const action = button.dataset.action;
            switch (action) {
                case 'copy':
                    copySupplierOrdersToClipboard();
                    break;
                case 'csv':
                    exportSupplierOrdersCsv();
                    break;
                case 'pdf':
                    printSupplierOrders();
                    break;
                case 'print':
                    printSupplierOrders();
                    break;
                case 'add':
                    openAddModal();
                    break;
                default:
                    break;
            }
        });

        cardBody.insertBefore(toolbar, supplierOrderTable);
    }

    function buttonHtml(action, label, extraClass = '') {
        return `<button type="button" class="btn btn-primary btn-xs btn3d ${extraClass}" data-action="${action}">${label}</button>`;
    }

    function attachGlobalHandlers() {
        supplierOrderTBody.addEventListener('click', onSupplierOrderRowClick);
        supplierOrderTBody.addEventListener('contextmenu', onSupplierOrderContextMenu);

        document.addEventListener('click', onDocumentClick);

        document.addEventListener('click', async (event) => {
            const target = event.target;

            if (!(target instanceof Element)) {
                return;
            }

            if (target.matches('#supplier_order_save')) {
                event.preventDefault();
                await submitForm('form#supplier-order-form-new', endpoints.add, 'Bestellung erfolgreich gespeichert');
                return;
            }

            if (target.matches('#edit_supplier_order_save')) {
                event.preventDefault();
                const supplierOrderIdInput = document.getElementById('edit_supplierOrder_supplierOrderId');
                const supplierOrderId = supplierOrderIdInput instanceof HTMLInputElement ? supplierOrderIdInput.value : '';
                await submitForm('form#supplier-order-form-edit', endpoints.edit(supplierOrderId), 'Bestellung erfolgreich gespeichert');
                return;
            }

            if (target.matches('#delete_supplier_order_delete')) {
                event.preventDefault();
                const supplierOrderIdInput = document.getElementById('delete_supplier_order_supplierOrderId');
                const supplierOrderId = supplierOrderIdInput instanceof HTMLInputElement ? supplierOrderIdInput.value : '';
                await submitForm('form#supplier-order-modal-delete-ask', endpoints.remove(supplierOrderId), 'Bestellung erfolgreich gelöscht');
                return;
            }

            if (target.closest('.abort')) {
                hideModal();
            }
        });
    }

    async function reloadTables() {
        await Promise.all([loadSupplierOrders(), loadSupplierOrderPositions()]);
        renderSupplierOrderTable();
        renderPosTable();
    }

    async function loadSupplierOrders() {
        state.supplierOrders = await fetchJson(endpoints.supplierOrders, []);
    }

    async function loadSupplierOrderPositions() {
        state.supplierOrderPositions = await fetchJson(endpoints.supplierOrderPos, []);
    }

    function renderSupplierOrderTable() {
        supplierOrderTBody.innerHTML = '';

        const rows = state.supplierOrders.slice(0, 10);

        for (const row of rows) {
            const tr = document.createElement('tr');
            tr.dataset.supplierOrderId = String(row.supplier_order_id);
            tr.className = state.selectedSupplierOrderId === row.supplier_order_id ? 'table-active' : '';

            const lastChange = row.updated_at ?? row.created_at ?? '';

            tr.innerHTML = `
                <td class="text-center">${escapeHtml(row.supplier_order_nr)}</td>
                <td class="text-center">${escapeHtml(row.supplier_order_reference)}</td>
                <td class="text-center">${escapeHtml(row.supplier_nr)}</td>
                <td class="text-center">${escapeHtml(row.supplier_name)}</td>
                <td class="text-center">${formatDate(row.supplier_order_creation_date)}</td>
                <td class="text-center">${escapeHtml(row.username)}</td>
                <td class="text-center">${escapeHtml(lastChange)}</td>
            `;

            supplierOrderTBody.appendChild(tr);
        }
    }

    function renderPosTable() {
        posTBody.innerHTML = '';

        if (state.selectedSupplierOrderId === null) {
            return;
        }

        const selectedPositions = state.supplierOrderPositions
            .filter((row) => row.supplier_order_id === state.selectedSupplierOrderId)
            .slice(0, 5);

        for (const row of selectedPositions) {
            const delivered = row.lbw_menge != null ? Number(row.lbw_menge) : 0;
            const ordered = Number(row.supplier_order_pos_quantity ?? 0);
            const open = ordered - delivered;

            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td class="text-center">${escapeHtml(row.supplier_order_nr)}</td>
                <td class="text-center">${escapeHtml(row.article_nr)}</td>
                <td>${escapeHtml(row.article_name)}</td>
                <td class="text-center">${numberWithCommas(ordered)}</td>
                <td class="text-center">${numberWithCommas(delivered)}</td>
                <td class="text-center">${numberWithCommas(open)}</td>
            `;
            posTBody.appendChild(tr);
        }
    }

    function onSupplierOrderRowClick(event) {
        const row = event.target instanceof Element ? event.target.closest('tr[data-supplier-order-id]') : null;
        if (!row) {
            return;
        }

        const id = Number(row.dataset.supplierOrderId);

        if (state.selectedSupplierOrderId === id) {
            state.selectedSupplierOrderId = null;
        } else {
            state.selectedSupplierOrderId = id;
        }

        renderSupplierOrderTable();
        renderPosTable();
    }

    function onSupplierOrderContextMenu(event) {
        const row = event.target instanceof Element ? event.target.closest('tr[data-supplier-order-id]') : null;
        if (!row) {
            return;
        }

        event.preventDefault();

        const supplierOrderId = Number(row.dataset.supplierOrderId);
        state.selectedSupplierOrderId = supplierOrderId;
        renderSupplierOrderTable();
        renderPosTable();

        showContextMenu(event.clientX, event.clientY, supplierOrderId);
    }

    function showContextMenu(x, y, supplierOrderId) {
        removeContextMenu();

        const menu = document.createElement('div');
        menu.id = 'supplier-order-context-menu';
        menu.className = 'card shadow';
        menu.style.position = 'fixed';
        menu.style.left = `${x}px`;
        menu.style.top = `${y}px`;
        menu.style.zIndex = '2000';
        menu.style.minWidth = '180px';

        menu.innerHTML = `
            <button type="button" class="dropdown-item" data-action="edit">Bearbeiten</button>
            <button type="button" class="dropdown-item text-danger" data-action="delete">Löschen</button>
        `;

        menu.addEventListener('click', (event) => {
            const button = event.target instanceof Element ? event.target.closest('button[data-action]') : null;
            if (!button) {
                return;
            }

            const action = button.dataset.action;
            if (action === 'edit') {
                openEditModal(supplierOrderId);
            }

            if (action === 'delete') {
                openDeleteModal(supplierOrderId);
            }

            removeContextMenu();
        });

        document.body.appendChild(menu);
    }

    function onDocumentClick(event) {
        const menu = document.getElementById('supplier-order-context-menu');
        if (!menu) {
            return;
        }

        if (!(event.target instanceof Node) || !menu.contains(event.target)) {
            removeContextMenu();
        }
    }

    function removeContextMenu() {
        const menu = document.getElementById('supplier-order-context-menu');
        if (menu) {
            menu.remove();
        }
    }

    async function openAddModal() {
        await loadModal('Bestellung anlegen', endpoints.add, 98);
    }

    async function openEditModal(supplierOrderId) {
        await loadModal('Bestellung bearbeiten', endpoints.edit(supplierOrderId), 98);
    }

    async function openDeleteModal(supplierOrderId) {
        await loadModal('Bestellung löschen', endpoints.remove(supplierOrderId), 30);
    }

    async function loadModal(title, url, maxWidthPercent) {
        const modalTitle = document.querySelector('#modalCenter .modal-title');
        const modalBody = document.getElementById('modal-content-ajax');
        const modalDialog = document.querySelector('#modalCenter .modal-dialog');

        if (!modalBody) {
            return;
        }

        if (modalTitle) {
            modalTitle.textContent = title;
        }

        if (modalDialog) {
            modalDialog.style.maxWidth = `${maxWidthPercent}%`;
        }

        modalBody.innerHTML = '<div class="modal-body"></div>';
        showModal();

        try {
            const html = await fetchText(url);
            modalBody.innerHTML = html;
        } catch (error) {
            showErrorAlert('Fehler beim Laden des Modals', error);
        }
    }

    async function submitForm(formSelector, url, successTitle) {
        const form = document.querySelector(formSelector);

        if (!(form instanceof HTMLFormElement)) {
            showErrorAlert('Formular nicht gefunden');
            return;
        }

        try {
            const formData = new FormData(form);
            const response = await fetch(url, {
                method: 'POST',
                body: new URLSearchParams(formData),
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            const data = await response.json();

            if (data.error) {
                showValidationErrors(data.error);
                return;
            }

            showSuccessAlert(successTitle, data.message ?? 'Erfolgreich gespeichert');
            hideModal();
            await reloadTables();
        } catch (error) {
            showErrorAlert('Fehler beim Speichern', error);
        }
    }

    async function fetchJson(url, fallback = []) {
        try {
            const response = await fetch(url, {
                method: 'GET',
                cache: 'no-store',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }

            const data = await response.json();
            return Array.isArray(data) ? data : fallback;
        } catch (error) {
            showErrorAlert(`Fehler beim Laden: ${url}`, error);
            return fallback;
        }
    }

    async function fetchText(url) {
        const response = await fetch(url, {
            method: 'GET',
            cache: 'no-store',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            },
        });

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }

        return response.text();
    }

    function showModal() {
        const modalElement = document.getElementById('modalCenter');
        if (!modalElement) {
            return;
        }

        if (window.bootstrap?.Modal) {
            window.bootstrap.Modal.getOrCreateInstance(modalElement).show();
            return;
        }

        modalElement.classList.add('show');
        modalElement.style.display = 'block';
    }

    function hideModal() {
        const modalElement = document.getElementById('modalCenter');
        if (!modalElement) {
            return;
        }

        if (window.bootstrap?.Modal) {
            window.bootstrap.Modal.getOrCreateInstance(modalElement).hide();
            return;
        }

        modalElement.classList.remove('show');
        modalElement.style.display = 'none';
    }

    function showValidationErrors(errorObj) {
        const errors = Object.values(errorObj).map((value) => String(value));
        showErrorAlert('Bestellung konnte nicht gespeichert werden', errors.join('\n'));
    }

    function showSuccessAlert(title, content) {
        if (window.Swal) {
            window.Swal.fire({
                icon: 'success',
                title,
                text: content,
                timer: 3500,
                showConfirmButton: false,
            });
            return;
        }

        alert(`${title}\n\n${content}`);
    }

    function showErrorAlert(title, error = '') {
        const message = typeof error === 'string'
            ? error
            : (error instanceof Error ? error.message : 'Unbekannter Fehler');

        if (window.Swal) {
            window.Swal.fire({
                icon: 'error',
                title,
                text: message,
            });
            return;
        }

        alert(`${title}\n\n${message}`);
    }

    function copySupplierOrdersToClipboard() {
        const rows = state.supplierOrders.map((row) => [
            row.supplier_order_nr,
            row.supplier_order_reference,
            row.supplier_nr,
            row.supplier_name,
            formatDate(row.supplier_order_creation_date),
            row.username,
            row.updated_at ?? row.created_at ?? '',
        ].join('\t'));

        const content = rows.join('\n');
        navigator.clipboard.writeText(content)
            .then(() => showSuccessAlert('Kopiert', 'Tabelleninhalt wurde kopiert.'))
            .catch((error) => showErrorAlert('Kopieren fehlgeschlagen', error));
    }

    function exportSupplierOrdersCsv() {
        const header = ['Bestellungs-Nr', 'Bestellungs Referenz', 'Lieferanten-Nr', 'Lieferanten-Name', 'Bestellungsdatum', 'Benutzer', 'Letzte Änderung'];
        const body = state.supplierOrders.map((row) => [
            row.supplier_order_nr,
            row.supplier_order_reference,
            row.supplier_nr,
            row.supplier_name,
            formatDate(row.supplier_order_creation_date),
            row.username,
            row.updated_at ?? row.created_at ?? '',
        ]);

        const csv = [header, ...body]
            .map((line) => line.map(csvEscape).join(';'))
            .join('\n');

        const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
        const url = URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.download = 'supplier_orders.csv';
        link.click();
        URL.revokeObjectURL(url);
    }

    function printSupplierOrders() {
        window.print();
    }

    function formatDate(value) {
        if (!value) {
            return '';
        }

        const date = new Date(value);
        if (Number.isNaN(date.getTime())) {
            return String(value);
        }

        return new Intl.DateTimeFormat('de-DE').format(date);
    }

    function numberWithCommas(number) {
        return new Intl.NumberFormat('de-DE', {
            style: 'decimal',
            minimumFractionDigits: 0,
            maximumFractionDigits: 3,
        }).format(Number(number) || 0);
    }

    function csvEscape(value) {
        const str = String(value ?? '');
        if (str.includes(';') || str.includes('"') || str.includes('\n')) {
            return `"${str.replaceAll('"', '""')}"`;
        }
        return str;
    }

    function escapeHtml(value) {
        return String(value ?? '')
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }
})();
