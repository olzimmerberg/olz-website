/* eslint-env jasmine */

import React from 'react';
import {initReact} from '../../../src/Utils/reactUtils';
import {timeout} from '../../../src/Utils/generalUtils';

describe('initReact', () => {
    function initTestReactRoot() {
        document.body.innerHTML = '';
        const elem = document.createElement('div');
        elem.id = 'test-id';
        document.body.appendChild(elem);
        return elem;
    }

    it('works', async () => {
        initTestReactRoot();

        const result = initReact('test-id', <div>test-text</div>);
        await timeout(1);

        expect(result).toEqual(false);
        expect(document.body.textContent).toEqual('test-text');
    });

    it('aborts when element does not exist', async () => {
        initTestReactRoot();

        const result = initReact('inexistent-id', <div>test-text</div>);
        await timeout(1);

        expect(result).toEqual(false);
        expect(document.body.textContent).toEqual('');
    });

    it('unmounts existing react element', async () => {
        initTestReactRoot();

        const result1 = initReact('test-id', <div>test-text-1</div>);
        await timeout(1);

        expect(result1).toEqual(false);
        expect(document.body.textContent).toEqual('test-text-1');

        const result2 = initReact('test-id', <div>test-text-2</div>);
        await timeout(1);

        expect(result2).toEqual(false);
        expect(document.body.textContent).toEqual('test-text-2');
    });
});
