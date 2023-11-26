import React from 'react';
import {DPI, MM_PER_INCH, PANINI_SHORT, PANINI_LONG} from '../../Utils/panini2024Utils';
import {initReact} from '../../../../Utils/reactUtils';

import './OlzPanini2024Masks.scss';

const PIXEL_SHORT = Math.round(PANINI_SHORT * DPI / MM_PER_INCH);
const PIXEL_LONG = Math.round(PANINI_LONG * DPI / MM_PER_INCH);

interface MaskConfig {
    isLandscape: boolean;
    draw: (ctx: CanvasRenderingContext2D, wid: number, hei: number) => void;
}

function drawTop(
    ctx: CanvasRenderingContext2D,
    wid: number,
    hei: number,
): void {
    const barHei = hei * 0.175;
    const stripeHei = hei * 0.0175;
    const p0 = [wid + 50, barHei];
    const p1 = [wid * 3 / 4, barHei * 1.2];
    const p2 = [wid / 2, barHei * 0.8];
    const p3 = [-50, barHei * 1.2];

    // Green Box
    const grd = ctx.createLinearGradient(0, 0, 0, barHei * 1.2);
    grd.addColorStop(0, 'rgb(0,150,0)');
    grd.addColorStop(1, 'rgb(0,120,0)');
    ctx.beginPath();
    ctx.moveTo(0, 0);
    ctx.lineTo(wid, 0);
    ctx.lineTo(p0[0], p0[1]);
    ctx.bezierCurveTo(
        p1[0], p1[1],
        p2[0], p2[1],
        p3[0], p3[1],
    );
    ctx.closePath();
    ctx.fillStyle = grd;
    ctx.fill();

    // Yellow Stripe
    ctx.beginPath();
    ctx.moveTo(p0[0], p0[1]);
    ctx.bezierCurveTo(
        p1[0], p1[1],
        p2[0], p2[1],
        p3[0], p3[1],
    );
    ctx.lineTo(p3[0], p3[1] - stripeHei);
    ctx.bezierCurveTo(
        p2[0], p2[1] - stripeHei,
        p1[0], p1[1] - stripeHei,
        p0[0], p0[1] - stripeHei,
    );
    ctx.fillStyle = 'rgb(255,255,0)';
    ctx.shadowColor = 'rgba(0,0,0,0)';
    ctx.fill();

    // Black Stripe
    ctx.beginPath();
    ctx.moveTo(p0[0], p0[1]);
    ctx.bezierCurveTo(
        p1[0], p1[1],
        p2[0], p2[1],
        p3[0], p3[1],
    );
    ctx.lineTo(p3[0], p3[1] + stripeHei);
    ctx.bezierCurveTo(
        p2[0], p2[1] + stripeHei,
        p1[0], p1[1] + stripeHei,
        p0[0], p0[1] + stripeHei,
    );
    ctx.fillStyle = 'rgb(0,0,0)';
    ctx.shadowBlur = stripeHei;
    ctx.shadowColor = 'rgba(0,0,0,0.8)';
    ctx.shadowOffsetX = 0;
    ctx.shadowOffsetY = stripeHei / 2;
    ctx.fill();
}

function drawBottom(
    ctx: CanvasRenderingContext2D,
    wid: number,
    hei: number,
): void {
    const barHei = hei * 0.175;
    const stripeHei = hei * 0.0175;
    const p0 = [wid + 50, hei - barHei];
    const p1 = [wid / 2, hei - barHei * 0.8];
    const p2 = [wid / 4, hei - barHei * 1.2];
    const p3 = [-50, hei - barHei * 0.8];

    // Green Box
    const grd = ctx.createLinearGradient(0, hei - barHei * 1.2, 0, hei);
    grd.addColorStop(0, 'rgb(0,150,0)');
    grd.addColorStop(1, 'rgb(0,120,0)');
    ctx.beginPath();
    ctx.moveTo(0, hei);
    ctx.lineTo(wid, hei);
    ctx.lineTo(p0[0], p0[1]);
    ctx.bezierCurveTo(
        p1[0], p1[1],
        p2[0], p2[1],
        p3[0], p3[1],
    );
    ctx.closePath();
    ctx.fillStyle = grd;
    ctx.fill();

    // Yellow Stripe
    ctx.beginPath();
    ctx.moveTo(p0[0], p0[1]);
    ctx.bezierCurveTo(
        p1[0], p1[1],
        p2[0], p2[1],
        p3[0], p3[1],
    );
    ctx.lineTo(p3[0], p3[1] - stripeHei);
    ctx.bezierCurveTo(
        p2[0], p2[1] - stripeHei,
        p1[0], p1[1] - stripeHei,
        p0[0], p0[1] - stripeHei,
    );
    ctx.fillStyle = 'rgb(255,255,0)';
    ctx.shadowBlur = stripeHei;
    ctx.shadowColor = 'rgba(0,0,0,0.8)';
    ctx.shadowOffsetX = 0;
    ctx.shadowOffsetY = -stripeHei / 2;
    ctx.fill();

    // Black Stripe
    ctx.beginPath();
    ctx.moveTo(p0[0], p0[1]);
    ctx.bezierCurveTo(
        p1[0], p1[1],
        p2[0], p2[1],
        p3[0], p3[1],
    );
    ctx.lineTo(p3[0], p3[1] + stripeHei);
    ctx.bezierCurveTo(
        p2[0], p2[1] + stripeHei,
        p1[0], p1[1] + stripeHei,
        p0[0], p0[1] + stripeHei,
    );
    ctx.fillStyle = 'rgb(0,0,0)';
    ctx.shadowColor = 'rgba(0,0,0,0)';
    ctx.fill();
}

function drawAssociation(
    ctx: CanvasRenderingContext2D,
    wid: number,
    hei: number,
): void {
    const offset = (wid + hei) * 0.01;
    associationStencilPath(ctx, wid, hei);
    ctx.fillStyle = 'rgb(0,0,0)';
    ctx.shadowBlur = offset;
    ctx.shadowColor = 'rgba(0,0,0,0.4)';
    ctx.shadowOffsetX = 0;
    ctx.shadowOffsetY = offset / 2;
    ctx.fill();
    ctx.shadowBlur = offset;
    ctx.shadowColor = 'rgba(0,0,0,0.4)';
    ctx.shadowOffsetX = 0;
    ctx.shadowOffsetY = -offset / 2;
    ctx.fill();
    ctx.shadowBlur = offset;
    ctx.shadowColor = 'rgba(0,0,0,0.4)';
    ctx.shadowOffsetX = offset / 2;
    ctx.shadowOffsetY = 0;
    ctx.fill();
    ctx.shadowBlur = offset;
    ctx.shadowColor = 'rgba(0,0,0,0.4)';
    ctx.shadowOffsetX = -offset / 2;
    ctx.shadowOffsetY = 0;
    ctx.fill();
}

function drawAssociationStencil(
    ctx: CanvasRenderingContext2D,
    wid: number,
    hei: number,
): void {
    ctx.fillStyle = 'rgb(0,0,0)';
    ctx.fillRect(0, 0, wid, hei);
    associationStencilPath(ctx, wid, hei);
    ctx.fillStyle = 'rgb(255,255,255)';
    ctx.fill();
}

function associationStencilPath(
    ctx: CanvasRenderingContext2D,
    wid: number,
    hei: number,
): void {
    const offset = (wid + hei) * 0.01;
    const offX = offset;
    const offY = offset;
    const scale = (wid + hei) * 0.009;
    ctx.beginPath();
    // m 0.200343,5.0 c
    // -0.467757,2.833449 1.186392,4.778136 4.014549,4.778136
    // 2.827311,0 5.124449,-1.944687 5.591994,-4.778136
    // 0.43524,-2.636043 -1.039812,-4.843462 -4.003992,-4.843462
    // -2.963518,0 -5.167523,2.207419 -5.602551,4.843462
    let curX = 0.2;
    let curY = 5.0;
    ctx.moveTo(offX + scale * curX, offY + scale * curY);
    ctx.bezierCurveTo(
        offX + scale * (curX - 0.467757), offY + scale * (curY + 2.833449),
        offX + scale * (curX + 1.186392), offY + scale * (curY + 4.778136),
        offX + scale * (curX + 4.014549), offY + scale * (curY + 4.778136),
    );
    curX += 4.014549;
    curY += 4.778136;
    ctx.bezierCurveTo(
        offX + scale * (curX + 2.827311), offY + scale * (curY + 0),
        offX + scale * (curX + 5.124449), offY + scale * (curY - 1.944687),
        offX + scale * (curX + 5.591994), offY + scale * (curY - 4.778136),
    );
    curX += 5.591994;
    curY -= 4.778136;
    ctx.bezierCurveTo(
        offX + scale * (curX + 0.43524), offY + scale * (curY - 2.636043),
        offX + scale * (curX - 1.039812), offY + scale * (curY - 4.843462),
        offX + scale * (curX - 4.003992), offY + scale * (curY - 4.843462),
    );
    curX -= 4.003992;
    curY -= 4.843462;
    ctx.bezierCurveTo(
        offX + scale * (curX - 2.963518), offY + scale * (curY + 0),
        offX + scale * (curX - 5.167523), offY + scale * (curY + 2.207419),
        offX + scale * (curX - 5.602551), offY + scale * (curY + 4.843462),
    );
    ctx.closePath();
}

export const MASKS_CONFIG: {[mask: string]: MaskConfig} = {
    topP: {
        isLandscape: false,
        draw: drawTop,
    },
    topL: {
        isLandscape: true,
        draw: drawTop,
    },
    bottomP: {
        isLandscape: false,
        draw: drawBottom,
    },
    bottomL: {
        isLandscape: true,
        draw: drawBottom,
    },
    associationP: {
        isLandscape: false,
        draw: drawAssociation,
    },
    associationL: {
        isLandscape: true,
        draw: drawAssociation,
    },
    associationStencilP: {
        isLandscape: false,
        draw: drawAssociationStencil,
    },
    associationStencilL: {
        isLandscape: true,
        draw: drawAssociationStencil,
    },
};

interface OlzPanini2024MasksProps {
    mask: string;
}

export const OlzPanini2024Masks = (
    props: OlzPanini2024MasksProps,
): React.ReactElement => {
    console.log(props);
    const config = MASKS_CONFIG[props.mask];

    if (!config) {
        return <div>Invalid mask: {props.mask}</div>;
    }

    const [imgSrc, setImgSrc] = React.useState<string>('');

    const canvasRef = React.useRef<HTMLCanvasElement>(null);
    const wid = config.isLandscape ? PIXEL_LONG : PIXEL_SHORT;
    const hei = config.isLandscape ? PIXEL_SHORT : PIXEL_LONG;
    const fileName = `${props.mask}_${wid}x${hei}.png`;

    React.useEffect(() => {
        const cnv = canvasRef.current;
        if (!cnv) {
            return;
        }
        const ctx = cnv.getContext('2d');
        if (!ctx) {
            return;
        }
        ctx.fillStyle = 'rgba(0,0,0,0)';
        ctx.fillRect(0, 0, cnv.width, cnv.height);
        config.draw(ctx, cnv.width, cnv.height);
        const dataUrl = cnv.toDataURL();
        setImgSrc(dataUrl);
    }, [config, canvasRef]);

    return (<>
        <div>
            <a href={imgSrc} download={fileName}>{fileName}</a>
        </div>
        <div>
            <canvas
                ref={canvasRef}
                width={wid}
                height={hei}
                className='mask-canvas'
            />
            <img
                src={imgSrc}
                className='mask-img'
            />
        </div>
    </>);
};

export function initOlzPaniniMasks(): boolean {
    const olzPanini2024Mask = (window as unknown as {olzPanini2024Mask: string}).olzPanini2024Mask;
    let reactElem = <OlzPanini2024Masks mask={olzPanini2024Mask} />;
    if (olzPanini2024Mask === 'all') {
        reactElem = (<>{
            Object.keys(MASKS_CONFIG).map((mask) => (
                <div><OlzPanini2024Masks mask={mask} /></div>
            ))
        }</>);
    }
    initReact('panini-react-root-masks', reactElem);
    return false;
}
