import {OlzTerminLabelData, OlzTerminTemplateData} from '../../Api/client/generated_olz_api_types';
import {getFormBoolean, getFormString, validateDateOrNull} from '../../Utils/formUtils';
import {Entity} from '../../Utils/generalUtils';
import {isoNow} from '../../Utils/constants';

export interface TerminUpdate {
    startTime: string;
    endDate: string;
    endTime: string;
    title: string;
    text: string;
    deadline: string;
    shouldPromote: string;
    hasNewsletter: string|boolean;
    types: (string|boolean)[];
    locationId: number|null;
    fileIds: string[];
    imageIds: string[];
}

export function getTerminUpdateFromTemplate(
    templateData: OlzTerminTemplateData,
    startDate: string,
    startTime: string,
    labels: Entity<OlzTerminLabelData>[],
): TerminUpdate|null {
    const [error, computedStartDate] = validateDateOrNull(startDate);
    const computedStartTime = startTime || templateData.startTime || '';
    const startIso = error
        ? `${isoNow.substring(0, 10)} ${computedStartTime}`
        : `${computedStartDate} ${computedStartTime}`;
    const start = new Date(Date.parse(startIso));
    if (!(start instanceof Date) || isNaN(start.valueOf())) {
        console.warn(startIso);
        return null;
    }

    let endDate = '';
    let endTime = '';
    if (templateData.startTime && templateData.durationSeconds) {
        const end = new Date(start.getTime() + templateData.durationSeconds * 1000);
        const isoEnd = getISO(end);
        endDate = getFormString(isoEnd.substring(0, 10));
        endTime = getFormString(isoEnd.substring(11, 19));
    }

    let deadline = '';
    if (templateData.startTime && templateData.deadlineEarlierSeconds) {
        const deadlineOffset = new Date(start.getTime() - templateData.deadlineEarlierSeconds * 1000);
        if (!templateData.deadlineTime) {
            deadline = getFormString(getISO(deadlineOffset));
        } else {
            const deadlineDay = deadlineOffset.toISOString().substring(0, 10);
            const deadlineTimeThatDayIso = `${deadlineDay} ${templateData.deadlineTime}`;
            const deadlineTimeThatDay = new Date(Date.parse(deadlineTimeThatDayIso));
            const diffSeconds = (deadlineOffset.getTime() - deadlineTimeThatDay.getTime()) / 1000;
            let deadlineAtTime = deadlineTimeThatDay;
            if (diffSeconds < -12 * 60 * 60) {
                deadlineAtTime = new Date(deadlineTimeThatDay.getTime() - 24 * 60 * 60 * 1000);
            } else if (diffSeconds > 12 * 60 * 60) {
                deadlineAtTime = new Date(deadlineTimeThatDay.getTime() + 24 * 60 * 60 * 1000);
            }
            const isoDeadlineAtTime = getISO(deadlineAtTime);
            deadline = getFormString(isoDeadlineAtTime);
        }
    }

    const typesSet = new Set(templateData.types ?? []);
    const types = labels.map(
        (label) => getFormBoolean(typesSet.has(label.data.ident)),
    );

    return {
        startTime: getFormString(computedStartTime),
        endDate,
        endTime,
        title: getFormString(templateData.title),
        text: getFormString(templateData.text),
        deadline,
        shouldPromote: getFormBoolean(templateData.shouldPromote),
        hasNewsletter: getFormBoolean(templateData.newsletter),
        types,
        locationId: templateData.locationId ?? null,
        imageIds: templateData.imageIds ?? [],
        fileIds: templateData.fileIds ?? [],
    };
}

function getISO(dateTime: Date): string {
    const utcDate = toUTC(dateTime);
    const datePart = utcDate.toISOString().substring(0, 10);
    const timePart = utcDate.toISOString().substring(11, 19);
    return getFormString(`${datePart} ${timePart}`);
}

function toUTC(dateTime: Date): Date {
    return new Date(dateTime.getTime() - dateTime.getTimezoneOffset() * 60 * 1000);
}
