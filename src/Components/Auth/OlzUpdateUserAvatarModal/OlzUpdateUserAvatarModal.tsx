import * as bootstrap from 'bootstrap';
import React from 'react';
import ReactDOM from 'react-dom';
import {useDropzone} from 'react-dropzone';
import Cropper from 'react-easy-crop';
import {Point, Area} from 'react-easy-crop/types';
import {OlzProgressBar} from '../../Common/OlzProgressBar/OlzProgressBar';
import {readBase64} from '../../../Utils/fileUtils';
import {getBase64FromCanvas, getCanvasOfSize, getCroppedCanvas, loadImageFromBase64} from '../../../Utils/imageUtils';
import {Uploader} from '../../../Utils/Uploader';
import {dataHref} from '../../../Utils/constants';

const MIN_ZOOM = 0.05;
const MAX_ZOOM = 10.0;

const TARGET_WIDTH = 84;
const TARGET_HEIGHT = 120;

const uploader = Uploader.getInstance();

export type OlzUpdateUserAvatarModalChangeEvent = CustomEvent<{
    uploadId: string,
    dataUrl: string,
}>

interface OlzUpdateUserAvatarModalProps {
    onChange: (e: OlzUpdateUserAvatarModalChangeEvent) => void;
}

export const OlzUpdateUserAvatarModal = (props: OlzUpdateUserAvatarModalProps): React.ReactElement => {
    const [imageSrc, setImageSrc] = React.useState<string|null>(null);
    const [crop, setCrop] = React.useState<Point>({x: 0, y: 0});
    const [rotation, setRotation] = React.useState<number>(0);
    const [zoom, setZoom] = React.useState<number>(1);
    const [croppedAreaPixels, setCroppedAreaPixels] = React.useState<Area|null>(null);
    const [uploadProgress, setUploadProgress] = React.useState<number|null>(null);

    const onCropComplete = React.useCallback((
        _croppedArea: Area,
        croppedAreaPixels_: Area,
    ) => {
        setCroppedAreaPixels(croppedAreaPixels_);
    }, []);

    const onSubmit = React.useCallback(async (
        event: React.FormEvent<HTMLFormElement>,
    ): Promise<boolean> => {
        event.preventDefault();
        try {
            setUploadProgress(0.1);
            if (!imageSrc) {
                throw new Error('Image source is not available');
            }
            if (!croppedAreaPixels) {
                throw new Error('Cropped area is unknown');
            }
            const img = await loadImageFromBase64(imageSrc);
            const croppedCanvas = getCroppedCanvas(img, croppedAreaPixels, rotation);
            const resizedCanvas = getCanvasOfSize(croppedCanvas, TARGET_WIDTH, TARGET_HEIGHT);
            const resizedBase64 = getBase64FromCanvas(resizedCanvas);
            if (!resizedBase64) {
                throw new Error('An error occurred croping the image');
            }
            setUploadProgress(0.3);
            const uploadId = await uploader.upload(resizedBase64, '.jpg');
            setUploadProgress(1);
            const changeEvent: OlzUpdateUserAvatarModalChangeEvent =
                new CustomEvent('change', {detail: {uploadId, dataUrl: resizedBase64}});
            props.onChange(changeEvent);
            const modal = document.getElementById('update-user-avatar-modal');
            if (modal) {
                bootstrap.Modal.getInstance(modal)?.hide();
            }
        } catch (e) {
            console.error(e);
        }
        return false;
    }, [imageSrc, croppedAreaPixels, rotation]);

    const onDrop = async (acceptedFiles: File[]) => {
        for (let fileListIndex = 0; fileListIndex < acceptedFiles.length; fileListIndex++) {
            const file = acceptedFiles[fileListIndex];
            // eslint-disable-next-line no-await-in-loop
            const base64Content = await readBase64(file);
            if (!base64Content.match(/^data:image\/(jpg|jpeg|png)/i)) {
                console.error(`${file.name} ist ein beschädigtes Bild, bitte wähle ein korrektes Bild aus. \nEin Bild hat meist die Endung ".jpg", ".jpeg" oder ".png".`);
                continue; // eslint-disable-line no-continue
            }
            setImageSrc(base64Content);
        }
    };

    const {getRootProps, getInputProps, isDragActive} = useDropzone({
        accept: {'image/*': ['.png', '.jpg', '.jpeg']},
        onDrop,
    });

    const modalBody = uploadProgress === null ? (imageSrc ? (<>
        <div className="cropper-container">
            <Cropper
                image={imageSrc}
                crop={crop}
                rotation={rotation}
                zoom={zoom}
                minZoom={MIN_ZOOM}
                maxZoom={MAX_ZOOM}
                aspect={TARGET_WIDTH / TARGET_HEIGHT}
                onCropChange={setCrop}
                onRotationChange={setRotation}
                onCropComplete={onCropComplete}
                onZoomChange={setZoom}
            />
        </div>
        <div>
            <button
                type='button'
                className='btn btn-secondary'
                onClick={() => setZoom((zoom_) => Math.max(zoom_ * 0.9, MIN_ZOOM))}
            >
                --
            </button>
            <button
                type='button'
                className='btn btn-secondary'
                onClick={() => setZoom((zoom_) => Math.max(zoom_ * 0.99, MIN_ZOOM))}
            >
                -
            </button>
            Zoom
            <button
                type='button'
                className='btn btn-secondary'
                onClick={() => setZoom((zoom_) => Math.min(zoom_ * 1.01, MAX_ZOOM))}
            >
                +
            </button>
            <button
                type='button'
                className='btn btn-secondary'
                onClick={() => setZoom((zoom_) => Math.min(zoom_ * 1.1, MAX_ZOOM))}
            >
                ++
            </button>
        </div>
    </>) : (
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
                    <div>Bild hierhin ziehen...</div> :
                    <div>Bild hierhin ziehen, oder Klicken, um ein Bild auszuwählen</div>
            }
        </div>
    )) : (
        <OlzProgressBar progress={uploadProgress} />
    );

    return (
        <div className='modal fade' id='update-user-avatar-modal' tabIndex={-1} aria-labelledby='olz-update-user-avatar-label' aria-hidden='true'>
            <div className='modal-dialog'>
                <div className='modal-content'>
                    <form className='default-form' onSubmit={onSubmit}>
                        <div className='modal-header'>
                            <h5 className='modal-title' id='olz-update-user-avatar-label'>Benutzer-Bild</h5>
                            <button type='button' className='btn-close' data-bs-dismiss='modal' aria-label='Schliessen'></button>
                        </div>
                        <div className='modal-body'>
                            {modalBody}
                        </div>
                        <div className='modal-footer'>
                            <button type='button' className='btn btn-secondary' data-bs-dismiss='modal'>Abbrechen</button>
                            <button type='submit' className='btn btn-primary' id='submit-button'>OK</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    );
};

export function initOlzUpdateUserAvatarModal(
    onChange: (e: OlzUpdateUserAvatarModalChangeEvent) => void,
): void {
    const reactRoot = document.getElementById('update-user-avatar-react-root');
    if (!reactRoot) {
        throw new Error('React root is not present');
    }
    ReactDOM.unmountComponentAtNode(reactRoot);
    ReactDOM.render(
        <OlzUpdateUserAvatarModal onChange={onChange} />,
        reactRoot,
    );
    const modal = document.getElementById('update-user-avatar-modal');
    if (modal) {
        new bootstrap.Modal(modal, {backdrop: 'static'}).show();
    }
}
