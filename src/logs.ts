import {OlzApiEndpoint, callOlzApi} from './api/client';

export function olzLogsGetLogs(): boolean {
    callOlzApi(
        OlzApiEndpoint.getLogs,
        {index: 0},
    )
        .then((response) => {
            $('#logs').html(processLogs(response.content));
        })
        .catch((err) => {
            console.log(err);
        });
    return false;
}

function processLogs(logs: string): string {
    const lines = logs.split('\n');
    return lines
        .map((line) => (line.includes('access forbidden by rule') ? `<div class='greyed-out'>${line}</div>` : line))
        .map((line) => `<div>${line}</div>`)
        .join('\n');
}
