import React from 'react';

import './OlzPanini2024Masks.scss';

const CANVAS_WID = 1928;
const CANVAS_HEI = 2646;

interface MaskConfig {
    isLandscape: boolean;
    draw: (ctx: CanvasRenderingContext2D, wid: number, hei: number) => void;
}

function drawTop(
    ctx: CanvasRenderingContext2D,
    wid: number,
    _hei: number,
    isLandscape: boolean,
): void {
    const barHei = isLandscape ? 350 : 500;
    const stripeHei = isLandscape ? 35 : 50;
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
    ctx.shadowBlur = 50;
    ctx.shadowColor = 'rgba(0,0,0,0.8)';
    ctx.shadowOffsetX = 0;
    ctx.shadowOffsetY = 25;
    ctx.fill();
}

function drawBottom(
    ctx: CanvasRenderingContext2D,
    wid: number,
    hei: number,
    isLandscape: boolean,
): void {
    const barHei = isLandscape ? 350 : 500;
    const stripeHei = isLandscape ? 35 : 50;
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
    ctx.shadowBlur = 50;
    ctx.shadowColor = 'rgba(0,0,0,0.8)';
    ctx.shadowOffsetX = 0;
    ctx.shadowOffsetY = -25;
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

const MASKS_CONFIG: {[mask: string]: MaskConfig} = {
    topP: {
        isLandscape: false,
        draw: (ctx, wid, hei) => {
            drawTop(ctx, wid, hei, false);
        },
    },
    topL: {
        isLandscape: true,
        draw: (ctx, wid, hei) => {
            drawTop(ctx, wid, hei, true);
        },
    },
    bottomP: {
        isLandscape: false,
        draw: (ctx, wid, hei) => {
            drawBottom(ctx, wid, hei, false);
        },
    },
    bottomL: {
        isLandscape: true,
        draw: (ctx, wid, hei) => {
            drawBottom(ctx, wid, hei, true);
        },
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

    const [imgSrc, setImgSrc] = React.useState<string>('');

    const canvasRef = React.useRef<HTMLCanvasElement>(null);

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
        <canvas
            ref={canvasRef}
            width={config.isLandscape ? CANVAS_HEI : CANVAS_WID}
            height={config.isLandscape ? CANVAS_WID : CANVAS_HEI}
            className='mask-canvas'
        />
        <img
            src={imgSrc}
            className='mask-img'
        />
    </>);
};
