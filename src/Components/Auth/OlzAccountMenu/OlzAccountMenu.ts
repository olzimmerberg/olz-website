import {callOlzApi} from '../../../../src/Api/client';

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
