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
        const uploadingInfo = `Uploading: ${uploadingFile.file.name} - ${uploadingFile.uploadId}`;
        return (
            <div className='olz-upload-image uploading' title={uploadingInfo}>
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
        <div className='olz-upload-image uploaded' title={uploadedInfo}>
            <div className='image-container'>
                <img
                    src={`/temp/${uploadedFile.uploadId}`}
                    alt=''
                    className='image'
                />
            </div>
            <div className='info'>
                {uploadedInfo}
            </div>
        </div>
    );
};
