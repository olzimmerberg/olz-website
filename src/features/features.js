window.addEventListener('load', () => {
    const featuresConfig = localStorage.getItem('FEATURES');
    const features = featuresConfig.split(/\s*,\s*/);
    for (const feature of features) {
        const elems = document.getElementsByClassName(feature);
        for (const elem of elems) {
            if (elem.classList.contains('feature')) {
                elem.classList.add('enabled');
            }
        }
    }
});