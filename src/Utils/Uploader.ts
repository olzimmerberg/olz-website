import range from 'lodash/range';
import {OlzApi} from '../Api/client';
import {EventTarget} from './EventTarget';
import {assertUnreachable} from './generalUtils';
import {obfuscateForUpload} from './uploadUtils';

const MAX_CONCURRENT_REQUESTS = 5;
export const MAX_PART_LENGTH = 1024 * 32;

type FileUploadId = string;

interface FileUpload {
    uploadId: FileUploadId;
    base64Content: string;
    parts: FileUploadPart[];
    status: FileUploadStatus;
}

type FileUploadStatus = 'UPLOADING'|'FINISHING'|'DONE';

interface FileUploadPart {
    status: FileUploadPartStatus;
}

type FileUploadPartStatus = 'READY'|'UPLOADING'| 'DONE';

interface UploaderState {
    numberOfRunningRequests: number;
    uploadIds: FileUploadId[];
    uploadsById: {[uploadId: string]: UploadState};
    nextRequests: UploadRequest[];
}

interface UploadState {
    progress: number; // 0 = 0% done; 1 = 100% done
    size: number;
}

type UploadRequest = UpdateUploadRequest|FinishUploadRequest;

interface UpdateUploadRequest {
    type: 'UPDATE';
    id: FileUploadId;
    part: number;
    content: string;
}

interface FinishUploadRequest {
    type: 'FINISH';
    id: FileUploadId;
    numberOfParts: number;
}

export class Uploader extends EventTarget<{'uploadFinished': FileUploadId}> {
    private static instance: Uploader|null = null;

    protected olzApi = new OlzApi();

    protected uploadQueue: FileUpload[] = [];

    public async upload(base64Content: string, suffix: string|null): Promise<FileUploadId> {
        const uploadId = await this.add(base64Content, suffix);
        return new Promise((resolve) => {
            const onUploadFinished = (e: CustomEvent<string>) => {
                const finishedUploadId = e.detail;
                if (finishedUploadId === uploadId) {
                    this.removeEventListener('uploadFinished', onUploadFinished);
                    resolve(finishedUploadId);
                }
            };
            this.addEventListener('uploadFinished', onUploadFinished);
        });
    }

    public add(base64Content: string, suffix: string|null): Promise<FileUploadId> {
        return this.olzApi.call('startUpload', {suffix})
            .then((response) => {
                const uploadId: FileUploadId|null = response.id;
                if (!uploadId) {
                    throw new Error('olzApi.startUpload did not return an id');
                }
                const numParts = Math.ceil(base64Content.length / MAX_PART_LENGTH);
                const parts: FileUploadPart[] = range(numParts).map(() => ({
                    status: 'READY',
                }));
                this.uploadQueue.push({
                    uploadId,
                    base64Content,
                    parts,
                    status: 'UPLOADING',
                });
                this.process();
                return uploadId;
            });
    }

    protected process(): void {
        this.uploadQueue = this.uploadQueue.filter(
            (upload) => upload.status !== 'DONE',
        );
        if (this.uploadQueue.length < 1) {
            return;
        }
        const state = this.getState();
        const numberOfRequestsToStart = MAX_CONCURRENT_REQUESTS - state.numberOfRunningRequests;
        for (let requestIndex = 0; requestIndex < numberOfRequestsToStart; requestIndex++) {
            const requestAtIndex = state.nextRequests[requestIndex];
            if (requestAtIndex) {
                this.processRequest(requestAtIndex);
            }
        }
    }

    protected processRequest(request: UploadRequest): Promise<any> {
        const requestUpload = this.uploadQueue.find((upload) =>
            upload.uploadId === request.id);
        if (!requestUpload) {
            throw new Error('upload queue not found');
        }
        switch (request.type) {
            case 'UPDATE': {
                const part = requestUpload.parts[request.part];
                part.status = 'UPLOADING';
                return this.olzApi.call('updateUpload', {
                    id: request.id,
                    part: request.part,
                    content: request.content,
                })
                    .then(() => {
                        part.status = 'DONE';
                        this.process();
                    })
                    .catch(() => {
                        part.status = 'READY';
                        this.process();
                    });
            }
            case 'FINISH': {
                requestUpload.status = 'FINISHING';
                return this.olzApi.call('finishUpload', {
                    id: request.id,
                    numberOfParts: request.numberOfParts,
                })
                    .then(() => {
                        this.dispatchEvent('uploadFinished', request.id);
                        requestUpload.status = 'DONE';
                        this.process();
                    })
                    .catch(() => {
                        requestUpload.status = 'UPLOADING';
                        this.process();
                    });
            }
            /* istanbul ignore next */
            default:
                return assertUnreachable(request);
        }
    }

    public getState(): UploaderState {
        let numberOfRunningRequests = 0;
        const uploadIds: FileUploadId[] = [];
        const uploadsById: {[uploadId: string]: UploadState} = {};
        const nextRequests: UploadRequest[] = [];
        for (let uploadIndex = 0; uploadIndex < this.uploadQueue.length; uploadIndex++) {
            const uploadAtIndex = this.uploadQueue[uploadIndex];
            if (uploadAtIndex.status === 'FINISHING') {
                numberOfRunningRequests++;
            }
            let numDoneParts = 0;
            const numParts = uploadAtIndex.parts.length;
            for (let partIndex = 0; partIndex < numParts; partIndex++) {
                const partAtIndex = uploadAtIndex.parts[partIndex];
                switch (partAtIndex.status) {
                    case 'READY': {
                        const partContent = uploadAtIndex.base64Content.substr(
                            partIndex * MAX_PART_LENGTH, MAX_PART_LENGTH,
                        );
                        const obfuscatedContent = obfuscateForUpload(partContent);
                        nextRequests.push({
                            type: 'UPDATE',
                            id: uploadAtIndex.uploadId,
                            part: partIndex,
                            content: obfuscatedContent,
                        });
                        break;
                    }
                    case 'UPLOADING': {
                        numberOfRunningRequests++;
                        numDoneParts += 0.5;
                        break;
                    }
                    case 'DONE': {
                        numDoneParts += 1;
                        break;
                    }
                    /* istanbul ignore next */
                    default:
                        return assertUnreachable(partAtIndex.status);
                }
            }
            if (uploadAtIndex.status === 'UPLOADING' && numDoneParts === numParts) {
                nextRequests.push({
                    type: 'FINISH',
                    id: uploadAtIndex.uploadId,
                    numberOfParts: numParts,
                });
            }
            uploadIds.push(uploadAtIndex.uploadId);
            uploadsById[uploadAtIndex.uploadId] = {
                progress: numDoneParts / (numParts + 1),
                size: uploadAtIndex.base64Content.length,
            };
        }
        return {
            numberOfRunningRequests,
            uploadIds,
            uploadsById,
            nextRequests,
        };
    }

    public static getInstance(): Uploader {
        if (Uploader.instance === null) {
            Uploader.instance = new Uploader();
        }
        return Uploader.instance;
    }
}

export type TestOnlyFileUpload = FileUpload;
export type TestOnlyUploadRequest = UploadRequest;
export type TestOnlyUpdateUploadRequest = UpdateUploadRequest;
