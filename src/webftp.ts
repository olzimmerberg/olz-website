import {callOlzApi} from './api/client';

export function generateWebdavAccessToken(): boolean {
    callOlzApi(
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
    callOlzApi(
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
