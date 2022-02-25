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
        const uploadingInfo = `Uploading: ${uploadingFile.file.name} (${uploadingFile.uploadId})`;
        return (
            <div className='olz-upload-file uploading' title={uploadingInfo}>
                <div className='progress-container'>
                    <OlzProgressBar progress={uploadingFile.uploadProgress} />
                </div>
                <div className='info'>
                    {uploadingInfo}
                </div>
            </div>
        );
    }
    const uploadedFile: UploadedFile = uploadFile;
    const uploadedInfo = `Uploaded: ${uploadedFile.uploadId}`;
    return (
        <div className='olz-upload-file uploaded' title={uploadedInfo}>
            <div className='info'>
                {uploadedInfo}
            </div>
        </div>
    );
};
