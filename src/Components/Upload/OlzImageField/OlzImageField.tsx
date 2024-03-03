import React from 'react';
import {useController, Control, FieldValues, FieldErrors, UseControllerProps, Path} from 'react-hook-form';
import {useDropzone} from 'react-dropzone';
import {dataHref} from '../../../../src/Utils/constants';
import {readBase64} from '../../../../src/Utils/fileUtils';
import {getBase64FromCanvas, getResizedCanvas, loadImageFromBase64} from '../../../../src/Utils/imageUtils';
import {Uploader} from '../../../../src/Utils/Uploader';
import {OlzUploadImage} from '../OlzUploadImage/OlzUploadImage';
import {UploadingFile, UploadedFile} from '../types';
import {serializeUploadFile} from '../utils';

import '../../../Components/Common/OlzStyles/dropzone.scss';
import './OlzImageField.scss';

const MAX_IMAGE_SIZE = 800;

const uploader = Uploader.getInstance();

interface OlzImageFieldProps<Values extends FieldValues, Name extends Path<Values>> {
    title?: string;
    name: Name;
    rules?: UseControllerProps<Values, Name>['rules'];
    errors?: FieldErrors<Values>;
    control: Control<Values, Name>;
    setIsLoading: (isLoading: boolean) => void;
    maxImageSize?: number;
    disabled?: boolean;
}

export const OlzImageField = <
Values extends FieldValues,
Name extends Path<Values>
>(props: OlzImageFieldProps<Values, Name>): React.ReactElement => {
    const errorMessage = props.errors?.[props.name]?.message;
    const errorClassName = errorMessage ? ' is-invalid' : '';
    const disabledClassName = props.disabled ? ' disabled' : '';

    const {field} = useController({
        name: props.name,
        control: props.control,
        rules: props.rules,
    });

    const initialUploadedFile: UploadedFile|null = field.value && {uploadState: 'UPLOADED', uploadId: field.value} || null;
    const [file, setFile] = React.useState<UploadedFile|UploadingFile|null>(initialUploadedFile);

    React.useEffect(() => {
        if (file?.uploadState !== 'UPLOADING') {
            return () => undefined;
        }
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
            field.onChange(file.uploadId);
        };
        uploader.addEventListener('uploadFinished', callback);
        return () => uploader.removeEventListener('uploadFinished', callback);
    }, [file]);

    React.useEffect(() => {
        const isUploadingFile = file?.uploadState === 'UPLOADING';
        props.setIsLoading(isUploadingFile);
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
            const canvas = getResizedCanvas(img, props.maxImageSize ?? MAX_IMAGE_SIZE);
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
        field.onChange(null);
    }, []);

    const {getRootProps, getInputProps, isDragActive} = useDropzone({
        accept: {'image/*': ['.png', '.jpg', '.jpeg']},
        disabled: props.disabled,
        maxFiles: 1,
        onDrop,
    });

    return (<>
        <label htmlFor={`${props.name}-field`}>{props.title}</label>
        <div id={`${props.name}-field`} className={`olz-image-field${disabledClassName}`}>
            <div className='state'>
                {file ? <OlzUploadImage
                    key={serializeUploadFile(file)}
                    uploadFile={file}
                    onDelete={onDelete}
                /> : []}
            </div>
            <div className={`dropzone${disabledClassName}${errorClassName}`} {...getRootProps()}>
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
