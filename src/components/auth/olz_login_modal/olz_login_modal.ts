import {callOlzApi} from '../../../api/client';

$(() => {
    $('#login-modal').on('shown.bs.modal', () => {
        $('#login-username-input').trigger('focus');
    });

    if (window.location.hash === '#login-dialog') {
        $('#login-modal').modal('show');
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
    $('#login-modal').modal('hide');
    $('#password-reset-modal').modal('show');
}
