/* eslint-env jasmine */

import {readBase64} from '../../../src/Utils/fileUtils';

const SIMPLE_FILE = new File(['simple'], 'simple.txt', {type: 'text/plain'});

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
