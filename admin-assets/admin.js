
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
        setBehavior();
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
        setBehavior();
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
});

function setBehavior() {
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