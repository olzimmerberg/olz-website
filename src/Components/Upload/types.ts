export interface RegisteringFile {
    readonly uploadState: 'REGISTERING';
    id: string;
    file: File;
}

export interface UploadingFile {
    readonly uploadState: 'UPLOADING';
    id: string;
    file: File;
    uploadId: string;
    uploadProgress: number;
}

export interface UploadedFile {
    readonly uploadState: 'UPLOADED';
    uploadId: string;
}

export type UploadFile = RegisteringFile|UploadingFile|UploadedFile;
