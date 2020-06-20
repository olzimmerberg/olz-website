function resizeHeaderBar() {
    const headerBarElem = document.getElementById('header-bar');
    const isMinified = headerBarElem.className.indexOf('minified') !== -1;
    const shouldBeMinified = window.scrollY > 100;
    if (shouldBeMinified && !isMinified) {
        headerBarElem.className = `${headerBarElem.className} minified`;
    }
    if (!shouldBeMinified && isMinified) {
        headerBarElem.className = headerBarElem.className.replace(/\bminified\b/g, '');
    }
}
window.addEventListener('load', resizeHeaderBar);
window.addEventListener('scroll', resizeHeaderBar);

let isMenuOpen = false;
export function toggleMenu() {
    if (isMenuOpen) {
        closeMenu();
    } else {
        openMenu();
    }
}
function closeMenu() {
    const menuSwitchElem = document.getElementById('header-bar');
    menuSwitchElem.className = menuSwitchElem.className.replace('menu-opened', 'menu-closed');
    isMenuOpen = false;
}
function openMenu() {
    const menuSwitchElem = document.getElementById('header-bar');
    menuSwitchElem.className = menuSwitchElem.className.replace('menu-closed', 'menu-opened');
    isMenuOpen = true;
}
window.addEventListener('resize', () => {
    if (window.innerWidth > 1024 && isMenuOpen) {
        closeMenu();
    }
});
