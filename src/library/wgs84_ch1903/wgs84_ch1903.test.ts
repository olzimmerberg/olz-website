/* eslint-env jasmine */

import {DECtoSEX, WGStoCHy, WGStoCHx, CHtoWGSlat, CHtoWGSlng} from './wgs84_ch1903';

const GROSSMUENSTER = {
    lat: 47.37022,
    lng: 8.54377,
    y: 683471,
    x: 247185,
};

describe('DECtoSEX', () => {
    it('works', () => {
        expect(DECtoSEX(0)).toBe(0);
        expect(DECtoSEX(1)).toBe(3600);
        expect(DECtoSEX(0.5)).toBe(1800);
    });
});

describe('WGS to CH', () => {
    it('works for Grossmünster', () => {
        const x = WGStoCHx(GROSSMUENSTER.lat, GROSSMUENSTER.lng);
        const y = WGStoCHy(GROSSMUENSTER.lat, GROSSMUENSTER.lng);
        expect(Math.round(x)).toBe(GROSSMUENSTER.x);
        expect(Math.round(y)).toBe(GROSSMUENSTER.y);
    });
});

describe('CH to WGS', () => {
    it('works for Grossmünster', () => {
        const lat = CHtoWGSlat(GROSSMUENSTER.y, GROSSMUENSTER.x);
        const lng = CHtoWGSlng(GROSSMUENSTER.y, GROSSMUENSTER.x);
        expect(roundToPlaces(lat, 4)).toBe(roundToPlaces(GROSSMUENSTER.lat, 4));
        expect(roundToPlaces(lng, 4)).toBe(roundToPlaces(GROSSMUENSTER.lng, 4));
    });
});

function roundToPlaces(number: number, places: number): number {
    const factor = Math.pow(10, places);
    return Math.round(number * factor) / factor;
}