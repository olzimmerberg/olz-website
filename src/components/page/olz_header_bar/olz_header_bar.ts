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
    menuSwitchElem.className = menuSwitchElem.className.replace('menu-opened', 'menu-closed');
    isMenuOpen = false;
}

function openMenu(): void {
    const menuSwitchElem = document.getElementById('header-bar');
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
    const isShown = (elem.style.display === 'block');
    document.getElementById('ranking-kids-2020').style.display = 'none';
    document.getElementById('ranking-kids-2019').style.display = 'none';
    document.getElementById('ranking-junds-2020').style.display = 'none';
    document.getElementById('ranking-junds-2019').style.display = 'none';
    elem.style.display = (isShown ? 'none' : 'block');
    return false;
}
