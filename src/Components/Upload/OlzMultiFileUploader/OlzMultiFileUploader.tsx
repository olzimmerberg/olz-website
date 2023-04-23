import React from 'react';
import {useDropzone} from 'react-dropzone';
import {readBase64} from '../../../../src/Utils/fileUtils';
import {isDefined} from '../../../../src/Utils/generalUtils';
import {Uploader} from '../../../../src/Utils/Uploader';
import {OlzUploadFile} from '../OlzUploadFile/OlzUploadFile';
import {UploadFile, UploadingFile, UploadedFile} from '../types';
import {serializeUploadFile} from '../utils';
import {codeHref} from '../../../../src/Utils/constants';

import '../../../../_/styles/dropzone.scss';
import './OlzMultiFileUploader.scss';

const uploader = Uploader.getInstance();

interface OlzMultiFileUploaderProps {
    initialUploadIds?: string[];
    onUploadIdsChange?: (uploadIds: string[]) => unknown;
}

export const OlzMultiFileUploader = (props: OlzMultiFileUploaderProps): React.ReactElement => {
    const initialUploadedFiles: UploadedFile[] = props.initialUploadIds?.map(
        (uploadId) => ({uploadState: 'UPLOADED', uploadId}),
    ) || [];
    const [uploadingFiles, setUploadingFiles] = React.useState<UploadingFile[]>([]);
    const [uploadedFiles, setUploadedFiles] = React.useState<UploadedFile[]>(initialUploadedFiles);

    React.useEffect(() => {
        const clock = setInterval(() => {
            const state = uploader.getState();
            const newUploadingFiles = uploadingFiles.map((uploadingFile) => {
                if (!uploadingFile.uploadId) {
                    return uploadingFile;
                }
                const stateOfUploadingFile = state.uploadsById[uploadingFile.uploadId];
                if (!stateOfUploadingFile) {
                    return undefined;
                }
                uploadingFile.uploadProgress = stateOfUploadingFile.progress;
                return uploadingFile;
            }).filter(isDefined);
            setUploadingFiles(newUploadingFiles);
        }, 1000);
        return () => clearInterval(clock);
    }, [uploadingFiles]);

    React.useEffect(() => {
        const callback = (event: CustomEvent<string>) => {
            const uploadId = event.detail;
            const newUploadingFiles = uploadingFiles.filter(
                (uploadingFile) => uploadingFile.uploadId !== uploadId,
            );
            const wasUploading =
                newUploadingFiles.length !== uploadingFiles.length;
            if (wasUploading) {
                setUploadingFiles(newUploadingFiles);
                const newUploadedFile: UploadedFile = {uploadState: 'UPLOADED', uploadId};
                const newUploadedFiles = [...uploadedFiles, newUploadedFile];
                setUploadedFiles(newUploadedFiles);
                const uploadIds = newUploadedFiles.map((uploadedFile) => uploadedFile.uploadId);
                if (props.onUploadIdsChange) {
                    props.onUploadIdsChange(uploadIds);
                }
            }
        };
        uploader.addEventListener('uploadFinished', callback);
        return () => uploader.removeEventListener('uploadFinished', callback);
    }, [uploadingFiles, uploadedFiles]);

    const onDrop = async (acceptedFiles: File[]) => {
        const newUploadingFiles = [...uploadingFiles];
        setUploadingFiles(newUploadingFiles);
        for (let fileListIndex = 0; fileListIndex < acceptedFiles.length; fileListIndex++) {
            const file = acceptedFiles[fileListIndex];
            newUploadingFiles.push({uploadState: 'UPLOADING', file, uploadProgress: 0});
            // eslint-disable-next-line no-await-in-loop
            const base64Content = await readBase64(file);
            const suffix = file.name.split('.').slice(-1)[0];
            // eslint-disable-next-line no-await-in-loop
            const uploadId = await uploader.add(base64Content, `.${suffix}`);
            const evenNewerUploadingFiles = [...newUploadingFiles];
            evenNewerUploadingFiles[fileListIndex].uploadId = uploadId;
            setUploadingFiles(evenNewerUploadingFiles);
        }
    };

    const onDelete = React.useCallback((uploadId: string) => {
        setUploadedFiles(uploadedFiles.filter(
            (uploadedFile) => uploadedFile.uploadId !== uploadId,
        ));
    }, [uploadedFiles]);

    const {getRootProps, getInputProps, isDragActive} = useDropzone({onDrop});

    const uploadFiles: UploadFile[] = [...uploadedFiles, ...uploadingFiles];

    return (
        <div className='olz-multi-file-uploader'>
            <div className='state'>
                {uploadFiles.map((uploadFile) => <OlzUploadFile
                    key={serializeUploadFile(uploadFile)}
                    uploadFile={uploadFile}
                    onDelete={onDelete}
                />)}
            </div>
            <div className="dropzone" {...getRootProps()}>
                <input {...getInputProps()} />
                <img
                    src={`${codeHref}icns/link_any_16.svg`}
                    alt=""
                    className="noborder"
                    width="32"
                    height="32"
                />
                {
                    isDragActive ?
                        <div>Dateien hierhin ziehen...</div> :
                        <div>Dateien hierhin ziehen, oder Klicken, um Dateien auszuwählen</div>
                }
            </div>
        </div>
    );
};
