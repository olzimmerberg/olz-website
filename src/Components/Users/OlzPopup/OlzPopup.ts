const isShownByPopupIdent: {[ident: string]: boolean} = {};

export function olzPopupToggle(ident: string): boolean {
    const popupElem = document.getElementById(`popup${ident}`);
    if (!popupElem) {
        return false;
    }
    if (popupElem.style.display === 'block') {
        olzPopupHide(ident);
    } else {
        olzPopupShow(ident);
    }
    return false;
}

function olzPopupShow(ident: string):boolean {
    const popupElem = document.getElementById(`popup${ident}`);
    const triggerElem = document.getElementById(`trigger${ident}`);
    const hiddenPopupHost = triggerElem?.parentElement;
    if (!popupElem || !triggerElem || !hiddenPopupHost) {
        return false;
    }

    hiddenPopupHost.removeChild(popupElem);
    document.body.appendChild(popupElem);
    popupElem.style.display = 'block';
    const rect = triggerElem.getBoundingClientRect();
    const popupRect = popupElem.getBoundingClientRect();
    const top = rect.top + rect.height + 5;
    const left = rect.left + rect.width / 2 - popupRect.width / 2;
    popupElem.style.top = `${Math.round(top)}px`;
    popupElem.style.left = `${Math.round(left)}px`;
    isShownByPopupIdent[ident] = true;
    return false;
}

function olzPopupHide(ident: string): boolean {
    const popupElem = document.getElementById(`popup${ident}`);
    const triggerElem = document.getElementById(`trigger${ident}`);
    const hiddenPopupHost = triggerElem?.parentElement;
    if (!popupElem || !hiddenPopupHost) {
        return false;
    }

    popupElem.style.display = 'none';
    document.body.removeChild(popupElem);
    hiddenPopupHost.appendChild(popupElem);
    isShownByPopupIdent[ident] = false;
    return false;
}

let timeout: number|null = null;
window.addEventListener('scroll', () => {
    if (timeout !== null) {
        return;
    }
    timeout = window.setTimeout(() => {
        for (const ident of Object.keys(isShownByPopupIdent)) {
            const isShown = isShownByPopupIdent[ident];
            if (isShown) {
                olzPopupHide(ident);
            }
        }
        timeout = null;
    }, 300);
});
