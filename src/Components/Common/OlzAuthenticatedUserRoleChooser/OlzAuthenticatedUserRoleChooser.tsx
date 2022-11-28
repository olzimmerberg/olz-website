import 'bootstrap';
import React from 'react';
import {olzApi} from '../../../Api/client';
import {OlzAuthenticatedRole, OlzAuthenticatedUser} from '../../../Api/client/generated_olz_api_types';

interface OlzAuthenticatedUserRoleChooserProps {
    userId: number|null;
    roleId: number|null;
    onUserIdChange: (e: CustomEvent<number|null>) => void;
    onRoleIdChange: (e: CustomEvent<number|null>) => void;
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

    const userChoices = authenticatedUser ? (
        <button
            className="dropdown-item"
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
            {authenticatedUser.firstName} {authenticatedUser.lastName}
        </button>
    ) : (
        <button className="dropdown-item" type="button" disabled>Lädt...</button>
    );

    const rolesChoices = authenticatedRoles ? authenticatedRoles.map(role => (
        <button
            className="dropdown-item"
            type="button"
            onClick={() => {
                props.onUserIdChange(new CustomEvent('userIdChange', {
                    detail: null,
                }));
                props.onRoleIdChange(new CustomEvent('roleIdChange', {
                    detail: role.id,
                }));
            }}
            key={`role-${role.id}`}
        >
            {role.name}
        </button>
    )) : (
        <button className="dropdown-item" type="button" disabled>Lädt...</button>
    );

    return (
        <div>
            <button className="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Dropdown button
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
