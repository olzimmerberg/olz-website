import $ from 'jquery';

import {callOlzApi} from '../../../../Api/client';

import './OlzLogs.scss';

let logIndex = 0;

export function olzLogsGetFirstLog(): boolean {
    logIndex = 0;
    return olzLogsGetNextLog();
}

export function olzLogsGetNextLog(): boolean {
    callOlzApi(
        'getLogs',
        {index: logIndex},
    )
        .then((response) => {
            logIndex++;
            $('#logs').prepend(processLogs(response.content ?? ''));
        })
        .catch((err) => {
            console.log(err);
        });
    return false;
}

export function olzLogsLevelFilterChange(): void {
    const logLevelFilterSelect = document.getElementById('log-level-filter-select') as HTMLSelectElement;
    const newLogLevelFilter = logLevelFilterSelect.value;
    $('#logs').removeClass('levels-all');
    $('#logs').removeClass('levels-info-higher');
    $('#logs').removeClass('levels-notice-higher');
    $('#logs').removeClass('levels-warning-higher');
    $('#logs').removeClass('levels-error-higher');
    $('#logs').addClass(newLogLevelFilter);
}

function processLogs(logs: string): string {
    const lines = logs.split('\n');
    const formattingRegex = /(\S+)\.(DEBUG|INFO|NOTICE|WARNING|ERROR|CRITICAL|ALERT|EMERGENCY)/;
    return lines
        .map((line) => line
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;'))
        .map((line) => (
            line.includes('access forbidden by rule')
                ? `<div class='greyed-out'>${line}</div>`
                : line
        ))
        .map((line) => {
            let logLevel = 'unknown';
            const replacedLine = line.replace(
                formattingRegex,
                (match) => {
                    const res = formattingRegex.exec(match);
                    if (!res) {
                        return match;
                    }
                    logLevel = res[2].toLowerCase();
                    return `<span class='log-channel'>${res[1]}</span>.<span class='log-level'>${res[2]}</span>`;
                },
            );
            return `<div class='log-line level-${logLevel}'>${replacedLine}</div>`;
        })
        .join('\n');
}

$(() => {
    olzLogsGetFirstLog();
    olzLogsLevelFilterChange();
});
