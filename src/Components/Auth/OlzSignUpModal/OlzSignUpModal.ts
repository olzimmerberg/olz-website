import $ from 'jquery';

$(() => {
    $('#sign-up-modal').on('shown.bs.modal', () => {
        console.log('SIGN UP');
    });
});

// TODO: remove dummy export
export default null;
