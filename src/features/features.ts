window.addEventListener('load', () => {
    const featuresConfig = localStorage.getItem('FEATURES');
    const features = featuresConfig?.split(/\s*,\s*/) ?? [];
    for (const feature of features) {
        const elems = document.getElementsByClassName(feature);
        for (let i = 0; i < elems.length; i++) {
            const elem = elems[i];
            if (elem.classList.contains('feature')) {
                elem.classList.add('enabled');
            }
        }
    }
});

// TODO: remove dummy export
export default null;
