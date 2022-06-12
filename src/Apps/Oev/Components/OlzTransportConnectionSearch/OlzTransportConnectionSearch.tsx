import React from 'react';
import {OlzApiResponses} from '../../../../Api/client';
import {OlzTransportSuggestion} from '../../../../Api/client/generated_olz_api_types';
import {olzDefaultFormSubmit, OlzRequestFieldResult, GetDataForRequestFunction, getFormField, getIsoDateTimeFromSwissFormat, getRequired, getStringOrNull,isFieldResultOrDictThereofValid, getFieldResultOrDictThereofErrors, getFieldResultOrDictThereofValue, validFormData, invalidFormData} from '../../../../../_/components/common/olz_default_form/olz_default_form';
import {OlzTransportConnectionView} from '../OlzTransportConnectionView/OlzTransportConnectionView';

export function olzOevSearchConnection(
    form: HTMLFormElement
): Promise<OlzApiResponses['searchTransportConnection']> {
    const getDataForRequestFn: GetDataForRequestFunction<'searchTransportConnection'> = (f) => {
        const fieldResults: OlzRequestFieldResult<'searchTransportConnection'> = {
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

export const OlzTransportConnectionSearch = () => {
    const [connectionSuggestions, setConnectionSuggestions] =
        React.useState<OlzTransportSuggestion[]>([]);

    const destinationInput = React.useRef<HTMLInputElement>();
    const arrivalInput = React.useRef<HTMLInputElement>();

    React.useEffect(() => {
        const params = new URLSearchParams(window.location.search);
        if (!destinationInput.current || !arrivalInput.current) {
            return;
        }
        destinationInput.current.value = params.get('nach');
        arrivalInput.current.value = params.get('ankunft');
    }, [destinationInput, arrivalInput]);

    const handleSubmit = React.useCallback((e: React.FormEvent<HTMLFormElement>) => {
        e.preventDefault();
        olzOevSearchConnection(e.target as HTMLFormElement)
            .then((response: OlzApiResponses['searchTransportConnection']) => {
                setConnectionSuggestions(response.suggestions);
            });
        return false;
    }, []);

    return (<>
        <form
            id='oev-form'
            className='default-form'
            onSubmit={handleSubmit}
        >
            <div className='success-message alert alert-success' role='alert'></div>
            <div>
                <label htmlFor='oev-destination-input'>
                    Nach
                    <span className='required-field-asterisk'> *</span>
                </label>
                <input
                    type='text'
                    name='destination'
                    className='form-control'
                    id='oev-destination-input'
                    ref={destinationInput}
                />
            </div>
            <div>
                <label htmlFor='oev-arrival-input'>
                    Ankunft (TT.MM.JJJJ SS:MM) 
                    <span className='required-field-asterisk'> *</span>
                </label>
                <input
                    type='text'
                    name='arrival'
                    className='form-control'
                    id='oev-arrival-input'
                    ref={arrivalInput}
                />
            </div>
            <p>
                <span className='required-field-asterisk'>* </span> 
                Zwingend notwendige Felder sind mit einem roten Sternchen gekennzeichnet.
            </p>
            <button type='submit' className='btn btn-primary'>Verbindung suchen</button>
            <div className='error-message alert alert-danger' role='alert'></div>
        </form>
        {connectionSuggestions.map(suggestion => (
            <OlzTransportConnectionView suggestion={suggestion} />
        ))}
    </>);
}
