import React from 'react';
import {useController, Control, FieldValues, FieldErrors, UseControllerProps, Path} from 'react-hook-form';
import {useDropzone} from 'react-dropzone';
import {readBase64} from '../../../Utils/fileUtils';
import {assert, isDefined} from '../../../Utils/generalUtils';
import {Uploader} from '../../../Utils/Uploader';
import {OlzUploadFile} from '../OlzUploadFile/OlzUploadFile';
import {UploadFile, UploadedFile, RegisteringFile} from '../types';
import {isRegisteringFile, isUploadedFile, isUploadingFile, serializeUploadFile} from '../utils';
import {dataHref} from '../../../Utils/constants';

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
    isMarkdown?: boolean;
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

    const [uploadFiles, setUploadFiles] = React.useState<UploadFile[]>(() => field.value.map(
        (uploadId: string) => ({uploadState: 'UPLOADED', uploadId}),
    ));
    const [registeringId, setRegisteringId] = React.useState<string|null>(null);

    React.useEffect(() => {
        if (uploadFiles.length === 0) {
            return () => undefined;
        }
        const clock = setInterval(() => {
            const state = uploader.getState();
            const newUploadFiles = uploadFiles.map((uploadFile) => {
                if (!isUploadingFile(uploadFile)) {
                    return uploadFile;
                }
                const stateOfUploadingFile = state.uploadsById[uploadFile.uploadId];
                if (!stateOfUploadingFile) {
                    return undefined;
                }
                uploadFile.uploadProgress = stateOfUploadingFile.progress;
                return uploadFile;
            }).filter(isDefined);
            setUploadFiles(newUploadFiles);
        }, 1000);
        return () => clearInterval(clock);
    }, [uploadFiles]);

    const onUploadFinished = React.useCallback((event: CustomEvent<string>) => {
        const uploadId = event.detail;
        const newUploadFiles = uploadFiles.map((uploadFile): UploadFile => {
            if (!isUploadingFile(uploadFile) || uploadFile.uploadId !== uploadId) {
                return uploadFile;
            }
            return {...uploadFile, uploadState: 'UPLOADED'};
        });
        setUploadFiles(newUploadFiles);
    }, [uploadFiles]);
    React.useEffect(() => {
        uploader.addEventListener('uploadFinished', onUploadFinished);
        return () => uploader.removeEventListener('uploadFinished', onUploadFinished);
    }, [onUploadFinished]);

    React.useEffect(() => {
        if (registeringId !== null) { // Currently registering another upload
            return;
        }
        const registeringFile = uploadFiles.find<RegisteringFile>(isRegisteringFile);
        if (!registeringFile) {
            return;
        }
        const registerUpload = async () => {
            setRegisteringId(registeringFile.id);
            const file = registeringFile.file;
            try {
                const base64Content = await readBase64(file);
                const suffix = file.name.split('.').slice(-1)[0];
                const uploadId = await uploader.add(assert(base64Content), `.${suffix}`);
                setUploadFiles((current) => current.map((uploadFile) => {
                    if (!isRegisteringFile(uploadFile) || uploadFile.id !== registeringFile.id) {
                        return uploadFile;
                    }
                    return {...uploadFile, uploadState: 'UPLOADING', uploadId, uploadProgress: 0};
                }));
            } catch (err: unknown) {
                setUploadFiles((current) => current.filter((uploadFile) =>
                    !isRegisteringFile(uploadFile) || uploadFile.id !== registeringFile.id));
            }
            setRegisteringId(null);
        };
        registerUpload();
    }, [uploadFiles, registeringId]);

    React.useEffect(() => {
        const hasProcessingFiles = uploadFiles.some((uploadFile) => !isUploadedFile(uploadFile));
        props.setIsLoading(hasProcessingFiles);
    }, [uploadFiles]);

    React.useEffect(() => {
        const uploadIds = uploadFiles
            .filter<UploadedFile>(isUploadedFile)
            .map((uploadFile) => uploadFile.uploadId);
        field.onChange(uploadIds);
    }, [uploadFiles]);

    const onDrop = async (acceptedFiles: File[]) => {
        const newUploadFiles: UploadFile[] = [
            ...uploadFiles,
            ...acceptedFiles.map((acceptedFile, index): RegisteringFile => ({
                uploadState: 'REGISTERING',
                id: `${acceptedFile.name}-${index}-${Date.now()}`,
                file: acceptedFile,
            })),
        ];
        setUploadFiles(newUploadFiles);
    };

    const onDelete = React.useCallback((uploadId: string) => {
        const newUploadFiles = uploadFiles.filter(
            (uploadFile) => !isUploadedFile(uploadFile) || uploadFile.uploadId !== uploadId,
        );
        setUploadFiles(newUploadFiles);
    }, [uploadFiles]);

    const {getRootProps, getInputProps, isDragActive} = useDropzone({
        disabled: props.disabled,
        onDrop,
    });

    return (<>
        <label htmlFor={`${props.name}-field`}>{props.title}</label>
        <div id={`${props.name}-field`} className={`olz-multi-file-field${disabledClassName}`}>
            <div className='state'>
                {uploadFiles.map((uploadFile) => <OlzUploadFile
                    key={serializeUploadFile(uploadFile)}
                    uploadFile={uploadFile}
                    onDelete={onDelete}
                    isMarkdown={props.isMarkdown}
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
