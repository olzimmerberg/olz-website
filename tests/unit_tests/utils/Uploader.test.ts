/* eslint-env jasmine */

import cloneDeep from 'lodash/cloneDeep';
import {OlzApi, OlzApiResponses} from '../../../public/_/api/client/index';
import {MAX_PART_LENGTH, TestOnlyUpdateUploadRequest, TestOnlyFileUploadPartStatus, TestOnlyUploadRequest, TestOnlyUploadRequestType, TestOnlyFileUploadStatus, TestOnlyFileUpload, Uploader} from '../../../public/_/utils/Uploader';
import {FakeOlzApi} from '../../fake/FakeOlzApi';

class UploaderForUnitTest extends Uploader {
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

const NEW_UPLOAD = {
    uploadId: 'default-id',
    filename: 'default.txt',
    base64Content: 'a'.repeat(MAX_PART_LENGTH + MAX_PART_LENGTH + 32),
    parts: [
        {status: TestOnlyFileUploadPartStatus.READY},
        {status: TestOnlyFileUploadPartStatus.READY},
        {status: TestOnlyFileUploadPartStatus.READY},
    ],
    status: TestOnlyFileUploadStatus.UPLOADING,
};

const DEFAULT_UPLOAD = {
    uploadId: 'default-id',
    filename: 'default.txt',
    base64Content: 'a'.repeat(MAX_PART_LENGTH + MAX_PART_LENGTH + 32),
    parts: [
        {status: TestOnlyFileUploadPartStatus.DONE},
        {status: TestOnlyFileUploadPartStatus.UPLOADING},
        {status: TestOnlyFileUploadPartStatus.READY},
    ],
    status: TestOnlyFileUploadStatus.UPLOADING,
};

const UPLOADED_UPLOAD = {
    uploadId: 'uploaded-id',
    filename: 'uploaded.txt',
    base64Content: 'a'.repeat(MAX_PART_LENGTH + MAX_PART_LENGTH + 32),
    parts: [
        {status: TestOnlyFileUploadPartStatus.DONE},
        {status: TestOnlyFileUploadPartStatus.DONE},
        {status: TestOnlyFileUploadPartStatus.DONE},
    ],
    status: TestOnlyFileUploadStatus.UPLOADING,
};

const FINISHING_UPLOAD = {
    uploadId: 'finishing-id',
    filename: 'finishing.txt',
    base64Content: 'a'.repeat(MAX_PART_LENGTH + MAX_PART_LENGTH + 32),
    parts: [
        {status: TestOnlyFileUploadPartStatus.DONE},
        {status: TestOnlyFileUploadPartStatus.DONE},
        {status: TestOnlyFileUploadPartStatus.DONE},
    ],
    status: TestOnlyFileUploadStatus.FINISHING,
};

const FINISHED_UPLOAD = {
    uploadId: 'finished-id',
    filename: 'finished.txt',
    base64Content: 'a'.repeat(MAX_PART_LENGTH + MAX_PART_LENGTH + 32),
    parts: [
        {status: TestOnlyFileUploadPartStatus.DONE},
        {status: TestOnlyFileUploadPartStatus.DONE},
        {status: TestOnlyFileUploadPartStatus.DONE},
    ],
    status: TestOnlyFileUploadStatus.DONE,
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
            const promise = uploader.upload('a'.repeat(MAX_PART_LENGTH + MAX_PART_LENGTH + 32), 'upload.txt');

            let promiseIsResolvedWith: string|null = null;
            promise.then((uploadId: string) => {
                promiseIsResolvedWith = uploadId;
            });
            expect(uploader.getUploadQueue()).toEqual([]);

            // Wait for request response
            await Promise.resolve();

            expect(uploader.getUploadQueue()).toEqual([{
                uploadId: 'new-id',
                base64Content: 'a'.repeat(MAX_PART_LENGTH + MAX_PART_LENGTH + 32),
                parts: [
                    {status: TestOnlyFileUploadPartStatus.READY},
                    {status: TestOnlyFileUploadPartStatus.READY},
                    {status: TestOnlyFileUploadPartStatus.READY},
                ],
                status: TestOnlyFileUploadStatus.UPLOADING,
            }]);
            expect(uploader.processHasBeenCalledTimes).toEqual(1);
            expect(promiseIsResolvedWith).toEqual(null);

            uploader.testOnlyProcess();

            expect(uploader.getUploadQueue()).toEqual([{
                uploadId: 'new-id',
                base64Content: 'a'.repeat(MAX_PART_LENGTH + MAX_PART_LENGTH + 32),
                parts: [
                    {status: TestOnlyFileUploadPartStatus.UPLOADING},
                    {status: TestOnlyFileUploadPartStatus.UPLOADING},
                    {status: TestOnlyFileUploadPartStatus.UPLOADING},
                ],
                status: TestOnlyFileUploadStatus.UPLOADING,
            }]);
            expect(uploader.processHasBeenCalledTimes).toEqual(1);

            await Promise.resolve();

            expect(uploader.getUploadQueue()).toEqual([{
                uploadId: 'new-id',
                base64Content: 'a'.repeat(MAX_PART_LENGTH + MAX_PART_LENGTH + 32),
                parts: [
                    {status: TestOnlyFileUploadPartStatus.DONE},
                    {status: TestOnlyFileUploadPartStatus.DONE},
                    {status: TestOnlyFileUploadPartStatus.DONE},
                ],
                status: TestOnlyFileUploadStatus.UPLOADING,
            }]);
            expect(uploader.processHasBeenCalledTimes).toEqual(4);

            uploader.testOnlyProcess();

            expect(uploader.getUploadQueue()).toEqual([{
                uploadId: 'new-id',
                base64Content: 'a'.repeat(MAX_PART_LENGTH + MAX_PART_LENGTH + 32),
                parts: [
                    {status: TestOnlyFileUploadPartStatus.DONE},
                    {status: TestOnlyFileUploadPartStatus.DONE},
                    {status: TestOnlyFileUploadPartStatus.DONE},
                ],
                status: TestOnlyFileUploadStatus.FINISHING,
            }]);
            expect(uploader.processHasBeenCalledTimes).toEqual(4);

            await Promise.resolve();

            expect(uploader.getUploadQueue()).toEqual([{
                uploadId: 'new-id',
                base64Content: 'a'.repeat(MAX_PART_LENGTH + MAX_PART_LENGTH + 32),
                parts: [
                    {status: TestOnlyFileUploadPartStatus.DONE},
                    {status: TestOnlyFileUploadPartStatus.DONE},
                    {status: TestOnlyFileUploadPartStatus.DONE},
                ],
                status: TestOnlyFileUploadStatus.DONE,
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
            const promise = uploader.add('a'.repeat(MAX_PART_LENGTH + MAX_PART_LENGTH + 32), 'add.txt');

            expect(uploader.getUploadQueue()).toEqual([]);

            // Wait for request response
            await promise;

            expect(uploader.getUploadQueue()).toEqual([{
                uploadId: 'new-id',
                base64Content: 'a'.repeat(MAX_PART_LENGTH + MAX_PART_LENGTH + 32),
                parts: [
                    {status: TestOnlyFileUploadPartStatus.READY},
                    {status: TestOnlyFileUploadPartStatus.READY},
                    {status: TestOnlyFileUploadPartStatus.READY},
                ],
                status: TestOnlyFileUploadStatus.UPLOADING,
            }]);
            expect(uploader.processHasBeenCalledTimes).toEqual(1);

            uploader.testOnlyProcess();

            expect(uploader.getUploadQueue()).toEqual([{
                uploadId: 'new-id',
                base64Content: 'a'.repeat(MAX_PART_LENGTH + MAX_PART_LENGTH + 32),
                parts: [
                    {status: TestOnlyFileUploadPartStatus.UPLOADING},
                    {status: TestOnlyFileUploadPartStatus.UPLOADING},
                    {status: TestOnlyFileUploadPartStatus.UPLOADING},
                ],
                status: TestOnlyFileUploadStatus.UPLOADING,
            }]);
            expect(uploader.processHasBeenCalledTimes).toEqual(1);

            await Promise.resolve();

            expect(uploader.getUploadQueue()).toEqual([{
                uploadId: 'new-id',
                base64Content: 'a'.repeat(MAX_PART_LENGTH + MAX_PART_LENGTH + 32),
                parts: [
                    {status: TestOnlyFileUploadPartStatus.DONE},
                    {status: TestOnlyFileUploadPartStatus.DONE},
                    {status: TestOnlyFileUploadPartStatus.DONE},
                ],
                status: TestOnlyFileUploadStatus.UPLOADING,
            }]);
            expect(uploader.processHasBeenCalledTimes).toEqual(4);

            uploader.testOnlyProcess();

            expect(uploader.getUploadQueue()).toEqual([{
                uploadId: 'new-id',
                base64Content: 'a'.repeat(MAX_PART_LENGTH + MAX_PART_LENGTH + 32),
                parts: [
                    {status: TestOnlyFileUploadPartStatus.DONE},
                    {status: TestOnlyFileUploadPartStatus.DONE},
                    {status: TestOnlyFileUploadPartStatus.DONE},
                ],
                status: TestOnlyFileUploadStatus.FINISHING,
            }]);
            expect(uploader.processHasBeenCalledTimes).toEqual(4);

            await Promise.resolve();

            expect(uploader.getUploadQueue()).toEqual([{
                uploadId: 'new-id',
                base64Content: 'a'.repeat(MAX_PART_LENGTH + MAX_PART_LENGTH + 32),
                parts: [
                    {status: TestOnlyFileUploadPartStatus.DONE},
                    {status: TestOnlyFileUploadPartStatus.DONE},
                    {status: TestOnlyFileUploadPartStatus.DONE},
                ],
                status: TestOnlyFileUploadStatus.DONE,
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
                        type: TestOnlyUploadRequestType.UPDATE,
                        id: 'default-id',
                        part: 0,
                        content: (state.nextRequests[0] as TestOnlyUpdateUploadRequest).content,
                    },
                    {
                        type: TestOnlyUploadRequestType.UPDATE,
                        id: 'default-id',
                        part: 1,
                        content: (state.nextRequests[1] as TestOnlyUpdateUploadRequest).content,
                    },
                    {
                        type: TestOnlyUploadRequestType.UPDATE,
                        id: 'default-id',
                        part: 2,
                        content: (state.nextRequests[2] as TestOnlyUpdateUploadRequest).content,
                    },
                ],
                numberOfRunningRequests: 0,
                uploadIds: ['default-id'],
                uploadsById: {
                    'default-id': {
                        progress: 0,
                        size: MAX_PART_LENGTH + MAX_PART_LENGTH + 32,
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
                        type: TestOnlyUploadRequestType.UPDATE,
                        id: 'default-id',
                        part: 2,
                        content: (state.nextRequests[0] as TestOnlyUpdateUploadRequest).content,
                    },
                ],
                numberOfRunningRequests: 1,
                uploadIds: ['default-id'],
                uploadsById: {
                    'default-id': {
                        progress: 0.375,
                        size: MAX_PART_LENGTH + MAX_PART_LENGTH + 32,
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
                        type: TestOnlyUploadRequestType.FINISH,
                        id: 'uploaded-id',
                        numberOfParts: 3,
                    },
                ],
                numberOfRunningRequests: 0,
                uploadIds: ['uploaded-id'],
                uploadsById: {
                    'uploaded-id': {
                        progress: 0.75,
                        size: MAX_PART_LENGTH + MAX_PART_LENGTH + 32,
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
                        progress: 0.75,
                        size: MAX_PART_LENGTH + MAX_PART_LENGTH + 32,
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
                        progress: 0.75,
                        size: MAX_PART_LENGTH + MAX_PART_LENGTH + 32,
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
                        type: TestOnlyUploadRequestType.FINISH,
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
                type: TestOnlyUploadRequestType.UPDATE,
                id: DEFAULT_UPLOAD.uploadId,
                part: 2,
                content: 'a'.repeat(32),
            });

            expect(uploadQueue[0]).toEqual({
                ...DEFAULT_UPLOAD,
                parts: [
                    {status: TestOnlyFileUploadPartStatus.DONE},
                    {status: TestOnlyFileUploadPartStatus.UPLOADING},
                    {status: TestOnlyFileUploadPartStatus.UPLOADING},
                ],
            });

            // Wait for request response
            await promise;

            expect(uploadQueue[0]).toEqual({
                ...DEFAULT_UPLOAD,
                parts: [
                    {status: TestOnlyFileUploadPartStatus.DONE},
                    {status: TestOnlyFileUploadPartStatus.UPLOADING},
                    {status: TestOnlyFileUploadPartStatus.DONE},
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
                type: TestOnlyUploadRequestType.UPDATE,
                id: DEFAULT_UPLOAD.uploadId,
                part: 2,
                content: 'a'.repeat(32),
            });

            expect(uploadQueue[0]).toEqual({
                ...DEFAULT_UPLOAD,
                parts: [
                    {status: TestOnlyFileUploadPartStatus.DONE},
                    {status: TestOnlyFileUploadPartStatus.UPLOADING},
                    {status: TestOnlyFileUploadPartStatus.UPLOADING},
                ],
            });

            // Wait for request response
            await promise;

            expect(uploadQueue[0]).toEqual({
                ...DEFAULT_UPLOAD,
                parts: [
                    {status: TestOnlyFileUploadPartStatus.DONE},
                    {status: TestOnlyFileUploadPartStatus.UPLOADING},
                    {status: TestOnlyFileUploadPartStatus.READY},
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
                type: TestOnlyUploadRequestType.FINISH,
                id: UPLOADED_UPLOAD.uploadId,
                numberOfParts: UPLOADED_UPLOAD.parts.length,
            });

            expect(uploadQueue[0]).toEqual({
                ...UPLOADED_UPLOAD,
                status: TestOnlyFileUploadStatus.FINISHING,
            });

            // Wait for request response
            await promise;

            expect(uploadQueue[0]).toEqual({
                ...UPLOADED_UPLOAD,
                status: TestOnlyFileUploadStatus.DONE,
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
                type: TestOnlyUploadRequestType.FINISH,
                id: UPLOADED_UPLOAD.uploadId,
                numberOfParts: UPLOADED_UPLOAD.parts.length,
            });

            expect(uploadQueue[0]).toEqual({
                ...UPLOADED_UPLOAD,
                status: TestOnlyFileUploadStatus.FINISHING,
            });

            // Wait for request response
            await promise;

            expect(uploadQueue[0]).toEqual({
                ...UPLOADED_UPLOAD,
                status: TestOnlyFileUploadStatus.UPLOADING,
            });
        });
    });
});
