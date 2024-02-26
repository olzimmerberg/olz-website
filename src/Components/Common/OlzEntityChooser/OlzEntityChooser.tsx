import 'bootstrap';
import React from 'react';
import {olzApi} from '../../../Api/client';
import {OlzSearchableEntityTypes, OlzEntityResult} from '../../../Api/client/generated_olz_api_types';

import './OlzEntityChooser.scss';

interface OlzEntityChooserProps {
    entityType: OlzSearchableEntityTypes;
    entityId: number|null;
    onEntityIdChange: (e: CustomEvent<number|null>) => void;
    nullLabel?: string;
}

export const OlzEntityChooser = (props: OlzEntityChooserProps): React.ReactElement => {
    const [searchString, setSearchString] = React.useState<string>('');
    const [entityResults, setEntityResults] = React.useState<OlzEntityResult[]|null>(null);
    const [currentEntityTitle, setCurrentEntityTitle] = React.useState<string|null>(null);

    const nullLabel = props.nullLabel ?? 'Bitte wählen';
    const buttonLabel = currentEntityTitle ?? nullLabel;

    const searchInput = React.useRef<HTMLInputElement>(null);

    React.useEffect(() => {
        setCurrentEntityTitle(null);
    }, [props.entityId]);

    React.useEffect(() => {
        if (props.entityId && !currentEntityTitle) {
            setCurrentEntityTitle('Lädt...');
            olzApi.call('searchEntities', {
                entityType: props.entityType,
                query: null,
                id: props.entityId,
            })
                .then((response) => {
                    const entityResult = response.result?.[0];
                    if (entityResult?.id === props.entityId) {
                        setCurrentEntityTitle(entityResult.title);
                    }
                });
        }
    }, [props.entityType, props.entityId, currentEntityTitle]);

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
                className="form-select"
                type="button"
                id="dropdownMenuButton"
                data-bs-toggle="dropdown"
                aria-haspopup="true"
                aria-expanded="false"
                onClick={() => {
                    if (searchInput.current) {
                        searchInput.current.focus();
                    }
                }}
            >
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
