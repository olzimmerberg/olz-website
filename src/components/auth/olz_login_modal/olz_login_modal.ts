import * as bootstrap from 'bootstrap';
import {callOlzApi} from '../../../api/client';

$(() => {
    $('#login-modal').on('shown.bs.modal', () => {
        $('#login-username-input').trigger('focus');
    });

    if (window.location.hash === '#login-dialog') {
        bootstrap.Modal.getInstance(
            document.getElementById('login-modal'),
        ).show();
    }
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

export function olzLoginModalPasswordReset(): void {
    bootstrap.Modal.getInstance(
        document.getElementById('login-modal'),
    ).hide();
    bootstrap.Modal.getInstance(
        document.getElementById('password-reset-modal'),
    ).show();
}
