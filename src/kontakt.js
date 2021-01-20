export function highlight_organigramm(id) {
    highlight_organigramm_scroll(id);
}

export function highlight_organigramm_scroll(id) {
    let elem = document.getElementById(id);
    if (/box\\-[0-9]+\\-[0-9]+/.exec(elem.parentElement.id)) { elem = elem.parentElement; }
    elem.style.backgroundColor = 'rgba(0,0,0,0)';
    const rect = elem.getBoundingClientRect();
    const optimalPageYOffset = window.pageYOffset + rect.top + rect.height / 2 - window.innerHeight / 2;
    const nextPageYOffset = window.pageYOffset + (optimalPageYOffset - window.pageYOffset) / 4;
    if (nextPageYOffset <= 0) {
        window.scrollTo(0, 0);
        highlight_organigramm_color(id);
    } else if (document.getElementsByTagName('body')[0].offsetHeight - window.innerHeight <= nextPageYOffset) {
        window.scrollTo(0, document.getElementsByTagName('body')[0].offsetHeight - window.innerHeight);
        highlight_organigramm_color(id);
    } else if (Math.abs(nextPageYOffset - optimalPageYOffset) <= 3) {
        window.scrollTo(0, optimalPageYOffset);
        highlight_organigramm_color(id);
    } else {
        window.scrollTo(0, Math.round(nextPageYOffset));
        window.setTimeout(() => { highlight_organigramm_scroll(id); }, 50);
    }
}

export function highlight_organigramm_color(id) {
    let elem = document.getElementById(id);
    if (/box\\-[0-9]+\\-[0-9]+/.exec(elem.parentElement.id)) { elem = elem.parentElement; }
    for (let i = 0; i < 20; i++) {
        window.setTimeout(() => {
            elem.style.backgroundColor = `rgba(0,220,0,${Math.pow(Math.sin(i * Math.PI / 12), 2)})`;
        }, i * 100);
    }
}
