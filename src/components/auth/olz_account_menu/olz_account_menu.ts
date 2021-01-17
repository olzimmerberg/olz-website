import {OlzApiEndpoint, callOlzApi} from '../../../api/client';

export function olzAccountMenuLogout(): void {
    callOlzApi(
        OlzApiEndpoint.logout,
        {},
    )
        .then(() => {
            // TODO: This could probably be done more smoothly!
            window.location.reload();
        })
        .catch(() => {
            // TODO: This could probably be done more smoothly!
            window.location.reload();
        });
}
