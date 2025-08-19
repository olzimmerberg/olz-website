import React from 'react';
import {useHash} from 'react-use';

import {olzApi} from '../../../../Api/client';
import {OlzLogLevel, OlzLogsQuery} from '../../../../Api/client/generated_olz_api_types';
import {OlzInfiniteScroll} from '../OlzInfiniteScroll/OlzInfiniteScroll';
import {dataHref, isoNow} from '../../../../Utils/constants';
import {validateDate, validateDateTime} from '../../../../Utils/formUtils';
import {toISO} from '../../../../Utils/dateUtils';
import {assert} from '../../../../Utils/generalUtils';

import './OlzLogs.scss';

const MAX_LINE_LENGTH = 5000;

const serializeHash = (channel: string, date: string, logLevel: string, textSearch: string) => {
    const encChannel = encodeURIComponent(channel);
    const encDate = encodeURIComponent(date);
    const encLogLevel = encodeURIComponent(logLevel);
    const encTextSearch = encodeURIComponent(textSearch);
    return `#${encChannel}/${encDate}/${encLogLevel}/${encTextSearch}`;
};

const deserializeHash = (hash: string) => {
    const hashArray = hash.substring(1).split('/');
    if (hashArray.length !== 4) {
        return undefined;
    }
    return hashArray.map(decodeURIComponent);
};

const elementFromHash = (
    hash: string,
    setHash: (newHash: string) => void,
    index: number,
    defaultValue: string,
) => {
    const initialValue = deserializeHash(hash)?.[index] ?? defaultValue;
    const [value, setValue] = React.useState(initialValue);
    return [
        value,
        (newValue: string) => {
            setValue(newValue);
            const arr = deserializeHash(hash) ?? ['', '', '', ''];
            arr[index] = newValue;
            setHash(serializeHash(arr[0], arr[1], arr[2], arr[3]));
        },
    ] as const;
};

const getQuery = (channelArg: string, dateArg: string, logLevelArg: string, textSearchArg: string): OlzLogsQuery | null => {
    let targetDate: string | null = null;
    let firstDate: string | null = null;
    let lastDate: string | null = null;
    const [dateTimeError, isoDateTime] = validateDateTime(dateArg);
    const [dateError, isoDate] = validateDate(dateArg);
    if (/^\s*(now|jetzt)\s*$/i.exec(dateArg)) {
        targetDate = toISO(new Date());
    } else if (/^\s*(today|heute)\s*$/i.exec(dateArg)) {
        const todayIso = toISO(new Date()).substring(0, 10);
        firstDate = `${todayIso} 00:00:00`;
        lastDate = `${todayIso} 23:59:59`;
    } else if (!dateTimeError) {
        targetDate = isoDateTime;
    } else if (!dateError) {
        firstDate = `${isoDate} 00:00:00`;
        lastDate = `${isoDate} 23:59:59`;
    } else {
        return null;
    }

    const logLevelMap: {[key: string]: OlzLogLevel | null} = {
        'levels-all': null,
        'levels-info-higher': 'info',
        'levels-notice-higher': 'notice',
        'levels-warning-higher': 'warning',
        'levels-error-higher': 'error',
        'levels-critical-higher': 'critical',
        'levels-alert-higher': 'alert',
        'levels-emergency-higher': 'emergency',
    };
    const minLogLevel: OlzLogLevel | null = logLevelMap[logLevelArg] ?? null;

    const textSearch = textSearchArg || null;

    return {
        channel: channelArg,
        pageToken: null,
        targetDate,
        firstDate,
        lastDate,
        minLogLevel,
        textSearch,
    };
};

const getTokenQuery = (
    initialQuery: OlzLogsQuery,
    pageToken: string | null,
): OlzLogsQuery | null => (pageToken ? {
    ...initialQuery,
    pageToken,
} : null);

const renderItem = (line: string) => {
    if (line === '---') {
        return (
            <div className='log-line' id='initial-position'>
                <div id='initial-scroll'></div>
                {line}
            </div>
        );
    }

    const mustCrop = line.length > MAX_LINE_LENGTH;
    const croppedLine = mustCrop
        ? `${line.substring(0, MAX_LINE_LENGTH - 1)}\u{2026}`
        : line;
    const shouldGreyOut = shouldLineBeGreyedOut(line);
    if (shouldGreyOut) {
        return (
            <div className='log-line greyed-out'>
                {croppedLine}
            </div>
        );
    }

    const formattingRegex = /^(.*\s+)(\S*)\.(DEBUG|INFO|NOTICE|WARNING|ERROR|CRITICAL|ALERT|EMERGENCY)(.*)/;
    const match = formattingRegex.exec(croppedLine);
    if (!match) {
        return (<div className='log-line level-unknown'>{croppedLine}</div>);
    }
    const lineLogLevel = match[3].toLowerCase();
    return (
        <div className={`log-line level-${lineLogLevel}`}>
            {match[1]}
            <span className='log-channel'>
                {match[2]}
            </span>
            .
            <span className='log-level'>
                {match[3]}
            </span>
            {match[4]}
        </div>
    );
};

export const OlzLogs = (): React.ReactElement => {
    const channels = (window as unknown as {
        olzLogsChannels: {[id: string]: string}
    }).olzLogsChannels;

    const [hash, setHash] = useHash();

    const [channel, setChannel] = elementFromHash(hash, setHash, 0, Object.keys(channels)[0]);
    const [date, setDate] = elementFromHash(hash, setHash, 1, isoNow);
    const [logLevel, setLogLevel] = elementFromHash(hash, setHash, 2, 'levels-all');
    const [textSearch, setTextSearch] = elementFromHash(hash, setHash, 3, '');

    React.useEffect(() => {
        if (hash.substring(1).split('/').length !== 4) {
            setHash(serializeHash(channel, date, logLevel, textSearch));
        }
    }, []);

    const initialQuery = getQuery(channel, date, logLevel, textSearch);

    const fetch = async (query: OlzLogsQuery) => {
        const response = await olzApi.call(
            'getLogs',
            {query},
        );
        return {
            items: response.content,
            prevQuery: getTokenQuery(assert(initialQuery), response.pagination.previous),
            nextQuery: getTokenQuery(assert(initialQuery), response.pagination.next),
        };
    };

    return (<>
        <div className='logs-header bar'>
            <select
                id='log-channel-select'
                className='form-control form-select form-select-sm'
                value={channel}
                onChange={(e) => {
                    const select = e.target;
                    const newLogChannel = select.options[select.selectedIndex].value;
                    setChannel(newLogChannel);
                }}
            >
                {Object.keys(channels).map((id) => (
                    <option value={id} key={id}>{channels[id]}</option>
                ))}
            </select>
            <div className='input-group input-group-sm'>
                <div className='input-group-prepend input-group-text input-group-text-sm'>
                    <img src={`${dataHref}assets/icns/calendar.svg`} className='noborder icon' />
                </div>
                <input
                    type='text'
                    id='date-filter-input'
                    className='form-control form-control-sm'
                    value={date}
                    onChange={(e) => {
                        const newDate = e.target.value;
                        setDate(newDate);
                    }}
                />
            </div>
            <select
                id='log-level-filter-select'
                className='form-control form-select form-select-sm'
                value={logLevel}
                onChange={(e) => {
                    const select = e.target;
                    const newLogLevel = select.options[select.selectedIndex].value;
                    setLogLevel(newLogLevel);
                }}
            >
                <option value='levels-all'>Alle Log-Levels</option>
                <option value='levels-info-higher'>"Info" & höher</option>
                <option value='levels-notice-higher'>"Notice" & höher</option>
                <option value='levels-warning-higher'>"Warning" & höher</option>
                <option value='levels-error-higher'>"Error" & höher</option>
                <option value='levels-critical-higher'>"Critical" & höher</option>
                <option value='levels-alert-higher'>"Alert" & höher</option>
                <option value='levels-emergency-higher'>"Emergency" & höher</option>
            </select>
            <div className='input-group input-group-sm'>
                <div className='input-group-prepend input-group-text input-group-text-sm'>
                    <img src={`${dataHref}assets/icns/magnifier_16.svg`} className='noborder icon' />
                </div>
                <input
                    type='text'
                    id='text-search-filter-input'
                    className='form-control form-control-sm'
                    placeholder='Suche...'
                    value={textSearch}
                    onChange={(e) => {
                        const newTextSearch = e.target.value;
                        setTextSearch(newTextSearch);
                    }}
                />
            </div>
        </div>
        <div id='logs'>
            {initialQuery ? (
                <OlzInfiniteScroll
                    initialQuery={initialQuery}
                    fetch={fetch}
                    renderItem={renderItem}
                />
            ) : (
                <div className='error-message'>Ungültige Anfrage</div>
            )}
        </div>
    </>);
};

export function shouldLineBeGreyedOut(line: string): boolean {
    return (
        line.includes(' Olz\\Command\\Monitor') ||
        line.includes(' command olz:monitor') ||
        line.includes('access forbidden by rule')
    );
}

// $(() => {
//     olzLogsFetch();
//     olzLogsGetFirstLog();
//     olzLogsLevelFilterChange();
// });
