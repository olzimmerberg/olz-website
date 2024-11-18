/* eslint-env jasmine */

import * as dateUtils from '../../../src/Utils/dateUtils';

const defaultDateMs = Date.parse('2020-03-13 19:30:00');
const springDstMs = Date.parse('2020-03-29 02:00:00');
const fallDstMs = Date.parse('2020-10-25 02:00:00');
const oneHourMs = 60 * 60 * 1000;
const oneSecondMs = 1000;

describe('toUTC', () => {
    it('works', () => {
        const date = new Date(defaultDateMs);
        expect(dateUtils.toUTC(date)).toEqual(new Date(defaultDateMs + oneHourMs));
        expect(dateUtils.toUTC(new Date(defaultDateMs - oneHourMs))).toEqual(date);
    });

    it('works around 1970', () => {
        expect(dateUtils.toUTC(new Date(0))).toEqual(new Date(oneHourMs));
        expect(dateUtils.toUTC(new Date(-oneHourMs))).toEqual(new Date(0));
    });
});

describe('toISO', () => {
    it('works', () => {
        expect(dateUtils.toISO(new Date(0))).toEqual('1970-01-01 01:00:00');
        expect(dateUtils.toISO(new Date(-oneHourMs))).toEqual('1970-01-01 00:00:00');
    });

    it('is equivalent to alternative implementation', () => {
        const date = new Date(defaultDateMs);
        const year = date.getFullYear().toString();
        const month = (date.getMonth() + 1).toString().padStart(2, '0');
        const day = date.getDate().toString().padStart(2, '0');
        const hour = date.getHours().toString().padStart(2, '0');
        const min = date.getMinutes().toString().padStart(2, '0');
        const sec = date.getSeconds().toString().padStart(2, '0');
        const expectedIso = `${year}-${month}-${day} ${hour}:${min}:${sec}`;
        expect(dateUtils.toISO(date)).toEqual(expectedIso);
    });

    it('generates the same timestamp when parsing', () => {
        const now = new Date(defaultDateMs);
        expect(new Date(Date.parse(dateUtils.toISO(now)))).toEqual(now);
        expect(Date.parse(dateUtils.toISO(now))).toEqual(now.getTime());
    });

    it('works at daylight saving time boundaries', () => {
        expect(dateUtils.toISO(new Date(springDstMs - oneSecondMs))).toEqual('2020-03-29 01:59:59');
        expect(dateUtils.toISO(new Date(springDstMs))).toEqual('2020-03-29 03:00:00');
        expect(dateUtils.toISO(new Date(springDstMs + oneSecondMs))).toEqual('2020-03-29 03:00:01');

        expect(dateUtils.toISO(new Date(fallDstMs - oneSecondMs))).toEqual('2020-10-25 01:59:59');
        expect(dateUtils.toISO(new Date(fallDstMs))).toEqual('2020-10-25 02:00:00');
        expect(dateUtils.toISO(new Date(fallDstMs + oneSecondMs))).toEqual('2020-10-25 02:00:01');
        expect(dateUtils.toISO(new Date(fallDstMs + oneHourMs - oneSecondMs))).toEqual('2020-10-25 02:59:59');
        expect(dateUtils.toISO(new Date(fallDstMs + oneHourMs))).toEqual('2020-10-25 02:00:00');
        expect(dateUtils.toISO(new Date(fallDstMs + oneHourMs + oneSecondMs))).toEqual('2020-10-25 02:00:01');
        expect(dateUtils.toISO(new Date(fallDstMs + 2 * oneHourMs - oneSecondMs))).toEqual('2020-10-25 02:59:59');
        expect(dateUtils.toISO(new Date(fallDstMs + 2 * oneHourMs))).toEqual('2020-10-25 03:00:00');
        expect(dateUtils.toISO(new Date(fallDstMs + 2 * oneHourMs + oneSecondMs))).toEqual('2020-10-25 03:00:01');
    });
});
