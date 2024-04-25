import {UploadFile, RegisteringFile, UploadingFile, UploadedFile} from './types';

export function serializeUploadFile(uploadFile: UploadFile): string {
    if (uploadFile.uploadState === 'UPLOADED') {
        return `${uploadFile.uploadState}-${uploadFile.uploadId}`;
    }
    return `${uploadFile.uploadState}-${uploadFile.id}`;
}

export function isRegisteringFile(uploadFile: UploadFile): uploadFile is RegisteringFile {
    return uploadFile.uploadState === 'REGISTERING';
}

export function isUploadingFile(uploadFile: UploadFile): uploadFile is UploadingFile {
    return uploadFile.uploadState === 'UPLOADING';
}

export function isUploadedFile(uploadFile: UploadFile): uploadFile is UploadedFile {
    return uploadFile.uploadState === 'UPLOADED';
}
