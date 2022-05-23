<?php

/*
 * phpWebFileManager language file
 *
 * language: hungarian
 * encoding: iso-8859-2
 * date: 14/07/2004
 * author: Sandor Domokos <ingyenszoftver@netelek.hu>
 */

/* $Platon: phpWebFileManager/lang/hun/global.php,v 1.2 2005/10/01 16:50:52 nepto Exp $ */

define('_FM_FILE_MANAGER', 'Fájlkezelõ');

/* This will be directly passed to date() function.
   It needs the &nbsp; to prevent bad column breaking. */
define('_FM_DATE_FORMAT', 'M&nbsp;d,&nbsp;Y');  // Mar 24, 2002

define('_FM_REALLY_DELETE', 'Biztos törli a fájlt?');
define('_FM_FROM_DIR', 'könyvtárból');
define('_FM_FROM_ROOT_DIR', 'legfelsõ könyvtárból');
define('_FM_REALLY_REMOVE', 'Biztos törli a könyvtárat?');
define('_FM_MUST_BE_EMPTY', 'A könyvtárnak üresnek kell lenni!');
define('_FM_RENAME_FROM', 'Átnevezés errõl');
define('_FM_RENAME_TO', 'erre');
define('_FM_ENTER_NAME', 'Irja be a nevet');
define('_FM_SELECT_LOCAL', 'Válassza ki a lokális fájlt');
define('_FM_SELECT_LOCALS', 'Select a local files'); // TODO
define('_FM_REALLY_EDIT', 'Biztos szerkeszti a fájlt?');
define('_FM_IN_DIR', 'könyvtárban');
define('_FM_IN_ROOT_DIR', 'legfelsõ könyvtárban');

define('_FM_FILE', 'Fájl');
define('_FM_DIR', 'Könyvtár');
define('_FM_PARENT_DIR', 'egy szinttel feljebb');
define('_FM_BACK', 'Vissza');
define('_FM_CANCELED', 'Mûvelet megszakitva.');
define('_FM_CANCEL', 'Megszakit');

define('_FM_FILE_DELETE_ERR', 'Nem sikerült a fájlt törölni.');
define('_FM_FILE_DELETE_OK', 'Fájl törölve.');
define('_FM_DIR_REMOVE_ERR', 'Nem sikerült a könyvtárat törölni. (üres a törlendõ könyvtár?)');
define('_FM_DIR_REMOVE_OK', 'Könyvtár törölve.');
define('_FM_RENAME_ERR', 'Nem sikerült a fájlt átnevezni.');
define('_FM_RENAME_OK', 'Fájl átnevezve.');
define('_FM_DIR_CREATE_ERR', 'Nem sikerült az új könyvtárat létrehozni. (lehet, hogy már létezik ez a könyvtár?)');
define('_FM_DIR_CREATE_OK', 'Könyvtár létrehozva.');
define('_FM_FILE_SAVE_OK', 'Fájl mentve.');
define('_FM_FILE_SAVE_ERR', 'Nem sikerült menteni. (Nincs elég üres hely a céllemezen, vagy nem megfelelõek a jogosultságok.)');
define('_FM_FILE_EDIT_ERR', 'A fájlt nem lehet szerkeszteni (nem irható/olvasható).');
define('_FM_FILE_CREATE_ERR1', 'Létrehozás sikertelen. A fájl már létezik.');
define('_FM_FILE_CREATE_ERR2', 'Létrehozás sikertelen. (Nincs elég üres hely a céllemezen, vagy nem megfelelõek a jogosultságok.)');
define('_FM_FILE_CREATE_OK', 'Fájl létrehozva.');
define('_FM_CHMOD_ERR', 'Mód váltása sikertelen.');
define('_FM_CHMOD_OK', 'Módváltás végrehajtva.');
define('_FM_FILENAME_CHANGED', 'A fájlnév már létezik. Fájlnév megváltoztatva.');
define('_FM_FILE_UPLOAD_OK', 'Fájl sikeresen feltöltve.');
define('_FM_FILE_UPLOAD_ERR', 'Átviteli hiba. A fájlt nem sikerült feltölteni.');

define('_FM_FILE_RENAME', 'Átnevez');
define('_FM_FILE_DELETE', 'Töröl');
define('_FM_FILE_VIEW', 'Megnéz');
define('_FM_FILE_EDIT', 'Szerkeszt');
define('_FM_FILE_SAVE', 'Ment');
define('_FM_DIR_REMOVE', 'Töröl');
define('_FM_DIR_RENAME', 'Átnevez');
define('_FM_DIR_ENTER', 'Belép');
define('_FM_DIR_CREATE', 'könyvtár létrehozása');
define('_FM_FILE_CREATE', 'Fájl létrehozása');
define('_FM_FILE_UPLOAD', 'Fájl feltöltése');

?>
