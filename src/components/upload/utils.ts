import {UploadFile} from './types';

export function serializeUploadFile(uploadFile: UploadFile): string {
    return `${uploadFile.uploadState}-${uploadFile.uploadId}`;
}
