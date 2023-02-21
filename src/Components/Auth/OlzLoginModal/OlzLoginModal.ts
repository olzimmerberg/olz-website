import * as bootstrap from 'bootstrap';
import $ from 'jquery';

import {olzApi} from '../../../../src/Api/client';
import {user} from '../../../Utils/constants';

$(() => {
    const loginModalElem = document.getElementById('login-modal');
    if (!loginModalElem) {
        return;
    }

    const usernameOrEmail = localStorage.getItem('OLZ_AUTO_LOGIN');
    const reauthToken = localStorage.getItem('OLZ_REAUTH_TOKEN');
    if (!user?.username && usernameOrEmail && reauthToken) {
        olzApi.call(
            'reauth',
            {usernameOrEmail, reauthToken},
        )
            .then((response) => {
                if (response.status === 'AUTHENTICATED') {
                    window.location.reload();
                } else {
                    bootstrap.Modal.getOrCreateInstance(loginModalElem).show();
                }
            })
            .catch(() => {
                bootstrap.Modal.getOrCreateInstance(loginModalElem).show();
            });
    }
    const rememberMeElem = document.getElementById('login-remember-me-input');
    if (rememberMeElem) {
        (rememberMeElem as HTMLInputElement).checked = !!usernameOrEmail;
    }
    const usernameElem = document.getElementById('login-username-input');
    if (usernameOrEmail && usernameElem) {
        (usernameElem as HTMLInputElement).value = usernameOrEmail;
    }

    loginModalElem.addEventListener('shown.bs.modal', () => {
        $('#login-username-input').trigger('focus');
        window.location.href = '#login-dialog';
    });
    loginModalElem.addEventListener('hidden.bs.modal', () => {
        window.location.href = '#';
    });
    const openLoginDialogIfHash = () => {
        if (window.location.hash === '#login-dialog') {
            bootstrap.Modal.getOrCreateInstance(loginModalElem).show();
        }
    };
    window.addEventListener('hashchange', openLoginDialogIfHash);
    openLoginDialogIfHash();
});

export function olzLoginModalLogin(): void {
    const usernameOrEmail = String($('#login-username-input').val());
    const password = String($('#login-password-input').val());
    const rememberMeElem = document.getElementById('login-remember-me-input');
    const rememberMe = (rememberMeElem as HTMLInputElement)?.checked ?? false;

    olzApi.call(
        'login',
        {usernameOrEmail, password, rememberMe},
    )
        .then((response) => {
            if (response.status === 'AUTHENTICATED') {
                if (response.reauthToken) {
                    localStorage.setItem('OLZ_AUTO_LOGIN', usernameOrEmail);
                    localStorage.setItem('OLZ_REAUTH_TOKEN', response.reauthToken);
                } else {
                    localStorage.removeItem('OLZ_AUTO_LOGIN');
                    localStorage.removeItem('OLZ_REAUTH_TOKEN');
                }
                window.location.href = '#';
                // TODO: This could probably be done more smoothly!
                window.location.reload();
            } else {
                $('#login-message').text(response.status);
            }
        })
        .catch((err) => {
            $('#login-message').text(err.message);
        });
}

export function olzLoginModalShow(): void {
    const modal = document.getElementById('login-modal');
    if (modal) {
        bootstrap.Modal.getOrCreateInstance(modal).show();
    }
    window.location.href = '#login-dialog';
}

export function olzLoginModalCancel(): void {
    const modal = document.getElementById('login-modal');
    if (modal) {
        bootstrap.Modal.getOrCreateInstance(modal).hide();
    }
    window.location.href = '#';
}

export function olzLoginModalPasswordReset(): void {
    const modal = document.getElementById('login-modal');
    if (modal) {
        bootstrap.Modal.getOrCreateInstance(modal).hide();
    }
    const resetModal = document.getElementById('password-reset-modal');
    if (resetModal) {
        bootstrap.Modal.getOrCreateInstance(resetModal).show();
    }
}
