import React from 'react';
import {OlzProgressBar} from '../../Common/OlzProgressBar/OlzProgressBar';
import {UploadFile, UploadingFile, UploadedFile} from '../types';
import {dataHref} from '../../../Utils/constants';

import './OlzUploadFile.scss';

interface OlzUploadFileProps {
    uploadFile?: UploadFile;
    onDelete?: (uploadId: string) => unknown;
    isMarkdown?: boolean;
}

export const OlzUploadFile = (props: OlzUploadFileProps): React.ReactElement => {
    const uploadFile = props.uploadFile;
    if (uploadFile?.uploadState === 'UPLOADING') {
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
    if (uploadFile?.uploadState === 'UPLOADED') {
        const uploadedFile: UploadedFile = uploadFile;
        const uploadedInfo = `Uploaded: ${uploadedFile.uploadId}`;
        const onCopy = React.useCallback(() => {
            const copyContent = props.isMarkdown
                ? `[LABEL](./${uploadFile.uploadId})`
                : `<DATEI=${uploadFile.uploadId} text="LABEL">`;
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
            <div className='olz-upload-file uploaded' title={uploadedInfo}>
                <div className='uploaded-file-container test-flaky'>
                    <div className='info'>
                        {uploadedInfo}
                    </div>
                    {copyButton}
                    {deleteButton}
                </div>
            </div>
        );
    }
    throw new Error('Tertium non datur');

};
