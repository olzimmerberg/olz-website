import React from 'react';
import {olzApi} from '../../../Api/client';
import {OlzCaptchaConfig} from '../../../Api/client/generated_olz_api_types';

import './OlzCaptcha.scss';

const WID = 400;
const HEI = 200;

interface OlzCaptchaProps {
    onToken: (captchaToken: string|null) => void;
}

interface TokenContent {
    log: string[];
    config: OlzCaptchaConfig;
}

interface LogEntry {
    event: 'D' |'M'| 'U';
    x: number;
    y: number;
}

export const OlzCaptcha = (props: OlzCaptchaProps): React.ReactElement => {
    const [config, setConfig] = React.useState<OlzCaptchaConfig|null>(null);
    const [currentRatio, setCurrentRatio] = React.useState<number>(0.0);
    const [isDragging, setIsDragging] = React.useState<boolean>(false);
    const [log, setLog] = React.useState<string[]>([]);

    const canvas = React.useRef<HTMLCanvasElement>(null);

    const inpWid = 300;
    const inpHei = 24;
    const rand = window.atob(config?.rand ?? '');
    const targetValue = Math.round(2 + rand.charCodeAt(2) / 20);
    const inpX = rand.charCodeAt(0) * (WID - inpWid - 30) / 255;
    const inpY = rand.charCodeAt(1) * (HEI - inpHei) / 255;
    const knobX = inpX + currentRatio * (inpWid - 20) + 10;
    const knobY = inpY + inpHei / 2;

    React.useEffect(() => {
        const getConfig = async () => {
            const response = await olzApi.call('startCaptcha', {});
            setConfig(response.config);
        };
        getConfig();
    }, []);

    React.useEffect(() => {
        const ctx = canvas.current?.getContext('2d');
        if (!ctx || rand.length !== 3) {
            return;
        }
        ctx.clearRect(0, 0, WID, HEI);

        ctx.beginPath();
        ctx.moveTo(inpX + 12, knobY - 2);
        ctx.lineTo(inpX + inpWid - 12, knobY - 2);
        ctx.quadraticCurveTo(inpX + inpWid - 10, knobY, inpX + inpWid - 12, knobY + 2);
        ctx.lineTo(inpX + 12, knobY + 2);
        ctx.quadraticCurveTo(inpX + 10, knobY, inpX + 12, knobY - 2);
        ctx.fillStyle = 'rgb(200,200,200)';
        ctx.strokeStyle = 'rgb(175,175,175)';
        ctx.lineWidth = 1;
        ctx.fill();
        ctx.stroke();

        ctx.beginPath();
        ctx.arc(knobX, knobY, 10, 0, Math.PI * 2);
        ctx.fillStyle = isDragging ? 'rgb(0,119,0)' : 'rgb(0,136,0)';
        ctx.strokeStyle = isDragging ? 'rgb(0,136,0)' : 'rgb(0,119,0)';
        ctx.fill();
        ctx.stroke();

        ctx.fillStyle = 'rgb(0,0,0)';
        ctx.textAlign = 'left';
        ctx.textBaseline = 'middle';
        ctx.font = `${Math.ceil(HEI / 14)}px 'Open Sans', arial, sans-serif`;
        ctx.fillText(`${Math.round(currentRatio * 15)}`, inpX + inpWid + 15, inpY + (inpHei / 2));
    }, [config, canvas, currentRatio, isDragging]);

    const onDrag = (x: number) => {
        const rawRatio = (x - inpX - 10) / (inpWid - 20);
        setCurrentRatio(Math.max(0, Math.min(1, rawRatio)));
    };

    const appendLog = (entry: LogEntry) => {
        const stringEntry = `${entry.event}${entry.x},${entry.y}`;
        setLog((log_) => [...log_, stringEntry]);
    };

    const onDown = (e: React.MouseEvent) => {
        const [x, y] = [e.nativeEvent.offsetX, e.nativeEvent.offsetY];
        const distSquare = (x - knobX) * (x - knobX) + (y - knobY) * (y - knobY);
        if (distSquare < 12 * 12) {
            setIsDragging(true);
            onDrag(x);
        }
        appendLog({event: 'D', x, y});
    };

    const onMove = (e: React.MouseEvent) => {
        const [x, y] = [e.nativeEvent.offsetX, e.nativeEvent.offsetY];
        if (isDragging) {
            onDrag(x);
        }
        appendLog({event: 'M', x, y});
    };

    const onUp = (e: React.MouseEvent) => {
        const [x, y] = [e.nativeEvent.offsetX, e.nativeEvent.offsetY];
        if (isDragging) {
            onDrag(x);
        }
        setIsDragging(false);
        appendLog({event: 'U', x, y});
        if (config && Math.round(currentRatio * 15) === targetValue) {
            const tokenContent: TokenContent = {log, config};
            props.onToken(window.btoa(JSON.stringify(tokenContent)));
        }
    };

    return (
        <div className='olz-captcha'>
            <div>
                <b>Bot-Prüfung:</b> Bitte den Regler auf {targetValue} stellen
                <button
                    type='button'
                    id='captcha-dev'
                    onClick={() => {
                        // works only on dev
                        props.onToken('dev');
                    }}
                >
                    &nbsp;
                </button>
            </div>
            <canvas
                width={WID}
                height={HEI}
                ref={canvas}
                onMouseDown={onDown}
                onMouseMove={onMove}
                onMouseUp={onUp}
            >
            </canvas>
        </div>
    );
};
