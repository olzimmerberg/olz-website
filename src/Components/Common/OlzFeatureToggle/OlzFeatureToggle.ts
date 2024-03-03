import './OlzFeatureToggle.scss';

const STYLE_ELEMENT_ID = 'olz-features-css';

export function olzInitFeatures(featuresConfig: string): void {
    const features = featuresConfig?.split(/\s*,\s*/) ?? [];
    let cssOut = '';
    for (const feature of features) {
        cssOut += `.feature.${feature} { display: block !important; }\n\n`;
    }
    let styleElem = document.getElementById(STYLE_ELEMENT_ID);
    if (!styleElem) {
        styleElem = document.createElement('style');
        styleElem.setAttribute('type', 'text/css');
        styleElem.setAttribute('id', STYLE_ELEMENT_ID);
    }
    styleElem.innerHTML = cssOut;
    document.head.appendChild(styleElem);
}

window.addEventListener('load', () => {
    const featuresConfig = localStorage.getItem('FEATURES');
    if (!featuresConfig) {
        return;
    }
    olzInitFeatures(featuresConfig);
});
