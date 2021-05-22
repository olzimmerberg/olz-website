import {olzInitFeatures} from './features';

describe('olzInitFeatures', () => {
    it('works when feature is present', () => {
        document.body.innerHTML = '<div id="feature-elem" class="feature is-test">TEST<div>';
        olzInitFeatures('is-test');
        expect(document.getElementById('feature-elem').classList.contains('enabled')).toBe(true);
    });

    it('works when feature is absent', () => {
        document.body.innerHTML = '<div id="feature-elem" class="feature is-prod">PROD</div>';
        olzInitFeatures('is-test');
        expect(document.getElementById('feature-elem').classList.contains('enabled')).toBe(false);
    });

    it('works when element does not have `feature` class', () => {
        document.body.innerHTML = '<div id="feature-elem" class="is-test">TEST</div>';
        olzInitFeatures('is-test');
        expect(document.getElementById('feature-elem').classList.contains('enabled')).toBe(false);
    });
});
