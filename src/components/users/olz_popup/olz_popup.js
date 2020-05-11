export function olzPopupToggle(ident) {
    const popupElem = document.getElementById(`popup${ident}`);
    const triggerElem = document.getElementById(`trigger${ident}`);
    if (popupElem.style.display === 'block') {
        popupElem.style.display = 'none';
    } else {
        popupElem.style.display = 'block';
        popupElem.style.marginTop = `${triggerElem.offsetHeight}px`;
    }
}
