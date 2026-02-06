import React from 'react';
import {OlzProgressBar} from '../../Common/OlzProgressBar/OlzProgressBar';
import {UploadFile, UploadingFile, UploadedFile, RegisteringFile} from '../types';
import {codeHref, dataHref} from '../../../Utils/constants';
import {getCompactUploadId, getFileWarning} from '../../../Utils/fileUtils';

import './OlzUploadFile.scss';

interface OlzUploadFileProps {
    uploadFile?: UploadFile;
    onDelete?: (uploadId: string) => unknown;
}

export const OlzUploadFile = (props: OlzUploadFileProps): React.ReactElement => {
    const uploadFile = props.uploadFile;
    if (uploadFile?.uploadState === 'REGISTERING') {
        const registeringFile: RegisteringFile = uploadFile;
        const registeringInfo = `Registering: ${registeringFile.file.name}`;
        return (
            <div className='olz-upload-file uploading' title={registeringInfo}>
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
        const uploadingInfo = `Uploading: ${uploadingFile.file.name} (${uploadingFile.uploadId})`;
        return (
            <div className='olz-upload-file uploading' title={uploadingInfo}>
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
        const warning = getFileWarning(uploadedFile.uploadId);
        const prettyWarning = warning ? (
            <a
                href={`${codeHref}fragen_und_antworten/datei_upload`}
                target='_blank'
                title={warning}
            >⚠️</a>
        ) : '✅';
        const fullInfo = `Uploaded: ${uploadedFile.uploadId}`;
        const compactInfo = `Uploaded: ${getCompactUploadId(uploadedFile.uploadId)}`;
        const onCopy = React.useCallback(() => {
            const copyContent = `[LABEL](./${uploadFile.uploadId})`;
            navigator.clipboard.writeText(copyContent);
        }, [props.uploadFile]);
        const copyButton = (
            <button
                id='copy-button'
                className='button'
                type='button'
                title='Datei-Code Kopieren'
                onClick={onCopy}
            >
                <img src={`${dataHref}assets/icns/copy_16.svg`} alt='Cp' />
            </button>
        );
        const deleteButton = props.onDelete ? (
            <button
                id='delete-button'
                className='button'
                type='button'
                title='Datei löschen'
                onClick={() => props.onDelete?.(uploadedFile.uploadId)}
            >
                <img src={`${dataHref}assets/icns/delete_16.svg`} alt='Lö' />
            </button>
        ) : undefined;
        return (
            <div className='olz-upload-file uploaded'>
                <div className='uploaded-file-container test-flaky'>
                    {prettyWarning}
                    <div className='info' title={fullInfo}>{compactInfo}</div>
                    {copyButton}
                    {deleteButton}
                </div>
            </div>
        );
    }
    throw new Error('Tertium non datur');

};
