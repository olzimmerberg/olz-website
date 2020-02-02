<?php

/*
 * phpWebFileManager language file
 *
 * language: Portuguese
 * encoding: iso-8859-1
 * date: 2004-10-19
 * author: Simao Mata <simao@bliter.com>
 */

/* $Platon: phpWebFileManager/lang/prt/global.php,v 1.2 2005/10/01 16:51:10 nepto Exp $ */

define('_FM_FILE_MANAGER', 'File manager');

/* This will be directly passed to date() function.
   It needs the &nbsp; to prevent bad column breaking. */
define('_FM_DATE_FORMAT', 'M&\n\b\s\p;d,&\n\b\s\p;Y');  // Mar 24, 2002

define('_FM_REALLY_DELETE', 'eliminar ficheiro');
define('_FM_FROM_DIR', 'da directoria');
define('_FM_FROM_ROOT_DIR', 'da directoria principal');
define('_FM_REALLY_REMOVE', 'eliminar directorio');
define('_FM_MUST_BE_EMPTY', 'Este directorio tem que estar vazio.');
define('_FM_RENAME_FROM', 'Mudar o nome de');
define('_FM_RENAME_TO', 'para');
define('_FM_ENTER_NAME', 'insira o nome');
define('_FM_SELECT_LOCAL', 'Selecione um ficheiro local');
define('_FM_SELECT_LOCALS', 'Select a local files'); // TODO
define('_FM_REALLY_EDIT', 'editar ficheiro');
define('_FM_IN_DIR', 'na directoria');
define('_FM_IN_ROOT_DIR', 'na directoria principal');

define('_FM_FILE', 'Ficheiro');
define('_FM_DIR', 'Directorio');
define('_FM_PARENT_DIR', 'directoria acima');
define('_FM_BACK', 'voltar atrás');
define('_FM_CANCELED', 'Operação cancelada.');
define('_FM_CANCEL', 'Cancelar');

define('_FM_FILE_DELETE_ERR', 'Remoção do ficheiro falhou. Eliminação não foi bem sucedida.');
define('_FM_FILE_DELETE_OK', 'A eliminação do ficheiro foi bem sucedida.');
define('_FM_DIR_REMOVE_ERR', 'Erro ao eliminar directoria. Está vazia?');
define('_FM_DIR_REMOVE_OK', 'Eliminação da directoria bem sucedida.');
define('_FM_RENAME_ERR', 'Erro ao mudar nome do ficheiro. Não foi mudado o nome do ficheiro.');
define('_FM_RENAME_OK', 'Mudança de nome bem sucedida.');
define('_FM_DIR_CREATE_ERR', 'Erro ao criar directoria. A directoria ja existe?');
define('_FM_DIR_CREATE_OK', 'Criação da directoria bem sucedida.');
define('_FM_FILE_SAVE_OK', 'O ficheiro foi guardado.');
define('_FM_FILE_SAVE_ERR', 'Erro ao guardar ficheiro. Não existe espaço no disco ou não tem previlegios suficientes?');
define('_FM_FILE_EDIT_ERR', 'Erro ao editar ficheiro. Este ficheiro não pode ser lido ou editado.');
define('_FM_FILE_CREATE_ERR1', 'Erro ao criar ficheiro. O ficheiro já existe.');
define('_FM_FILE_CREATE_ERR2', 'Erro ao criar ficheiro. Não existe espaço no disco ou não tem previlegios suficientes?');
define('_FM_FILE_CREATE_OK', 'Criação do ficheiro foi bem sucedida.');
define('_FM_CHMOD_ERR', 'Erro ao mudar \"modo\" do ficheiro.');
define('_FM_CHMOD_OK', 'Mudança de \"modo\" bem sucedida.');
define('_FM_FILENAME_CHANGED', 'O ficheiro ja existe. O nome do ficheiro foi mudado.');
define('_FM_FILE_UPLOAD_OK', 'O ficheiro foi enviado correctamente.');
define('_FM_FILE_UPLOAD_ERR', 'Erro de transmiçao. O ficheiro não foi enviado correctamente.');

define('_FM_FILE_RENAME', 'Mudar Nome');
define('_FM_FILE_DELETE', 'Eliminar');
define('_FM_FILE_VIEW', 'Ver');
define('_FM_FILE_EDIT', 'Editar');
define('_FM_FILE_SAVE', 'Guardar');
define('_FM_DIR_REMOVE', 'Remover');
define('_FM_DIR_RENAME', 'Mudar Nome');
define('_FM_DIR_ENTER', 'OK');
define('_FM_DIR_CREATE', 'Criar directoria');
define('_FM_FILE_CREATE', 'Criar ficheiro');
define('_FM_FILE_UPLOAD', 'Enviar ficheiro');

?>
