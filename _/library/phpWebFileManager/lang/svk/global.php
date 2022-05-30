<?php

/*
 * phpWebFileManager language file
 *
 * language: slovak
 * encoding: iso-8859-2
 * date: 16/02/2002
 * author: Ondrej Jombik <nepto@platon.sk>
 */

/* $Platon: phpWebFileManager/lang/svk/global.php,v 1.7 2005/10/01 16:51:16 nepto Exp $ */

define('_FM_FILE_MANAGER', 'Správca súborov');

/* This will be directly passed to date() function.
   It needs the &nbsp; to prevent bad column breaking. */
//define('_FM_DATE_FORMAT', 'd.&\n\b\s\pm.&\n\b\s\pY');  // 25. 03. 2002
define('_FM_DATE_FORMAT', 'd.m.Y');  // 25.03.2002

define('_FM_REALLY_DELETE', 'Naozaj odstráni»');
define('_FM_FROM_DIR', 'z adresára');
define('_FM_FROM_ROOT_DIR', 'z koreòového adresára');
define('_FM_REALLY_REMOVE', 'Naozaj odstráni» adresár');
define('_FM_MUST_BE_EMPTY', 'Tento adresár musí by» prázdny.');
define('_FM_RENAME_FROM', 'Premenova» z');
define('_FM_RENAME_TO', 'na');
define('_FM_ENTER_NAME', 'Zadajte meno');
define('_FM_SELECT_LOCAL', 'Vyberte lokálny súbor');
define('_FM_SELECT_LOCALS', 'Vyberte lokálne súbory');
define('_FM_REALLY_EDIT', 'Naozaj editova» súbor');
define('_FM_IN_DIR', 'v adresári');
define('_FM_IN_ROOT_DIR', 'v koreòovom adresári');

define('_FM_FILE', 'Súbor');
define('_FM_DIR', 'Adresár');
define('_FM_PARENT_DIR', 'rodièovský adresár');
define('_FM_BACK', 'Spä»');
define('_FM_CANCELED', 'Operácia zru¹ená.');
define('_FM_CANCEL', 'Storno');

define('_FM_FILE_DELETE_ERR', 'Odstránenie súboru zlyhalo.');
define('_FM_FILE_DELETE_OK', 'Súbor bol úspe¹ne odstránený.');
define('_FM_DIR_REMOVE_ERR', 'Odstránenie adresáru zlyhalo. Je prázdny?');
define('_FM_DIR_REMOVE_OK', 'Adresár bol úspe¹ne odstránený.');
define('_FM_RENAME_ERR', 'Premenovanie zlyhalo. Súbor alebo adresár nebol premenovaný.');
define('_FM_RENAME_OK', 'Súbor alebo adresár bol úspe¹ne premenovaný.');
define('_FM_DIR_CREATE_ERR', 'Vytvorenie adresáru zlyhalo. Neexistuje u¾ taký adresár?');
define('_FM_DIR_CREATE_OK', 'Adresár bol úspe¹ne vytvorený.');
define('_FM_FILE_SAVE_OK', 'Súbor bol úspe¹ne ulo¾ený.');
define('_FM_FILE_SAVE_ERR', 'Ukladanie zlyhalo. Nedostatok voµného miesta na serveri?'); // + add perm msg
define('_FM_FILE_EDIT_ERR', 'Nemô¾em editova» súbor. Do súboru nie je mo¾né zapisova» a èíta» z neho.');
define('_FM_FILE_CREATE_ERR1', 'Vytvorenie súboru zlyhalo. Súbor s týmto menom u¾ existuje.');
define('_FM_FILE_CREATE_ERR2', 'Vytvorenie súboru zlyhalo. Nedostatok voµného miesta na serveri?'); // + add perm msg
define('_FM_FILE_CREATE_OK', 'Súbor bol úspe¹ne vytvorený.');
define('_FM_CHMOD_ERR', 'Zmenenie módu zlyhalo.');
define('_FM_CHMOD_OK', 'Zmenenie módu úspe¹né.');
define('_FM_FILENAME_CHANGED', 'Súbor u¾ existuje. Meno bolo zmenené.');
define('_FM_FILE_UPLOAD_OK', 'Súbor bol úspe¹ne uploadnutý.');
define('_FM_FILE_UPLOAD_ERR', 'Chyba pri prenose. Súbor nebol uploadnutý.');

define('_FM_FILE_RENAME', 'Premenova»');
define('_FM_FILE_DELETE', 'Zmaza»');
define('_FM_FILE_VIEW', 'Pozrie»');
define('_FM_FILE_EDIT', 'Editova»');
define('_FM_FILE_SAVE', 'Ulo¾i»');
define('_FM_DIR_REMOVE', 'Odstráni»');
define('_FM_DIR_RENAME', 'Premenova»');
define('_FM_DIR_ENTER', 'Vstúpi»');
define('_FM_DIR_CREATE', 'Vytvori» adresár');
define('_FM_FILE_CREATE', 'Vytvori» súbor');
define('_FM_FILE_UPLOAD', 'Uploadnu» súbor');

?>
