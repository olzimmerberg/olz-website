import React from 'react';
import {useDropzone} from 'react-dropzone';
import {readBase64} from '../../../../src/Utils/fileUtils';
import {getBase64FromCanvas, getResizedCanvas, loadImageFromBase64} from '../../../../src/Utils/imageUtils';
import {Uploader} from '../../../../src/Utils/Uploader';
import {OlzUploadImage} from '../OlzUploadImage/OlzUploadImage';
import {UploadingFile, UploadedFile} from '../types';
import {serializeUploadFile} from '../utils';

import '../../../../_/styles/dropzone.scss';
import './OlzImageUploader.scss';

const MAX_IMAGE_SIZE = 800;

const uploader = Uploader.getInstance();

interface OlzImageUploaderProps {
    initialUploadId?: string|null;
    onUploadIdChange?: (uploadId: string|null) => unknown;
}

export const OlzImageUploader = (props: OlzImageUploaderProps): React.ReactElement => {
    const initialUploadedFile: UploadedFile|null = props.initialUploadId && {uploadState: 'UPLOADED', uploadId: props.initialUploadId} || null;
    const [file, setFile] = React.useState<UploadedFile|UploadingFile|null>(initialUploadedFile);

    React.useEffect(() => {
        const clock = setInterval(() => {
            const state = uploader.getState();
            if (!file?.uploadId) {
                return;
            }
            const stateOfUploadingFile = state.uploadsById[file.uploadId];
            if (!stateOfUploadingFile) {
                return;
            }
            if (file.uploadState === 'UPLOADING') {
                file.uploadProgress = stateOfUploadingFile.progress;
                setFile(file);

            }
        }, 1000);
        return () => clearInterval(clock);
    }, [file]);

    React.useEffect(() => {
        const callback = (_event: CustomEvent<string>) => {
            if (!file?.uploadId) {
                throw new Error('Upload ID must be defined');
            }
            const uploadedFile: UploadedFile = {
                uploadState: 'UPLOADED',
                uploadId: file.uploadId,
            };
            setFile(uploadedFile);
            if (props.onUploadIdChange) {
                props.onUploadIdChange(file.uploadId);
            }
        };
        uploader.addEventListener('uploadFinished', callback);
        return () => uploader.removeEventListener('uploadFinished', callback);
    }, [file]);

    const onDrop = async (acceptedFiles: File[]) => {
        const acceptedFile = acceptedFiles[0];
        setFile({
            uploadState: 'UPLOADING',
            file: acceptedFile,
            uploadProgress: 0,
        });
        const base64Content = await readBase64(acceptedFile);
        if (!base64Content.match(/^data:image\/(jpg|jpeg|png)/i)) {
            console.error(`${acceptedFile.name} ist ein beschädigtes Bild, bitte wähle ein korrektes Bild aus. \nEin Bild hat meist die Endung ".jpg", ".jpeg" oder ".png".`);
            setFile(null);
            return;
        }
        try {
            const img = await loadImageFromBase64(base64Content);
            const canvas = getResizedCanvas(img, MAX_IMAGE_SIZE);
            const resizedBase64 = getBase64FromCanvas(canvas);
            if (!resizedBase64) {
                setFile(null);
                return;
            }
            const uploadId = await uploader.add(resizedBase64, '.jpg');
            setFile({
                uploadState: 'UPLOADING',
                file: acceptedFile,
                uploadProgress: 0,
                uploadId: uploadId,
            });
        } catch (err: unknown) {
            console.error(`${acceptedFile.name} ist kein Bild, bitte wähle ein Bild aus. \nEin Bild hat meist die Endung ".jpg", ".jpeg" oder ".png".`);
            setFile(null);

        }
    };

    const onDelete = React.useCallback(() => {
        setFile(null);
    }, []);

    const {getRootProps, getInputProps, isDragActive} = useDropzone({
        accept: 'image/jpeg, image/png',
        maxFiles: 1,
        onDrop,
    });

    return (
        <div className='olz-image-uploader'>
            <div className='state'>
                {file ? <OlzUploadImage
                    key={serializeUploadFile(file)}
                    uploadFile={file}
                    onDelete={onDelete}
                /> : []}
            </div>
            <div className="dropzone" {...getRootProps()}>
                <input {...getInputProps()} />
                <img
                    src="icns/link_image_16.svg"
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
