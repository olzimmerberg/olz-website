export function highlight_menu(e) {
    const menuContainerElem = document.getElementById('menu-container');
    const menuContainerStyle = window.getComputedStyle(menuContainerElem);
    const menuContainerOpacity = menuContainerStyle.getPropertyValue('opacity');
    if (menuContainerOpacity < 0.5) {
        return;
    }
    const href = e.currentTarget.getAttribute('href');
    const elem = document.getElementById(`menu_a_page_${href}`);
    const rect = elem.getBoundingClientRect();
    const pointer = document.createElement('img');
    pointer.style.pointerEvents = 'none';
    pointer.style.position = 'fixed';
    pointer.style.zIndex = '1000';
    pointer.style.top = `${rect.top + rect.height / 2 - 50}px`;
    pointer.style.left = `${rect.left + rect.width}px`;
    pointer.style.height = '100px';
    pointer.style.border = '0px';
    pointer.src = 'icns/arrow_red.svg';
    pointer.id = `highlight_menu_${href}`;
    document.documentElement.appendChild(pointer);
    window.setTimeout(() => highlight_menu_ani(href, 0), 100);
}

export function highlight_menu_ani(href, stepArg) {
    let step = stepArg;
    if (step === 8) { step = 0; }
    const elem = document.getElementById(`highlight_menu_${href}`);
    if (!elem) { return; }
    elem.style.marginLeft = `${Math.sin(step * 2 * 3.1415 / 8) * 4}px`;
    window.setTimeout(() => highlight_menu_ani(href, step + 1), 100);
}

export function unhighlight_menu(e) {
    const href = e.currentTarget.getAttribute('href');
    const elem = document.getElementById(`highlight_menu_${href}`);
    if (elem) {
        elem.parentElement.removeChild(elem);
    }
}
