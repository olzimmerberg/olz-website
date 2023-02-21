import * as bootstrap from 'bootstrap';
import $ from 'jquery';

import {callOlzApi} from '../../../../src/Api/client';

$(() => {
    const loginModalElem = document.getElementById('login-modal');
    if (!loginModalElem) {
        return;
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
