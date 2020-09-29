export function olzAccountMenuLogout() {
    $.post('/_/api/index.php/logout', JSON.stringify({}))
        .done(() => {
            // TODO: This could probably be done more smoothly!
            window.location.reload();
        })
        .fail(data => {
            // TODO: This could probably be done more smoothly!
            window.location.reload();
        });
}
