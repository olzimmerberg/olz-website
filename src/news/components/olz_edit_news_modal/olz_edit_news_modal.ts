import {initOlzEditNewsView} from '../OlzEditNewsView/OlzEditNewsView';

$(() => {
    $('#edit-news-modal').on('shown.bs.modal', () => {
        initOlzEditNewsView();
    });
});

export function olzEditNewsModalSubmit(): void {
    console.error('Not implemented');
}
