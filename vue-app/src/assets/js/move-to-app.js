/**
 * Move elements on app load
 */
function moveToApp(className = 'move-to-app', propName = 'data-to') {
    const elems = document.querySelectorAll(`.${className}[${propName}]`);
    if (elems.length) {
        Array.from(elems).forEach(el => {
            const content = el.innerHTML.trim();
            const selector = el.getAttribute(propName);
            const to = document.querySelector(selector);
            if (to && content) {
                to.innerHTML = content;
            }
        });
    }
}

export default moveToApp;