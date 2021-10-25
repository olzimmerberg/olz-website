import React from 'react';
import {olzOevSearchConnection} from '../oev';
import {OlzApiResponses} from '../../api/client';
import {OlzTransportConnectionSuggestion} from '../../api/client/generated_olz_api_types';
import {OlzTransportConnectionView} from './OlzTransportConnectionView';

export const OlzTransportConnectionSearch = () => {
    const [connectionSuggestions, setConnectionSuggestions] =
        React.useState<OlzTransportConnectionSuggestion[]>([]);

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
