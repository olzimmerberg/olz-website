import {olzApi} from '../../../Api/client';

export function olzAccountMenuSwitchUser(userId: number): void {
    olzApi.call(
        'switchUser',
        {userId},
    )
        .then(() => {
            // This could probably be done more smoothly!
            window.location.reload();
        })
        .catch(() => {
            // This could probably be done more smoothly!
            window.location.reload();
        });
}

export function olzAccountMenuLogout(): void {
    localStorage.removeItem('OLZ_AUTO_LOGIN');
    olzApi.call(
        'logout',
        {},
    )
        .then(() => {
            // This could probably be done more smoothly!
            window.location.reload();
        })
        .catch(() => {
            // This could probably be done more smoothly!
            window.location.reload();
        });
}
