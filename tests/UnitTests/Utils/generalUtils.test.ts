import {assert, assertUnreachable, getErrorOrThrow, isDefined, isLocal, timeout} from '../../../src/Utils/generalUtils';

describe('assert', () => {
    it('works for value', () => {
        expect(assert('test')).toEqual('test');
    });

    it('throws for null', () => {
        expect(() => assert(null)).toThrow();
    });

    it('throws for undefined', () => {
        expect(() => assert(undefined)).toThrow();
    });

    it('works for {}', () => {
        expect(assert({})).toEqual({});
    });

    it('works for []', () => {
        expect(assert([])).toEqual([]);
    });

    it('works for ""', () => {
        expect(assert('')).toEqual('');
    });

    it('works for 0', () => {
        expect(assert(0)).toEqual(0);
    });

    it('works for false', () => {
        expect(assert(false)).toEqual(false);
    });

    it('works with message', () => {
        expect(() => assert(null, 'message')).toThrow('message');
    });
});

describe('assertUnreachable', () => {
    it('works if it is unreachable', () => {
        const aimForTheImpossible = () => {
            const wtf: 'a' | 'b' = 'a';
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
        const array: Array<number | undefined | null> = [undefined, 1, null, 4];
        expect(array.filter(isDefined)).toEqual([1, 4]);
    });
});

describe('isLocal', () => {
    it('returns true for test', () => {
        expect(isLocal()).toEqual(true);
    });
});

describe('timeout', () => {
    it('waits for 0 ms', async () => {
        let check = false;
        setTimeout(() => { check = true; }, 0);
        await timeout(0);
        expect(check).toEqual(true);
    });

    it('waits for 1 ms', async () => {
        let check = false;
        setTimeout(() => { check = true; }, 1);
        await timeout(1);
        expect(check).toEqual(true);
    });
});

// loadScript cannot be tested
