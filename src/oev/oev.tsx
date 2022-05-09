import React from 'react';
import ReactDOM from 'react-dom';
import {OlzApiResponses} from '../api/client';
import {olzDefaultFormSubmit, GetDataForRequestFunction, getFormField, getIsoDateTimeFromSwissFormat, getRequired, getStringOrNull,isFieldResultOrDictThereofValid, getFieldResultOrDictThereofErrors, getFieldResultOrDictThereofValue, validFormData, invalidFormData} from '../components/common/olz_default_form/olz_default_form';
import {OlzTransportConnectionSearch} from './components/OlzTransportConnectionSearch';

export function initOlzTransportConnectionSearch() {
    ReactDOM.render(
        <OlzTransportConnectionSearch />,
        document.getElementById('oev-root'),
    );
    return false;
}

export function olzOevSearchConnection(
    form: HTMLFormElement
): Promise<OlzApiResponses['searchTransportConnection']> {
    const getDataForRequestFn: GetDataForRequestFunction<'searchTransportConnection'> = (f) => {
        const fieldResults = {
            destination: getRequired(getStringOrNull(getFormField(f, 'destination'))),
            arrival: getRequired(getIsoDateTimeFromSwissFormat(getFormField(f, 'arrival'))),
        };
        const nach = getFormField(f, 'destination').value;
        const ankunft = getFormField(f, 'arrival').value;
        const queryParams = `?nach=${nach}&ankunft=${ankunft}`;
        window.history.pushState({} , '', queryParams);
        if (!isFieldResultOrDictThereofValid(fieldResults)) {
            return invalidFormData(getFieldResultOrDictThereofErrors(fieldResults));
        }
        return validFormData(getFieldResultOrDictThereofValue(fieldResults));
    };

    return olzDefaultFormSubmit(
        'searchTransportConnection',
        getDataForRequestFn,
        form,
        handleResponse,
    );
}

function handleResponse(response: OlzApiResponses['searchTransportConnection']): string|void {
    if (response.status !== 'OK') {
        throw new Error(`Antwort: ${response.status}`);
    }
    return 'Anfrage war erfolgreich!';
}
