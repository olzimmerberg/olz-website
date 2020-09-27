<?php

require_once __DIR__.'/../../../config/doctrine.php';
require_once __DIR__.'/../../../model/index.php';

$user_repo = $entityManager->getRepository(User::class);
$username = $_SESSION['user'];
$user = $user_repo->findOneBy(['username' => $username]);

$esc_id = htmlentities(json_encode($user->getId()));

echo "<div class='modal fade' id='change-password-modal' tabindex='-1' aria-labelledby='change-password-modal-label' aria-hidden='true'>";
echo "  <div class='modal-dialog'>";
echo "    <div class='modal-content'>";
echo "      <form id='change-password-form' onsubmit='return olzChangePasswordModalUpdate({$esc_id}, this)'>";
echo "        <div class='modal-header'>";
echo "          <h5 class='modal-title' id='change-password-modal-label'>Passwort ändern</h5>";
echo "          <button type='button' class='close' data-dismiss='modal' aria-label='Schliessen'>";
echo "            <span aria-hidden='true'>&times;</span>";
echo "          </button>";
echo "        </div>";
echo "        <div class='modal-body'>";
echo "          <div class='form-group'>";
echo "            <label for='change-password-old-input'>Bisheriges Passwort</label>";
echo "            <input type='password' name='old' class='form-control' id='change-password-old-input' />";
echo "          </div>";
echo "          <div class='form-group'>";
echo "            <label for='change-password-new-input'>Neues Passwort</label>";
echo "            <input type='password' name='new' class='form-control' id='change-password-new-input' />";
echo "          </div>";
echo "          <div class='form-group'>";
echo "            <label for='change-password-repeat-input'>Neues Passwort wiederholen</label>";
echo "            <input type='password' name='repeat' class='form-control' id='change-password-repeat-input' />";
echo "          </div>";
echo "          <input type='submit' class='hidden' />";
echo "          <div id='login-message' class='alert alert-danger' role='alert'></div>";
echo "        </div>";
echo "        <div class='modal-footer'>";
echo "          <button type='button' class='btn btn-secondary' data-dismiss='modal'>Abbrechen</button>";
echo "          <button type='submit' class='btn btn-primary'>Passwort ändern</button>";
echo "        </div>";
echo "      </form>";
echo "    </div>";
echo "  </div>";
echo "</div>";
