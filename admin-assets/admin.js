
window.addEventListener('load', evt => {
    const select = document.querySelector('#langs');
    const editButton = document.querySelector('#edit_button');
    const cancelButton = document.querySelector('#cancel_saving');
    const saveButton = document.querySelector('#save_language');
    const saveSettingsButton = document.querySelector('#save_settings');
    const createButton = document.querySelector('#create_button');
    const translator = document.querySelector('.translator');
    const stage = translator.querySelector('.strings');
    const field = document.querySelector('textarea#value-string');

    saveSettingsButton.addEventListener('click', () => {
        document.querySelector('input#action').value = 'save-settings';
        document.querySelector('form#vuewp-form').submit();
    });

    editButton.addEventListener('click', evt => {
        const lng = select.value;
        const content = document.querySelector(`[data-lang="${lng}"]`);
        stage.innerHTML = content.innerHTML;
        translator.style.display = 'block';
        translator.setAttribute('data-lang', lng);
        translator.removeAttribute('data-new');
        setTranslationsBehavior();
    });
    
    cancelButton.addEventListener('click', evt => {
        stage.innerHTML = '';
        translator.style.display = 'none';
        translator.removeAttribute('data-lang');
        translator.removeAttribute('data-new');
        document.querySelector('input#lang').value = '';
        document.querySelector('input#strings').value = '';
    });

    field.addEventListener('input', evt => {
        const span = document.querySelector('.translator .str-line.selected .val');
        if (span) {
            span.innerHTML = evt.target.value;
        }
    });

    createButton.addEventListener('click', evt => {
        const code = document.querySelector('#new_code');
        const name = document.querySelector('#new_name');
        if (!code.value || !name.value) {
            if (!code.value) code.focus();
            else if (!name.value) name.focus();
            return false;
        }
        const ids = getIds();
        let htm = `<h3>Creating new language file '${code.value}.json' (${name.value})</h3>\n`;
        ids.forEach(id => {
            htm += `<div class="str-line">\n`;
            htm += `    <span class="key">${id}</span>\n`;
            htm += `    <span class="val">${'language_name' == id ? name.value : ''}</span>\n`;
            htm += `</div>\n`;
        });
        stage.innerHTML = htm;
        translator.style.display = 'block';
        translator.setAttribute('data-lang', code.value);
        translator.setAttribute('data-new', '1');
        setTranslationsBehavior();
    });

    saveButton.addEventListener('click', saveStrings);

    const dismissMessage = document.querySelector('button.notice-dismiss');
    if (dismissMessage) {
        dismissMessage.addEventListener('click', evt => {
            const notice = evt.target.closest('.notice');
            notice.parentNode.removeChild(notice);
        });
    }

    const tabEl = document.querySelector('.tabs');
    if (tabEl) {
        const tabs = tabEl.querySelectorAll('.tab-links a');
        Array.from(tabs).forEach(tab => {
            tab.addEventListener('click', evt => {
                evt.preventDefault();
                const name = evt.target.getAttribute('data-tab');
                tabEl.setAttribute('data-tab', name);
            });
        });
    }

    // routes
    const comps = document.querySelector('select#route-component');
    comps.addEventListener('input', evt => {
        const component = evt.target.value;
        // console.log('Selected view', vuewpViews[component])
        let htm = '';
        if (component) {
            document.querySelector('#route-path').value = '/';
            const cmp = vuewpViews[component] ?? {};
            if (cmp.params) {
                htm = 'Required params: ';
                let prms = [];
                cmp.params.forEach(prm => {
                    prms.push(`<code data-param="${prm}" class="route-param">${prm}</code>`);
                });
                htm += prms.join(', ');
            } else {
                htm = 'No params required';
            }
        }
        document.querySelector('.route-variables').innerHTML = htm;
    });
    
    document.body.addEventListener('click', evt => {
        if (evt.target.matches('.route-param')) {
            evt.preventDefault();
            const field = document.querySelector('#route-path');
            let val = field.value;
            const param = evt.target.getAttribute('data-param');
            val = val.replace(`/:${param}`, '').replace(/\/$/, '') + `/:${param}`;
            field.value = val;
        }
        if (evt.target.matches('.remove-route')) {
            evt.preventDefault();
            const index = evt.target.getAttribute('data-index');
            deleteRoute(index);
        }
        if (evt.target.matches('.up-route')) {
            evt.preventDefault();
            const index = evt.target.getAttribute('data-index');
            upRoute(index);
        }
        if (evt.target.matches('.down-route')) {
            evt.preventDefault();
            const index = evt.target.getAttribute('data-index');
            downRoute(index);
        }
    });
    
    document.querySelector('#add-route').addEventListener('click', () => addRoute());
    document.querySelector('#save-routes').addEventListener('click', () => saveRoutes());

    setVuewpRoutes();
    populateRoutesList();
});

function populateRoutesList() {
    const routes = document.querySelector('div.current-routes');
    const addRoute = (p, c, i) => {
        const route = document.createElement('div');
        const path = document.createElement('span');
        const component = document.createElement('span');
        const actions = document.createElement('span');
        actions.className = 'actions';
        path.className = 'route-path';
        path.innerHTML = p;
        component.className = 'route-component';
        component.innerHTML = ` (${c})`;
        actions.innerHTML = ` <a href="#" class="remove-route" data-index="${i}">Remove</a>`;
        actions.innerHTML += ` | <a href="#" class="up-route" data-index="${i}">Up</a>`;
        actions.innerHTML += ` | <a href="#" class="down-route" data-index="${i}">Down</a>`;
        route.appendChild(path);
        route.appendChild(component);
        route.appendChild(actions);
        routes.appendChild(route);
    };
    routes.innerHTML = '';
    vuewpRoutes.forEach((route, index) => {
        addRoute(route.path, route.component, index);
    });
}

function upRoute(index) {
    index = Number(index);
    if (index == 0) {
        return;
    }
    let arr = vuewpRoutes;
    const r = arr[index];
    arr.splice(index, 1);
    arr.splice(index - 1, 0, r);
    vuewpRoutes = arr;
    setVuewpRoutes();
    populateRoutesList();
}

function downRoute(index) {
    index = Number(index);
    const next = index + 1;
    let arr = vuewpRoutes;
    if (next == arr.length) {
        return;
    }
    const r = arr[index];
    arr.splice(Number(index), 1);
    arr.splice(next, 0, r);
    vuewpRoutes = arr;
    setVuewpRoutes();
    populateRoutesList();
}

function setVuewpRoutes() {
    if (window.vuewpRoutes) {
        document.querySelector('input#routes').value = JSON.stringify(vuewpRoutes);
    }
}

function deleteRoute(index) {
    if (window.vuewpRoutes && vuewpRoutes[index]) {
        vuewpRoutes.splice(index, 1);
        setVuewpRoutes();
    }
}

function addRoute() {
    const path = document.querySelector('input#route-path').value;
    const component = document.querySelector('select#route-component').value;
    if (path && component) {
        if (routeExists(path, component)) {
            const msg = 'The sent path or component is already in use.';
            const err = document.querySelector('.route-error')
            err.innerHTML = msg;
            setTimeout(() => err.innerHTML = '', 5000);
            return;
        }
        vuewpRoutes.push({ path, component });
        setVuewpRoutes();
        populateRoutesList();
    }
}

function routeExists(path, comp) {
    let exists = false;
    vuewpRoutes.forEach(route => {
        if (route.path == path || route.component == comp) {
            exists = true;
        }
    });
    return exists;
}

function saveRoutes() {
    document.querySelector('input#action').value =  'save-routes';
    document.querySelector('form#vuewp-form').submit();
}

function setTranslationsBehavior() {
    const lines = document.querySelectorAll('.translator .str-line');
    Array.from(lines).forEach(line => {
        line.addEventListener('click', evt => {
            const key = line.querySelector('.key').innerHTML.trim();
            const value = line.querySelector('.val');
            const val = value.innerHTML.trim();
            checkLine(line);
            document.querySelector('textarea#key-string').value = key;
            const field = document.querySelector('textarea#value-string');
            field.value = val;
            field.focus();
            field.select();
        })
    });
}

function checkLine(checkedLine) {
    const lines = document.querySelectorAll('.translator .str-line');
    Array.from(lines).forEach(line => {
        if (line.isSameNode(checkedLine)) {
            line.classList.add('selected');
        } else {
            line.classList.remove('selected');
        }
    });
}

function getIds() {
    const lines = document.querySelectorAll('.lang-set:first-child .str-line');
    let ids = [];
    Array.from(lines).forEach(line => {
        ids.push(line.querySelector('.key').innerHTML.trim());
    });
    return ids;
}

function saveStrings() {
    const translator = document.querySelector('.translator');
    const lang = translator.getAttribute('data-lang');
    const isNew = translator.getAttribute('data-new');
    const lines = translator.querySelectorAll('.str-line');
    let json = {};
    Array.from(lines).forEach(line => {
        const key = line.querySelector('.key').innerHTML.trim();
        const value = line.querySelector('.val').innerHTML.trim();
        json[key] = value;
    });
    document.querySelector('input#lang').value = lang;
    document.querySelector('input#strings').value = JSON.stringify(json);
    document.querySelector('input#action').value = isNew ? 'create-lang' : 'save-lang';
    document.querySelector('form#vuewp-form').submit();
}