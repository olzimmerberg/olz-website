<?php

/*
 * phpWebFileManager language file
 *
 * language: swedish
 * encoding: iso-8859-1
 * date: 02/09/2003
 * author: Möttis sm5uxq <lars@borlange.nu>
 */

/* $Platon: phpWebFileManager/lang/swe/global.php,v 1.3 2005/10/01 16:51:20 nepto Exp $ */

define('_FM_FILE_MANAGER', 'Filhanterare');

/* This will be directly passed to date() function.
   It needs the &nbsp; to prevent bad column breaking. */
define('_FM_DATE_FORMAT', 'Y;m;d');  // 2002 03 24, 

define('_FM_REALLY_DELETE', 'Radera filen');
define('_FM_FROM_DIR', 'fr&aring;n mapp');
define('_FM_FROM_ROOT_DIR', 'fr&aring;n root mapp');
define('_FM_REALLY_REMOVE', 'Radera mapp');
define('_FM_MUST_BE_EMPTY', 'Denna mapp m&aring;ste var tom.');
define('_FM_RENAME_FROM', 'Byt namn fr&aring;n');
define('_FM_RENAME_TO', 'till');
define('_FM_ENTER_NAME', 'Skriv namn');
define('_FM_SELECT_LOCAL', 'Välj lokal fil');
define('_FM_SELECT_LOCALS', 'Select a local files'); // TODO
define('_FM_REALLY_EDIT', 'Editera fil');
define('_FM_IN_DIR', 'i mapp');
define('_FM_IN_ROOT_DIR', 'i root mapp');

define('_FM_FILE', 'Fil');
define('_FM_DIR', 'Mapp');
define('_FM_PARENT_DIR', 'upp&aring;t');
define('_FM_BACK', 'Backa');
define('_FM_CANCELED', 'Operation avbruten.');
define('_FM_CANCEL', 'Avbryt');

define('_FM_FILE_DELETE_ERR', 'Filen raderades inte.');
define('_FM_FILE_DELETE_OK', 'Filen raderad.');
define('_FM_DIR_REMOVE_ERR', 'Mappen togs inte bort. Är den tom?');
define('_FM_DIR_REMOVE_OK', 'Mappen raderad.');
define('_FM_RENAME_ERR', 'Fel vid ändring av filnamn.');
define('_FM_RENAME_OK', 'Filnamnet ändrat.');
define('_FM_DIR_CREATE_ERR', 'Mapp skapades inte. Finns namnet redan?');
define('_FM_DIR_CREATE_OK', 'Mapp skapad.');
define('_FM_FILE_SAVE_OK', 'Filen sparad.');
define('_FM_FILE_SAVE_ERR', 'Fel vid spara. Saknas diskutrymme eller rättigheter?');
define('_FM_FILE_EDIT_ERR', 'Kan ej editera fil. Fil är inte läs och skrivbar.');
define('_FM_FILE_CREATE_ERR1', 'Kan ej skapa. Fil finns redan.');
define('_FM_FILE_CREATE_ERR2', 'Kan ej skapa. Saknas diskutrymme eller rättigheter?');
define('_FM_FILE_CREATE_OK', 'Filen skapad.');
define('_FM_CHMOD_ERR', 'Fel vid byte av mode.');
define('_FM_CHMOD_OK', 'Mode ändrad.');
define('_FM_FILENAME_CHANGED', 'Filnamn ändrat.');
define('_FM_FILE_UPLOAD_OK', 'Fil är nu uppladdad.');
define('_FM_FILE_UPLOAD_ERR', 'Överförings fel. Filen blev inte uppladdad.');

define('_FM_FILE_RENAME', 'Byt namn');
define('_FM_FILE_DELETE', 'Ta bort');
define('_FM_FILE_VIEW', 'Visa');
define('_FM_FILE_EDIT', 'Editera');
define('_FM_FILE_SAVE', 'Spara');
define('_FM_DIR_REMOVE', 'Ta bort');
define('_FM_DIR_RENAME', 'Byt namn');
define('_FM_DIR_ENTER', 'Enter');
define('_FM_DIR_CREATE', 'Skapa mapp');
define('_FM_FILE_CREATE', 'Skapa fil');
define('_FM_FILE_UPLOAD', 'Ladda upp fil');

?>
