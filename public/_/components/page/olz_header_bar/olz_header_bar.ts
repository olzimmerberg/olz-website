export function resizeHeaderBar(): void {
    const headerBarElem = document.getElementById('header-bar');
    if (!headerBarElem) {
        return;
    }
    const isMinified = headerBarElem.className.indexOf('minified') !== -1;
    const shouldBeMinified = window.pageYOffset > 100;
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

export function headerToggle(ident: string): boolean {
    const elem = document.getElementById(ident);
    const kids2019Elem = document.getElementById('ranking-kids-2019');
    const kids2020Elem = document.getElementById('ranking-kids-2020');
    const junds2019Elem = document.getElementById('ranking-junds-2019');
    const junds2020Elem = document.getElementById('ranking-junds-2020');
    if (!elem || !kids2019Elem || !kids2020Elem || !junds2019Elem || !junds2020Elem) {
        return false;
    }
    const isShown = (elem.style.display === 'block');
    kids2020Elem.style.display = 'none';
    kids2019Elem.style.display = 'none';
    junds2020Elem.style.display = 'none';
    junds2020Elem.style.display = 'none';
    elem.style.display = (isShown ? 'none' : 'block');
    return false;
}
