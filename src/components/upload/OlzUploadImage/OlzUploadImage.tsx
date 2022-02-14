import React from 'react';
import {OlzProgressBar} from '../../common/OlzProgressBar/OlzProgressBar';
import {UploadFile, UploadingFile, UploadedFile} from '../types';

interface OlzUploadImageProps {
    uploadFile?: UploadFile;
}

export const OlzUploadImage = (props: OlzUploadImageProps) => {
    const uploadFile = props.uploadFile;
    if (uploadFile.uploadState === 'UPLOADING') {
        const uploadingFile: UploadingFile = uploadFile;
        return (
            <div className='olz-upload-image uploading'>
                Uploading: {uploadingFile.file.name} - {uploadingFile.uploadId}
                <OlzProgressBar progress={uploadingFile.uploadProgress} />
            </div>
        );
    }
    const uploadedFile: UploadedFile = uploadFile;
    return (
        <div className='olz-upload-image uploaded'>
            Uploaded: {uploadedFile.uploadId}
        </div>
    );
};
