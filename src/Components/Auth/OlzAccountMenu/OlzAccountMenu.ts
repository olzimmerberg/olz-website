import {olzApi} from '../../../../src/Api/client';

export function olzAccountMenuLogout(): void {
    olzApi.call(
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
