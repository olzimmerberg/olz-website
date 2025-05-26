import 'bootstrap';
import React from 'react';
import {useController, Control, FieldValues, FieldErrors, UseControllerProps, Path} from 'react-hook-form';
import {olzApi} from '../../../Api/client';
import {OlzSearchableEntityType} from '../../../Api/client/generated_olz_api_types';
import {OlzEntityChooser} from '../OlzEntityChooser/OlzEntityChooser';

import './OlzPositionField.scss';

type BeforeAfterValue = 'BEFORE'|'AFTER'|'ANYWHERE'|'NOWHERE';

const getBeforeAfterValue = (value: string): BeforeAfterValue|null => {
    switch (value) {
        case 'ANYWHERE':
            return 'ANYWHERE';
        case 'BEFORE':
            return 'BEFORE';
        case 'AFTER':
            return 'AFTER';
        case 'NOWHERE':
            return 'NOWHERE';
        default:
            return null;
    }
};

const MAX_FLOAT = 1e6;

interface OlzPositionFieldProps<
    Values extends FieldValues,
    Name extends Path<Values>,
> {
    title?: React.ReactNode;
    entityType: OlzSearchableEntityType;
    name: Name;
    filter?: {[key: string]: string};
    rules?: UseControllerProps<Values, Name>['rules'];
    errors?: FieldErrors<Values>;
    control: Control<Values, Name>;
    setIsLoading?: (isLoading: boolean) => void;
    disabled?: boolean;
    nullLabel?: string;
}

export const OlzPositionField = <
    Values extends FieldValues,
    Name extends Path<Values>,
>(props: OlzPositionFieldProps<Values, Name>): React.ReactElement => {
    const {field} = useController({
        name: props.name,
        control: props.control,
        rules: props.rules,
    });

    const [isInitialized, setIsInitialized] = React.useState<boolean>(false);
    const [beforeAfter, setBeforeAfter] = React.useState<BeforeAfterValue>(field.value === '' ? 'NOWHERE' : 'ANYWHERE');
    const [id, setId] = React.useState<number|null>(null);

    const filterCache = React.useMemo<string>(() => JSON.stringify(props.filter), [props.filter]);
    const errorMessage = props.errors?.[props.name]?.message?.toString();
    const errorClassName = errorMessage ? ' olz-is-invalid' : '';
    const errorComponent = errorMessage && <p className='error'>{errorMessage}</p>;

    React.useEffect(() => {
        if (isInitialized) {
            return;
        }
        if (field.value === '') {
            setIsInitialized(true);
        }
        props.setIsLoading?.(true);
        olzApi.call('getEntitiesAroundPosition', {
            entityType: props.entityType,
            entityField: props.name,
            position: parseFloat(field.value),
            filter: props.filter,
        })
            .then((response) => {
                if (response.before) {
                    setBeforeAfter('AFTER');
                    setId(response.before.id);
                } else if (response.after) {
                    setBeforeAfter('BEFORE');
                    setId(response.after.id);
                }
                setIsInitialized(true);
                props.setIsLoading?.(false);
            });
    }, [isInitialized, props.entityType, props.name, id, field.value]);

    React.useEffect(() => {
        if (!isInitialized) {
            return;
        }
        if (beforeAfter === 'NOWHERE') {
            field.onChange('');
            return;
        }
        if (beforeAfter === 'ANYWHERE') {
            field.onChange('0');
            return;
        }
        if (!id) {
            return;
        }
        props.setIsLoading?.(true);
        olzApi.call('getEntitiesAroundPosition', {
            entityType: props.entityType,
            entityField: props.name,
            id: id,
            filter: props.filter,
        })
            .then((response) => {
                const beforePosition = response.before?.position ?? -MAX_FLOAT;
                const thisPosition = response.this?.position ?? 0;
                const afterPosition = response.after?.position ?? MAX_FLOAT;
                const newPosition = beforeAfter === 'AFTER'
                    ? (thisPosition + afterPosition) / 2
                    : (thisPosition + beforePosition) / 2;
                field.onChange(newPosition.toString());
                props.setIsLoading?.(false);
            });
    }, [props.entityType, props.name, filterCache, id, beforeAfter]);

    const isEntityChooserDisabled = (!isInitialized || beforeAfter === 'ANYWHERE' || beforeAfter === 'NOWHERE')
        ? true : props?.disabled;

    return (<>
        <label htmlFor={`${props.name}-field`}>
            {props.title}
        </label>
        <div className='olz-position-field' id={`${props.name}-field`}>
            <select
                className={`form-control form-select${errorClassName}`}
                id='before-after-input'
                value={beforeAfter}
                onChange={(e) => {
                    setBeforeAfter(getBeforeAfterValue(e.target.value) ?? 'NOWHERE');
                }}
                disabled={isInitialized ? props?.disabled : true}
            >
                <option value='BEFORE'>vor</option>
                <option value='AFTER'>nach</option>
                <option value='ANYWHERE'>irgendwo</option>
                <option value='NOWHERE'>nirgends</option>
            </select>
            <OlzEntityChooser
                entityType={props.entityType}
                filter={props.filter}
                entityId={id}
                onEntityIdChange={(e) => {
                    setId(e.detail);
                }}
                setIsLoading={props?.setIsLoading}
                disabled={isEntityChooserDisabled}
                nullLabel={isInitialized ? props.nullLabel : 'Lädt...'}
                nothingAvailableLabel='Keine existierenden Einträge: Bitte "irgendwo" auswählen.'
                hasError={Boolean(errorMessage)}
            >
            </OlzEntityChooser>
        </div>
        {errorComponent}
    </>);
};
