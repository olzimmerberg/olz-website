/* eslint-env jasmine */

import {EventTarget} from '../../../_/utils/EventTarget';

describe('EventTarget', () => {
    it('works (integration test)', async () => {
        const eventTarget = new EventTarget<{'message': string, 'measurement': number}>();
        const messageCalls: string[] = [];
        const messageCallback = (event: CustomEvent<string>) => {
            messageCalls.push(event.detail);
        };
        const measurementCalls: number[] = [];
        const measurementCallback = (event: CustomEvent<number>) => {
            measurementCalls.push(event.detail);
        };

        eventTarget.addEventListener('message', messageCallback);
        eventTarget.dispatchEvent('message', 'one');
        eventTarget.dispatchEvent('measurement', 1);

        expect(messageCalls).toEqual(['one']);
        expect(measurementCalls).toEqual([]);

        eventTarget.addEventListener('measurement', measurementCallback);
        eventTarget.dispatchEvent('message', 'two');
        eventTarget.dispatchEvent('measurement', 2);

        expect(messageCalls).toEqual(['one', 'two']);
        expect(measurementCalls).toEqual([2]);

        eventTarget.removeEventListener('message', messageCallback);
        eventTarget.dispatchEvent('message', 'three');
        eventTarget.dispatchEvent('measurement', 3);

        expect(messageCalls).toEqual(['one', 'two']);
        expect(measurementCalls).toEqual([2, 3]);

        eventTarget.removeAllEventListeners();
        eventTarget.dispatchEvent('message', 'four');
        eventTarget.dispatchEvent('measurement', 4);

        expect(messageCalls).toEqual(['one', 'two']);
        expect(measurementCalls).toEqual([2, 3]);
    });

    it('can handle failing listeners', () => {
        const eventTarget = new EventTarget<{'message': string, 'measurement': number}>();

        let failedCallbackCalled = false;
        const failingCallback = () => {
            failedCallbackCalled = true;
            throw new Error('test');
        };
        eventTarget.addEventListener('message', failingCallback);
        eventTarget.dispatchEvent('message', 'four');

        expect(failedCallbackCalled).toEqual(true);
    });

    it('can remove inexistent listener', () => {
        const eventTarget = new EventTarget<{'message': string, 'measurement': number}>();

        eventTarget.removeEventListener('message', () => undefined);

        expect(eventTarget).not.toEqual(null);
    });
});
