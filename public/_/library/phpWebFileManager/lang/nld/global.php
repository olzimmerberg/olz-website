<?php

/*
 * phpWebFileManager language file
 *
 * language: dutch
 * encoding: iso-8859-1
 * date: 09/12/2003
 * author: Erik Spaan <erik.spaan@planet.nl>
 */

/* $Platon: phpWebFileManager/lang/nld/global.php,v 1.3 2005/10/01 16:50:58 nepto Exp $ */

define('_FM_FILE_MANAGER', 'Bestands beheer');

/* This will be directly passed to date() function.
   It needs the &nbsp; to prevent bad column breaking. */
define('_FM_DATE_FORMAT', 'd&\n\b\s\p;M&\n\b\s\p;Y');  // 24 Mar 2002

define('_FM_REALLY_DELETE', 'Bestand werkelijk verwijderen');
define('_FM_FROM_DIR', 'van directory');
define('_FM_FROM_ROOT_DIR', 'van root directory');
define('_FM_REALLY_REMOVE', 'Directory werkelijk verwijderen');
define('_FM_MUST_BE_EMPTY', 'Deze directory moet leeg zijn.');
define('_FM_RENAME_FROM', 'Hernoemen van');
define('_FM_RENAME_TO', 'naar');
define('_FM_ENTER_NAME', 'Voer de naam in');
define('_FM_SELECT_LOCAL', 'Selecteer lokaal bestand');
define('_FM_SELECT_LOCALS', 'Select a local files'); // TODO
define('_FM_REALLY_EDIT', 'Bestand werkelijk bewerken');
define('_FM_IN_DIR', 'in directory');
define('_FM_IN_ROOT_DIR', 'in root directory');

define('_FM_FILE', 'Bestand');
define('_FM_DIR', 'Directory');
define('_FM_PARENT_DIR', 'parent directory');
define('_FM_BACK', 'Terug');
define('_FM_CANCELED', 'Actie geannuleerd.');
define('_FM_CANCEL', 'Annuleren');

define('_FM_FILE_DELETE_ERR', 'Bestand verwijderen is mislukt. Het bestand is niet verwijderd.');
define('_FM_FILE_DELETE_OK', 'Bestand is succesvol verwijderd.');
define('_FM_DIR_REMOVE_ERR', 'Directory verwijderen is mislukt. Is de directory wel leeg?');
define('_FM_DIR_REMOVE_OK', 'Directory is succesvol verwijderd.');
define('_FM_RENAME_ERR', 'Bestandsnaam wijzigen is niet gelukt. Bestandsnaam is niet gewijzigd.');
define('_FM_RENAME_OK', 'Bestandsnaam is succesvol gewijzigd.');
define('_FM_DIR_CREATE_ERR', 'Directory aanmaken is mislukt. Bestaat deze al?');
define('_FM_DIR_CREATE_OK', 'Directory is succesvol aangemaakt.');
define('_FM_FILE_SAVE_OK', 'Bestand is succesvol opgeslagen.');
define('_FM_FILE_SAVE_ERR', 'Opslaan mislukt. Diskruimte vol of geen permissies?');
define('_FM_FILE_EDIT_ERR', 'Kan bestand niet bewerken. Bestand is niet leesbaar en schrijfbaar.');
define('_FM_FILE_CREATE_ERR1', 'Aanmaken mislukt. Bestand bestaat al.');
define('_FM_FILE_CREATE_ERR2', 'Aanmaken mislukt. Diskruimte vol of geen permissies?');
define('_FM_FILE_CREATE_OK', 'Bestand is succesvol aangemaakt.');
define('_FM_CHMOD_ERR', 'Permissies wijzigen mislukt.');
define('_FM_CHMOD_OK', 'Permissies succesvol gewijzigd.');
define('_FM_FILENAME_CHANGED', 'Bestand bestaat al. Bestandsnaam gewijzigd.');
define('_FM_FILE_UPLOAD_OK', 'Bestand is succesvol uploaded.');
define('_FM_FILE_UPLOAD_ERR', 'Transmissie fout. Bestand is niet succesvol uploaded.');

define('_FM_FILE_RENAME', 'Hernoemen');
define('_FM_FILE_DELETE', 'Verwijderen');
define('_FM_FILE_VIEW', 'Bekijken');
define('_FM_FILE_EDIT', 'Bewerken');
define('_FM_FILE_SAVE', 'Opslaan');
define('_FM_DIR_REMOVE', 'Verwijderen');
define('_FM_DIR_RENAME', 'Hernoemen');
define('_FM_DIR_ENTER', 'Openen');
define('_FM_DIR_CREATE', 'Directory aanmaken');
define('_FM_FILE_CREATE', 'Bestand aanmaken');
define('_FM_FILE_UPLOAD', 'Bestand uploaden');

?>
