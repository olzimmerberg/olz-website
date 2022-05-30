<?php

/*
 * phpWebFileManager language file
 *
 * language: english
 * encoding: iso-8859-1
 * date: 16/02/2002
 * author: Ondrej Jombik <nepto@platon.sk>
 */

/* $Platon: phpWebFileManager/lang/eng/global.php,v 1.7 2005/10/01 16:50:36 nepto Exp $ */

define('_FM_FILE_MANAGER', 'File manager');

/* This will be directly passed to date() function.
   It needs the &nbsp; to prevent bad column breaking. */
define('_FM_DATE_FORMAT', 'M&\n\b\s\p;d,&\n\b\s\p;Y');  // Mar 24, 2002

define('_FM_REALLY_DELETE', 'Really delete file');
define('_FM_FROM_DIR', 'from directory');
define('_FM_FROM_ROOT_DIR', 'from root directory');
define('_FM_REALLY_REMOVE', 'Really remove directory');
define('_FM_MUST_BE_EMPTY', 'This directory must be empty.');
define('_FM_RENAME_FROM', 'Rename from');
define('_FM_RENAME_TO', 'to');
define('_FM_ENTER_NAME', 'Enter the name');
define('_FM_SELECT_LOCAL', 'Select a local file');
define('_FM_SELECT_LOCALS', 'Select a local files');
define('_FM_REALLY_EDIT', 'Really edit file');
define('_FM_IN_DIR', 'in directory');
define('_FM_IN_ROOT_DIR', 'in root directory');

define('_FM_FILE', 'File');
define('_FM_DIR', 'Directory');
define('_FM_PARENT_DIR', 'parent directory');
define('_FM_BACK', 'Back');
define('_FM_CANCELED', 'Operation canceled.');
define('_FM_CANCEL', 'Cancel');

define('_FM_FILE_DELETE_ERR', 'File removing failed. Delete was not successful.');
define('_FM_FILE_DELETE_OK', 'File was succesfully removed.');
define('_FM_DIR_REMOVE_ERR', 'Directory removing failed. Is it empty?');
define('_FM_DIR_REMOVE_OK', 'Directory was succesfully removed.');
define('_FM_RENAME_ERR', 'Filename changing failed. File was not renamed.');
define('_FM_RENAME_OK', 'File was succesfully renamed.');
define('_FM_DIR_CREATE_ERR', 'Directory creating failed. Already exists?');
define('_FM_DIR_CREATE_OK', 'Directory was succesfully created.');
define('_FM_FILE_SAVE_OK', 'File was successfully saved.');
define('_FM_FILE_SAVE_ERR', 'Saving failed. No space left on the device or insufficient privileges?');
define('_FM_FILE_EDIT_ERR', 'Cannot edit file. File is not readable and writeable.');
define('_FM_FILE_CREATE_ERR1', 'Creation failed. File already exists.');
define('_FM_FILE_CREATE_ERR2', 'Creation failed. No space left on the device or insufficient privileges?');
define('_FM_FILE_CREATE_OK', 'File was successfully created.');
define('_FM_CHMOD_ERR', 'Change mode failed.');
define('_FM_CHMOD_OK', 'Change mode successful.');
define('_FM_FILENAME_CHANGED', 'Filename already exists. Filename changed.');
define('_FM_FILE_UPLOAD_OK', 'File was successfully uploaded.');
define('_FM_FILE_UPLOAD_ERR', 'Transmission error. File was not successfully uploaded.');

define('_FM_FILE_RENAME', 'Rename');
define('_FM_FILE_DELETE', 'Delete');
define('_FM_FILE_VIEW', 'View');
define('_FM_FILE_EDIT', 'Edit');
define('_FM_FILE_SAVE', 'Save');
define('_FM_DIR_REMOVE', 'Remove');
define('_FM_DIR_RENAME', 'Rename');
define('_FM_DIR_ENTER', 'Enter');
define('_FM_DIR_CREATE', 'Create directory');
define('_FM_FILE_CREATE', 'Create file');
define('_FM_FILE_UPLOAD', 'File upload');

?>
