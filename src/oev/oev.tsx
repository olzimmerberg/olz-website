import React from 'react';
import ReactDOM from 'react-dom';
import {OlzApiResponses, OlzApiEndpoint} from '../api/client';
import {olzDefaultFormSubmit, GetDataForRequestDict, getFormField, getIsoDateTimeFromSwissFormat, getRequired} from '../components/common/olz_default_form/olz_default_form';
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
): Promise<OlzApiResponses[OlzApiEndpoint.searchTransportConnection]> {
    const getDataForRequestDict: GetDataForRequestDict<OlzApiEndpoint.searchTransportConnection> = {
        destination: (f) => getRequired('destination', getFormField(f, 'destination')),
        arrival: (f) => getRequired('arrival', getIsoDateTimeFromSwissFormat('arrival', getFormField(f, 'arrival'))),
    };

    return olzDefaultFormSubmit(
        OlzApiEndpoint.searchTransportConnection,
        getDataForRequestDict,
        form,
        handleResponse,
    );
}

function handleResponse(response: OlzApiResponses[OlzApiEndpoint.searchTransportConnection]): string|void {
    if (response.status !== 'OK') {
        throw new Error(`Antwort: ${response.status}`);
    }
    return 'Anfrage war erfolgreich!';
}
