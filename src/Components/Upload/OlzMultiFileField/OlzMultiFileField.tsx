import React from 'react';
import {useController, Control, FieldValues, FieldErrors, UseControllerProps, Path} from 'react-hook-form';
import {useDropzone} from 'react-dropzone';
import {readBase64} from '../../../../src/Utils/fileUtils';
import {isDefined} from '../../../../src/Utils/generalUtils';
import {Uploader} from '../../../../src/Utils/Uploader';
import {OlzUploadFile} from '../OlzUploadFile/OlzUploadFile';
import {UploadFile, UploadingFile, UploadedFile} from '../types';
import {serializeUploadFile} from '../utils';
import {dataHref} from '../../../../src/Utils/constants';

import '../../../Components/Common/OlzStyles/dropzone.scss';
import './OlzMultiFileField.scss';

const uploader = Uploader.getInstance();

interface OlzMultiFileFieldProps<Values extends FieldValues, Name extends Path<Values>> {
    title?: string;
    name: Name;
    rules?: UseControllerProps<Values, Name>['rules'];
    errors?: FieldErrors<Values>;
    control: Control<Values, Name>;
    setIsLoading: (isLoading: boolean) => void;
    disabled?: boolean;
}

export const OlzMultiFileField = <
    Values extends FieldValues,
    Name extends Path<Values>
>(props: OlzMultiFileFieldProps<Values, Name>): React.ReactElement => {
    const errorMessage = props.errors?.[props.name]?.message;
    const errorClassName = errorMessage ? ' is-invalid' : '';
    const disabledClassName = props.disabled ? ' disabled' : '';

    const {field} = useController({
        name: props.name,
        control: props.control,
        rules: props.rules,
    });

    const [uploadingFiles, setUploadingFiles] = React.useState<UploadingFile[]>([]);
    const [uploadedFiles, setUploadedFiles] = React.useState<UploadedFile[]>(() => field.value.map(
        (uploadId: string) => ({uploadState: 'UPLOADED', uploadId}),
    ));

    React.useEffect(() => {
        if (uploadingFiles.length === 0) {
            return () => undefined;
        }
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
                field.onChange(uploadIds);
            }
        };
        uploader.addEventListener('uploadFinished', callback);
        return () => uploader.removeEventListener('uploadFinished', callback);
    }, [uploadingFiles, uploadedFiles]);

    React.useEffect(() => {
        const hasUploadingFiles = uploadingFiles.length > 0;
        props.setIsLoading(hasUploadingFiles);
    }, [uploadingFiles]);

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
        const newUploadedFiles = uploadedFiles.filter(
            (uploadedFile) => uploadedFile.uploadId !== uploadId,
        );
        setUploadedFiles(newUploadedFiles);
        const uploadIds = newUploadedFiles.map((uploadedFile) => uploadedFile.uploadId);
        field.onChange(uploadIds);
    }, [uploadedFiles]);

    const {getRootProps, getInputProps, isDragActive} = useDropzone({
        disabled: props.disabled,
        onDrop,
    });

    const uploadFiles: UploadFile[] = [...uploadedFiles, ...uploadingFiles];

    return (<>
        <label htmlFor={`${props.name}-field`}>{props.title}</label>
        <div id={`${props.name}-field`} className={`olz-multi-file-field${disabledClassName}`}>
            <div className='state'>
                {uploadFiles.map((uploadFile) => <OlzUploadFile
                    key={serializeUploadFile(uploadFile)}
                    uploadFile={uploadFile}
                    onDelete={onDelete}
                />)}
            </div>
            <div className={`dropzone${errorClassName}`} {...getRootProps()}>
                <input {...getInputProps()} disabled={props.disabled} id={`${props.name}-input`} />
                <img
                    src={`${dataHref}assets/icns/link_any_16.svg`}
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
        {errorMessage && <p className='error'>{String(errorMessage)}</p>}
    </>);
};
