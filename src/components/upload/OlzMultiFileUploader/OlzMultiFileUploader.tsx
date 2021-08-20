import React, {ChangeEvent} from 'react';
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
            const newUploadedFiles = [
                ...uploadedFiles,
                {uploadId: event.detail},
            ];
            setUploadedFiles(newUploadedFiles);
            const uploadIds = newUploadedFiles.map(uploadedFile => uploadedFile.uploadId);
            props.onUploadIdsChange(uploadIds);
        };
        uploader.addEventListener('uploadFinished', callback);
        return () => uploader.removeEventListener('uploadFinished', callback);
    }, [uploadedFiles]);

    const onFileInput = (event: ChangeEvent<HTMLInputElement>) => {
        const fileList = event.target.files;
        const newUploadingFiles = [...uploadingFiles];
        for (let fileListIndex = 0; fileListIndex < fileList.length; fileListIndex++) {
            const file = fileList[fileListIndex];
            const reader = new FileReader();
            reader.onload = (e: ProgressEvent<FileReader>) => {
                const base64Content = e.target.result;
                if (typeof base64Content !== 'string') {
                    return;
                }
                const suffix = file.name.split('.').slice(-1)[0];
                uploader.add(base64Content, `.${suffix}`)
                    .then((uploadId: string) => {
                        const evenNewerUploadingFiles = [...newUploadingFiles];
                        evenNewerUploadingFiles[fileListIndex].uploadId = uploadId;
                        setUploadingFiles(evenNewerUploadingFiles);
                    });
            };
            reader.readAsDataURL(file);
            newUploadingFiles.push({file, uploadProgress: 0});
        }
        setUploadingFiles(newUploadingFiles);
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
                id='multi-file-uploader-input'
                onChange={onFileInput}
            />
        </div>
    );
};
