import 'bootstrap';
import React from 'react';
import {olzApi} from '../../../Api/client';
import {OlzSearchableEntityType, OlzEntityResult} from '../../../Api/client/generated_olz_api_types';

import './OlzEntityChooser.scss';

interface OlzEntityChooserProps {
    entityType: OlzSearchableEntityType;
    filter?: {[key: string]: string};
    entityId: number | null;
    onEntityIdChange: (e: CustomEvent<number | null>) => void;
    setIsLoading?: (isLoading: boolean) => void;
    disabled?: boolean;
    nullLabel?: string;
    nothingAvailableLabel?: string;
    hasError?: boolean;
}

export const OlzEntityChooser = (props: OlzEntityChooserProps): React.ReactElement => {
    const [searchString, setSearchString] = React.useState<string>('');
    const [confirmedSearch, setConfirmedSearch] = React.useState<string>('');
    const [entityResults, setEntityResults] = React.useState<OlzEntityResult[] | null>(null);
    const [currentEntityTitle, setCurrentEntityTitle] = React.useState<string | null>(null);

    const filterCache = React.useMemo<string>(() => JSON.stringify(props.filter), [props.filter]);

    const nullLabel = props.nullLabel ?? 'Bitte wählen';
    const noResultsLabel =
        (confirmedSearch === '' ? props.nothingAvailableLabel : null) ?? '(Keine Resultate)';
    const buttonLabel = currentEntityTitle ?? nullLabel;
    const errorClassName = props.hasError ? ' is-invalid' : '';

    const searchInput = React.useRef<HTMLInputElement>(null);

    React.useEffect(() => {
        setCurrentEntityTitle(null);
    }, [props.entityId]);

    React.useEffect(() => {
        if (props.entityId && !currentEntityTitle) {
            setCurrentEntityTitle('Lädt...');
            props.setIsLoading?.(true);
            olzApi.call('searchEntities', {
                entityType: props.entityType,
                query: null,
                id: props.entityId,
            })
                .then((response) => {
                    const entityResult = response.result?.[0];
                    if (entityResult?.id === props.entityId) {
                        setCurrentEntityTitle(entityResult.title);
                        props.setIsLoading?.(false);
                    }
                });
        }
    }, [props.entityType, props.entityId, currentEntityTitle]);

    React.useEffect(() => {
        const timeoutId = window.setTimeout(() => setConfirmedSearch(searchString), 300);
        return () => {
            window.clearTimeout(timeoutId);
        };
    }, [searchString]);

    React.useEffect(() => {
        setEntityResults(null);
        props.setIsLoading?.(true);
        olzApi.call('searchEntities', {
            entityType: props.entityType,
            query: confirmedSearch,
            id: null,
            filter: props.filter,
        })
            .then((response) => {
                setEntityResults(response.result);
                props.setIsLoading?.(false);
            });
    }, [props.entityType, filterCache, confirmedSearch]);

    const searchResults = entityResults === null ? (
        <button className="dropdown-item" type="button" disabled>Lädt...</button>
    )
        : entityResults.length === 0 ? (
            <button className="dropdown-item" id="no-results" type="button" disabled>
                {noResultsLabel}
            </button>
        ) : entityResults.map((entity, index) => (
            <button
                className="dropdown-item entity-choice"
                id={`entity-index-${index}`}
                type="button"
                onClick={() => {
                    props.onEntityIdChange(new CustomEvent('entityIdChange', {
                        detail: entity?.id ?? null,
                    }));
                }}
                key={`entity-${entity.id}`}
            >
                {entity ? entity.title : '(?)'}
            </button>
        ));

    return (
        <div className='olz-entity-chooser'>
            <button
                className={`form-select${errorClassName}`}
                type="button"
                id="dropdown-menu-button"
                data-bs-toggle="dropdown"
                aria-haspopup="true"
                aria-expanded="false"
                disabled={props?.disabled}
                onClick={() => {
                    if (searchInput.current) {
                        searchInput.current.focus();
                    }
                }}
            >
                {buttonLabel}
            </button>
            <div className="dropdown-menu" aria-labelledby="dropdown-menu-button">
                <div className="entity-search-container">
                    <input
                        type="text"
                        value={searchString}
                        onChange={(e) => setSearchString(e.target.value)}
                        className="form-control"
                        id="entity-search-input"
                        placeholder='Suche...'
                        ref={searchInput}
                    />
                </div>
                <div className="dropdown-divider"></div>
                <button
                    className="dropdown-item entity-choice"
                    id={'entity-none'}
                    type="button"
                    onClick={() => {
                        props.onEntityIdChange(new CustomEvent('entityIdChange', {
                            detail: null,
                        }));
                    }}
                    key={'entity-none'}
                >
                    (Auswahl zurücksetzen)
                </button>
                <div className="dropdown-divider"></div>
                {searchResults}
            </div>
        </div>
    );
};
