import {olzInitFeatures} from './OlzFeatureToggle';

describe('olzInitFeatures', () => {
    function getCssDisplayById(id: string) {
        const elem = document.getElementById(id);
        if (!elem) {
            return undefined;
        }
        const cssObj = window.getComputedStyle(elem, null);
        return cssObj.getPropertyValue('display');
    }

    beforeEach(() => {
        const elem = document.createElement('style');
        elem.setAttribute('type', 'text/css');
        elem.innerHTML = `
            .feature {
                display: none !important;
            }
        `;
        document.head.appendChild(elem);
    });

    it('works when feature is present', () => {
        document.body.innerHTML = '<div id="feature-elem" class="feature is-test">TEST<div>';
        olzInitFeatures('is-test');
        expect(document.getElementById('olz-features-css')?.innerHTML)
            .toBe('.feature.is-test { display: block !important; }\n\n');
        expect(getCssDisplayById('feature-elem')).toEqual('block');
    });

    it('works when feature is absent', () => {
        document.body.innerHTML = '<div id="feature-elem" class="feature is-prod">PROD</div>';
        olzInitFeatures('is-test');
        expect(document.getElementById('olz-features-css')?.innerHTML)
            .toBe('.feature.is-test { display: block !important; }\n\n');
        expect(getCssDisplayById('feature-elem')).toBe('none');
    });

    it('works when element does not have `feature` class', () => {
        document.body.innerHTML = '<div id="feature-elem" class="is-test">TEST</div>';
        olzInitFeatures('is-test');
        expect(document.getElementById('olz-features-css')?.innerHTML)
            .toBe('.feature.is-test { display: block !important; }\n\n');
        expect(getCssDisplayById('feature-elem')).toBe('block');
    });

    it('works with multiple enabled features', () => {
        document.body.innerHTML = '<div id="feature-elem" class="is-test">TEST</div>';
        olzInitFeatures('is-test,is-deterministic');
        expect(document.getElementById('olz-features-css')?.innerHTML)
            .toBe('.feature.is-test { display: block !important; }\n\n.feature.is-deterministic { display: block !important; }\n\n');
        expect(getCssDisplayById('feature-elem')).toBe('block');
    });
});
