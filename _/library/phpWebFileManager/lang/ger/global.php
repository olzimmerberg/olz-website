<?php

/*
 * phpWebFileManager language file
 *
 * language: german
 * encoding: iso-8859-1
 * date: 04/03/2002, 10/03/2002 (v1.1)
 * authors:
 *   Ken Kizaki <ken_kizaki@yahoo.co.jp>
 *   Karsten Nordsiek <abstract@linuxeinsteiger.info>
 */

/* v1.1: fixed a bug that cause all functions containing special
   characters not to function anymore. This array definetely
   doesn't like HTML standards ;) */

/* $Platon: phpWebFileManager/lang/ger/global.php,v 1.7 2005/10/01 16:50:48 nepto Exp $ */

define('_FM_FILE_MANAGER', 'Datei manager');

/* This will be directly passed to date() function.
   It needs the &nbsp; to prevent bad column breaking. */
//define('_FM_DATE_FORMAT', 'd.&\n\b\s\pm.&\n\b\s\pY');  // 25. 03. 2002
define('_FM_DATE_FORMAT', 'd.m.Y');  // 25.03.2002

define('_FM_REALLY_DELETE', 'Datei wirklich löschen?');
define('_FM_FROM_DIR', 'aus Verzeichnis');
define('_FM_FROM_ROOT_DIR', 'aus Stammverzeichnis');
define('_FM_REALLY_REMOVE', 'Verzeichnis wirklich löschen');
define('_FM_MUST_BE_EMPTY', 'Verzeichnis muß leer sein.');
define('_FM_RENAME_FROM', 'Umbenennen von');
define('_FM_RENAME_TO', 'nach');
define('_FM_ENTER_NAME', 'Bitte Namen angeben:');
define('_FM_SELECT_LOCAL', 'Eine lokale Datei auswählen:');
define('_FM_SELECT_LOCALS', 'Select a local files'); // TODO
define('_FM_REALLY_EDIT', 'Datei wirklich bearbeiten?');
define('_FM_IN_DIR', 'Im Verzeichnis');
define('_FM_IN_ROOT_DIR', 'Im Root-Verzeichnis');

define('_FM_FILE', 'Datei');
define('_FM_DIR', 'Verzeichnis');
define('_FM_PARENT_DIR', 'Übergeordnetes Verzeichnis');
define('_FM_BACK', 'Zurück');
define('_FM_CANCELED', 'Vorgang abgebrochen.');
define('_FM_CANCEL', 'Abbrechen');

define('_FM_FILE_DELETE_ERR', 'Löschen der Datei schlug fehl.');
define('_FM_FILE_DELETE_OK', 'Datei wurde erfolgreich gelöscht.');
define('_FM_DIR_REMOVE_ERR', 'Löschen des Verzeichnisses schlug fehl. Ist es auch leer?');
define('_FM_DIR_REMOVE_OK', 'Verzeichnis wurde erfolgreich gelöscht.');
define('_FM_RENAME_ERR', 'Umbennen der Datei fehlgeschlagen. Alter Dateiname wurde beibehalten.');
define('_FM_RENAME_OK', 'Datei wurde erfolgreich umbenannt.');
define('_FM_DIR_CREATE_ERR', 'Erstellen des Verzeichnisses schlug fehl. Ist es bereits vorhanden?');
define('_FM_DIR_CREATE_OK', 'Verzeichnis wurde erfolgreich erstellt.');
define('_FM_FILE_SAVE_OK', 'Datei erfolgreich gespeichert.');
define('_FM_FILE_SAVE_ERR', 'Speichern der Datei fehlgeschlagen! Nicht genügender Speicherplatz oder keine Berechtigung?');
define('_FM_FILE_EDIT_ERR', 'Kann Datei nicht bearbeiten - Fehlende Lese- und oder Schreibrechte');
define('_FM_FILE_CREATE_ERR1', 'Erstellen fehlgeschlagen. Datei ist bereits vorhanden.');
define('_FM_FILE_CREATE_ERR2', 'Erstellen fehlgeschlagen. Kein freier Speicherplatz mehr im Zielverzeichnis?');
define('_FM_FILE_CREATE_OK', 'Datei wurde erfolgreich erstellt.');
define('_FM_CHMOD_ERR', 'Ändern der Zugriffsrechte fehlgeschlagen.');
define('_FM_CHMOD_OK', 'Zugriffsrechte erfolgreich geändert.');
define('_FM_FILENAME_CHANGED', 'Datei bereits vorhanden. Dateiname wurde geändert.');
define('_FM_FILE_UPLOAD_OK', 'Datei wurde erfolgreich hochgeladen.');
define('_FM_FILE_UPLOAD_ERR', 'Fehler bei der Datenübertragung. Datei konnte nicht erfolgreich hochgeladen werden.');

define('_FM_FILE_RENAME', 'Umbenennen');
define('_FM_FILE_DELETE', 'Löschen');
define('_FM_FILE_VIEW', 'Anzeigen');
define('_FM_FILE_EDIT', 'Bearbeiten');
define('_FM_FILE_SAVE', 'Speichern');
define('_FM_DIR_REMOVE', 'Löschen');
define('_FM_DIR_RENAME', 'Umbenennen');
define('_FM_DIR_ENTER', 'Anzeigen');
define('_FM_DIR_CREATE', 'Verzeichnis erstellen');
define('_FM_FILE_CREATE', 'Datei erstellen');
define('_FM_FILE_UPLOAD', 'Datei hochladen');


/*   define('_FM_REALLY_DELETE', 'Datei wirklich l&ouml;schen');
   define('_FM_FROM_DIR', 'aus Verzeichnis');
   define('_FM_FROM_ROOT_DIR', 'aus Stammverzeichnis');
   define('_FM_REALLY_REMOVE', 'Verzeichnis wirklich l&ouml;schen');
   define('_FM_MUST_BE_EMPTY', 'Verzeichnis mu&szlig; leer sein.');
   define('_FM_RENAME_FROM', 'Umbenennen von');
   define('_FM_RENAME_TO', 'nach');
   define('_FM_ENTER_NAME', 'Bitte Namen angeben');
   define('_FM_SELECT_LOCAL', 'Eine lokale Datei ausw&auml;hlen');
   define('_FM_DIR', 'Verzeichnis');
   define('_FM_PARENT_DIR', '&Uuml;bergeordnetes Verzeichnis');
   define('_FM_BACK', 'Zur&uuml;ck');
   define('_FM_CANCELED', 'Vorgang abgebrochen.');
   define('_FM_CANCEL', 'Abbrechen');
   define('_FM_FILE_DELETE_ERR', 'L&ouml;schen der Datei schlug fehl.');
   define('_FM_FILE_DELETE_OK', 'Datei wurde erfolgreich gel&ouml;scht.');
   define('_FM_DIR_REMOVE_ERR', 'L&ouml;schen des Verzeichnisses schlug fehl. Ist es auch leer?');
   define('_FM_DIR_REMOVE_OK', 'Verzeichnis wurde erfolgreich gel&ouml;scht.');
   define('_FM_RENAME_ERR', 'Umbennen der Datei fehlgeschlagen. Alter Dateiname wurde beibehalten.');
   define('_FM_RENAME_OK', 'Datei wurde erfolgreich umbenannt.');
   define('_FM_DIR_CREATE_ERR', 'Erstellen des Verzeichnisses schlug fehl. Ist es bereits vorhanden?');
   define('_FM_DIR_CREATE_OK', 'Verzeichnis wurde erfolgreich erstellt.');
   define('_FM_FILE_CREATE_ERR1', 'Erstellen fehlgeschlagen. Datei ist bereits vorhanden.');
   define('_FM_FILE_CREATE_ERR2', 'Erstellen fehlgeschlagen. Kein freier Speicherplatz mehr im Zielverzeichnis?');
   define('_FM_FILE_CREATE_OK', 'Datei wurde erfolgreich erstellt.');
   define('_FM_CHMOD_ERR', '&Auml;ndern der Zugriffsrechte fehlgeschlagen.');
   define('_FM_CHMOD_OK', 'Zugriffsrechte erfolgreich ge&auml;ndert.');
   define('_FM_FILENAME_CHANGED', 'Datei bereits vorhanden. Dateiname wurde ge&auml;ndert.');
   define('_FM_FILE_UPLOAD_OK', 'Datei wurde erfolgreich hochgeladen.');
   define('_FM_FILE_UPLOAD_ERR', 'Fehler bei der Daten&uuml;bertragung. Datei konnte nicht erfolgreich hochgeladen werden.');
   define('_FM_FILE_RENAME', 'Umbenennen');
   define('_FM_FILE_DELETE', 'L&ouml;schen');
   define('_FM_FILE_VIEW', 'Anzeigen');
   define('_FM_DIR_REMOVE', 'L&ouml;schen');
   define('_FM_DIR_RENAME', 'Umbenennen');
   define('_FM_DIR_ENTER', 'Anzeigen');
   define('_FM_DIR_CREATE', 'Verzeichnis erstellen');
   define('_FM_FILE_CREATE', 'Datei erstellen');
   define('_FM_FILE_UPLOAD', 'Datei hochladen');
 */

?>
