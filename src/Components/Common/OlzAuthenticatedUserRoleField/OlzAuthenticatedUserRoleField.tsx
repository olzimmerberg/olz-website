import 'bootstrap';
import React from 'react';
import {useController, Control, FieldValues, FieldErrors, UseControllerProps, Path} from 'react-hook-form';
import {dataHref} from '../../../Utils/constants';
import {olzApi} from '../../../Api/client';
import {OlzAuthenticatedRole, OlzAuthenticatedUser} from '../../../Api/client/generated_olz_api_types';

import './OlzAuthenticatedUserRoleField.scss';

interface OlzAuthenticatedUserRoleFieldProps<
    Values extends FieldValues,
    UserName extends Path<Values>,
    RoleName extends Path<Values>,
> {
    title?: string;
    userName: UserName;
    roleName: RoleName;
    userRules?: UseControllerProps<Values, UserName>['rules'];
    roleRules?: UseControllerProps<Values, RoleName>['rules'];
    errors?: FieldErrors<Values>;
    userControl: Control<Values, UserName>;
    roleControl: Control<Values, RoleName>;
    setIsLoading: (isLoading: boolean) => void;
    disabled?: boolean;
    nullLabel?: string;
}

export const OlzAuthenticatedUserRoleField = <
    Values extends FieldValues,
    UserName extends Path<Values>,
    RoleName extends Path<Values>,
>(props: OlzAuthenticatedUserRoleFieldProps<Values, UserName, RoleName>): React.ReactElement => {
    const userErrorMessage = props.errors?.[props.userName]?.message;
    const roleErrorMessage = props.errors?.[props.roleName]?.message;
    const errorClassName = (userErrorMessage || roleErrorMessage) ? ' is-invalid' : '';

    const {field: userField} = useController({
        name: props.userName,
        control: props.userControl,
        rules: props.userRules,
    });

    const {field: roleField} = useController({
        name: props.roleName,
        control: props.roleControl,
        rules: props.roleRules,
    });

    const [authenticatedUser, setAuthenticatedUser] = React.useState<OlzAuthenticatedUser | null>(null);
    const [authenticatedRoles, setAuthenticatedRoles] = React.useState<OlzAuthenticatedRole[] | null>(null);

    React.useEffect(() => {
        olzApi.call('getAuthenticatedUser', {}).then(({user}) => {
            setAuthenticatedUser(user ?? null);
        });
        olzApi.call('getAuthenticatedRoles', {}).then(({roles}) => {
            setAuthenticatedRoles(roles ?? null);
        });
    }, []);

    let selectionClass = 'none-selected';
    let buttonLabel = props.nullLabel ?? 'Bitte wählen';
    const user = (userField.value && authenticatedUser?.id === userField.value)
        ? authenticatedUser : null;
    const role = roleField.value
        ? authenticatedRoles?.find((role_) => role_.id === roleField.value) : null;
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
            type="button"
            disabled={props.disabled}
            id="user-index-0"
            className="dropdown-item user-choice"
            onClick={() => {
                userField.onChange(authenticatedUser.id);
                roleField.onChange(null);
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
        ) : authenticatedRoles.map((role_, index) => (
            <button
                type="button"
                disabled={props.disabled}
                id={`role-index-${index}`}
                className="dropdown-item role-choice"
                onClick={() => {
                    userField.onChange(authenticatedUser?.id ?? null);
                    roleField.onChange(role_.id);
                }}
                key={`role-${role_.id}`}
            >
                <span className='badge'>
                    {(
                        authenticatedUser
                            ? `${authenticatedUser.firstName} ${authenticatedUser.lastName}, ${role_.name}`
                            : `${role_.name}`
                    )}
                    <img
                        src={`${dataHref}assets/icns/author_role_20.svg`}
                        alt=''
                        className='author-icon'
                    />
                </span>
            </button>
        ));

    return (<>
        <label htmlFor={`${props.userName}-${props.roleName}-field`}>
            {props.title}
        </label>
        <div
            id={`${props.userName}-${props.roleName}-field`}
            className={`${selectionClass}${errorClassName}`}
        >
            <button
                type="button"
                disabled={props.disabled}
                id="dropdown-menu-button"
                className="btn btn-outline-secondary dropdown-toggle"
                data-bs-toggle="dropdown"
                aria-haspopup="true"
                aria-expanded="false"
            >
                {buttonLabel}
            </button>
            <div className="dropdown-menu" aria-labelledby="dropdown-menu-button">
                <h6 className="dropdown-header">Benutzer</h6>
                {userChoices}
                <h6 className="dropdown-header">Rolle</h6>
                {rolesChoices}
            </div>
        </div>
        {userErrorMessage && <p className='error'>{String(userErrorMessage)}</p>}
        {roleErrorMessage && <p className='error'>{String(roleErrorMessage)}</p>}
    </>);
};
