<?php

/*
 * phpWebFileManager language file
 *
 * language: norwegian
 * encoding: iso-8859-4
 * date: 19/07/2002
 * author: Asbjørn Værnes <avaernes@online.no>
 */

/* $Platon: phpWebFileManager/lang/nor/global.php,v 1.5 2005/10/01 16:51:02 nepto Exp $ */

define('_FM_FILE_MANAGER', '???');

/* This will be directly passed to date() function.
   It needs the &nbsp; to prevent bad column breaking. */
define('_FM_DATE_FORMAT', 'd.m.Y');  // Mar 24, 2002

define('_FM_REALLY_DELETE', 'Vil du slette filen');
define('_FM_FROM_DIR', 'fra katalogen');
define('_FM_FROM_ROOT_DIR', 'fra rotkatalogen');
define('_FM_REALLY_REMOVE', 'Vil du fjerne katalogen');
define('_FM_MUST_BE_EMPTY', 'Katalogen må være tom.');
define('_FM_RENAME_FROM', 'Gi nytt navn fra');
define('_FM_RENAME_TO', 'til');
define('_FM_ENTER_NAME', 'Skriv inn nytt navn');
define('_FM_SELECT_LOCAL', 'Velg en lokal fil');
define('_FM_SELECT_LOCALS', 'Select a local files'); // TODO
//define('_FM_REALLY_EDIT', 'Really edit file'); // untranslated
//define('_FM_IN_DIR', 'in directory'); // untranslated
//define('_FM_IN_ROOT_DIR', 'in root directory'); // untranslated

define('_FM_FILE', 'Filen');
define('_FM_DIR', 'Katalog');
define('_FM_PARENT_DIR', 'Ett trinn opp');
define('_FM_BACK', 'Tilbake');
define('_FM_CANCELED', 'Handling avsluttet.');
define('_FM_CANCEL', 'Avslutt');

define('_FM_FILE_DELETE_ERR', 'Filen ble ikke fjernet. Slettingen var mislykket.');
define('_FM_FILE_DELETE_OK', 'Filen ble fjernet.');
define('_FM_DIR_REMOVE_ERR', 'Sletting av katalog var mislykket. Er katalogen tom?');
define('_FM_DIR_REMOVE_OK', 'Katalogen ble slettet.');
define('_FM_RENAME_ERR', 'Endring av filnavn mislyktes.');
define('_FM_RENAME_OK', 'Filen har fått nytt navn');
define('_FM_DIR_CREATE_ERR', 'Opprettelse av katalog mislyktes. Finnes katalogen allerede?');
define('_FM_DIR_CREATE_OK', 'Ny katalog opprettet.');
//define('_FM_FILE_SAVE_OK', 'File was successfully saved.'); // untranslated
//define('_FM_FILE_SAVE_ERR', 'Saving failed. No space left on the device or insufficient privileges?'); // untranslated
//define('_FM_FILE_EDIT_ERR', 'Cannot edit file. File is not readable and writeable.'); // untranslated
define('_FM_FILE_CREATE_ERR1', 'Opprettelse av fil mislyktes. Filen eksisterer allerede.');
define('_FM_FILE_CREATE_ERR2', 'Opprettelse av fil mislyktes. Ikke mer diskplass?');
define('_FM_FILE_CREATE_OK', 'Ny fil opprettet.');
define('_FM_CHMOD_ERR', 'Endring av status mislyktes.');
define('_FM_CHMOD_OK', 'Endret filstatus.');
define('_FM_FILENAME_CHANGED', 'Filnavnet eksisterer allerede. Filnavn endret.');
define('_FM_FILE_UPLOAD_OK', 'Opplasting av fil var vellykket.');
define('_FM_FILE_UPLOAD_ERR', 'Feil ved overføring. Opplasting av fil mislyktes');

define('_FM_FILE_RENAME', 'Gi nytt navn');
define('_FM_FILE_DELETE', 'Slett');
define('_FM_FILE_VIEW', 'Vis/Last  ned');
//define('_FM_FILE_EDIT', 'Edit'); // untranslated
//define('_FM_FILE_SAVE', 'Save'); // untranslated
define('_FM_DIR_REMOVE', 'Fjern');
define('_FM_DIR_RENAME', 'Gi nytt navn');
define('_FM_DIR_ENTER', 'Åpne');
define('_FM_DIR_CREATE', 'Lag katalog');
define('_FM_FILE_CREATE', 'Lag fil');
define('_FM_FILE_UPLOAD', 'Last opp fil');

?>
