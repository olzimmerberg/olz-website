import React from 'react';
import {useDropzone} from 'react-dropzone';
import {readBase64} from '../../../utils/fileUtils';
import {Uploader} from '../../../utils/Uploader';

const uploader = Uploader.getInstance();

interface UploadingFile {
    file: File;
    uploadId?: string;
    uploadProgress: number;
}

interface UploadedFile {
    uploadId: string;
}

interface OlzMultiFileUploaderProps {
    onUploadIdsChange?: (uploadIds: string[]) => any;
}

export const OlzMultiFileUploader = (props: OlzMultiFileUploaderProps) => {
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

    const onDrop = async (acceptedFiles: File[]) => {
        const newUploadingFiles = [...uploadingFiles];
        setUploadingFiles(newUploadingFiles);
        for (let fileListIndex = 0; fileListIndex < acceptedFiles.length; fileListIndex++) {
            const file = acceptedFiles[fileListIndex];
            newUploadingFiles.push({file, uploadProgress: 0});
            const base64Content = await readBase64(file);
            const suffix = file.name.split('.').slice(-1)[0];
            const uploadId = await uploader.add(base64Content, `.${suffix}`);
            const evenNewerUploadingFiles = [...newUploadingFiles];
            evenNewerUploadingFiles[fileListIndex].uploadId = uploadId;
            setUploadingFiles(evenNewerUploadingFiles);
        }
    };

    const {getRootProps, getInputProps, isDragActive} = useDropzone({onDrop})

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
            <div className="dropzone" {...getRootProps()}>
                <input {...getInputProps()} />
                <img
                    src="icns/link_any_16.svg"
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
