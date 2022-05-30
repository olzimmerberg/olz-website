/* eslint-env jasmine */

import {assertUnreachable, getErrorOrThrow} from '../../../_/utils/generalUtils';

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

// loadScript cannot be tested
