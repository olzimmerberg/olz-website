export function highlightOrganigramm(id: string): void {
    highlightOrganigrammScroll(id);
}

export function highlightOrganigrammScroll(id: string): void {
    let elem = document.getElementById(id);
    if (elem && /box\\-[0-9]+\\-[0-9]+/.exec(elem.parentElement?.id ?? '')) {
        elem = elem.parentElement;
    }
    if (!elem) {
        return;
    }
    elem.style.backgroundColor = 'rgba(0,0,0,0)';
    const rect = elem.getBoundingClientRect();
    const optimalPageYOffset = window.scrollY + rect.top + rect.height / 2 - window.innerHeight / 2;
    window.scrollTo({top: optimalPageYOffset, behavior: 'smooth'});
    window.setTimeout(() => {
        highlightOrganigrammColor(id);
    }, 200);
}

export function highlightOrganigrammColor(id: string): void {
    let elem = document.getElementById(id);
    if (elem && /box\\-[0-9]+\\-[0-9]+/.exec(elem.parentElement?.id ?? '')) {
        elem = elem.parentElement;
    }
    for (let i = 0; i < 20; i++) {
        window.setTimeout(() => {
            if (!elem) {
                return;
            }
            elem.style.backgroundColor = `rgba(0,220,0,${Math.pow(Math.sin(i * Math.PI / 12), 2)})`;
        }, i * 100);
    }
}
