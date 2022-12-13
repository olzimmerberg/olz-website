/* eslint-env jasmine */

import {assertUnreachable, getErrorOrThrow, isDefined} from '../../../src/Utils/generalUtils';

describe('assertUnreachable', () => {
    it('works if it is unreachable', () => {
        const aimForTheImpossible = () => {
            const wtf: 'a'|'b' = 'a';
            if (wtf === 'a' || wtf === 'b') {
                return;
            }
            assertUnreachable(wtf);
        };
        expect(aimForTheImpossible()).toEqual(undefined);
    });

    it('passes weird test, just for coverage', () => {
        const wtf = 'a' as never;
        expect(() => assertUnreachable(wtf)).toThrow(Error);
    });
});

describe('getErrorOrThrow', () => {
    it('returns error if it is a proper error', () => {
        const err = new Error('test');
        const unk = err as unknown;
        expect(getErrorOrThrow(unk)).toEqual(err);
    });

    it('throws an error if it is not an error', () => {
        expect(() => getErrorOrThrow('not an error!')).toThrow(Error);
    });
});

describe('isDefined', () => {
    it('returns false for null', () => {
        expect(isDefined(null)).toEqual(false);
    });

    it('returns false for undefined', () => {
        expect(isDefined(undefined)).toEqual(false);
    });

    it('returns true for e.g. string', () => {
        expect(isDefined('not an error!')).toEqual(true);
    });

    it('filters array', () => {
        const array: Array<number|undefined|null> = [undefined, 1, null, 4];
        expect(array.filter(isDefined)).toEqual([1, 4]);
    });
});

// loadScript cannot be tested
