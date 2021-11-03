/* eslint-env jasmine */

import {obfuscateForUpload, deobfuscateUpload, assertUnreachable, getErrorOrThrow} from '../../../src/utils/generalUtils';

describe('obfuscateForUpload', () => {
    it('for simple string', () => {
        expect(obfuscateForUpload('test')).toMatch(/^[0-9]+;[a-zA-Z0-9\\+\\/]+[=]*$/);
    });

    it('for special characters string', () => {
        expect(obfuscateForUpload('ĩä😎=/+')).toMatch(/^[0-9]+;[a-zA-Z0-9\\+\\/]+[=]*$/);
    });
});

describe('deobfuscateUpload', () => {
    it('for simple string', () => {
        expect(deobfuscateUpload('60280;n+aetQ==')).toEqual('test');
    });

    it('for special characters string', () => {
        const obfuscated = '36902;tTcqk0yuflo5H1nIohtler1GN3s5Bnfg2/VIrUmvbNk6';
        expect(deobfuscateUpload(obfuscated)).toEqual('ĩä😎=/+');
    });

    it('from PHP', () => {
        const obfuscatedFromPhp = '39842;vpsPDLtg/ynqf7gfHZ2oSS6akhROeG4rcG0xCoaZMULR';
        expect(deobfuscateUpload(obfuscatedFromPhp)).toEqual('ĩä😎=/+');
    });
});

describe('deobfuscate of obfuscate gives back the same result', () => {
    it('for simple string', () => {
        const original = 'test';
        const obfuscated = obfuscateForUpload(original);
        expect(deobfuscateUpload(obfuscated)).toEqual(original);
    });

    it('for special characters string', () => {
        const original = 'ĩä😎=/+';
        const obfuscated = obfuscateForUpload(original);
        expect(deobfuscateUpload(obfuscated)).toEqual(original);
    });
});

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
