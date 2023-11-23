import React from 'react';
import {FieldErrors, FieldValues, Path, RegisterOptions, UseFormRegister} from 'react-hook-form';

interface OlzTextFieldProps<Values extends FieldValues, Name extends Path<Values>> {
    title?: string;
    name: Name;
    options?: RegisterOptions<Values, Name>;
    errors?: FieldErrors<Values>;
    register: UseFormRegister<Values>;
}

export const OlzTextField = <
    Values extends FieldValues,
    Name extends Path<Values>,
>(props: OlzTextFieldProps<Values, Name>): React.ReactElement => {
    const errorMessage = props.errors?.[props.name]?.message;
    const errorClassName = errorMessage ? ' is-invalid' : '';
    return (<>
        <label htmlFor={`${props.name}-input`}>{props.title}</label>
        <input
            type='text'
            {...props.register(props.name, props.options)}
            className={`form-control${errorClassName}`}
            id={`${props.name}-input`}
        />
        {errorMessage && <p className='error'>{String(errorMessage)}</p>}
    </>);
};
