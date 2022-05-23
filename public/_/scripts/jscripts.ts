export function olz_toggle_vorstand(ident: string): void {
    const pelem = document.getElementById(`popup${ident}`);
    const selem = document.getElementById(`source${ident}`);
    if (!pelem || !selem) {
        return;
    }
    if (pelem.style.display === 'none') {
        pelem.style.display = 'block';
        pelem.style.marginTop = `${selem.offsetHeight}px`;
    } else {
        pelem.style.display = 'none';
    }
}


export function trim(stringToTrim: string): string {
    return stringToTrim.replace(/^\s+|\s+$/g, '');
}


/* EMAILADRESSE MASKIEREN (global) */
export function MailTo(name: string, domain: string, text: string, subject = ''): string {
    let mytext = '';
    const linktext = text;
    const email1 = name;
    const email2 = domain;
    const email3 = subject;
    const mailtoPrefix = 'mailto:';
    mytext = (`<a href="${mailtoPrefix}${email1}@${email2}?subject=${email3}" class="linkmail">${linktext}</a>`);
    const scriptElement = document.currentScript;
    const parentNode = scriptElement?.parentNode;
    if (!scriptElement || !parentNode) {
        return mytext;
    }
    if (/MailTo\(/.exec(scriptElement.innerHTML)) {
        const span = document.createElement('span');
        parentNode.insertBefore(span, scriptElement);
        span.innerHTML = mytext;
        return '';
    }
    return mytext;
}

/* MENÜ UNTERMENÜS ZEIGEN (menu.php) */
export function menu(menuid: string): void {
    const div = document.getElementById(menuid);
    if (!div) {
        return;
    }
    if (div.style.display === 'none') {
        div.style.display = 'block';
    } else {
        div.style.display = 'none';
    }
}

/* JAHREÜBERSICHT ZEIGEN (menu.php) */
export function show_year(year1: string, year2: string): void {
    const div1 = document.getElementById(year1);
    if (!div1) {
        return;
    }
    if (div1.style.display === 'none') {
        div1.style.display = 'block';
    } else {
        div1.style.display = 'none';
    }
    const div2 = document.getElementById(year2);
    if (!div2) {
        return;
    }
    if (div2.style.display === 'none') {
        div2.style.display = 'block';
    } else {
        div2.style.display = 'none';
    }
}

/* DOPPELBILD (global) */

export function expand(menuid: string): void {
    const div = document.getElementById(menuid);
    if (!div) {
        return;
    }
    div.style.display = 'block';
}
export function collapse(menuid: string): void {
    const div = document.getElementById(menuid);
    if (!div) {
        return;
    }
    div.style.display = 'none';
}

export function open_link(adresse: string): void {
    const url = adresse;
    window.open(url, '_newtab');
}

export function open_link2(adresse: string): void {
    const url = adresse;
    window.open(url, '');
}
