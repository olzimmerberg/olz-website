import 'bootstrap';
import React from 'react';
import {useController, Control, FieldValues, FieldErrors, UseControllerProps, Path} from 'react-hook-form';
import {OlzSearchableEntityType} from '../../../Api/client/generated_olz_api_types';
import {OlzEntityChooser} from '../OlzEntityChooser/OlzEntityChooser';

interface OlzEntityFieldProps<
    Values extends FieldValues,
    Name extends Path<Values>,
> {
    title?: React.ReactNode;
    entityType: OlzSearchableEntityType;
    filter?: {[key: string]: string};
    name: Name;
    rules?: UseControllerProps<Values, Name>['rules'];
    errors?: FieldErrors<Values>;
    control: Control<Values, Name>;
    setIsLoading?: (isLoading: boolean) => void;
    disabled?: boolean;
    nullLabel?: string;
}

export const OlzEntityField = <
    Values extends FieldValues,
    Name extends Path<Values>,
>(props: OlzEntityFieldProps<Values, Name>): React.ReactElement => {
    const {field} = useController({
        name: props.name,
        control: props.control,
        rules: props.rules,
    });

    const errorMessage = props.errors?.[props.name]?.message?.toString();
    const errorComponent = errorMessage && <p className='error'>{errorMessage}</p>;

    return (<>
        <label htmlFor={`${props.name}-field`}>
            {props.title}
        </label>
        <div className='olz-entity-field' id={`${props.name}-field`}>
            <OlzEntityChooser
                entityType={props.entityType}
                entityId={field.value}
                onEntityIdChange={(e) => {
                    field.onChange(e.detail);
                }}
                setIsLoading={props?.setIsLoading}
                disabled={props?.disabled}
                nullLabel={props.nullLabel}
                hasError={Boolean(errorMessage)}
            >
            </OlzEntityChooser>
        </div>
        {errorComponent}
    </>);
};
