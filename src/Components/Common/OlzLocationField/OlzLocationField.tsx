import React from 'react';
import {FieldErrors, FieldValues, Path, useController, Control, UseControllerProps} from 'react-hook-form';
import {OlzLocationCoordinates} from '../../../Api/client';
import {WGStoCHx, WGStoCHy, CHtoWGSlat, CHtoWGSlng} from '../../../Utils/mapUtils';
import {getOlzLocationMap} from '../OlzLocationMap/OlzLocationMap';

import './OlzLocationField.scss';

export function deserializeLocation(location: string): OlzLocationCoordinates {
    const match = /^(-?[0-9.]+),(-?[0-9.]+)$/.exec(location);
    const latitude = Number(match?.[1] ?? '0');
    const longitude = Number(match?.[2] ?? '0');
    return {latitude, longitude};
}

export function serializeLocation(location: OlzLocationCoordinates): string {
    return `${location.latitude.toFixed(5)},${location.longitude.toFixed(5)}`;
}

function parseUserLocation(userLocation: string): OlzLocationCoordinates | null {
    const matchLatLng = /^\s*(-?[0-9.]+),\s*(-?[0-9.]+)\s*$/.exec(userLocation);
    if (matchLatLng) {
        return {
            latitude: Number(matchLatLng[1]),
            longitude: Number(matchLatLng[2]),
        };
    }
    const matchCh03 = /^\s*2?([0-9']+(?:[.,][0-9']+)?)[,\s]+1?([0-9']+(?:[.,][0-9']+)?)\s*$/.exec(userLocation);
    if (matchCh03) {
        const chY = Number(matchCh03[1].replaceAll('\'', '').replace(',', '.'));
        const chX = Number(matchCh03[2].replaceAll('\'', '').replace(',', '.'));
        ;
        return {
            latitude: CHtoWGSlat(chY, chX),
            longitude: CHtoWGSlng(chY, chX),
        };
    }
    return null;
}

function userTextLocation(location: OlzLocationCoordinates): string {
    return `${location.latitude.toFixed(5)}, ${location.longitude.toFixed(5)}`;
}

interface OlzLocationFieldProps<Values extends FieldValues, Name extends Path<Values>> {
    title?: React.ReactNode;
    name: Name;
    rules?: UseControllerProps<Values, Name>['rules'];
    errors?: FieldErrors<Values>;
    control: Control<Values, Name>;
    disabled?: boolean;
}

export const OlzLocationField = <Values extends FieldValues, Name extends Path<Values>>(
    props: OlzLocationFieldProps<Values, Name>,
): React.ReactElement => {
    const {field} = useController({
        name: props.name,
        control: props.control,
    });

    const value = field.value;
    const location = deserializeLocation(value);
    const userLocation = userTextLocation(location);

    const [userValue, setUserValue] = React.useState(userLocation);

    React.useEffect(() => {
        const newLocation = parseUserLocation(userValue);
        const newValue = newLocation ? serializeLocation(newLocation) : '';
        if (newLocation && newValue !== value) {
            field.onChange(newValue);
        }
    }, [value, userValue]);

    React.useEffect(() => {
        const newLocation = deserializeLocation(value);
        console.log(value, newLocation);
        getOlzLocationMap(
            'location-field-map-overview',
            '',
            newLocation?.latitude ?? 0,
            newLocation?.longitude ?? 0,
            8,
        );
        getOlzLocationMap(
            'location-field-map-medium',
            '',
            newLocation?.latitude ?? 0,
            newLocation?.longitude ?? 0,
            13,
        );
        getOlzLocationMap(
            'location-field-map-detail',
            '',
            newLocation?.latitude ?? 0,
            newLocation?.longitude ?? 0,
            18,
        );
    }, [value]);

    const errorMessage = props.errors?.[props.name]?.message;
    const errorClassName = errorMessage ? ' is-invalid' : '';
    const inputId = `${props.name}-input`;
    const className = `olz-location-field${errorClassName}`;
    const labelComponent = <label htmlFor={inputId}>{props.title}</label>;
    const errorComponent = errorMessage && <p className='error'>{String(errorMessage)}</p>;

    const chX = Math.round(WGStoCHx(location.latitude, location.longitude));
    const chY = Math.round(WGStoCHy(location.latitude, location.longitude));
    const latSign = location.latitude > 0 ? 'N' : 'S';
    const lngSign = location.longitude > 0 ? 'E' : 'W';
    const latDeg = Math.round(Math.abs(location.latitude));
    const lngDeg = Math.round(Math.abs(location.longitude));
    const latMin = Math.round(Math.abs(location.latitude) * 60) % 60;
    const lngMin = Math.round(Math.abs(location.longitude) * 60) % 60;
    const latSec = Math.round(Math.abs(location.latitude) * 60 * 60) % 60;
    const lngSec = Math.round(Math.abs(location.longitude) * 60 * 60) % 60;
    return (<>
        {labelComponent}
        <div className={className}>
            <div id='location-field-map-overview'></div>
            <div id='location-field-map-medium'></div>
            <div id='location-field-map-detail'></div>
            <div id='location-field-input-column'>
                <input
                    type='text'
                    id={inputId}
                    disabled={props.disabled}
                    value={userValue}
                    onChange={(e) => {
                        setUserValue(e.target.value);
                    }}
                    className='form-control'
                />
                <table>
                    <tr>
                        <td>WGS 84:</td>
                        <td>{location.latitude}, {location.longitude}</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>{latDeg}° {latMin}' {latSec}" {latSign}, {lngDeg}° {lngMin}' {lngSec}" {lngSign}</td>
                    </tr>
                    <tr>
                        <td>CH1903+ / LV 95:</td>
                        <td>2{chY}, 1{chX}</td>
                    </tr>
                    <tr>
                        <td>CH1903 / LV 03:</td>
                        <td>{chY}, {chX}</td>
                    </tr>
                </table>
            </div>
        </div>
        {errorComponent}
    </>);
};
