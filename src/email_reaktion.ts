import {OlzApiEndpoint, callOlzApi} from './api/client';

export function olzExecuteEmailReaction(token: string): boolean {
    callOlzApi(
        OlzApiEndpoint.executeEmailReaction,
        {token},
    )
        .then((response) => {
            if (response.status === 'OK') {
                $('#email-reaction-success-message').text('Die Änderung war erfolgreich.');
                $('#email-reaction-error-message').text('');
            } else {
                $('#email-reaction-success-message').text('');
                $('#email-reaction-error-message').text('Bei der Änderung ist ein Problem aufgetreten.');
            }
        })
        .catch(() => {
            $('#email-reaction-success-message').text('');
            $('#email-reaction-error-message').text('Die Änderung konnte nicht ausgeführt werden.');
        });
    return false;
}
