import React from 'react';
import {useDropzone} from 'react-dropzone';
import {readBase64} from '../../../../src/Utils/fileUtils';
import {isDefined} from '../../../../src/Utils/generalUtils';
import {getBase64FromCanvas, getResizedCanvas, loadImageFromBase64} from '../../../../src/Utils/imageUtils';
import {Uploader} from '../../../../src/Utils/Uploader';
import {OlzUploadImage} from '../OlzUploadImage/OlzUploadImage';
import {UploadFile, UploadingFile, UploadedFile} from '../types';
import {serializeUploadFile} from '../utils';
import {dataHref} from '../../../../src/Utils/constants';

import '../../../../_/styles/dropzone.scss';
import './OlzMultiImageUploader.scss';

const MAX_IMAGE_SIZE = 800;

const uploader = Uploader.getInstance();

interface OlzMultiImageUploaderProps {
    initialUploadIds?: string[];
    onUploadIdsChange?: (uploadIds: string[]) => unknown;
}

/** @deprecated Use react-hook-form and OlzMultiImageField. */
export const OlzMultiImageUploader = (props: OlzMultiImageUploaderProps): React.ReactElement => {
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
            if (!base64Content.match(/^data:image\/(jpg|jpeg|png)/i)) {
                console.error(`${file.name} ist ein beschädigtes Bild, bitte wähle ein korrektes Bild aus. \nEin Bild hat meist die Endung ".jpg", ".jpeg" oder ".png".`);
                continue; // eslint-disable-line no-continue
            }
            try {
                // eslint-disable-next-line no-await-in-loop
                const img = await loadImageFromBase64(base64Content);
                const canvas = getResizedCanvas(img, MAX_IMAGE_SIZE);
                const resizedBase64 = getBase64FromCanvas(canvas);
                if (!resizedBase64) {
                    continue; // eslint-disable-line no-continue
                }
                // eslint-disable-next-line no-await-in-loop
                const uploadId = await uploader.add(resizedBase64, '.jpg');
                const evenNewerUploadingFiles = [...newUploadingFiles];
                evenNewerUploadingFiles[fileListIndex].uploadId = uploadId;
                setUploadingFiles(evenNewerUploadingFiles);
            } catch (err: unknown) {
                console.error(`${file.name} ist kein Bild, bitte wähle ein Bild aus. \nEin Bild hat meist die Endung ".jpg", ".jpeg" oder ".png".`);
                continue; // eslint-disable-line no-continue
            }
        }
    };

    const onDelete = React.useCallback((uploadId: string) => {
        const newUploadedFiles = uploadedFiles.filter(
            (uploadedFile) => uploadedFile.uploadId !== uploadId,
        );
        setUploadedFiles(newUploadedFiles);
        const uploadIds = newUploadedFiles.map((uploadedFile) => uploadedFile.uploadId);
        if (props.onUploadIdsChange) {
            props.onUploadIdsChange(uploadIds);
        }
    }, [uploadedFiles]);

    const {getRootProps, getInputProps, isDragActive} = useDropzone({
        accept: {'image/*': ['.png', '.jpg', '.jpeg']},
        onDrop,
    });

    const uploadFiles: UploadFile[] = [...uploadedFiles, ...uploadingFiles];

    return (
        <div className='olz-multi-image-uploader'>
            <div className='state'>
                {uploadFiles.map((uploadFile) => <OlzUploadImage
                    key={serializeUploadFile(uploadFile)}
                    uploadFile={uploadFile}
                    onDelete={onDelete}
                />)}
            </div>
            <div className="dropzone" {...getRootProps()}>
                <input {...getInputProps()} />
                <img
                    src={`${dataHref}assets/icns/link_image_16.svg`}
                    alt=""
                    className="noborder"
                    width="32"
                    height="32"
                />
                {
                    isDragActive ?
                        <div>Bilder hierhin ziehen...</div> :
                        <div>Bilder hierhin ziehen, oder Klicken, um Bilder auszuwählen</div>
                }
            </div>
        </div>
    );
};
