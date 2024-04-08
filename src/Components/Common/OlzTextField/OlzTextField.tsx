import React from 'react';
import {FieldErrors, FieldValues, Path, RegisterOptions, UseFormRegister} from 'react-hook-form';

type OlzTextFieldMode = 'text-input'|'password-input'|'textarea';

interface OlzTextFieldProps<Values extends FieldValues, Name extends Path<Values>> {
    mode?: OlzTextFieldMode;
    title?: React.ReactNode;
    name: Name;
    options?: RegisterOptions<Values, Name>;
    errors?: FieldErrors<Values>;
    register: UseFormRegister<Values>;
    disabled?: boolean;
    placeholder?: string;
    autoComplete?: string;
}

export const OlzTextField = <
    Values extends FieldValues,
    Name extends Path<Values>,
>(props: OlzTextFieldProps<Values, Name>): React.ReactElement => {
    const errorMessage = props.errors?.[props.name]?.message;
    const errorClassName = errorMessage ? ' is-invalid' : '';
    const inputId = `${props.name}-input`;
    const className = `form-control${errorClassName}`;
    const labelComponent = <label htmlFor={inputId}>{props.title}</label>;
    const errorComponent = errorMessage && <p className='error'>{String(errorMessage)}</p>;

    if (props?.mode === 'textarea') {
        return (<>
            {labelComponent}
            <textarea
                {...props.register(props.name, props.options)}
                className={className}
                id={inputId}
                disabled={props.disabled}
                placeholder={props.placeholder}
            />
            {errorComponent}
        </>);
    }
    return (<>
        {labelComponent}
        <input
            type={props?.mode === 'password-input' ? 'password' : 'text'}
            {...props.register(props.name, props.options)}
            className={className}
            id={inputId}
            disabled={props.disabled}
            autoComplete={props.autoComplete}
            placeholder={props.placeholder}
        />
        {errorComponent}
    </>);
};
