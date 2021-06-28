<?php

function olz_edit_news_modal($args = []): string {
    return <<<'ZZZZZZZZZZ'
    <div class='modal fade' id='edit-news-modal' tabindex='-1' aria-labelledby='edit-news-modal-label' aria-hidden='true'>
        <div class='modal-dialog'>
            <div class='modal-content'>
                <form onsubmit='olzEditNewsModalSubmit();return false;'>
                    <div class='modal-header'>
                        <h5 class='modal-title' id='edit-news-modal-label'>News bearbeiten</h5>
                        <button type='button' class='close' data-dismiss='modal' aria-label='Schliessen'>
                            <span aria-hidden='true'>&times;</span>
                        </button>
                    </div>
                    <div class='modal-body'>
                        <div id='edit-news-react-root'>
                            Lädt...
                        </div>
                        <input type='submit' class='hidden' />
                        <div id='edit-news-message' class='alert alert-danger' role='alert'></div>
                    </div>
                    <div class='modal-footer'>
                        <button type='button' class='btn btn-secondary' data-dismiss='modal'>Abbrechen</button>
                        <button type='submit' class='btn btn-primary' id='submit-button'>Speichern</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    ZZZZZZZZZZ;
}
