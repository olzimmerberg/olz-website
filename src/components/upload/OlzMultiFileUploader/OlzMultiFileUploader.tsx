import React, {ChangeEvent} from 'react';
import {Uploader} from '../../../utils/Uploader';

const uploader = Uploader.getInstance();

interface UploadingFile {
    file: File;
    uploadId?: string;
    uploadProgress: number;
}

export const OlzMultiFileUploader = () => {
    const [uploadingFiles, setUploadingFiles] = React.useState<UploadingFile[]>([]);

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
                uploader.add(base64Content, file.name)
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
            {uploadingFile.file.name} - {uploadingFile.uploadId} - {uploadingFile.uploadProgress}
        </div>
    ));

    return (
        <div>
            {uploadingElems}
            <input
                type='file'
                multiple
                id='multi-file-uploader-input'
                onChange={onFileInput}
            />
        </div>
    );
};
