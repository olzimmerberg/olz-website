<?php

echo <<<'ZZZZZZZZZZ'
<div class='modal fade' id='login-modal' tabindex='-1' aria-labelledby='login-modal-label' aria-hidden='true'>
    <div class='modal-dialog'>
        <div class='modal-content'>
            <div class='modal-header'>
                <h5 class='modal-title' id='login-modal-label'>Login</h5>
                <button type='button' class='close' data-dismiss='modal' aria-label='Schliessen'>
                    <span aria-hidden='true'>&times;</span>
                </button>
            </div>
            <div class='modal-body'>
                <form onsubmit='olzLoginModalLogin()'>
                    <div class='form-group'>
                        <label for='login-username-input'>Benutzername</label>
                        <input type='text' class='form-control' id='login-username-input' autofocus />
                    </div>
                    <div class='form-group'>
                        <label for='login-password-input'>Passwort</label>
                        <input type='password' class='form-control' id='login-password-input' />
                    </div>
                    <input type='submit' class='hidden' />
                    <div id='login-message' class='alert alert-danger' role='alert'></div>
                </form>
            </div>
            <div class='modal-footer'>
                <button type='button' class='btn btn-secondary' data-dismiss='modal'>Abbrechen</button>
                <button
                    id='login-button'
                    type='button'
                    class='btn btn-primary'
                    onclick='olzLoginModalLogin()'
                >
                    Login
                </button>
            </div>
        </div>
    </div>
</div>
ZZZZZZZZZZ;
