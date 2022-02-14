import React from 'react';
import {OlzProgressBar} from '../../common/OlzProgressBar/OlzProgressBar';
import {UploadFile, UploadingFile, UploadedFile} from '../types';

interface OlzUploadFileProps {
    uploadFile?: UploadFile;
}

export const OlzUploadFile = (props: OlzUploadFileProps) => {
    const uploadFile = props.uploadFile;
    if (uploadFile.uploadState === 'UPLOADING') {
        const uploadingFile: UploadingFile = uploadFile;
        return (
            <div className='olz-upload-file uploading'>
                Uploading: {uploadingFile.file.name} ({uploadingFile.uploadId})
                <OlzProgressBar progress={uploadingFile.uploadProgress} />
            </div>
        );
    }
    const uploadedFile: UploadedFile = uploadFile;
    return (
        <div className='olz-upload-file uploaded'>
            Uploaded: {uploadedFile.uploadId}
        </div>
    );
};
