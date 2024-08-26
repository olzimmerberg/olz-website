export function olzPopupToggle(ident: string): boolean {
    const popupElem = document.getElementById(`popup${ident}`);
    const triggerElem = document.getElementById(`trigger${ident}`);
    const hiddenPopupHost = triggerElem?.parentElement;
    if (!popupElem || !triggerElem || !hiddenPopupHost) {
        return false;
    }
    if (popupElem.style.display === 'block') {
        popupElem.style.display = 'none';
        document.body.removeChild(popupElem);
        hiddenPopupHost.appendChild(popupElem);
    } else {
        hiddenPopupHost.removeChild(popupElem);
        document.body.appendChild(popupElem);
        popupElem.style.display = 'block';
        const rect = triggerElem.getBoundingClientRect();
        const popupRect = popupElem.getBoundingClientRect();
        const top = rect.top + rect.height + 5;
        const left = rect.left + rect.width / 2 - popupRect.width / 2;
        popupElem.style.top = `${Math.round(top)}px`;
        popupElem.style.left = `${Math.round(left)}px`;
    }
    return false;
}
