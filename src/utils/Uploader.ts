import range from 'lodash/range';
import {OlzApi} from '../api/client';
import {EventTarget} from './EventTarget';
import {assertUnreachable, obfuscateForUpload} from './generalUtils';

const MAX_CONCURRENT_REQUESTS = 5;
export const MAX_PART_LENGTH = 1024 * 32;

type FileUploadId = string;

interface FileUpload {
    uploadId: FileUploadId;
    base64Content: string;
    parts: FileUploadPart[];
    status: FileUploadStatus;
}

enum FileUploadStatus {
    UPLOADING = 'UPLOADING',
    FINISHING = 'FINISHING',
    DONE = 'DONE',
}

interface FileUploadPart {
    status: FileUploadPartStatus;
}

enum FileUploadPartStatus {
    READY = 'READY',
    UPLOADING = 'UPLOADING',
    DONE = 'DONE',
}

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
    type: UploadRequestType.UPDATE;
    id: FileUploadId;
    part: number;
    content: string;
}

interface FinishUploadRequest {
    type: UploadRequestType.FINISH;
    id: FileUploadId;
    numberOfParts: number;
}

enum UploadRequestType {
    UPDATE = 'UPDATE',
    FINISH = 'FINISH',
}

export class Uploader extends EventTarget<{'uploadFinished': FileUploadId}> {
    private static instance: Uploader = null;

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
                const uploadId: FileUploadId = response.id;
                const numParts = Math.ceil(base64Content.length / MAX_PART_LENGTH);
                const parts: FileUploadPart[] = range(numParts).map(() => ({
                    status: FileUploadPartStatus.READY,
                }));
                this.uploadQueue.push({
                    uploadId,
                    base64Content,
                    parts,
                    status: FileUploadStatus.UPLOADING,
                });
                this.process();
                return uploadId;
            });
    }

    protected process(): void {
        this.uploadQueue = this.uploadQueue.filter(
            (upload) => upload.status !== FileUploadStatus.DONE,
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
        const requestUpload = this.uploadQueue.find((upload) => upload.uploadId === request.id);
        switch (request.type) {
            case UploadRequestType.UPDATE: {
                const part = requestUpload.parts[request.part];
                part.status = FileUploadPartStatus.UPLOADING;
                return this.olzApi.call('updateUpload', {
                    id: request.id,
                    part: request.part,
                    content: request.content,
                })
                    .then(() => {
                        part.status = FileUploadPartStatus.DONE;
                        this.process();
                    })
                    .catch(() => {
                        part.status = FileUploadPartStatus.READY;
                        this.process();
                    });
            }
            case UploadRequestType.FINISH: {
                requestUpload.status = FileUploadStatus.FINISHING;
                return this.olzApi.call('finishUpload', {
                    id: request.id,
                    numberOfParts: request.numberOfParts,
                })
                    .then(() => {
                        this.dispatchEvent('uploadFinished', request.id);
                        requestUpload.status = FileUploadStatus.DONE;
                        this.process();
                    })
                    .catch(() => {
                        requestUpload.status = FileUploadStatus.UPLOADING;
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
            if (uploadAtIndex.status === FileUploadStatus.FINISHING) {
                numberOfRunningRequests++;
            }
            let numDoneParts = 0;
            const numParts = uploadAtIndex.parts.length;
            for (let partIndex = 0; partIndex < numParts; partIndex++) {
                const partAtIndex = uploadAtIndex.parts[partIndex];
                switch (partAtIndex.status) {
                    case FileUploadPartStatus.READY: {
                        const partContent = uploadAtIndex.base64Content.substr(
                            partIndex * MAX_PART_LENGTH, MAX_PART_LENGTH,
                        );
                        const obfuscatedContent = obfuscateForUpload(partContent);
                        nextRequests.push({
                            type: UploadRequestType.UPDATE,
                            id: uploadAtIndex.uploadId,
                            part: partIndex,
                            content: obfuscatedContent,
                        });
                        break;
                    }
                    case FileUploadPartStatus.UPLOADING: {
                        numberOfRunningRequests++;
                        numDoneParts += 0.5;
                        break;
                    }
                    case FileUploadPartStatus.DONE: {
                        numDoneParts += 1;
                        break;
                    }
                    /* istanbul ignore next */
                    default:
                        return assertUnreachable(partAtIndex.status);
                }
            }
            if (uploadAtIndex.status === FileUploadStatus.UPLOADING && numDoneParts === numParts) {
                nextRequests.push({
                    type: UploadRequestType.FINISH,
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
export const TestOnlyFileUploadPartStatus = FileUploadPartStatus;
export type TestOnlyUploadRequest = UploadRequest;
export const TestOnlyUploadRequestType = UploadRequestType;
export type TestOnlyUpdateUploadRequest = UpdateUploadRequest;
export const TestOnlyFileUploadStatus = FileUploadStatus;
