import {ModalHandler} from './modalHandler.js';

const ARTICLE_CREATE_URL = '/artikel_anlegen';
const ARTICLE_OVERVIEW_QUERY_URL = '/query/articles/overview';
const ARTICLE_EDIT_URL_PREFIX = '/artikel_bearbeiten/articleId/';
const ARTICLE_DELETE_URL_PREFIX = '/artikel_l%C3%B6schen/articleId/';
const ARTICLE_STOCK_URL_PREFIX = '/stock_occupancy_ajax_article/';

const normalizeArticleId = (article) => {
    const rawId = article?.articleId ?? article?.article_id ?? article?.id ?? null;
    const numericId = Number.parseInt(String(rawId), 10);
    return Number.isInteger(numericId) && numericId > 0 ? String(numericId) : null;
};

const assignArticleRowIds = (articleList) => {
    if (!articleList || !Array.isArray(articleList.items)) {
        return;
    }

    const rows = document.querySelectorAll('#articleTable tbody tr');
    rows.forEach((row, idx) => {
        const item = articleList.items[idx];
        const values = item && typeof item.values === 'function' ? item.values() : null;
        const articleId = normalizeArticleId(values);
        if (articleId) {
            row.dataset.id = articleId;
        }
    });
};

const showAlert = (title, text, icon) => {
    if (window.Swal && typeof window.Swal.fire === 'function') {
        window.Swal.fire(title, text, icon);
        return;
    }
    window.alert(text);
};

const showLoading = (modalEl, title) => {
    if (!modalEl) {
        return;
    }

    const titleEl = modalEl.querySelector('.modal-title');
    const bodyEl = modalEl.querySelector('.modal-body');
    if (!titleEl || !bodyEl) {
        return;
    }

    titleEl.textContent = title;
    bodyEl.innerHTML = '<div class="d-flex justify-content-center align-items-center" style="height:200px"><div class="spinner-border text-primary" role="status"></div></div>';

    if (window.bootstrap && window.bootstrap.Modal) {
        window.bootstrap.Modal.getOrCreateInstance(modalEl).show();
    }
};

const createArticleList = () => {
    if (typeof window.List !== 'function' || typeof window.ListPagination !== 'function') {
        return null;
    }

    return new window.List('webwms-article-overview', {
        valueNames: [
            'articleId', 'articleNr', 'articleName', 'articleCategory', 'articleWeight',
            'articleEan', 'articleUnit', 'articleDepth', 'articleWidth', 'articleHeight',
            'inStock', 'incomingStock', 'reservedStock'
        ],
        page: 10,
        pagination: true,
        plugins: [window.ListPagination({ left: 2, right: 2 })],
        item: '<tr><td class="articleNr"></td><td class="articleName"></td><td class="articleWeight"></td><td class="articleEan"></td><td class="articleUnit"></td><td class="articleDepth"></td><td class="articleWidth"></td><td class="articleHeight"></td><td class="inStock"></td><td class="incomingStock"></td><td class="reservedStock"></td></tr>'
    });
};

const loadArticles = async (articleList) => {
    if (!articleList) {
        return;
    }

    try {
        const response = await fetch(ARTICLE_OVERVIEW_QUERY_URL);
        if (!response.ok) {
            throw new Error('HTTP ' + response.status);
        }

        const payload = await response.json();
        const data = Array.isArray(payload)
            ? payload
            : (payload && Array.isArray(payload.items) ? payload.items : []);
        if (!Array.isArray(data)) {
            return;
        }

        const normalizedData = data.map((article) => ({
            ...article,
            articleId: normalizeArticleId(article),
        }));

        articleList.add(normalizedData);
        assignArticleRowIds(articleList);
    } catch (error) {
        console.error('Fehler beim Laden der Artikeldaten:', error);
    }
};

const initCreateButton = (modal) => {
    const createButton = document.getElementById('create-btn');
    if (!createButton) {
        return;
    }

    createButton.addEventListener('click', (event) => {
        event.preventDefault();
        modal.open({
            title: 'Artikel hinzufügen',
            url: ARTICLE_CREATE_URL,
        });
    });
};

const initExportButton = (articleList) => {
    const exportButton = document.querySelector('#exportJsonBtn');
    if (!exportButton || !articleList) {
        return;
    }

    exportButton.addEventListener('click', () => {
        const exportData = articleList.items.map((item) => item.values());
        const blob = new Blob([JSON.stringify(exportData, null, 2)], { type: 'application/json' });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = 'export_article_overview.json';
        document.body.appendChild(link);
        link.click();
        link.remove();
        URL.revokeObjectURL(link.href);
    });
};

const renderStockOccupancyList = (modalBody, data) => {
    modalBody.innerHTML = '<div class="card p-3"><div id="stockListWrapper"><table class="table table-striped table-hover"><thead><tr><th data-sort="lagereinheit" class="text-center">Lagereinheit</th><th data-sort="lagerplatz" class="text-center">Lagerplatz</th><th data-sort="articleNr" class="text-center">Artikel-Nr</th><th data-sort="bezeichnung" class="text-center">Bezeichnung</th><th data-sort="transEin" class="text-center">Zugang</th><th data-sort="transAus" class="text-center">Abgang</th><th data-sort="lpBestand" class="text-center">LP Bestand</th><th data-sort="letzterZugang" class="text-center">Letzter Zugang</th><th data-sort="letzterAbgang" class="text-center">Letzter Abgang</th></tr></thead><tbody class="list"></tbody></table><div class="d-flex justify-content-end"><div class="pagination-wrap hstack gap-2"><a class="page-item pagination-prev disabled" href="#">Vorherige</a><ul class="pagination listjs-pagination mb-0"></ul><a class="page-item pagination-next" href="#">Naechste</a></div></div></div></div>';

    if (typeof window.List !== 'function' || typeof window.ListPagination !== 'function') {
        return;
    }

    new window.List('stockListWrapper', {
        valueNames: [
            'lagereinheit', 'lagerplatz', 'articleNr', 'bezeichnung',
            'transEin', 'transAus', 'lpBestand', 'letzterZugang', 'letzterAbgang'
        ],
        listClass: 'list',
        pagination: true,
        page: 10,
        plugins: [window.ListPagination({ left: 1, right: 1 })],
        item: '<tr><td class="lagereinheit text-center"></td><td class="lagerplatz text-center"></td><td class="articleNr text-center"></td><td class="bezeichnung text-center"></td><td class="transEin text-center"></td><td class="transAus text-center"></td><td class="lpBestand text-center"></td><td class="letzterZugang text-center"></td><td class="letzterAbgang text-center"></td></tr>'
    }).add(data);
};

const initContextMenus = (actions) => {
    document.querySelectorAll('[data-context-for]').forEach((menu) => {
        const table = document.getElementById(menu.dataset.contextFor);
        if (!table) {
            return;
        }

        let currentId = null;

        table.addEventListener('contextmenu', (event) => {
            const row = event.target instanceof Element ? event.target.closest('tr[data-id]') : null;
            if (!row) {
                event.preventDefault();
                return;
            }

            currentId = row.dataset.id;
            menu.style.cssText = 'top:' + event.clientY + 'px;left:' + event.clientX + 'px;position:absolute;z-index:9999;';
            menu.classList.remove('d-none');
            event.preventDefault();
        });

        document.addEventListener('click', (event) => {
            if (!(event.target instanceof Node) || !menu.contains(event.target)) {
                menu.classList.add('d-none');
            }
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                menu.classList.add('d-none');
            }
        });

        menu.querySelectorAll('[data-action]').forEach((button) => {
            button.addEventListener('click', () => {
                const parsedId = Number.parseInt(String(currentId), 10);
                if (!Number.isInteger(parsedId) || parsedId <= 0) {
                    showAlert('Fehler', 'Ungueltige Artikel-ID. Bitte Liste neu laden.', 'error');
                    menu.classList.add('d-none');
                    currentId = null;
                    return;
                }
                const articleId = String(parsedId);

                switch (button.dataset.action) {
                    case 'edit':
                        actions.edit(articleId);
                        break;
                    case 'delete':
                        actions.delete(articleId);
                        break;
                    case 'stock':
                        actions.stock(articleId);
                        break;
                    default:
                        console.warn('Unbekannte Aktion:', button.dataset.action);
                }

                menu.classList.add('d-none');
                currentId = null;
            });
        });
    });
};

const openStockOccupancy = async (articleId) => {
    const modalEl = document.getElementById('modalCenter');
    if (!modalEl) {
        return;
    }

    const dialog = modalEl.querySelector('.modal-dialog');
    if (dialog) {
        dialog.classList.add('modal-xl');
    }

    showLoading(modalEl, 'Lagerbelegung');

    try {
        const response = await fetch(ARTICLE_STOCK_URL_PREFIX + articleId);
        if (!response.ok) {
            throw new Error('HTTP ' + response.status);
        }

        const data = await response.json();
        if (!Array.isArray(data) || data.length === 0) {
            showAlert('Keine Daten', 'Fuer diesen Artikel wurden keine Lagerdaten gefunden.', 'info');
            return;
        }

        const modalTitle = modalEl.querySelector('.modal-title');
        const modalBody = modalEl.querySelector('.modal-body');
        if (!modalTitle || !modalBody) {
            return;
        }

        modalTitle.textContent = 'Lagerbelegung fuer Artikel ' + data[0].articleNr;
        renderStockOccupancyList(modalBody, data);
    } catch (error) {
        console.error(error);
        showAlert('Fehler', 'Die Lagerbelegung konnte nicht geladen werden.', 'error');
    }
};

document.addEventListener('DOMContentLoaded', () => {
    const articleOverview = document.getElementById('webwms-article-overview');
    if (!articleOverview) {
        return;
    }

    const articleList = createArticleList();
    initExportButton(articleList);
    loadArticles(articleList);

    const modal = new ModalHandler();

    initCreateButton(modal);

    initContextMenus({
        edit: (id) => modal.open({
            title: 'Artikel bearbeiten',
            url: ARTICLE_EDIT_URL_PREFIX + id,
        }),
        delete: (id) => modal.open({
            title: 'Artikel loeschen',
            url: ARTICLE_DELETE_URL_PREFIX + id,
            onError: () => showAlert('Fehler', 'Artikel konnte nicht geladen werden.', 'error'),
        }),
        stock: (id) => openStockOccupancy(id),
    });
});
