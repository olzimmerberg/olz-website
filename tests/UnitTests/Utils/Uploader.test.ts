/* eslint-env jasmine */

import cloneDeep from 'lodash/cloneDeep';
import {OlzApi, OlzApiResponses} from '../../../src/Api/client/index';
import {TestOnlyUpdateUploadRequest, TestOnlyUploadRequest, TestOnlyFileUpload, Uploader} from '../../../src/Utils/Uploader';
import {FakeOlzApi} from '../../Fake/FakeOlzApi';

const MAX_PART_LENGTH = 4;
const MAX_CONCURRENT_REQUESTS = 2;

class UploaderForUnitTest extends Uploader {
    protected maxPartLength = MAX_PART_LENGTH;
    protected maxConcurrentRequests = MAX_CONCURRENT_REQUESTS;

    public processHasBeenCalledTimes = 0;

    public setOlzApi(olzApi: OlzApi) {
        this.olzApi = olzApi;
    }

    public getUploadQueue() {
        return this.uploadQueue;
    }

    public setUploadQueue(uploadQueue: TestOnlyFileUpload[]) {
        this.uploadQueue = uploadQueue;
    }

    protected process(): void {
        this.processHasBeenCalledTimes++;
    }

    public testOnlyProcess(): void {
        super.process();
    }

    public testOnlyProcessRequest(request: TestOnlyUploadRequest) {
        return super.processRequest(request);
    }
}

const NEW_UPLOAD: TestOnlyFileUpload = {
    uploadId: 'default-id',
    base64Content: 'a'.repeat(MAX_PART_LENGTH * 2.5),
    parts: [
        {status: 'READY'},
        {status: 'READY'},
        {status: 'READY'},
    ],
    status: 'UPLOADING',
};

const DEFAULT_UPLOAD: TestOnlyFileUpload = {
    uploadId: 'default-id',
    base64Content: 'a'.repeat(MAX_PART_LENGTH * 2.5),
    parts: [
        {status: 'DONE'},
        {status: 'UPLOADING'},
        {status: 'READY'},
    ],
    status: 'UPLOADING',
};

const UPLOADED_UPLOAD: TestOnlyFileUpload = {
    uploadId: 'uploaded-id',
    base64Content: 'a'.repeat(MAX_PART_LENGTH * 2.5),
    parts: [
        {status: 'DONE'},
        {status: 'DONE'},
        {status: 'DONE'},
    ],
    status: 'UPLOADING',
};

const FINISHING_UPLOAD: TestOnlyFileUpload = {
    uploadId: 'finishing-id',
    base64Content: 'a'.repeat(MAX_PART_LENGTH * 2.5),
    parts: [
        {status: 'DONE'},
        {status: 'DONE'},
        {status: 'DONE'},
    ],
    status: 'FINISHING',
};

const FINISHED_UPLOAD: TestOnlyFileUpload = {
    uploadId: 'finished-id',
    base64Content: 'a'.repeat(MAX_PART_LENGTH * 2.5),
    parts: [
        {status: 'DONE'},
        {status: 'DONE'},
        {status: 'DONE'},
    ],
    status: 'DONE',
};

const HUGE_UPLOAD: TestOnlyFileUpload = {
    uploadId: 'huge-id',
    base64Content: 'a'.repeat(MAX_PART_LENGTH * 1024),
    parts: [
        {status: 'DONE'},
        {status: 'UPLOADING'},
        ...Array(1022).fill(0).map((_) => ({status: 'READY' as const})),
    ],
    status: 'UPLOADING',
};


describe('Uploader', () => {
    it('getInstance', () => {
        const uploader1 = Uploader.getInstance();
        const uploader2 = Uploader.getInstance();
        expect(uploader1).toEqual(uploader2);
    });

    it('is not undefined', () => {
        const uploader = new Uploader();
        expect(uploader).not.toEqual(undefined);
    });

    describe('upload', () => {
        it('works', async () => {
            const uploader = new UploaderForUnitTest();
            const fakeOlzApi = new FakeOlzApi();
            fakeOlzApi.mock('startUpload', () => Promise.resolve({status: 'OK', id: 'new-id'}));
            fakeOlzApi.mock('updateUpload', () => Promise.resolve({status: 'OK'}));
            fakeOlzApi.mock('finishUpload', () => Promise.resolve({status: 'OK'}));
            uploader.setOlzApi(fakeOlzApi);

            // Start request
            const promise = uploader.upload('a'.repeat(MAX_PART_LENGTH * 2.5), 'upload.txt');

            let promiseIsResolvedWith: string|null = null;
            promise.then((uploadId: string) => {
                promiseIsResolvedWith = uploadId;
            });
            expect(uploader.getUploadQueue()).toEqual([]);

            // Wait for request response
            await Promise.resolve();

            expect(uploader.getUploadQueue()).toEqual([{
                uploadId: 'new-id',
                base64Content: 'a'.repeat(MAX_PART_LENGTH * 2.5),
                parts: [
                    {status: 'READY'},
                    {status: 'READY'},
                    {status: 'READY'},
                ],
                status: 'UPLOADING',
            }]);
            expect(uploader.processHasBeenCalledTimes).toEqual(1);
            expect(promiseIsResolvedWith).toEqual(null);

            uploader.testOnlyProcess();

            expect(uploader.getUploadQueue()).toEqual([{
                uploadId: 'new-id',
                base64Content: 'a'.repeat(MAX_PART_LENGTH * 2.5),
                parts: [
                    {status: 'UPLOADING'},
                    {status: 'UPLOADING'},
                    {status: 'READY'}, // Max. 2 concurrent
                ],
                status: 'UPLOADING',
            }]);
            expect(uploader.processHasBeenCalledTimes).toEqual(1);

            // Wait for request response
            await Promise.resolve();

            expect(uploader.getUploadQueue()).toEqual([{
                uploadId: 'new-id',
                base64Content: 'a'.repeat(MAX_PART_LENGTH * 2.5),
                parts: [
                    {status: 'DONE'},
                    {status: 'DONE'},
                    {status: 'READY'},
                ],
                status: 'UPLOADING',
            }]);
            expect(uploader.processHasBeenCalledTimes).toEqual(3);

            uploader.testOnlyProcess();

            expect(uploader.getUploadQueue()).toEqual([{
                uploadId: 'new-id',
                base64Content: 'a'.repeat(MAX_PART_LENGTH * 2.5),
                parts: [
                    {status: 'DONE'},
                    {status: 'DONE'},
                    {status: 'UPLOADING'},
                ],
                status: 'UPLOADING',
            }]);
            expect(uploader.processHasBeenCalledTimes).toEqual(3);

            // Wait for request response
            await Promise.resolve();

            expect(uploader.getUploadQueue()).toEqual([{
                uploadId: 'new-id',
                base64Content: 'a'.repeat(MAX_PART_LENGTH * 2.5),
                parts: [
                    {status: 'DONE'},
                    {status: 'DONE'},
                    {status: 'DONE'},
                ],
                status: 'UPLOADING',
            }]);
            expect(uploader.processHasBeenCalledTimes).toEqual(4);

            uploader.testOnlyProcess();

            expect(uploader.getUploadQueue()).toEqual([{
                uploadId: 'new-id',
                base64Content: 'a'.repeat(MAX_PART_LENGTH * 2.5),
                parts: [
                    {status: 'DONE'},
                    {status: 'DONE'},
                    {status: 'DONE'},
                ],
                status: 'FINISHING',
            }]);
            expect(uploader.processHasBeenCalledTimes).toEqual(4);

            // Wait for (inexistent) request response
            await Promise.resolve();

            expect(uploader.getUploadQueue()).toEqual([{
                uploadId: 'new-id',
                base64Content: 'a'.repeat(MAX_PART_LENGTH * 2.5),
                parts: [
                    {status: 'DONE'},
                    {status: 'DONE'},
                    {status: 'DONE'},
                ],
                status: 'DONE',
            }]);
            expect(uploader.processHasBeenCalledTimes).toEqual(5);

            uploader.testOnlyProcess();

            expect(uploader.getUploadQueue()).toEqual([]);
            expect(uploader.processHasBeenCalledTimes).toEqual(5);

            await Promise.resolve();
            await Promise.resolve();

            expect(promiseIsResolvedWith).toEqual('new-id');
        });
    });

    describe('add', () => {
        it('works (integration test)', async () => {
            const uploader = new UploaderForUnitTest();
            const fakeOlzApi = new FakeOlzApi();
            fakeOlzApi.mock('startUpload', () => Promise.resolve({status: 'OK', id: 'new-id'}));
            fakeOlzApi.mock('updateUpload', () => Promise.resolve({status: 'OK'}));
            fakeOlzApi.mock('finishUpload', () => Promise.resolve({status: 'OK'}));
            uploader.setOlzApi(fakeOlzApi);

            // Start request
            const promise = uploader.add('a'.repeat(MAX_PART_LENGTH * 2.5), 'add.txt');

            expect(uploader.getUploadQueue()).toEqual([]);

            // Wait for request response
            await promise;

            expect(uploader.getUploadQueue()).toEqual([{
                uploadId: 'new-id',
                base64Content: 'a'.repeat(MAX_PART_LENGTH * 2.5),
                parts: [
                    {status: 'READY'},
                    {status: 'READY'},
                    {status: 'READY'},
                ],
                status: 'UPLOADING',
            }]);
            expect(uploader.processHasBeenCalledTimes).toEqual(1);

            uploader.testOnlyProcess();

            expect(uploader.getUploadQueue()).toEqual([{
                uploadId: 'new-id',
                base64Content: 'a'.repeat(MAX_PART_LENGTH * 2.5),
                parts: [
                    {status: 'UPLOADING'},
                    {status: 'UPLOADING'},
                    {status: 'READY'}, // Max. 2 concurrent
                ],
                status: 'UPLOADING',
            }]);
            expect(uploader.processHasBeenCalledTimes).toEqual(1);

            await Promise.resolve();

            expect(uploader.getUploadQueue()).toEqual([{
                uploadId: 'new-id',
                base64Content: 'a'.repeat(MAX_PART_LENGTH * 2.5),
                parts: [
                    {status: 'DONE'},
                    {status: 'DONE'},
                    {status: 'READY'},
                ],
                status: 'UPLOADING',
            }]);
            expect(uploader.processHasBeenCalledTimes).toEqual(3);

            uploader.testOnlyProcess();

            expect(uploader.getUploadQueue()).toEqual([{
                uploadId: 'new-id',
                base64Content: 'a'.repeat(MAX_PART_LENGTH * 2.5),
                parts: [
                    {status: 'DONE'},
                    {status: 'DONE'},
                    {status: 'UPLOADING'},
                ],
                status: 'UPLOADING',
            }]);
            expect(uploader.processHasBeenCalledTimes).toEqual(3);

            await Promise.resolve();

            expect(uploader.getUploadQueue()).toEqual([{
                uploadId: 'new-id',
                base64Content: 'a'.repeat(MAX_PART_LENGTH * 2.5),
                parts: [
                    {status: 'DONE'},
                    {status: 'DONE'},
                    {status: 'DONE'},
                ],
                status: 'UPLOADING',
            }]);
            expect(uploader.processHasBeenCalledTimes).toEqual(4);

            uploader.testOnlyProcess();

            expect(uploader.getUploadQueue()).toEqual([{
                uploadId: 'new-id',
                base64Content: 'a'.repeat(MAX_PART_LENGTH * 2.5),
                parts: [
                    {status: 'DONE'},
                    {status: 'DONE'},
                    {status: 'DONE'},
                ],
                status: 'FINISHING',
            }]);
            expect(uploader.processHasBeenCalledTimes).toEqual(4);

            await Promise.resolve();

            expect(uploader.getUploadQueue()).toEqual([{
                uploadId: 'new-id',
                base64Content: 'a'.repeat(MAX_PART_LENGTH * 2.5),
                parts: [
                    {status: 'DONE'},
                    {status: 'DONE'},
                    {status: 'DONE'},
                ],
                status: 'DONE',
            }]);
            expect(uploader.processHasBeenCalledTimes).toEqual(5);

            uploader.testOnlyProcess();

            expect(uploader.getUploadQueue()).toEqual([]);
            expect(uploader.processHasBeenCalledTimes).toEqual(5);
        });
    });

    describe('getState', () => {
        it('works for initial queue', () => {
            const uploader = new Uploader();
            const state = uploader.getState();
            expect(state).toEqual({
                nextRequests: [],
                numberOfRunningRequests: 0,
                uploadIds: [],
                uploadsById: {},
            });
        });

        it('works for new upload', () => {
            const uploader = new UploaderForUnitTest();
            uploader.setUploadQueue([
                NEW_UPLOAD,
            ]);
            const state = uploader.getState();
            expect(state).toEqual({
                nextRequests: [
                    {
                        type: 'UPDATE',
                        id: 'default-id',
                        part: 0,
                        content: (state.nextRequests[0] as TestOnlyUpdateUploadRequest).content,
                    },
                    {
                        type: 'UPDATE',
                        id: 'default-id',
                        part: 1,
                        content: (state.nextRequests[1] as TestOnlyUpdateUploadRequest).content,
                    },
                ],
                numberOfRunningRequests: 0,
                uploadIds: ['default-id'],
                uploadsById: {
                    'default-id': {
                        progress: 0,
                        size: MAX_PART_LENGTH * 2.5,
                    },
                },
            });
        });

        it('works for default upload', () => {
            const uploader = new UploaderForUnitTest();
            uploader.setUploadQueue([
                DEFAULT_UPLOAD,
            ]);
            const state = uploader.getState();
            expect(state).toEqual({
                nextRequests: [
                    {
                        type: 'UPDATE',
                        id: 'default-id',
                        part: 2,
                        content: (state.nextRequests[0] as TestOnlyUpdateUploadRequest).content,
                    },
                ],
                numberOfRunningRequests: 1,
                uploadIds: ['default-id'],
                uploadsById: {
                    'default-id': {
                        progress: 1.5 / 4, // 3 parts + finishing
                        size: MAX_PART_LENGTH * 2.5,
                    },
                },
            });
        });

        it('works for uploaded upload', () => {
            const uploader = new UploaderForUnitTest();
            uploader.setUploadQueue([
                UPLOADED_UPLOAD,
            ]);
            const state = uploader.getState();
            expect(state).toEqual({
                nextRequests: [
                    {
                        type: 'FINISH',
                        id: 'uploaded-id',
                        numberOfParts: 3,
                    },
                ],
                numberOfRunningRequests: 0,
                uploadIds: ['uploaded-id'],
                uploadsById: {
                    'uploaded-id': {
                        progress: 3 / 4, // 3 parts + finishing
                        size: MAX_PART_LENGTH * 2.5,
                    },
                },
            });
        });

        it('works for finishing upload', () => {
            const uploader = new UploaderForUnitTest();
            uploader.setUploadQueue([
                FINISHING_UPLOAD,
            ]);
            const state = uploader.getState();
            expect(state).toEqual({
                nextRequests: [],
                numberOfRunningRequests: 1,
                uploadIds: ['finishing-id'],
                uploadsById: {
                    'finishing-id': {
                        progress: 3 / 4, // 3 parts + finishing
                        size: MAX_PART_LENGTH * 2.5,
                    },
                },
            });
        });

        it('works for finished upload', () => {
            const uploader = new UploaderForUnitTest();
            uploader.setUploadQueue([
                FINISHED_UPLOAD,
            ]);
            const state = uploader.getState();
            expect(state).toEqual({
                nextRequests: [],
                numberOfRunningRequests: 0,
                uploadIds: ['finished-id'],
                uploadsById: {
                    'finished-id': {
                        progress: 3 / 4, // 3 parts + finishing
                        size: MAX_PART_LENGTH * 2.5,
                    },
                },
            });
        });

        it('works for huge upload', () => {
            const uploader = new UploaderForUnitTest();
            uploader.setUploadQueue([
                HUGE_UPLOAD,
            ]);
            const state = uploader.getState();
            expect(state).toEqual({
                nextRequests: [
                    {
                        type: 'UPDATE',
                        id: 'huge-id',
                        part: 2,
                        content: (state.nextRequests[0] as TestOnlyUpdateUploadRequest).content,
                    },
                    {
                        type: 'UPDATE',
                        id: 'huge-id',
                        part: 3,
                        content: (state.nextRequests[1] as TestOnlyUpdateUploadRequest).content,
                    },
                ],
                numberOfRunningRequests: 1,
                uploadIds: ['huge-id'],
                uploadsById: {
                    'huge-id': {
                        progress: 1.5 / 1025, // 1024 parts + finishing
                        size: MAX_PART_LENGTH * 1024,
                    },
                },
            });
        });

        it('works for one new empty upload', () => {
            const uploader = new UploaderForUnitTest();
            uploader.setUploadQueue([
                {
                    ...DEFAULT_UPLOAD,
                    base64Content: '',
                    parts: [],
                },
            ]);
            const state = uploader.getState();
            expect(state).toEqual({
                nextRequests: [
                    {
                        type: 'FINISH',
                        id: 'default-id',
                        numberOfParts: 0,
                    },
                ],
                numberOfRunningRequests: 0,
                uploadIds: ['default-id'],
                uploadsById: {
                    'default-id': {
                        progress: 0,
                        size: 0,
                    },
                },
            });
        });
    });

    describe('processRequest', () => {
        it('works for successful update upload request', async () => {
            const uploader = new UploaderForUnitTest();
            const fakeOlzApi = new FakeOlzApi();
            fakeOlzApi.mock('updateUpload', () => Promise.resolve({status: 'OK'}));
            uploader.setOlzApi(fakeOlzApi);
            const uploadQueue: TestOnlyFileUpload[] = [
                cloneDeep(DEFAULT_UPLOAD),
            ];
            uploader.setUploadQueue(uploadQueue);

            // Start request
            const promise = uploader.testOnlyProcessRequest({
                type: 'UPDATE',
                id: DEFAULT_UPLOAD.uploadId,
                part: 2,
                content: 'a'.repeat(32),
            });

            expect(uploadQueue[0]).toEqual({
                ...DEFAULT_UPLOAD,
                parts: [
                    {status: 'DONE'},
                    {status: 'UPLOADING'},
                    {status: 'UPLOADING'},
                ],
            });

            // Wait for request response
            await promise;

            expect(uploadQueue[0]).toEqual({
                ...DEFAULT_UPLOAD,
                parts: [
                    {status: 'DONE'},
                    {status: 'UPLOADING'},
                    {status: 'DONE'},
                ],
            });
        });

        it('works for failing update upload request', async () => {
            const uploader = new UploaderForUnitTest();
            const fakeOlzApi = new FakeOlzApi();
            const mockPromise: Promise<OlzApiResponses['updateUpload']> =
                Promise.reject(new Error('asdf'));
            fakeOlzApi.mock('updateUpload', () => mockPromise);
            uploader.setOlzApi(fakeOlzApi);
            const uploadQueue: TestOnlyFileUpload[] = [
                cloneDeep(DEFAULT_UPLOAD),
            ];
            uploader.setUploadQueue(uploadQueue);

            // Start request
            const promise = uploader.testOnlyProcessRequest({
                type: 'UPDATE',
                id: DEFAULT_UPLOAD.uploadId,
                part: 2,
                content: 'a'.repeat(32),
            });

            expect(uploadQueue[0]).toEqual({
                ...DEFAULT_UPLOAD,
                parts: [
                    {status: 'DONE'},
                    {status: 'UPLOADING'},
                    {status: 'UPLOADING'},
                ],
            });

            // Wait for request response
            await promise;

            expect(uploadQueue[0]).toEqual({
                ...DEFAULT_UPLOAD,
                parts: [
                    {status: 'DONE'},
                    {status: 'UPLOADING'},
                    {status: 'READY'},
                ],
            });
        });

        it('works for successful finish upload request', async () => {
            const uploader = new UploaderForUnitTest();
            const fakeOlzApi = new FakeOlzApi();
            fakeOlzApi.mock('finishUpload', () => Promise.resolve({status: 'OK'}));
            uploader.setOlzApi(fakeOlzApi);
            const uploadQueue: TestOnlyFileUpload[] = [
                cloneDeep(UPLOADED_UPLOAD),
            ];
            uploader.setUploadQueue(uploadQueue);


            // Start request
            const promise = uploader.testOnlyProcessRequest({
                type: 'FINISH',
                id: UPLOADED_UPLOAD.uploadId,
                numberOfParts: UPLOADED_UPLOAD.parts.length,
            });

            expect(uploadQueue[0]).toEqual({
                ...UPLOADED_UPLOAD,
                status: 'FINISHING',
            });

            // Wait for request response
            await promise;

            expect(uploadQueue[0]).toEqual({
                ...UPLOADED_UPLOAD,
                status: 'DONE',
            });
        });

        it('works for failing finish upload request', async () => {
            const uploader = new UploaderForUnitTest();
            const fakeOlzApi = new FakeOlzApi();
            const mockPromise: Promise<OlzApiResponses['finishUpload']> =
                Promise.reject(new Error('asdf'));
            fakeOlzApi.mock('finishUpload', () => mockPromise);
            uploader.setOlzApi(fakeOlzApi);
            const uploadQueue: TestOnlyFileUpload[] = [
                cloneDeep(UPLOADED_UPLOAD),
            ];
            uploader.setUploadQueue(uploadQueue);


            // Start request
            const promise = uploader.testOnlyProcessRequest({
                type: 'FINISH',
                id: UPLOADED_UPLOAD.uploadId,
                numberOfParts: UPLOADED_UPLOAD.parts.length,
            });

            expect(uploadQueue[0]).toEqual({
                ...UPLOADED_UPLOAD,
                status: 'FINISHING',
            });

            // Wait for request response
            await promise;

            expect(uploadQueue[0]).toEqual({
                ...UPLOADED_UPLOAD,
                status: 'UPLOADING',
            });
        });
    });
});
