
/* eslint-disable @typescript-eslint/no-unused-vars */
class FakeContext implements CanvasRenderingContext2D, ImageBitmapRenderingContext {
    createConicGradient(startAngle: number, x: number, y: number): CanvasGradient {
        throw new Error('Method not implemented.');
    }

    // @ts-ignore
    canvas: HTMLCanvasElement;
    getContextAttributes(): CanvasRenderingContext2DSettings {
        throw new Error('Method not implemented.');
    }

    // @ts-ignore
    globalAlpha: number;
    // @ts-ignore
    globalCompositeOperation: GlobalCompositeOperation;

    public drawnImages: Array<{image: any, sx: any, sy: any, sw?: any, sh?: any, dx?: any, dy?: any, dw?: any, dh?: any}> = [];
    drawImage(image: CanvasImageSource, dx: number, dy: number): void;
    drawImage(image: CanvasImageSource, dx: number, dy: number, dw: number, dh: number): void;
    drawImage(image: CanvasImageSource, sx: number, sy: number, sw: number, sh: number, dx: number, dy: number, dw: number, dh: number): void;
    drawImage(image: any, sx: any, sy: any, sw?: any, sh?: any, dx?: any, dy?: any, dw?: any, dh?: any): void {
        this.drawnImages.push({image, sx, sy, sw, sh, dx, dy, dw, dh});
    }

    beginPath(): void {
        throw new Error('Method not implemented.');
    }

    clip(fillRule?: CanvasFillRule): void;
    clip(path: Path2D, fillRule?: CanvasFillRule): void;
    clip(path?: any, fillRule?: any): void {
        throw new Error('Method not implemented.');
    }

    fill(fillRule?: CanvasFillRule): void;
    fill(path: Path2D, fillRule?: CanvasFillRule): void;
    fill(path?: any, fillRule?: any): void {
        throw new Error('Method not implemented.');
    }

    isPointInPath(x: number, y: number, fillRule?: CanvasFillRule): boolean;
    isPointInPath(path: Path2D, x: number, y: number, fillRule?: CanvasFillRule): boolean;
    isPointInPath(path: any, x: any, y?: any, fillRule?: any): boolean {
        throw new Error('Method not implemented.');
    }

    isPointInStroke(x: number, y: number): boolean;
    isPointInStroke(path: Path2D, x: number, y: number): boolean;
    isPointInStroke(path: any, x: any, y?: any): boolean {
        throw new Error('Method not implemented.');
    }

    stroke(): void;
    stroke(path: Path2D): void;
    stroke(path?: any): void {
        throw new Error('Method not implemented.');
    }

    // @ts-ignore
    fillStyle: string | CanvasGradient | CanvasPattern;
    // @ts-ignore
    strokeStyle: string | CanvasGradient | CanvasPattern;
    createLinearGradient(x0: number, y0: number, x1: number, y1: number): CanvasGradient {
        throw new Error('Method not implemented.');
    }

    createPattern(image: CanvasImageSource, repetition: string): CanvasPattern {
        throw new Error('Method not implemented.');
    }

    createRadialGradient(x0: number, y0: number, r0: number, x1: number, y1: number, r1: number): CanvasGradient {
        throw new Error('Method not implemented.');
    }

    // @ts-ignore
    filter: string;
    createImageData(sw: number, sh: number, settings?: ImageDataSettings): ImageData;
    createImageData(imagedata: ImageData): ImageData;
    createImageData(sw: any, sh?: any, settings?: any): ImageData {
        throw new Error('Method not implemented.');
    }

    public gottenImageData: Array<{sx: number, sy: number, sw: number, sh: number, settings?: ImageDataSettings}> = [];
    getImageData(sx: number, sy: number, sw: number, sh: number, settings?: ImageDataSettings): ImageData {
        this.gottenImageData.push({sx, sy, sw, sh, settings});
        return {} as ImageData;
    }

    public puttedImageData: Array<{imagedata: any, dx: any, dy: any, dirtyX?: any, dirtyY?: any, dirtyWidth?: any, dirtyHeight?: any}> = [];
    putImageData(imagedata: ImageData, dx: number, dy: number): void;
    putImageData(imagedata: ImageData, dx: number, dy: number, dirtyX: number, dirtyY: number, dirtyWidth: number, dirtyHeight: number): void;
    putImageData(imagedata: any, dx: any, dy: any, dirtyX?: any, dirtyY?: any, dirtyWidth?: any, dirtyHeight?: any): void {
        this.puttedImageData.push({imagedata, dx, dy, dirtyX, dirtyY, dirtyWidth, dirtyHeight});
    }

    // @ts-ignore
    imageSmoothingEnabled: boolean;
    // @ts-ignore
    imageSmoothingQuality: ImageSmoothingQuality;
    arc(x: number, y: number, radius: number, startAngle: number, endAngle: number, counterclockwise?: boolean): void {
        throw new Error('Method not implemented.');
    }

    arcTo(x1: number, y1: number, x2: number, y2: number, radius: number): void {
        throw new Error('Method not implemented.');
    }

    bezierCurveTo(cp1x: number, cp1y: number, cp2x: number, cp2y: number, x: number, y: number): void {
        throw new Error('Method not implemented.');
    }

    closePath(): void {
        throw new Error('Method not implemented.');
    }

    ellipse(x: number, y: number, radiusX: number, radiusY: number, rotation: number, startAngle: number, endAngle: number, counterclockwise?: boolean): void {
        throw new Error('Method not implemented.');
    }

    lineTo(x: number, y: number): void {
        throw new Error('Method not implemented.');
    }

    moveTo(x: number, y: number): void {
        throw new Error('Method not implemented.');
    }

    quadraticCurveTo(cpx: number, cpy: number, x: number, y: number): void {
        throw new Error('Method not implemented.');
    }

    rect(x: number, y: number, w: number, h: number): void {
        throw new Error('Method not implemented.');
    }

    roundRect(x: number, y: number, w: number, h: number, radii: number|number[]): void {
        throw new Error('Method not implemented.');
    }

    // @ts-ignore
    lineCap: CanvasLineCap;
    // @ts-ignore
    lineDashOffset: number;
    // @ts-ignore
    lineJoin: CanvasLineJoin;
    // @ts-ignore
    lineWidth: number;
    // @ts-ignore
    miterLimit: number;
    getLineDash(): number[] {
        throw new Error('Method not implemented.');
    }

    setLineDash(segments: number[]): void {
        throw new Error('Method not implemented.');
    }

    clearRect(x: number, y: number, w: number, h: number): void {
        throw new Error('Method not implemented.');
    }

    fillRect(x: number, y: number, w: number, h: number): void {
        throw new Error('Method not implemented.');
    }

    strokeRect(x: number, y: number, w: number, h: number): void {
        throw new Error('Method not implemented.');
    }

    // @ts-ignore
    shadowBlur: number;
    // @ts-ignore
    shadowColor: string;
    // @ts-ignore
    shadowOffsetX: number;
    // @ts-ignore
    shadowOffsetY: number;

    reset(): void {
        throw new Error('Method not implemented.');
    }

    restore(): void {
        throw new Error('Method not implemented.');
    }

    save(): void {
        throw new Error('Method not implemented.');
    }

    fillText(text: string, x: number, y: number, maxWidth?: number): void {
        throw new Error('Method not implemented.');
    }

    measureText(text: string): TextMetrics {
        throw new Error('Method not implemented.');
    }

    strokeText(text: string, x: number, y: number, maxWidth?: number): void {
        throw new Error('Method not implemented.');
    }

    // @ts-ignore
    direction: CanvasDirection;
    // @ts-ignore
    font: string;
    // @ts-ignore
    fontKerning: CanvasFontKerning;
    // @ts-ignore
    textAlign: CanvasTextAlign;
    // @ts-ignore
    textBaseline: CanvasTextBaseline;
    getTransform(): DOMMatrix {
        throw new Error('Method not implemented.');
    }

    resetTransform(): void {
        throw new Error('Method not implemented.');
    }

    public rotations: Array<{angle: number}> = [];
    rotate(angle: number): void {
        this.rotations.push({angle});
    }

    scale(x: number, y: number): void {
        throw new Error('Method not implemented.');
    }

    setTransform(a: number, b: number, c: number, d: number, e: number, f: number): void;
    setTransform(transform?: DOMMatrix2DInit): void;
    setTransform(a?: any, b?: any, c?: any, d?: any, e?: any, f?: any): void {
        throw new Error('Method not implemented.');
    }

    transform(a: number, b: number, c: number, d: number, e: number, f: number): void {
        throw new Error('Method not implemented.');
    }

    public translations: Array<{x: number, y: number}> = [];
    translate(x: number, y: number): void {
        this.translations.push({x, y});
    }

    drawFocusIfNeeded(element: Element): void;
    drawFocusIfNeeded(path: Path2D, element: Element): void;
    drawFocusIfNeeded(path: any, element?: any): void {
        throw new Error('Method not implemented.');
    }

    transferFromImageBitmap(bitmap: ImageBitmap): void {
        throw new Error('Method not implemented.');
    }
}
/* eslint-enable @typescript-eslint/no-unused-vars */

type ContextType = (CanvasRenderingContext2D&
    ImageBitmapRenderingContext&
    WebGL2RenderingContext&
    FakeContext);

export function getFakeContext(): ContextType {
    return new FakeContext() as unknown as ContextType;
}
