import {callOlzApi} from '../../../api/client';

export function olzAccountMenuLogout(): void {
    callOlzApi(
        'logout',
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
