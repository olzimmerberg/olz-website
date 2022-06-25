export interface UploadingFile {
    readonly uploadState: 'UPLOADING';
    file: File;
    uploadId?: string;
    uploadProgress: number;
}

export interface UploadedFile {
    readonly uploadState: 'UPLOADED';
    uploadId: string;
}

export type UploadFile = UploadingFile|UploadedFile;
