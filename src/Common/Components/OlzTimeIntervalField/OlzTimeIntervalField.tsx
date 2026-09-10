import React from 'react';
import {Control, FieldErrors, FieldValues, Path, useController} from 'react-hook-form';

import './OlzTimeIntervalField.scss';

type TimeIntervalUnit = 'MONTHS' | 'WEEKS' | 'DAYS' | 'HOURS' | 'MINUTES' | 'SECONDS';

const getTimeIntervalUnit = (value: string): TimeIntervalUnit | null => {
    switch (value) {
        case 'MONTHS':
            return 'MONTHS';
        case 'WEEKS':
            return 'WEEKS';
        case 'DAYS':
            return 'DAYS';
        case 'HOURS':
            return 'HOURS';
        case 'MINUTES':
            return 'MINUTES';
        case 'SECONDS':
            return 'SECONDS';
        default:
            return null;
    }
};

const getDefaultUnitForValue = (seconds: number): TimeIntervalUnit => {
    if (seconds < 2 * 60) {
        return 'SECONDS';
    } else if (seconds < 2 * 60 * 60) {
        return 'MINUTES';
    } else if (seconds < 2 * 60 * 60 * 24) {
        return 'HOURS';
    } else if (seconds < 2 * 60 * 60 * 24 * 7) {
        return 'DAYS';
    } else if (seconds < 2 * 60 * 60 * 24 * 30) {
        return 'WEEKS';
    }
    return 'MONTHS';
};

const getFactorForUnit = (unit: TimeIntervalUnit): number => {
    switch (unit) {
        case 'SECONDS':
            return 1;
        case 'MINUTES':
            return 60;
        case 'HOURS':
            return 60 * 60;
        case 'DAYS':
            return 60 * 60 * 24;
        case 'WEEKS':
            return 60 * 60 * 24 * 7;
        case 'MONTHS':
            return 60 * 60 * 24 * 30;
        default:
            throw new Error(`Unexpected unit: ${unit}`);
    }
};

const getUserValue = (value: number | null, unit: TimeIntervalUnit): string => {
    if (value === null) {
        return '';
    }
    return (value / getFactorForUnit(unit)).toString();
};

interface OlzTimeIntervalFieldProps<Values extends FieldValues, Name extends Path<Values>> {
    title?: React.ReactNode;
    name: Name;
    errors?: FieldErrors<Values>;
    control: Control<Values, Name>;
    disabled?: boolean;
    placeholder?: string;
    autoComplete?: string;
    onFocus?: React.FocusEventHandler<HTMLTextAreaElement & HTMLInputElement>;
}

export const OlzTimeIntervalField = <
    Values extends FieldValues,
    Name extends Path<Values>,
>(props: OlzTimeIntervalFieldProps<Values, Name>): React.ReactElement => {
    const {field} = useController({
        name: props.name,
        control: props.control,
    });

    const initialValue = field.value === '' ? null : Number(field.value);

    const [unit, setUnit] = React.useState<TimeIntervalUnit>(getDefaultUnitForValue(initialValue ?? 0));
    const [value, setValue] = React.useState<string>(getUserValue(initialValue, unit));

    React.useEffect(() => {
        if (value === '') {
            field.onChange('');
            return;
        }
        const numberValue = Number(value);
        if (Number.isNaN(numberValue)) {
            field.onChange('NaN');
            return;
        }
        const newValue = numberValue * getFactorForUnit(unit);
        field.onChange(newValue.toString());
    }, [value, unit]);

    const errorMessage = props.errors?.[props.name]?.message;
    const errorClassName = errorMessage ? ' is-invalid' : '';
    const inputId = `${props.name}-number-input`;
    const unitInputId = `${props.name}-unit-input`;
    const labelComponent = <label htmlFor={inputId}>{props.title}</label>;
    const errorComponent = errorMessage && <p className='error'>{String(errorMessage)}</p>;

    return (<>
        {labelComponent}
        <div className='olz-time-interval-field' id={`${props.name}-field`}>
            <input
                type='text'
                className={`form-control number-input${errorClassName}`}
                id={inputId}
                value={value}
                onChange={(e) => {
                    setValue(e.target.value);
                }}
                disabled={props.disabled}
                autoComplete={props.autoComplete}
                placeholder={props.placeholder}
                onFocus={props.onFocus}
            />
            <select
                className={`form-control form-select unit-input${errorClassName}`}
                id={unitInputId}
                value={unit}
                onChange={(e) => {
                    setUnit(getTimeIntervalUnit(e.target.value) ?? 'SECONDS');
                }}
                disabled={props.disabled}
            >
                <option value='SECONDS'>Sekunden</option>
                <option value='MINUTES'>Minuten</option>
                <option value='HOURS'>Stunden</option>
                <option value='DAYS'>Tage</option>
                <option value='WEEKS'>Wochen</option>
                <option value='MONTHS'>Monate</option>
            </select>
        </div>
        {errorComponent}
    </>);
};
