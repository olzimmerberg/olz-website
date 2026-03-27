import React from 'react';
import {FieldErrors, FieldValues, Path, useController, Control, UseControllerProps} from 'react-hook-form';

interface OlzLocationFieldProps<Values extends FieldValues,Name extends Path<Values>> {
    title?: React.ReactNode;
    name: Name;
    rules?: UseControllerProps<Values, Name>['rules'];
    errors?: FieldErrors<Values>;
    control: Control<Values, Name>;
    setIsLoading?: (isLoading: boolean) => void;
    disabled?: boolean;
}

export const OlzLocationField = <Values extends FieldValues, Name extends Path<Values>>(
    props: OlzLocationFieldProps<Values, Name>
): React.ReactElement => {
    const {field} = useController({
        name: props.name,
        control: props.control,
    });

    const value = field.value;
    const errorMessage = props.errors?.[props.name]?.message;
    const errorClassName = errorMessage ? ' is-invalid' : '';
    const inputId = `${props.name}-input`;
    const className = `olz-location-field form-control${errorClassName}`;
    const labelComponent = <label htmlFor={inputId}>{props.title}</label>;
    const errorComponent = errorMessage && <p className='error'>{String(errorMessage)}</p>;

    return (<>
        {labelComponent}
        <div className={className}>
            {value}
        </div>
        {errorComponent}
    </>);
};
