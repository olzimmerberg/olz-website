export function olzInitFeatures(featuresConfig: string): void {
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
}

window.addEventListener('load', () => {
    const featuresConfig = localStorage.getItem('FEATURES');
    olzInitFeatures(featuresConfig);
});
