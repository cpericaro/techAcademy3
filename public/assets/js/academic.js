(function () {
    'use strict';

    function endpoint(resource, action, parameters) {
        const url = new URL('index.php', window.location.href);
        url.searchParams.set('resource', resource);
        url.searchParams.set('action', action);

        if (parameters) {
            Object.keys(parameters).forEach(function (key) {
                url.searchParams.set(key, parameters[key]);
            });
        }

        return url;
    }

    async function request(resource, action, options) {
        const settings = options || {};
        const url = endpoint(resource, action, settings.parameters);
        const response = await fetch(url, {
            method: settings.method || 'GET',
            body: settings.body || null,
            headers: { 'Accept': 'application/json' },
        });
        const body = await response.json().catch(function () {
            return { error: { message: 'A resposta do servidor não é válida.' } };
        });

        if (!response.ok) {
            throw new Error(body.error && body.error.message ? body.error.message : 'Não foi possível concluir a operação.');
        }

        return body;
    }

    function showMessage(element, message, type) {
        if (!element) {
            return;
        }

        element.hidden = false;
        element.textContent = message;
        element.className = type === 'error' ? 'notice' : 'notice';
        element.setAttribute('role', type === 'error' ? 'alert' : 'status');
    }

    function clearElement(element) {
        while (element.firstChild) {
            element.removeChild(element.firstChild);
        }
    }

    function createCell(value) {
        const cell = document.createElement('td');
        cell.textContent = value === null || value === undefined || value === '' ? 'Não informado' : String(value);
        return cell;
    }

    function renderTable(container, columns, rows, emptyMessage, onRow) {
        clearElement(container);

        if (!rows.length) {
            const empty = document.createElement('p');
            empty.textContent = emptyMessage;
            container.appendChild(empty);
            return;
        }

        const table = document.createElement('table');
        const head = document.createElement('thead');
        const headRow = document.createElement('tr');
        const body = document.createElement('tbody');

        columns.forEach(function (column) {
            const cell = document.createElement('th');
            cell.scope = 'col';
            cell.textContent = column.label;
            headRow.appendChild(cell);
        });
        if (onRow) {
            const actionHeader = document.createElement('th');
            actionHeader.scope = 'col';
            actionHeader.textContent = 'Ações';
            headRow.appendChild(actionHeader);
        }
        head.appendChild(headRow);

        rows.forEach(function (row) {
            const tableRow = document.createElement('tr');
            columns.forEach(function (column) {
                tableRow.appendChild(createCell(column.value(row)));
            });

            if (onRow) {
                const actions = document.createElement('td');
                onRow(actions, row);
                tableRow.appendChild(actions);
            }
            body.appendChild(tableRow);
        });

        table.appendChild(head);
        table.appendChild(body);
        container.appendChild(table);
    }

    function bindForms(refresh) {
        document.querySelectorAll('form[data-api-form]:not([data-api-ignore])').forEach(function (form) {
            form.addEventListener('submit', async function (event) {
                event.preventDefault();
                const message = document.querySelector('[data-page-message]');
                const submitButton = form.querySelector('button[type="submit"]');

                if (form.dataset.confirm && !window.confirm(form.dataset.confirm)) {
                    return;
                }

                if (submitButton) {
                    submitButton.disabled = true;
                }

                try {
                    const formData = new FormData(form);
                    const method = (form.method || 'POST').toUpperCase();
                    const parameters = {};
                    formData.forEach(function (value, key) {
                        parameters[key] = value;
                    });
                    const result = await request(form.dataset.resource, form.dataset.action, method === 'GET' ? {
                        method: method,
                        parameters: parameters,
                    } : {
                        method: method,
                        body: formData,
                    });
                    const successMessage = result.data && result.data.message
                        ? result.data.message
                        : 'Operação realizada com sucesso.';
                    showMessage(message, successMessage, 'success');

                    if (form.dataset.reset === 'true') {
                        form.reset();
                    }
                    if (refresh) {
                        await refresh();
                    }
                } catch (error) {
                    showMessage(message, error.message, 'error');
                } finally {
                    if (submitButton) {
                        submitButton.disabled = false;
                    }
                }
            });
        });
    }

    window.PlanteraAcademic = {
        bindForms: bindForms,
        endpoint: endpoint,
        renderTable: renderTable,
        request: request,
        showMessage: function (message, type) {
            showMessage(document.querySelector('[data-page-message]'), message, type);
        },
    };
}());
