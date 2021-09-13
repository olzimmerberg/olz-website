import React, {ChangeEvent} from 'react';
import {readBase64} from '../../../utils/fileUtils';
import {getBase64FromCanvas, getResizedCanvas, loadImageFromBase64} from '../../../utils/imageUtils';
import {Uploader} from '../../../utils/Uploader';

const MAX_IMAGE_SIZE = 800;

const uploader = Uploader.getInstance();

interface UploadingFile {
    file: File;
    uploadId?: string;
    uploadProgress: number;
}

interface UploadedFile {
    uploadId: string;
}

interface OlzMultiImageUploaderProps {
    onUploadIdsChange?: (uploadIds: string[]) => any;
}

export const OlzMultiImageUploader = (props: OlzMultiImageUploaderProps) => {
    const [uploadingFiles, setUploadingFiles] = React.useState<UploadingFile[]>([]);
    const [uploadedFiles, setUploadedFiles] = React.useState<UploadedFile[]>([]);

    React.useEffect(() => {
        const clock = setInterval(() => {
            const state = uploader.getState();
            const newUploadingFiles = uploadingFiles.map(uploadingFile => {
                if (!uploadingFile.uploadId) {
                    return uploadingFile;
                }
                const stateOfUploadingFile = state.uploadsById[uploadingFile.uploadId];
                if (!stateOfUploadingFile) {
                    return undefined;
                }
                uploadingFile.uploadProgress = stateOfUploadingFile.progress;
                return uploadingFile;
            }).filter(uploadingFile => uploadingFile !== undefined);
            setUploadingFiles(newUploadingFiles);
        }, 1000);
        return () => clearInterval(clock)
    }, [uploadingFiles]);

    React.useEffect(() => {
        const callback = (event: CustomEvent<string>) => {
            const uploadId = event.detail;
            const newUploadingFiles = uploadingFiles.filter(
                uploadingFile => uploadingFile.uploadId !== uploadId);
            const wasUploading = 
                newUploadingFiles.length !== uploadingFiles.length;
            if (wasUploading) {
                setUploadingFiles(newUploadingFiles);
                const newUploadedFiles = [
                    ...uploadedFiles,
                    {uploadId},
                ];
                setUploadedFiles(newUploadedFiles);
                const uploadIds = newUploadedFiles.map(uploadedFile => uploadedFile.uploadId);
                props.onUploadIdsChange(uploadIds);
            }
        };
        uploader.addEventListener('uploadFinished', callback);
        return () => uploader.removeEventListener('uploadFinished', callback);
    }, [uploadingFiles, uploadedFiles]);

    const onFileInput = async (event: ChangeEvent<HTMLInputElement>) => {
        const fileList = event.target.files;
        const newUploadingFiles = [...uploadingFiles];
        setUploadingFiles(newUploadingFiles);
        for (let fileListIndex = 0; fileListIndex < fileList.length; fileListIndex++) {
            const file = fileList[fileListIndex];
            newUploadingFiles.push({file, uploadProgress: 0});
            const base64Content = await readBase64(file);
            if (!base64Content.match(/^data:image\/(jpg|jpeg|png)/i)) {
                console.error(`${file.name} ist ein beschädigtes Bild, bitte wähle ein korrektes Bild aus. \nEin Bild hat meist die Endung ".jpg", ".jpeg" oder ".png".`);
                continue;
            }
            try {
                const img = await loadImageFromBase64(base64Content);
                const canvas = getResizedCanvas(img, MAX_IMAGE_SIZE);
                const resizedBase64 = getBase64FromCanvas(canvas);
                if (!resizedBase64) {
                    continue;
                }
                const uploadId = await uploader.add(resizedBase64, `.jpg`);
                const evenNewerUploadingFiles = [...newUploadingFiles];
                evenNewerUploadingFiles[fileListIndex].uploadId = uploadId;
                setUploadingFiles(evenNewerUploadingFiles);
            } catch (err: unknown) {
                console.error(`${file.name} ist kein Bild, bitte wähle ein Bild aus. \nEin Bild hat meist die Endung ".jpg", ".jpeg" oder ".png".`);
                continue;
            }
        }
    };

    const uploadingElems = uploadingFiles.map(uploadingFile => (
        <div key={uploadingFile.file.name}>
            Uploading: {uploadingFile.file.name} - {uploadingFile.uploadId} - {uploadingFile.uploadProgress}
        </div>
    ));

    const uploadedElems = uploadedFiles.map(uploadedFile => (
        <div key={uploadedFile.uploadId}>
            Uploaded: {uploadedFile.uploadId}
        </div>
    ));

    return (
        <div>
            {uploadingElems}
            {uploadedElems}
            <input
                type='file'
                multiple
                id='multi-image-uploader-input'
                onChange={onFileInput}
            />
        </div>
    );
};
