import React from 'react';
import {useController, Control, FieldValues, FieldErrors, UseControllerProps, Path} from 'react-hook-form';
import {useDropzone} from 'react-dropzone';
import {readBase64} from '../../../Utils/fileUtils';
import {isDefined, assert} from '../../../Utils/generalUtils';
import {getBase64FromCanvas, getResizedCanvas, loadImageFromBase64} from '../../../Utils/imageUtils';
import {Uploader} from '../../../Utils/Uploader';
import {OlzUploadImage} from '../OlzUploadImage/OlzUploadImage';
import {UploadFile, UploadedFile, RegisteringFile} from '../types';
import {isRegisteringFile, isUploadedFile, isUploadingFile, serializeUploadFile} from '../utils';
import {dataHref} from '../../../Utils/constants';

import '../../../Components/Common/OlzStyles/dropzone.scss';
import './OlzMultiImageField.scss';

const MAX_IMAGE_SIZE = 800;

const uploader = Uploader.getInstance();

interface OlzMultiImageFieldProps<Values extends FieldValues, Name extends Path<Values>> {
    title?: string;
    name: Name;
    rules?: UseControllerProps<Values, Name>['rules'];
    errors?: FieldErrors<Values>;
    control: Control<Values, Name>;
    setIsLoading: (isLoading: boolean) => void;
    disabled?: boolean;
}

export const OlzMultiImageField = <
    Values extends FieldValues,
    Name extends Path<Values>,
>(props: OlzMultiImageFieldProps<Values, Name>): React.ReactElement => {
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
    const [registeringId, setRegisteringId] = React.useState<string | null>(null);

    React.useEffect(() => {
        const uploadedFiles = uploadFiles.filter((uploadFile) => uploadFile.uploadState === 'UPLOADED');
        // If the field.value has changed
        if (
            uploadedFiles.length !== field.value.length
            || uploadedFiles.some((uploadFile, index) => uploadFile.uploadId !== field.value[index])
        ) {
            setUploadFiles(field.value.map(
                (uploadId: string) => ({uploadState: 'UPLOADED', uploadId}),
            ));
        }
    }, [field.value]);

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
                assert(base64Content.match(/^data:image\/(jpg|jpeg|png)/i));
                const img = await loadImageFromBase64(base64Content);
                const canvas = getResizedCanvas(img, MAX_IMAGE_SIZE);
                const resizedBase64 = getBase64FromCanvas(canvas);
                const uploadId = await uploader.add(assert(resizedBase64), '.jpg');
                setUploadFiles((current) => current.map((uploadFile) => {
                    if (!isRegisteringFile(uploadFile) || uploadFile.id !== registeringFile.id) {
                        return uploadFile;
                    }
                    return {...uploadFile, uploadState: 'UPLOADING', uploadId, uploadProgress: 0};
                }));
            } catch (_err: unknown) {
                console.error(`${file.name} ist kein Bild, bitte wähle ein Bild aus. \nEin Bild hat meist die Endung ".jpg", ".jpeg" oder ".png".`);
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
        accept: {'image/*': ['.png', '.jpg', '.jpeg']},
        disabled: props.disabled,
        onDrop,
    });

    return (<>
        <label htmlFor={`${props.name}-field`}>{props.title}</label>
        <div id={`${props.name}-field`} className={`olz-multi-image-field${disabledClassName}`}>
            <div className='state'>
                {uploadFiles.map((uploadFile) => <OlzUploadImage
                    key={serializeUploadFile(uploadFile)}
                    uploadFile={uploadFile}
                    onDelete={onDelete}
                />)}
            </div>
            <div className={`dropzone${errorClassName}`} {...getRootProps()}>
                <input {...getInputProps()} disabled={props.disabled} id={`${props.name}-input`} />
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
        {errorMessage && <p className='error'>{String(errorMessage)}</p>}
    </>);
};
