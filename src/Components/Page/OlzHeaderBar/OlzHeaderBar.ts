export function resizeHeaderBar(): void {
    const isMinified = document.body.className.indexOf('header-minified') !== -1;
    const shouldBeMinified = window.scrollY > 30;
    if (shouldBeMinified && !isMinified) {
        document.body.className = `${document.body.className} header-minified`;
    }
    if (!shouldBeMinified && isMinified) {
        document.body.className = document.body.className.replace(/\bheader-minified\b/g, '');
    }
}
window.addEventListener('load', resizeHeaderBar);
window.addEventListener('scroll', resizeHeaderBar);

let isMenuOpen = false;
export function toggleMenu(): void {
    if (isMenuOpen) {
        closeMenu();
    } else {
        openMenu();
    }
}

function closeMenu(): void {
    const menuSwitchElem = document.getElementById('header-bar');
    if (!menuSwitchElem) {
        return;
    }
    menuSwitchElem.className = menuSwitchElem.className.replace('menu-opened', 'menu-closed');
    isMenuOpen = false;
}

function openMenu(): void {
    const menuSwitchElem = document.getElementById('header-bar');
    if (!menuSwitchElem) {
        return;
    }
    menuSwitchElem.className = menuSwitchElem.className.replace('menu-closed', 'menu-opened');
    isMenuOpen = true;
}

window.addEventListener('resize', () => {
    if (window.innerWidth > 1024 && isMenuOpen) {
        closeMenu();
    }
});
