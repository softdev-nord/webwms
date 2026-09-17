'use strict';

(() => {
    let contextMenuElement = null;

    const removeContextMenu = () => {
        if (contextMenuElement) {
            contextMenuElement.remove();
            contextMenuElement = null;
        }
    };

    const showModal = (modalElement) => {
        if (!modalElement) {
            return;
        }

        if (window.bootstrap && window.bootstrap.Modal) {
            const modal = window.bootstrap.Modal.getOrCreateInstance(modalElement);
            modal.show();
            return;
        }

        modalElement.style.display = 'block';
        modalElement.classList.add('show');
    };

    const loadFragmentIntoElement = async (url, sourceSelector, targetElement) => {
        if (!targetElement) {
            return;
        }

        const response = await fetch(url, {
            method: 'GET',
            cache: 'no-store',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        if (!response.ok) {
            throw new Error('Request failed with status ' + response.status);
        }

        const html = await response.text();
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        const source = doc.querySelector(sourceSelector);

        targetElement.innerHTML = source ? source.innerHTML : '';
    };

    const getArticleNumberFromRow = (rowElement) => {
        if (!rowElement) {
            return null;
        }

        const cells = rowElement.querySelectorAll('td');
        if (cells.length < 2) {
            return null;
        }

        return cells[1].textContent.trim();
    };

    const showContextMenu = (event, rowElement) => {
        removeContextMenu();

        const articleNumber = getArticleNumberFromRow(rowElement);
        if (!articleNumber) {
            return;
        }

        const menu = document.createElement('div');
        menu.style.position = 'fixed';
        menu.style.left = event.clientX + 'px';
        menu.style.top = event.clientY + 'px';
        menu.style.zIndex = '2000';
        menu.style.backgroundColor = '#fff';
        menu.style.border = '1px solid #ccc';
        menu.style.boxShadow = '0 2px 8px rgba(0, 0, 0, 0.2)';
        menu.style.borderRadius = '4px';
        menu.style.padding = '4px';

        const button = document.createElement('button');
        button.type = 'button';
        button.textContent = 'Artikel Lagerbelegungen';
        button.style.display = 'block';
        button.style.border = '0';
        button.style.background = 'transparent';
        button.style.padding = '8px 10px';
        button.style.cursor = 'pointer';
        button.style.whiteSpace = 'nowrap';
        button.addEventListener('click', () => {
            removeContextMenu();
            getStockOccupancyByArticle(articleNumber);
        });

        menu.appendChild(button);
        document.body.appendChild(menu);
        contextMenuElement = menu;
    };

    document.addEventListener('change', async (event) => {
        const target = event.target;
        if (!(target instanceof HTMLSelectElement) || target.id !== 'selectStock') {
            return;
        }

        const selectedOption = target.selectedOptions[0];
        if (!selectedOption) {
            return;
        }

        const stockCode = selectedOption.textContent.substring(0, 3);
        const url = '/stock_occupancy_ajax/stock_location_ln/' + stockCode;

        try {
            await loadFragmentIntoElement(url, '#stockLocationDiv', document.querySelector('#stockLocationDiv'));
            await loadFragmentIntoElement(url, '#stockSystem', document.querySelector('#stockSystem'));
        } catch (error) {
            // Keep current state if partial refresh fails.
            console.error(error);
        }
    });

    document.addEventListener('click', async (event) => {
        const coordinateElement = event.target instanceof Element ? event.target.closest('#stockCoordinate') : null;
        if (!coordinateElement) {
            removeContextMenu();
            return;
        }

        const modalDialog = document.querySelector('#modalCenter .modal-dialog');
        if (modalDialog instanceof HTMLElement) {
            modalDialog.style.maxWidth = '70%';
        }

        const currentCoordinate = coordinateElement.getAttribute('data-target');
        const stockComplete = coordinateElement.getAttribute('data-ln-komplett') || '';
        if (!currentCoordinate) {
            return;
        }

        const url = '/stock_occupancy_ajax/' + currentCoordinate;

        const currentCoordinateInput = document.querySelector('#currentCoordinate');
        if (currentCoordinateInput instanceof HTMLInputElement) {
            currentCoordinateInput.value = currentCoordinate;
        }

        const modalTitle = document.querySelector('#modalCenter .modal-title');
        if (modalTitle) {
            modalTitle.innerHTML = 'Lagerplatz Details ' + stockComplete;
        }

        const modalElement = document.querySelector('#modalCenter');
        showModal(modalElement);

        try {
            const response = await fetch(url, {
                method: 'GET',
                cache: 'no-store',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) {
                throw new Error('Request failed with status ' + response.status);
            }

            const html = await response.text();
            const modalContent = document.querySelector('#modal-content-ajax');
            if (modalContent) {
                modalContent.innerHTML = html;
            }

            await loadFragmentIntoElement(url, '#stockLocationTable', document.querySelector('#stockLocationTable'));
            await loadFragmentIntoElement(url, '#stockSystem', document.querySelector('#stockSystem'));
        } catch (error) {
            console.error(error);
        }
    });

    document.addEventListener('contextmenu', (event) => {
        const rowElement = event.target instanceof Element
            ? event.target.closest('#showStockDetailTable tr')
            : null;

        if (!rowElement) {
            removeContextMenu();
            return;
        }

        event.preventDefault();
        showContextMenu(event, rowElement);
    });

    document.addEventListener('click', (event) => {
        if (!contextMenuElement) {
            return;
        }

        const target = event.target;
        if (!(target instanceof Node) || !contextMenuElement.contains(target)) {
            removeContextMenu();
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            removeContextMenu();
        }
    });

    async function getStockOccupancyByArticle(articleNumber) {
        const url = '/stock_occupancy_ajax_article/' + articleNumber;
        const modalDialog = document.querySelector('#modalCenter .modal-dialog');
        if (modalDialog instanceof HTMLElement) {
            modalDialog.style.maxWidth = '90%';
        }

        const modalTitle = document.querySelector('#modalCenter .modal-title');
        if (modalTitle) {
            modalTitle.textContent = 'Artikel Lagerbelegungen';
        }

        const modalContent = document.querySelector('#modal-content-ajax');
        if (modalContent) {
            modalContent.innerHTML = '<div class="modal-body"></div>';
        }

        const modalElement = document.querySelector('#modalCenter');
        showModal(modalElement);

        try {
            const response = await fetch(url, {
                method: 'GET',
                cache: 'no-store',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) {
                throw new Error('Request failed with status ' + response.status);
            }

            if (modalContent) {
                modalContent.innerHTML = await response.text();
            }
        } catch (error) {
            console.error(error);
        }
    }
})();
