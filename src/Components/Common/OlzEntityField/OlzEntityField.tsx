import 'bootstrap';
import React from 'react';
import {useController, Control, FieldValues, FieldErrors, UseControllerProps, Path} from 'react-hook-form';
import {olzApi} from '../../../Api/client';
import {OlzSearchableEntityTypes, OlzEntityResult} from '../../../Api/client/generated_olz_api_types';

import './OlzEntityField.scss';

interface OlzEntityFieldProps<
    Values extends FieldValues,
    Name extends Path<Values>,
> {
    title?: string;
    entityType: OlzSearchableEntityTypes;
    name: Name;
    rules?: UseControllerProps<Values, Name>['rules'];
    errors?: FieldErrors<Values>;
    control: Control<Values, Name>;
    setIsLoading: (isLoading: boolean) => void;
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

    const [searchString, setSearchString] = React.useState<string>('');
    const [entityResults, setEntityResults] = React.useState<OlzEntityResult[]|null>(null);
    const [currentEntityTitle, setCurrentEntityTitle] = React.useState<string|null>(null);

    const nullLabel = props.nullLabel ?? 'Bitte wählen';
    const buttonLabel = currentEntityTitle ?? nullLabel;

    React.useEffect(() => {
        setCurrentEntityTitle(null);
    }, [field.value]);

    React.useEffect(() => {
        if (field.value && !currentEntityTitle) {
            setCurrentEntityTitle('Lädt...');
            olzApi.call('searchEntities', {
                entityType: props.entityType,
                query: null,
                id: field.value,
            })
                .then((response) => {
                    const entityResult = response.result?.[0];
                    if (entityResult?.id === field.value) {
                        setCurrentEntityTitle(entityResult.title);
                    }
                });
        }
    }, [props.entityType, field.value, currentEntityTitle]);

    React.useEffect(() => {
        setEntityResults(null);
        olzApi.call('searchEntities', {
            entityType: props.entityType,
            query: searchString,
            id: null,
        })
            .then((response) => {
                setEntityResults(response.result);
            });
    }, [props.entityType, searchString]);

    const searchResults = entityResults === null ? (
        <button className="dropdown-item" type="button" disabled>Lädt...</button>
    )
        : entityResults.length === 0 ? (
            <button className="dropdown-item" type="button" disabled>
                (Keine Resultate)
            </button>
        ) : entityResults.map((entity, index) => (
            <button
                className="dropdown-item entity-choice"
                id={`entity-index-${index}`}
                type="button"
                onClick={() => {
                    field.onChange(entity?.id ?? null);
                }}
                key={`entity-${entity.id}`}
            >
                {entity ? entity.title : '(?)'}
            </button>
        ));

    return (<>
        <label htmlFor={`${props.name}-field`}>
            {props.title}
        </label>
        <div className='olz-entity-field' id={`${props.name}-field`}>
            <button className="form-select" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                {buttonLabel}
            </button>
            <div className="dropdown-menu" aria-labelledby="dropdownMenuButton">
                <div className="entity-search-container">
                    <input
                        type="text"
                        value={searchString}
                        onChange={(e) => setSearchString(e.target.value)}
                        className="form-control"
                        id="entity-search-input"
                        placeholder='Suche...'
                    />
                </div>
                <div className="dropdown-divider"></div>
                <button
                    className="dropdown-item entity-choice"
                    id={'entity-none'}
                    type="button"
                    onClick={() => {
                        field.onChange(null);
                    }}
                    key={'entity-none'}
                >
                    (Auswahl zurücksetzen)
                </button>
                <div className="dropdown-divider"></div>
                {searchResults}
            </div>
        </div>
    </>);
};
