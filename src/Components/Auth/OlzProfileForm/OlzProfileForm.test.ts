/* eslint-env jasmine */

import {getUsernameSuggestion} from './OlzProfileForm';

describe('getUsernameSuggestion', () => {
    it('works', () => {
        expect(getUsernameSuggestion('First', 'Last')).toBe('first.last');
        expect(getUsernameSuggestion('Franklin D', 'Roosevelt')).toBe('franklin.d.roosevelt');
        expect(getUsernameSuggestion('Gölä', 'Pfeuti')).toBe('goelae.pfeuti');
        expect(getUsernameSuggestion('Walti', 'Rüdisüli')).toBe('walti.ruedisueli');
        expect(getUsernameSuggestion('Sacha', 'Baron Cohen')).toBe('sacha.baron.cohen');
        expect(getUsernameSuggestion('Micheline', 'Calmy-Rey')).toBe('micheline.calmy-rey');
        // for now we just strip other characters.
        expect(getUsernameSuggestion('Antonín', 'Dvořák')).toBe('antonn.dvok');
    });
});
