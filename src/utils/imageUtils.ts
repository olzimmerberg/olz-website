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
    /* istanbul ignore if */
    if (!process.env.JEST_WORKER_ID) { // No canvas API in tests!
        const context = canvas.getContext('2d');
        context.drawImage(drawable, 0, 0, width, height);
    }
    return canvas;
}

export function getBase64FromCanvas(
    canvas: HTMLCanvasElement,
): string|undefined {
    let resizedBase64: string|undefined;
    try {
        resizedBase64 = canvas.toDataURL('image/jpeg');
    } catch (err) {
        resizedBase64 = canvas.toDataURL();
    }
    return resizedBase64;
}
