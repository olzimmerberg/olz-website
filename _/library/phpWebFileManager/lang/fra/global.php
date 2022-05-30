<?php

/*
 * phpWebFileManager language file
 *
 * language: french
 * encoding: iso-8859-1
 * date: 18/02/2003
 * author: Olivier <kurius@bzh.net>
 */

/* $Platon: phpWebFileManager/lang/fra/global.php,v 1.2 2005/10/01 16:50:44 nepto Exp $ */

define('_FM_FILE_MANAGER', 'gestionnaire de fichiers');

/* This will be directly passed to date() function.
   It needs the &nbsp; to prevent bad column breaking. */
define('_FM_DATE_FORMAT', 'd&\n\b\s\p;M&\n\b\s\p;Y');  // Mar 24, 2002

define('_FM_REALLY_DELETE', 'R�ellement supprimer ce fichier ?');
define('_FM_FROM_DIR', 'du dossier');
define('_FM_FROM_ROOT_DIR', 'du dossier racine');
define('_FM_REALLY_REMOVE', 'R�ellement supprimer ce dossier ?');
define('_FM_MUST_BE_EMPTY', 'Ce dossier doit �tre vide.');
define('_FM_RENAME_FROM', 'Renommer de');
define('_FM_RENAME_TO', '�');
define('_FM_ENTER_NAME', 'Entrer le nom');
define('_FM_SELECT_LOCAL', 'S�lectionner un fichier local');
define('_FM_SELECT_LOCALS', 'Select a local files'); // TODO
define('_FM_REALLY_EDIT', 'R�ellement �diter ce fichier');
define('_FM_IN_DIR', 'dans le dossier');
define('_FM_IN_ROOT_DIR', 'dans le dossier racine');

define('_FM_FILE', 'Fichier');
define('_FM_DIR', 'Dossier');
define('_FM_PARENT_DIR', 'Dossier parent');
define('_FM_BACK', 'Retour');
define('_FM_CANCELED', 'Operation annul�e.');
define('_FM_CANCEL', 'Annulation');

define('_FM_FILE_DELETE_ERR', 'La suppression du fichier a �chou�');
define('_FM_FILE_DELETE_OK', 'Fichier supprim� avec succ�s.');
define('_FM_DIR_REMOVE_ERR', 'La suppression du fichier a �chou� Est\'il vide?');
define('_FM_DIR_REMOVE_OK', 'Dossier supprim� avec succ�s.');
define('_FM_RENAME_ERR', 'le changement de nom a �chou�. Le fichier n\'a pas �t� renomm�.');
define('_FM_RENAME_OK', 'Fichier renomm� avec succ�s.');
define('_FM_DIR_CREATE_ERR', 'La cr�ation du dossier a �chou�. Existe t\'il d�j�?');
define('_FM_DIR_CREATE_OK', 'Dossier cr�� avec succ�s.');
define('_FM_FILE_SAVE_OK', 'Fichier sauvegard� avec succ�s.');
define('_FM_FILE_SAVE_ERR', 'la sauvegarde a �chou�. Plus de place sur le disque ou droits insuffisants?');
define('_FM_FILE_EDIT_ERR', 'Impossible d\'�diter le fichier. Impossible de lire et d\'�crire sur ce fichier.');
define('_FM_FILE_CREATE_ERR1', 'La cr�ation du fichier a �chou�. Le fichier existe d�j�.');
define('_FM_FILE_CREATE_ERR2', 'La cr�ation a �chou�. plus de place sur le disque ou droits insuffisants?');
define('_FM_FILE_CREATE_OK', 'Fichier cr�� avec succ�s.');
define('_FM_CHMOD_ERR', 'Le changement des droits a �chou�.');
define('_FM_CHMOD_OK', 'le changement des droits a r�ussi.');
define('_FM_FILENAME_CHANGED', 'Le fichier existe d�ja. nom de fichier chang�.');
define('_FM_FILE_UPLOAD_OK', 'Fichier t�l�charg� avec succ�s.');
define('_FM_FILE_UPLOAD_ERR', 'Erreur de transmission. Le fichier n\'a pu �tre t�l�charg�.');

define('_FM_FILE_RENAME', 'Renommer');
define('_FM_FILE_DELETE', 'Supprimer');
define('_FM_FILE_VIEW', 'Voir');
define('_FM_FILE_EDIT', 'Editer');
define('_FM_FILE_SAVE', 'Sauvegarder');
define('_FM_DIR_REMOVE', 'Supprimer');
define('_FM_DIR_RENAME', 'Renommer');
define('_FM_DIR_ENTER', 'Entrer');
define('_FM_DIR_CREATE', 'Cr�er un dossier');
define('_FM_FILE_CREATE', 'Cr�er un fichier');
define('_FM_FILE_UPLOAD', 't�l�charger le fichier (upload)');

?>
