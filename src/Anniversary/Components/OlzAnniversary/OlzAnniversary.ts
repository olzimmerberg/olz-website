import {olzApi} from '../../../Api/client';
import {initOlzEditRunModal} from '../OlzEditRunModal/OlzEditRunModal';

import './OlzAnniversary.scss';

export function olzAnniversaryEditRun(
    runId: number,
): boolean {
    olzApi.call('editRun', {id: runId})
        .then((response) => {
            initOlzEditRunModal(response.id, response.meta, response.data);
        });
    return false;
}

export function handleRocketClick(elem: HTMLDivElement, click: MouseEvent): void {
    elem.parentElement?.removeChild(elem);
    document.body.appendChild(elem);
    elem.ondblclick = () => false;
    elem.style.position = 'absolute';
    elem.style.width = '100px';
    elem.style.margin = '0';
    elem.style.top = `${click.pageY}px`;
    elem.style.left = `${click.pageX}px`;
    const speed = 0.3;
    let currentPosition = [click.pageX, click.pageY];
    let currentTarget = [click.pageX, click.pageY, 0];
    window.setInterval(() => {
        currentPosition = [
            (currentPosition[0] + currentTarget[0] * speed) / (1 + speed),
            (currentPosition[1] + currentTarget[1] * speed) / (1 + speed),
        ];
        elem.style.top = `${currentPosition[1]}px`;
        elem.style.left = `${currentPosition[0]}px`;
        elem.style.transform = `translate(-50px,-62px) rotate(${currentTarget[2]}rad) translate(0,62px)`;
    }, 50);
    document.addEventListener('mousemove', (move: MouseEvent) => {
        const angle = Math.atan2(
            move.pageY - currentPosition[1],
            move.pageX - currentPosition[0],
        ) + Math.PI / 2;
        currentTarget = [move.pageX, move.pageY, angle];
    });
}

export function handleRocketTap(elem: HTMLDivElement): void {
    elem.ondblclick = () => false;
}
