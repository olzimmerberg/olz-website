<?php

/*
 * phpWebFileManager language file
 *
 * language: spanish
 * encoding: iso-8859-1
 * date: 23/12/2002
 * author: Claudio Cesar Torres Casanelli <ctorres@alumnos.ubiobio.cl>
 */

/* $Platon: phpWebFileManager/lang/esp/global.php,v 1.4 2005/10/01 16:50:39 nepto Exp $ */

define('_FM_FILE_MANAGER', 'Administrador de Archivos');

/* This will be directly passed to date() function.
   It needs the &nbsp; to prevent bad column breaking. */
define('_FM_DATE_FORMAT', 'M&\n\b\s\p;d,&\n\b\s\p;Y');  // Mar 24, 2002

define('_FM_REALLY_DELETE', 'Seguro desea borrar Archivo?');
define('_FM_FROM_DIR', 'desde directorio');
define('_FM_FROM_ROOT_DIR', 'desde directorio raíz');
define('_FM_REALLY_REMOVE', 'Seguro desea borrar Directorio?');
define('_FM_MUST_BE_EMPTY', 'Este Directorio debe estar vacío.');
define('_FM_RENAME_FROM', 'Cambiar nombre de');
define('_FM_RENAME_TO', 'a');
define('_FM_ENTER_NAME', 'Ingrese el nombre');
define('_FM_SELECT_LOCAL', 'Seleccione un Archivo local');
define('_FM_SELECT_LOCALS', 'Select a local files'); // TODO
define('_FM_REALLY_EDIT', 'Seguro desea editar Archivo?');
define('_FM_IN_DIR', 'en directorio');
define('_FM_IN_ROOT_DIR', 'en Directorio raíz');

define('_FM_FILE', 'Archivo');
define('_FM_DIR', 'Directorio');
define('_FM_PARENT_DIR', 'Subir');
define('_FM_BACK', 'Atrás');
define('_FM_CANCELED', 'Operación Cancelada.');
define('_FM_CANCEL', 'Cancelar');

define('_FM_FILE_DELETE_ERR', 'Error al borrar el Archivo.');
define('_FM_FILE_DELETE_OK', 'El Archivo no pudo eliminarse.');
define('_FM_DIR_REMOVE_ERR', 'Error al eliminar Directorio. Está vacio?');
define('_FM_DIR_REMOVE_OK', 'Directorio eliminado exitosamente.');
define('_FM_RENAME_ERR', 'Error al renombrar el Archivo.');
define('_FM_RENAME_OK', 'El Archivo fue renombrado.');
define('_FM_DIR_CREATE_ERR', 'Error al crear Directorio. Posiblemente ya existe.');
define('_FM_DIR_CREATE_OK', 'Directorio creado con éxito.');
define('_FM_FILE_SAVE_OK', 'Archivo guardado exitosamente.');
define('_FM_FILE_SAVE_ERR', 'Error al guardar. Falta espacio en disco o los privilegios no permiten la operación.');
define('_FM_FILE_EDIT_ERR', 'Imposible editar Archivo. El Archivo no se pudo leer ni escribir.');
define('_FM_FILE_CREATE_ERR1', 'Error al Crear Archivo. Ya existe.');
define('_FM_FILE_CREATE_ERR2', 'Error al Crear. Falta espacio en disco o los privilegios no permiten la operación.');
define('_FM_FILE_CREATE_OK', 'Archivo creado con éxito.');
define('_FM_CHMOD_ERR', 'Error al ejecutar CHMOD.');
define('_FM_CHMOD_OK', 'CHMOD se ejecutó con éxito.');
define('_FM_FILENAME_CHANGED', 'El nombre de archivo ya existe. Nombre de archivo cambiado.');
define('_FM_FILE_UPLOAD_OK', 'Archivo subido exitosamente.');
define('_FM_FILE_UPLOAD_ERR', 'Error de Transmisión. El archivo no pudo enviarse.');

define('_FM_FILE_RENAME', 'Cambiar Nombre');
define('_FM_FILE_DELETE', 'Eliminar');
define('_FM_FILE_VIEW', 'Ver');
define('_FM_FILE_EDIT', 'Editar');
define('_FM_FILE_SAVE', 'Guardar');
define('_FM_DIR_REMOVE', 'Borrar');
define('_FM_DIR_RENAME', 'Cambiar Nombre');
define('_FM_DIR_ENTER', 'Entrar');
define('_FM_DIR_CREATE', 'Crear directorio');
define('_FM_FILE_CREATE', 'Crear archivo');
define('_FM_FILE_UPLOAD', 'Subir archivo');

?>
