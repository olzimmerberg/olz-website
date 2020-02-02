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

define('_FM_REALLY_DELETE', 'Réellement supprimer ce fichier ?');
define('_FM_FROM_DIR', 'du dossier');
define('_FM_FROM_ROOT_DIR', 'du dossier racine');
define('_FM_REALLY_REMOVE', 'Réellement supprimer ce dossier ?');
define('_FM_MUST_BE_EMPTY', 'Ce dossier doit être vide.');
define('_FM_RENAME_FROM', 'Renommer de');
define('_FM_RENAME_TO', 'à');
define('_FM_ENTER_NAME', 'Entrer le nom');
define('_FM_SELECT_LOCAL', 'Sélectionner un fichier local');
define('_FM_SELECT_LOCALS', 'Select a local files'); // TODO
define('_FM_REALLY_EDIT', 'Réellement éditer ce fichier');
define('_FM_IN_DIR', 'dans le dossier');
define('_FM_IN_ROOT_DIR', 'dans le dossier racine');

define('_FM_FILE', 'Fichier');
define('_FM_DIR', 'Dossier');
define('_FM_PARENT_DIR', 'Dossier parent');
define('_FM_BACK', 'Retour');
define('_FM_CANCELED', 'Operation annulée.');
define('_FM_CANCEL', 'Annulation');

define('_FM_FILE_DELETE_ERR', 'La suppression du fichier a échoué');
define('_FM_FILE_DELETE_OK', 'Fichier supprimé avec succès.');
define('_FM_DIR_REMOVE_ERR', 'La suppression du fichier a échoué Est\'il vide?');
define('_FM_DIR_REMOVE_OK', 'Dossier supprimé avec succès.');
define('_FM_RENAME_ERR', 'le changement de nom a échoué. Le fichier n\'a pas été renommé.');
define('_FM_RENAME_OK', 'Fichier renommé avec succès.');
define('_FM_DIR_CREATE_ERR', 'La création du dossier a échoué. Existe t\'il déjà?');
define('_FM_DIR_CREATE_OK', 'Dossier créé avec succès.');
define('_FM_FILE_SAVE_OK', 'Fichier sauvegardé avec succès.');
define('_FM_FILE_SAVE_ERR', 'la sauvegarde a échoué. Plus de place sur le disque ou droits insuffisants?');
define('_FM_FILE_EDIT_ERR', 'Impossible d\'éditer le fichier. Impossible de lire et d\'écrire sur ce fichier.');
define('_FM_FILE_CREATE_ERR1', 'La création du fichier a échoué. Le fichier existe déjà.');
define('_FM_FILE_CREATE_ERR2', 'La création a échoué. plus de place sur le disque ou droits insuffisants?');
define('_FM_FILE_CREATE_OK', 'Fichier créé avec succès.');
define('_FM_CHMOD_ERR', 'Le changement des droits a échoué.');
define('_FM_CHMOD_OK', 'le changement des droits a réussi.');
define('_FM_FILENAME_CHANGED', 'Le fichier existe déja. nom de fichier changé.');
define('_FM_FILE_UPLOAD_OK', 'Fichier téléchargé avec succès.');
define('_FM_FILE_UPLOAD_ERR', 'Erreur de transmission. Le fichier n\'a pu être téléchargé.');

define('_FM_FILE_RENAME', 'Renommer');
define('_FM_FILE_DELETE', 'Supprimer');
define('_FM_FILE_VIEW', 'Voir');
define('_FM_FILE_EDIT', 'Editer');
define('_FM_FILE_SAVE', 'Sauvegarder');
define('_FM_DIR_REMOVE', 'Supprimer');
define('_FM_DIR_RENAME', 'Renommer');
define('_FM_DIR_ENTER', 'Entrer');
define('_FM_DIR_CREATE', 'Créer un dossier');
define('_FM_FILE_CREATE', 'Créer un fichier');
define('_FM_FILE_UPLOAD', 'télécharger le fichier (upload)');

?>
