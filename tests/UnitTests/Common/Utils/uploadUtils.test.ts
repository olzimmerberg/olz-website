import {obfuscateForUpload, deobfuscateUpload} from '../../../../src/Common/Utils/uploadUtils';

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
