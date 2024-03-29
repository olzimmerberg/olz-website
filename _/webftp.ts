// TODO: Remove this

import $ from 'jquery';

import {olzApi} from './../src/Api/client';

export function generateWebdavAccessToken(): boolean {
    olzApi.call(
        'getWebdavAccessToken',
        {},
    )
        .then((response) => {
            if (response.status === 'OK') {
                window.location.reload();
            } else {
                $('#generate-webdav-token-error-message').text('Beim Erstellen des Zugangs ist ein Problem aufgetreten.');
            }
        })
        .catch(() => {
            $('#generate-webdav-token-error-message').text('Der Zugang konnte nicht erstellt werden.');
        });
    return false;
}

export function revokeWebdavAccessToken(): boolean {
    olzApi.call(
        'revokeWebdavAccessToken',
        {},
    )
        .then((response) => {
            if (response.status === 'OK') {
                window.location.reload();
            } else {
                $('#revoke-webdav-token-error-message').text('Beim Deaktivieren des Zugangs ist ein Problem aufgetreten.');
            }
        })
        .catch(() => {
            $('#revoke-webdav-token-error-message').text('Der Zugang konnte nicht deaktiviert werden.');
        });
    return false;
}
