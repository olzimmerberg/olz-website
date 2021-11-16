import {Area} from 'react-easy-crop/types';

export interface LoadImageFromBase64Options {
    image?: HTMLImageElement;
}

export function loadImageFromBase64(
    base64: string,
    options?: LoadImageFromBase64Options,
): Promise<HTMLImageElement> {
    return new Promise((resolve, reject) => {
        const img = options?.image || document.createElement('img');
        img.onerror = () => {
            reject(new Error('Could not load image.'));
        };
        img.onload = () => {
            resolve(img);
        };
        img.src = base64;
    });
}

export function getResizedDimensions(
    originalWidth: number,
    originalHeight: number,
    maximumSize: number,
): [number, number] {
    let resizedWidth = originalWidth;
    let resizedHeight = originalHeight;
    if (originalHeight < originalWidth) {
        if (maximumSize < originalWidth) {
            resizedWidth = maximumSize;
            resizedHeight = resizedWidth * originalHeight / originalWidth;
        }
    } else if (maximumSize < originalHeight) {
        resizedHeight = maximumSize;
        resizedWidth = resizedHeight * originalWidth / originalHeight;
    }
    return [resizedWidth, resizedHeight];
}

/** Crop an image according to specs provided by react-easy-crop. */
export function getCroppedCanvas(
    original: HTMLImageElement|HTMLCanvasElement,
    area: Area,
    rotation = 0,
): HTMLCanvasElement {
    // set each dimensions to double largest dimension to allow for a safe area
    // for the image to rotate in without being clipped by canvas context
    const maxSize = Math.max(original.width, original.height);
    const safeArea = 2 * ((maxSize / 2) * Math.sqrt(2));
    const offsetX = safeArea / 2 - original.width / 2;
    const offsetY = safeArea / 2 - original.height / 2;

    const canvas = getCanvasOfSize(original, safeArea, safeArea);
    const ctx = canvas.getContext('2d');

    // translate canvas context to a central location on image to allow rotating
    // around the center.
    ctx.translate(safeArea / 2, safeArea / 2);
    ctx.rotate(getRadianAngle(rotation));
    ctx.translate(-safeArea / 2, -safeArea / 2);

    // draw rotated original image and store data.
    ctx.drawImage(original, offsetX, offsetY);
    const data = ctx.getImageData(0, 0, safeArea, safeArea);

    // set canvas width to final desired crop size
    canvas.width = area.width;
    canvas.height = area.height;

    // paste generated rotate image with correct offsets for x,y crop values.
    ctx.putImageData(
        data,
        Math.round(0 - offsetX - area.x),
        Math.round(0 - offsetY - area.y),
    );

    return canvas;
}

export function getRadianAngle(degreeValue: number): number {
    return (degreeValue * Math.PI) / 180;
}

export function getResizedCanvas(
    original: HTMLImageElement|HTMLCanvasElement,
    maximumSize: number,
): HTMLCanvasElement {
    const [destinationWidth, destinationHeight] = getResizedDimensions(
        original.width, original.height, maximumSize,
    );

    // Hack to improve interpolation quality
    let notTooHugeDrawable: HTMLImageElement|HTMLCanvasElement = original;
    if (
        destinationWidth * 2 < original.width
        && destinationHeight * 2 < original.height
    ) {
        notTooHugeDrawable = getCanvasOfSize(
            original,
            destinationWidth * 2,
            destinationHeight * 2,
        );
    }
    // End of hack

    return getCanvasOfSize(
        notTooHugeDrawable,
        destinationWidth,
        destinationHeight,
    );
}

export function getCanvasOfSize(
    drawable: HTMLImageElement|HTMLCanvasElement,
    width: number,
    height: number,
): HTMLCanvasElement {
    const canvas = document.createElement('canvas');
    canvas.width = width;
    canvas.height = height;
    const context = canvas.getContext('2d');
    context.drawImage(drawable, 0, 0, width, height);
    return canvas;
}

export function getBase64FromCanvas(
    canvas: HTMLCanvasElement,
): string|undefined {
    let resizedBase64: string|undefined;
    try {
        resizedBase64 = canvas.toDataURL('image/jpeg');
    } catch (err: unknown) {
        resizedBase64 = canvas.toDataURL();
    }
    return resizedBase64;
}
