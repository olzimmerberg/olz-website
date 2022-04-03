import * as bootstrap from 'bootstrap';
import {callOlzApi} from '../../../api/client';

$(() => {
    const loginModalElem = document.getElementById('login-modal');
    loginModalElem.addEventListener('shown.bs.modal', () => {
        $('#login-username-input').trigger('focus');
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

    callOlzApi(
        'login',
        {usernameOrEmail, password},
    )
        .then((response) => {
            if (response.status === 'AUTHENTICATED') {
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

export function olzLoginModalCancel(): void {
    bootstrap.Modal.getOrCreateInstance(
        document.getElementById('login-modal'),
    ).hide();
    window.location.href = '#';
}

export function olzLoginModalPasswordReset(): void {
    bootstrap.Modal.getOrCreateInstance(
        document.getElementById('login-modal'),
    ).hide();
    bootstrap.Modal.getOrCreateInstance(
        document.getElementById('password-reset-modal'),
    ).show();
}
