<?php

/*
 * phpWebFileManager language file
 *
 * language: italian
 * encoding: iso-8859-1
 * date: 02/10/2002
 * author: Lamberto Isola <scianzi@yoda2000.net>
 */

/* $Platon: phpWebFileManager/lang/ita/global.php,v 1.4 2005/10/01 16:50:56 nepto Exp $ */

define('_FM_FILE_MANAGER', 'Gestore File');

/* This will be directly passed to date() function.
   It needs the &nbsp; to prevent bad column breaking. */
define('_FM_DATE_FORMAT', 'M&\n\b\s\p;d,&\n\b\s\p;Y');  // Mar 24, 2002

define('_FM_REALLY_DELETE', 'Vuoi davvero cancellare il file');
define('_FM_FROM_DIR', 'dalla cartella');
define('_FM_FROM_ROOT_DIR', 'dalla cartella radice');
define('_FM_REALLY_REMOVE', 'Vuoi davvero cancellare la cartella');
define('_FM_MUST_BE_EMPTY', 'Questa cartella deve essere vuota.');
define('_FM_RENAME_FROM', 'Rinomina da');
define('_FM_RENAME_TO', 'a');
define('_FM_ENTER_NAME', 'Inserisci il nome');
define('_FM_SELECT_LOCAL', 'Seleziona un file locale');
define('_FM_SELECT_LOCALS', 'Select a local files'); // TODO
define('_FM_REALLY_EDIT', 'Vuoi veramente modificare il file');
define('_FM_IN_DIR', 'nella cartella');
define('_FM_IN_ROOT_DIR', 'nella cartella radice');

define('_FM_FILE', 'File');
define('_FM_DIR', 'Cartella');
define('_FM_PARENT_DIR', 'Cartella superiore');
define('_FM_BACK', 'Indietro');
define('_FM_CANCELED', 'Operazione annulata');
define('_FM_CANCEL', 'Annulla');

define('_FM_FILE_DELETE_ERR', 'Errore nella rimozione del file. Operazione
non correttamente conclusa');
define('_FM_FILE_DELETE_OK', 'File cancellato correttamente');
define('_FM_DIR_REMOVE_ERR', 'Errore nella rimozione della cartella. E\' vuota?');
define('_FM_DIR_REMOVE_OK', 'Cartella rimossa correttamente');
define('_FM_RENAME_ERR', 'Errore nel rinominare il file. Il file non è stato rinominato.');
define('_FM_RENAME_OK', 'File rinominato correttamente.');
define('_FM_DIR_CREATE_ERR', 'Impossibile creare la cartella. Magari esiste già?');
define('_FM_DIR_CREATE_OK', 'Cartella creata corretamenete .');
define('_FM_FILE_SAVE_OK', 'File salvato con successo');
define('_FM_FILE_SAVE_ERR', 'Impossibile salvare il file. Non c\'è spazio sufficiente sul disco o non hai i privilegi per farlo?');
define('_FM_FILE_EDIT_ERR', 'Impossibile modificare il file. Non hai i permessi di lettura e scrittura');
define('_FM_FILE_CREATE_ERR1', 'Creazione fallita. Il file è già presente.');
define('_FM_FILE_CREATE_ERR2', 'Creazione fallita. Non c\'è spazio sufficiente sul disco o non hai i privilegi per farlo?');
define('_FM_FILE_CREATE_OK', 'File creato con successo.');
define('_FM_CHMOD_ERR', 'Cambio di privilegi fallito.');
define('_FM_CHMOD_OK', 'cambio di previlegio avvenuto con successo.');
define('_FM_FILENAME_CHANGED', 'Il nome del file esiste già. Nome del file cambiato.');
define('_FM_FILE_UPLOAD_OK', 'Upload del file avvenuto con successo.');
define('_FM_FILE_UPLOAD_ERR', 'Errore nella trasimissione dei dati. Il file non è stato uplodato correttamente.');

define('_FM_FILE_RENAME', 'Rinomina');
define('_FM_FILE_DELETE', 'Cancella');
define('_FM_FILE_VIEW', 'Vedi');
define('_FM_FILE_EDIT', 'Modifica');
define('_FM_FILE_SAVE', 'Salva');
define('_FM_DIR_REMOVE', 'Rimuovi');
define('_FM_DIR_RENAME', 'Rinomina');
define('_FM_DIR_ENTER', 'Inserisci');
define('_FM_DIR_CREATE', 'Crea cartella');
define('_FM_FILE_CREATE', 'Crea file');
define('_FM_FILE_UPLOAD', 'Inserisci File');

?>
