/* eslint-disable wrap-iife, func-names */
(function () {
    let styleElem = document.getElementById('style-overrides');
    if (!styleElem) {
        styleElem = document.createElement('style');
        styleElem.setAttribute('type', 'text/css');
        styleElem.setAttribute('id', 'style-overrides');
        styleElem.innerHTML = `
        * {
            transition: none !important;
        }
        `;
        document.head.appendChild(styleElem);
    }

    const flakyElements = document.querySelectorAll('.test-flaky');
    for (let i = 0; i < flakyElements.length; i++) {
        const rect = flakyElements[i].getBoundingClientRect();
        const cover = document.createElement('div');
        document.documentElement.appendChild(cover);
        cover.id = `flaky-${i}`;
        cover.className = 'flaky-cover';
        cover.style.position = 'absolute';
        cover.style.backgroundColor = 'black';
        cover.style.pointerEvents = 'none';
        cover.style.zIndex = 999999;
        cover.style.width = `${Math.ceil(rect.width + 1)}px`;
        cover.style.height = `${Math.ceil(rect.height + 1)}px`;
        cover.style.top = `${Math.floor(rect.top)}px`;
        cover.style.left = `${Math.floor(rect.left)}px`;
    }

    const inputElements = document.querySelectorAll('input');
    for (let i = 0; i < inputElements.length; i++) {
        inputElements[i].setAttribute('spellcheck', 'false');
    }

    const textareaElements = document.querySelectorAll('textarea');
    for (let i = 0; i < textareaElements.length; i++) {
        textareaElements[i].setAttribute('spellcheck', 'false');
    }

    if (document.activeElement) {
        document.activeElement.blur();
    }
})();
