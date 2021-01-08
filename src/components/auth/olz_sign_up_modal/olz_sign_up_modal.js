
$(() => {
    $('#login-modal').on('shown.bs.modal', () => {
        $('#login-username-input').trigger('focus');
    });
});

export function olzLoginModalLogin() {
    const username = $('#login-username-input').val();
    const password = $('#login-password-input').val();
    $.post('/_/api/index.php/login', JSON.stringify({username, password}))
        .done(data => {
            const response = JSON.parse(data);
            if (response.status === 'AUTHENTICATED') {
                // TODO: This could probably be done more smoothly!
                window.location.reload();
            } else {
                $('#login-message').text(response.status);
            }
        })
        .fail(data => {
            const response = JSON.parse(data.responseText);
            $('#login-message').text(response);
        });
}
