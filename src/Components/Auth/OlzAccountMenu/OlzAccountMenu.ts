import {olzApi} from '../../../../src/Api/client';

export function olzAccountMenuLogout(): void {
    localStorage.removeItem('OLZ_AUTO_LOGIN');
    localStorage.removeItem('OLZ_REAUTH_TOKEN');
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
