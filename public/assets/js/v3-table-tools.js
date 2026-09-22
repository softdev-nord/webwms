(function (window, document, $) {
    'use strict';

    const FILTERABLE_HEADERS = new Set([
        'aktiv',
        'adapter',
        'bereich',
        'bewegung',
        'ergebnis',
        'lager',
        'protokoll',
        'prozessstatus',
        'ressource',
        'status',
        'strategie',
        'system',
        'typ'
    ]);
    const PAGE_LENGTHS = [10, 25, 50, 100];

    function normalize(value) {
        return String(value || '').replace(/\s+/g, ' ').trim();
    }

    function emptyMessage(table) {
        const rows = Array.from(table.tBodies[0]?.rows || []);
        if (rows.length !== 1) {
            return null;
        }

        const cells = rows[0].cells;
        if (cells.length !== 1 || !cells[0].hasAttribute('colspan')) {
            return null;
        }

        const message = normalize(cells[0].textContent);
        rows[0].remove();

        return message;
    }

    function addColumnFilters(api, table) {
        const container = $('<div class="v3-table-column-filters d-flex flex-wrap gap-2"></div>');

        api.columns().every(function (columnIndex) {
            const header = table.tHead?.rows[0]?.cells[columnIndex];
            const label = normalize(header?.textContent).toLocaleLowerCase('de-DE');
            if (!FILTERABLE_HEADERS.has(label)) {
                return;
            }

            const values = this.cache('search')
                .toArray()
                .map(normalize)
                .filter((value) => value !== '' && value !== '–')
                .filter((value, index, all) => all.indexOf(value) === index)
                .sort((left, right) => left.localeCompare(right, 'de'));

            if (values.length < 2 || values.length > 30) {
                return;
            }

            const select = $('<select class="form-select form-select-sm v3-table-column-filter"></select>')
                .attr('aria-label', header.textContent + ' filtern')
                .append($('<option></option>').val('').text(header.textContent + ': Alle'));

            values.forEach((value) => select.append($('<option></option>').val(value).text(value)));
            select.val(normalize(this.search()).replace(/^\^|\$$/g, '').replace(/\\([.*+?^${}()|[\]\\])/g, '$1'));
            select.on('change', () => {
                const selected = select.val();
                this.search(selected ? '^' + $.fn.dataTable.util.escapeRegex(selected) + '$' : '', true, false).draw();
            });
            container.append(select);
        });

        if (container.children().length > 0) {
            const wrapper = $(api.table().container());
            const toolbar = wrapper.find('.v3-table-toolbar').first();
            toolbar.append(container);
        }
    }

    function initializeTable(table, index) {
        if (table.dataset.tableTools === 'false' || $.fn.dataTable.isDataTable(table) || !table.tHead || !table.tBodies.length) {
            return;
        }

        const message = emptyMessage(table) || 'Keine passenden Einträge vorhanden.';
        if (!table.id) {
            table.id = 'v3-data-table-' + index;
        }

        const actionColumn = Array.from(table.tHead.rows[0].cells).map((cell, columnIndex) => ({
            columnIndex,
            empty: normalize(cell.textContent) === ''
        })).filter((column) => column.empty).map((column) => column.columnIndex);

        const dataTable = $(table).DataTable({
            autoWidth: false,
            columnDefs: actionColumn.length > 0 ? [{targets: actionColumn, orderable: false, searchable: false}] : [],
            dom: '<"v3-table-toolbar d-flex flex-wrap justify-content-between align-items-center gap-2 p-3"<"d-flex flex-wrap align-items-center gap-2"l><"d-flex flex-wrap align-items-center gap-2"f>><"table-responsive"t><"d-flex flex-wrap justify-content-between align-items-center gap-2 p-3"ip>',
            language: {
                decimal: ',',
                emptyTable: message,
                info: '_START_–_END_ von _TOTAL_ Einträgen',
                infoEmpty: '0 Einträge',
                infoFiltered: '(aus _MAX_ Einträgen gefiltert)',
                lengthMenu: '_MENU_ pro Seite',
                loadingRecords: 'Wird geladen …',
                paginate: {first: 'Erste', last: 'Letzte', next: 'Weiter', previous: 'Zurück'},
                processing: 'Wird verarbeitet …',
                search: '',
                searchPlaceholder: 'Tabelle durchsuchen …',
                zeroRecords: 'Keine passenden Einträge gefunden.'
            },
            lengthMenu: [PAGE_LENGTHS, PAGE_LENGTHS],
            order: [],
            pageLength: 25,
            stateDuration: 60 * 60 * 24 * 30,
            stateSave: true,
            initComplete: function () {
                const api = this.api();
                const wrapper = $(api.table().container());
                wrapper.addClass('v3-data-table');
                wrapper.find('.dataTables_filter input').addClass('form-control form-control-sm').attr('aria-label', 'Tabelle durchsuchen');
                wrapper.find('.dataTables_length select').addClass('form-select form-select-sm').attr('aria-label', 'Einträge pro Seite');
                addColumnFilters(api, table);
            }
        });

        $(table).on('page.dt length.dt search.dt', () => {
            window.requestAnimationFrame(() => dataTable.columns.adjust());
        });
    }

    function initialize() {
        if (!$ || !$.fn || !$.fn.DataTable) {
            return;
        }

        document.querySelectorAll('.main-content table.table').forEach(initializeTable);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initialize);
    } else {
        initialize();
    }
}(window, document, window.jQuery));
