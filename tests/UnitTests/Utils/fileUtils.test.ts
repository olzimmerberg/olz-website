import {getFileWarning, getCompactUploadId, readBase64} from '../../../src/Utils/fileUtils';

const SIMPLE_FILE = new File(['simple'], 'simple.txt', {type: 'text/plain'});

describe('getFileWarning', () => {
    it('returns non-PDF warnings', () => {
        expect(getFileWarning('test.doc')).toEqual('Wenn möglich PDF statt Word-Datei verwenden');
        expect(getFileWarning('test.docx')).toEqual('Wenn möglich PDF statt Word-Datei verwenden');
        expect(getFileWarning('test.xls')).toEqual('Wenn möglich PDF statt Excel-Datei verwenden');
        expect(getFileWarning('test.xlsx')).toEqual('Wenn möglich PDF statt Excel-Datei verwenden');
        expect(getFileWarning('test.ppt')).toEqual('Wenn möglich PDF statt PowerPoint-Datei verwenden');
        expect(getFileWarning('test.pptx')).toEqual('Wenn möglich PDF statt PowerPoint-Datei verwenden');
        expect(getFileWarning('test.odt')).toEqual('Wenn möglich PDF statt OpenDocument-Datei verwenden');
        expect(getFileWarning('test.ods')).toEqual('Wenn möglich PDF statt OpenDocument-Datei verwenden');
        expect(getFileWarning('test.odp')).toEqual('Wenn möglich PDF statt OpenDocument-Datei verwenden');
    });

    it('returns no warnings for appropriate file types', () => {
        expect(getFileWarning('test.pdf')).toEqual(null);
        expect(getFileWarning('test.zip')).toEqual(null);
        expect(getFileWarning('test.csv')).toEqual(null);
        expect(getFileWarning('test.txt')).toEqual(null);
    });

    it('edge cases', () => {
        expect(getFileWarning('.pdf')).toEqual(null);
    });
});

describe('getCompactUploadId', () => {
    it('returns compact upload ID', () => {
        expect(getCompactUploadId('aaaaaaaaaaaaaaaaaaaaaaaa.doc')).toEqual('aaaa…aaaa.doc');
        expect(getCompactUploadId('abcdefghijklmnopqrstuvwx.docx')).toEqual('abcd…uvwx.docx');
        expect(getCompactUploadId('ABCDEFGHIJKLMNOPQRSTUVWX.xls')).toEqual('ABCD…UVWX.xls');
        expect(getCompactUploadId('123456789012345678901234.xlsx')).toEqual('1234…1234.xlsx');
    });

    it('falls back for non-upload-IDs', () => {
        expect(getCompactUploadId('test.pdf')).toEqual('test.pdf');
        expect(getCompactUploadId('12345678901234567890123.xlsx')).toEqual('123456789…');
    });
});

describe('readBase64', () => {
    it('resolves with base64 content for simple file', async () => {
        const base64 = await readBase64(SIMPLE_FILE);
        expect(base64).toEqual('data:text/plain;base64,c2ltcGxl');
    });

    it('rejects for aborted read', async () => {
        const fileReader = new FileReader();
        const promise = readBase64(SIMPLE_FILE, {fileReader});
        fileReader.abort();
        try {
            await promise;
            fail('Error expected');
        } catch (err: unknown) {
            expect(err).not.toEqual(undefined);
        }
    });

    it('rejects for error reading', async () => {
        const fileReader = {readAsDataURL: () => {}} as unknown as FileReader;
        const fakeErrorEvent = {target: {error: new Error('test')}} as ProgressEvent<FileReader>;
        const promise = readBase64(SIMPLE_FILE, {fileReader});
        if (fileReader.onerror) {
            fileReader.onerror(fakeErrorEvent);
        }
        try {
            await promise;
            fail('Error expected');
        } catch (err: unknown) {
            expect(err).not.toEqual(undefined);
        }
    });

    it('rejects for undefined error reading', async () => {
        const fileReader = {readAsDataURL: () => {}} as unknown as FileReader;
        const fakeErrorEvent = {target: null} as ProgressEvent<FileReader>;
        const promise = readBase64(SIMPLE_FILE, {fileReader});
        if (fileReader.onerror) {
            fileReader.onerror(fakeErrorEvent);
        }
        try {
            await promise;
            fail('Error expected');
        } catch (err: unknown) {
            expect(err).toEqual(undefined);
        }
    });

    it('rejects for invalid result', async () => {
        const fileReader = {readAsDataURL: () => {}} as unknown as FileReader;
        const fakeLoadEvent = {target: {result: null}} as ProgressEvent<FileReader>;
        const promise = readBase64(SIMPLE_FILE, {fileReader});
        if (fileReader.onload) {
            fileReader.onload(fakeLoadEvent);
        }
        try {
            await promise;
            fail('Error expected');
        } catch (err: unknown) {
            expect(err).not.toEqual(undefined);
        }
    });

    it('rejects for undefined result', async () => {
        const fileReader = {readAsDataURL: () => {}} as unknown as FileReader;
        const fakeLoadEvent = {target: null} as ProgressEvent<FileReader>;
        const promise = readBase64(SIMPLE_FILE, {fileReader});
        if (fileReader.onload) {
            fileReader.onload(fakeLoadEvent);
        }
        try {
            await promise;
            fail('Error expected');
        } catch (err: unknown) {
            expect(err).not.toEqual(undefined);
        }
    });
});
