import 'bootstrap';
import React from 'react';
import {olzApi} from '../../../Api/client';
import {OlzAuthenticatedRole, OlzAuthenticatedUser} from '../../../Api/client/generated_olz_api_types';

import './OlzAuthenticatedUserRoleChooser.scss';

interface OlzAuthenticatedUserRoleChooserProps {
    userId: number|null;
    roleId: number|null;
    onUserIdChange: (e: CustomEvent<number|null>) => void;
    onRoleIdChange: (e: CustomEvent<number|null>) => void;
    nullLabel?: string;
}

export const OlzAuthenticatedUserRoleChooser = (props: OlzAuthenticatedUserRoleChooserProps) => {
    const [authenticatedUser, setAuthenticatedUser] = React.useState<OlzAuthenticatedUser|null>(null);
    const [authenticatedRoles, setAuthenticatedRoles] = React.useState<OlzAuthenticatedRole[]|null>(null);

    React.useEffect(() => {
        olzApi.call('getAuthenticatedUser', {}).then(({user}) => {
            setAuthenticatedUser(user);
        });
        olzApi.call('getAuthenticatedRoles', {}).then(({roles}) => {
            setAuthenticatedRoles(roles);
        });
    }, []);

    let selectionClass = 'none-selected';
    let buttonLabel = props.nullLabel ?? 'Bitte wählen';
    const user = props.userId && authenticatedUser?.id === props.userId ? authenticatedUser : null;
    const role = props.roleId ? authenticatedRoles?.find(role => role.id === props.roleId) : null;
    if (user && role) {
        selectionClass = 'role-selected';
        buttonLabel = `${user.firstName} ${user.lastName}, ${role.name}`;
    } else if (role) {
        selectionClass = 'role-selected';
        buttonLabel = role.name;
    } else if (user) {
        selectionClass = 'user-selected';
        buttonLabel = `${user.firstName} ${user.lastName}`;
    }

    const userChoices = authenticatedUser ? (
        <button
            className="dropdown-item user-choice"
            id="user-index-0"
            type="button"
            onClick={() => {
                props.onUserIdChange(new CustomEvent('userIdChange', {
                    detail: authenticatedUser.id,
                }));
                props.onRoleIdChange(new CustomEvent('roleIdChange', {
                    detail: null,
                }));
            }}
        >
            <span className='badge'>
                {authenticatedUser.firstName} {authenticatedUser.lastName}
            </span>
        </button>
    ) : (
        <button className="dropdown-item" type="button" disabled>Lädt...</button>
    );

    const rolesChoices = authenticatedRoles === null ? (
        <button className="dropdown-item" type="button" disabled>Lädt...</button>
    )
    : authenticatedRoles.length === 0 ? (
        <button className="dropdown-item" type="button" disabled>
            (Keine Rollen im Verein)
        </button>
    ) : authenticatedRoles.map((role, index) => (
        <button
            className="dropdown-item role-choice"
            id={`role-index-${index}`}
            type="button"
            onClick={() => {
                props.onUserIdChange(new CustomEvent('userIdChange', {
                    detail: authenticatedUser?.id ?? null,
                }));
                props.onRoleIdChange(new CustomEvent('roleIdChange', {
                    detail: role.id,
                }));
            }}
            key={`role-${role.id}`}
        >
            <span className='badge'>
                {(
                    authenticatedUser
                    ? `${authenticatedUser.firstName} ${authenticatedUser.lastName}, ${role.name}`
                    : `${role.name}`
                )}
                
            </span>
        </button>
    ));

    return (
        <div className={selectionClass}>
            <button className="btn btn-outline-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                {buttonLabel}
            </button>
            <div className="dropdown-menu" aria-labelledby="dropdownMenuButton">
                <h6 className="dropdown-header">Benutzer</h6>
                {userChoices}
                <h6 className="dropdown-header">Rolle</h6>
                {rolesChoices}
            </div>
        </div>
    );
};
