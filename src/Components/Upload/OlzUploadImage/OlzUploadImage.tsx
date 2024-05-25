import React from 'react';
import {dataHref} from '../../../Utils/constants';
import {OlzProgressBar} from '../../Common/OlzProgressBar/OlzProgressBar';
import {UploadFile, UploadingFile, UploadedFile, RegisteringFile} from '../types';

import './OlzUploadImage.scss';

interface OlzUploadImageProps {
    uploadFile?: UploadFile;
    onDelete?: (uploadId: string) => unknown;
}

export const OlzUploadImage = (props: OlzUploadImageProps): React.ReactElement => {
    const uploadFile = props.uploadFile;
    if (uploadFile?.uploadState === 'REGISTERING') {
        const registeringFile: RegisteringFile = uploadFile;
        const registeringInfo = `Registering: ${registeringFile.file.name}`;
        return (
            <div className='olz-upload-image registering' title={registeringInfo}>
                <div className='progress-container'>
                    <OlzProgressBar progress={0} />
                </div>
                <div className='info'>
                    {registeringInfo}
                </div>
            </div>
        );
    }
    if (uploadFile?.uploadState === 'UPLOADING') {
        const uploadingFile: UploadingFile = uploadFile;
        const uploadingInfo = `Uploading: ${uploadingFile.file.name} - ${uploadingFile.uploadId}`;
        return (
            <div className='olz-upload-image uploading' title={uploadingInfo}>
                <div className='progress-container'>
                    <OlzProgressBar progress={uploadingFile.uploadProgress * 0.9 + 0.1} />
                </div>
                <div className='info'>
                    {uploadingInfo}
                </div>
            </div>
        );
    }
    if (uploadFile?.uploadState === 'UPLOADED') {
        const uploadedFile: UploadedFile = uploadFile;
        const uploadedInfo = `Uploaded: ${uploadedFile.uploadId}`;
        const onCopy = React.useCallback(() => {
            const copyContent = `![](./${uploadFile.uploadId})`;
            navigator.clipboard.writeText(copyContent);
        }, [props.uploadFile]);
        const copyButton = (
            <button className='button' type='button' onClick={onCopy}>
                <img src={`${dataHref}assets/icns/copy_16.svg`} alt='Cp' />
            </button>
        );
        const deleteButton = props.onDelete ? (
            <button className='button' type='button' onClick={() => {
                if (props.onDelete) {
                    props.onDelete(uploadedFile.uploadId);
                }
            }}>
                <img src={`${dataHref}assets/icns/delete_16.svg`} alt='Lö' />
            </button>
        ) : undefined;
        return (
            <div className='olz-upload-image uploaded' title={uploadedInfo}>
                <div className='image-container'>
                    <img
                        src={`/temp/${uploadedFile.uploadId}`}
                        alt=''
                        className='image'
                    />
                </div>
                <div className='footer test-flaky'>
                    <div className='info'>
                        {uploadedInfo}
                    </div>
                    {copyButton}
                    {deleteButton}
                </div>
            </div>
        );
    }
    throw new Error('Tertium non datur.');

};
