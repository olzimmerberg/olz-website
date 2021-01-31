import {OlzApiEndpoint, callOlzApi} from './api/client';

let logIndex = 0;

export function olzLogsGetFirstLog(): boolean {
    logIndex = 0;
    return olzLogsGetNextLog();
}

export function olzLogsGetNextLog(): boolean {
    callOlzApi(
        OlzApiEndpoint.getLogs,
        {index: logIndex},
    )
        .then((response) => {
            logIndex++;
            $('#logs').prepend(processLogs(response.content));
        })
        .catch((err) => {
            console.log(err);
        });
    return false;
}

function processLogs(logs: string): string {
    const lines = logs.split('\n');
    return lines
        .map((line) => (
            line.includes('access forbidden by rule')
                ? `<div class='greyed-out'>${line}</div>`
                : line
        ))
        .map((line) => line.replace(
            /(\w+)\.(DEBUG|INFO|NOTICE|WARNING|ERROR|CRITICAL|ALERT|EMERGENCY)/,
            '<span class=\'log-channel\'>$1</span>.<span class=\'log-level $2\'>$2</span>',
        ))
        .map((line) => `<div>${line}</div>`)
        .join('\n');
}
