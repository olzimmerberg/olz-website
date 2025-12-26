import React from 'react';
import {olzApi} from '../../../Api/client';
import {OlzCaptchaConfig} from '../../../Api/client/generated_olz_api_types';

import './OlzCaptcha.scss';

const CAPTCHA_EXPIRATION_MS = 60 * 1000;

const WID = 400;
const HEI = 200;
const RES = 2;

interface OlzCaptchaProps {
    onToken: (captchaToken: string | null) => void;
}

interface TokenContent {
    log: string[];
    config: OlzCaptchaConfig;
}

interface LogEntry {
    event: 'D' | 'M' | 'U';
    x: number;
    y: number;
}

type Coord = [number, number];

export const OlzCaptcha = (props: OlzCaptchaProps): React.ReactElement => {
    const [config, setConfig] = React.useState<OlzCaptchaConfig | null>(null);
    const [currentRatio, setCurrentRatio] = React.useState<number>(0.0);
    const [isDragging, setIsDragging] = React.useState<boolean>(false);
    const [isFinished, setIsFinished] = React.useState<boolean>(false);
    const [log, setLog] = React.useState<string[]>([]);

    const canvas = React.useRef<HTMLCanvasElement>(null);

    const wid = WID * RES;
    const hei = HEI * RES;
    const inpWid = 300 * RES;
    const inpHei = 24 * RES;
    const rand = window.atob(config?.rand ?? '');
    const targetValue = Math.round(2 + rand.charCodeAt(2) / 20);
    const inpX = rand.charCodeAt(0) * (wid - inpWid - 25 * RES) / 255;
    const inpY = rand.charCodeAt(1) * (hei - inpHei) / 255;
    const knobX = inpX + currentRatio * (inpWid - 20 * RES) + 10 * RES;
    const knobY = inpY + inpHei / 2;

    React.useEffect(() => {
        if (!config) {
            return () => {};
        }
        const timeout = window.setTimeout(() => {
            setConfig(null);
            setCurrentRatio(0.0);
            setIsDragging(false);
            setIsFinished(false);
            setLog([]);
        }, CAPTCHA_EXPIRATION_MS);
        return () => {
            window.clearTimeout(timeout);
        };
    }, [config]);

    React.useEffect(() => {
        const ctx = canvas.current?.getContext('2d');
        ctx?.clearRect(0, 0, wid, hei);
        if (!ctx || rand.length !== 3) {
            return;
        }

        ctx.beginPath();
        ctx.moveTo(inpX + 12 * RES, knobY - 2 * RES);
        ctx.lineTo(inpX + inpWid - 12 * RES, knobY - 2 * RES);
        ctx.quadraticCurveTo(
            inpX + inpWid - 10 * RES, knobY,
            inpX + inpWid - 12 * RES, knobY + 2 * RES,
        );
        ctx.lineTo(inpX + 12 * RES, knobY + 2 * RES);
        ctx.quadraticCurveTo(
            inpX + 10 * RES, knobY,
            inpX + 12 * RES, knobY - 2 * RES,
        );
        ctx.fillStyle = 'rgb(200,200,200)';
        ctx.strokeStyle = 'rgb(175,175,175)';
        ctx.lineWidth = 2;
        ctx.fill();
        ctx.stroke();

        ctx.beginPath();
        ctx.arc(knobX, knobY, 10 * RES, 0, Math.PI * 2);
        ctx.fillStyle = isDragging ? 'rgb(0,119,0)' : 'rgb(0,136,0)';
        ctx.strokeStyle = isDragging ? 'rgb(0,136,0)' : 'rgb(0,119,0)';
        ctx.fill();
        ctx.stroke();

        ctx.fillStyle = 'rgb(0,0,0)';
        ctx.textAlign = 'left';
        ctx.textBaseline = 'middle';
        ctx.font = `${Math.ceil(HEI * RES / 14)}px 'Open Sans', arial, sans-serif`;
        ctx.fillText(`${Math.round(currentRatio * 15)}`, inpX + inpWid + 5 * RES, inpY + (inpHei / 2));
    }, [config, canvas, currentRatio, isDragging]);

    React.useEffect(() => {
        if (!isFinished || !config) {
            return;
        }
        const tokenContent: TokenContent = {log, config};
        props.onToken(window.btoa(JSON.stringify(tokenContent)));
    }, [isFinished]);

    const getMouseXY = (e: React.MouseEvent): Coord => {
        const rect = canvas.current?.getBoundingClientRect();
        return [
            Math.round(e.pageX - (rect?.left ?? 0)),
            Math.round(e.pageY - (rect?.top ?? 0)),
        ];
    };

    const onDrag = (x: number) => {
        const rawRatio = (x - inpX / RES - 10) / (inpWid / RES - 20);
        setCurrentRatio(Math.max(0, Math.min(1, rawRatio)));
    };

    const appendLog = (entry: LogEntry) => {
        const stringEntry = `${entry.event}${entry.x},${entry.y}`;
        setLog((log_) => [...log_, stringEntry]);
    };

    const onDown = ([x, y]: Coord) => {
        const distSquare = Math.pow(x - knobX / RES, 2) + Math.pow(y - knobY / RES, 2);
        if (distSquare < 12 * 12) {
            setIsDragging(true);
            onDrag(x);
        }
        appendLog({event: 'D', x, y});
    };

    const onMove = ([x, y]: Coord) => {
        if (isDragging) {
            onDrag(x);
        }
        appendLog({event: 'M', x, y});
    };

    const onUp = ([x, y]: Coord) => {
        if (isDragging) {
            onDrag(x);
        }
        setIsDragging(false);
        appendLog({event: 'U', x, y});
        if (config && Math.round(currentRatio * 15) === targetValue) {
            setIsFinished(true);
        }
    };

    const forEachTouch = (fn: ((coord: Coord) => void), e: React.TouchEvent) => {
        const rect = canvas.current?.getBoundingClientRect();
        for (let i = 0; i < e.nativeEvent.changedTouches.length; i++) {
            const t = e.nativeEvent.changedTouches[i];
            fn([
                Math.round(t.pageX - (rect?.left ?? 0)),
                Math.round(t.pageY - (rect?.top ?? 0)),
            ]);
        }
    };

    return (
        <div className='olz-captcha'>
            <div className='captcha-instructions'>

                <b>Bot-Prüfung:</b>&nbsp;
                {config
                    ? `Bitte den Regler auf ${targetValue} stellen`
                    : (
                        <button
                            type='button'
                            className='btn btn-primary btn-sm'
                            id='start-captcha-button'
                            onClick={() => {
                                const getConfig = async () => {
                                    const response = await olzApi.call('startCaptcha', {});
                                    setConfig(response.config);
                                };
                                getConfig();
                            }}
                        >
                            starten
                        </button>
                    )
                }
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
                className='captcha-canvas'
                width={wid}
                height={hei}
                style={{width: `${WID}px`, height: `${HEI}px`}}
                ref={canvas}
                onMouseDown={(e) => onDown(getMouseXY(e))}
                onMouseMove={(e) => onMove(getMouseXY(e))}
                onMouseUp={(e) => onUp(getMouseXY(e))}
                onTouchStart={(e) => forEachTouch(onDown, e)}
                onTouchMove={(e) => forEachTouch(onMove, e)}
                onTouchEnd={(e) => forEachTouch(onUp, e)}
            >
            </canvas>
        </div>
    );
};
