<?php

/*
 * phpWebFileManager language file
 *
 * language: Portuguese (Brazil)
 * encoding: iso-8859-1
 * date: 09/03/2004
 * author: Leandro Tappis Pozenato <ltappis@hotmail.com>
 */

/* $Platon: phpWebFileManager/lang/por/global.php,v 1.4 2005/10/01 16:51:06 nepto Exp $ */

define('_FM_FILE_MANAGER', 'File manager');

/* This will be directly passed to date() function.
   It needs the &nbsp; to prevent bad column breaking. */
define('_FM_DATE_FORMAT', 'M&\n\b\s\p;d,&\n\b\s\p;Y');  // Mar 24, 2002

define('_FM_REALLY_DELETE', 'Excluir realmente o arquivo');
define('_FM_FROM_DIR', 'Diretório do');
define('_FM_FROM_ROOT_DIR', 'Diretório do root');
define('_FM_REALLY_REMOVE', 'Excluir realmente o diretório');
define('_FM_MUST_BE_EMPTY', 'Este diretório está vazio.');
define('_FM_RENAME_FROM', 'Renomear de');
define('_FM_RENAME_TO', 'para');
define('_FM_ENTER_NAME', 'Entre com o nome');
define('_FM_SELECT_LOCAL', 'Selecionar o local do arquivo');
define('_FM_SELECT_LOCALS', 'Select a local files'); // TODO
define('_FM_REALLY_EDIT', 'Realmente editar o arquivo');
define('_FM_IN_DIR', 'no diretório');
define('_FM_IN_ROOT_DIR', 'no diretório do root');

define('_FM_FILE', 'Arquivo');
define('_FM_DIR', 'Diretório');
define('_FM_PARENT_DIR', 'Diretório anterior');
define('_FM_BACK', 'Voltar');
define('_FM_CANCELED', 'Operação cancelada.');
define('_FM_CANCEL', 'Cancelar');

define('_FM_FILE_DELETE_ERR', 'A remoção do arquivo falhou. A exclusão não foi bem sucedida.');
define('_FM_FILE_DELETE_OK', 'Arquivo removido com sucesso.');
define('_FM_DIR_REMOVE_ERR', 'A remoção do diretório falhou. Ele está vazio?');
define('_FM_DIR_REMOVE_OK', 'Diretório removido com sucesso.');
define('_FM_RENAME_ERR', 'A mudança de nome falhou. O arquivo não foi renomeado.');
define('_FM_RENAME_OK', 'O arquivo foi renomeado com sucesso.');
define('_FM_DIR_CREATE_ERR', 'Falha na criação do diretório. Já existe um diretório com esse nome?');
define('_FM_DIR_CREATE_OK', 'Diretório criado com sucesso.');
define('_FM_FILE_SAVE_OK', 'Arquivo salvo com sucesso.');
define('_FM_FILE_SAVE_ERR', 'Falha ao salvar. Falta espaço em disco ou você não tem privilégios suficientes para executar a operação.');
define('_FM_FILE_EDIT_ERR', 'Não é possivel editar o arquivo. O arquivo não pode ser modificado.');
define('_FM_FILE_CREATE_ERR1', 'Falha na criação. Arquivo já existe.');
define('_FM_FILE_CREATE_ERR2', 'Falha na criação. Falta espaço em disco ou você não tem privilégios suficientes para executar a operação?');
define('_FM_FILE_CREATE_OK', 'Arquivo criado com sucesso.');
define('_FM_CHMOD_ERR', 'Falha na mudança da modalidade.');
define('_FM_CHMOD_OK', 'Mudança de modalidade efetuada com sucesso.');
define('_FM_FILENAME_CHANGED', 'O nome do arquivo já existe. Mude o nome do arquivo.');
define('_FM_FILE_UPLOAD_OK', 'A subida de arquivo ocorreu com sucesso.');
define('_FM_FILE_UPLOAD_ERR', 'Erro na transmissão. Não foi possivel fazer a subida do arquivo.');

define('_FM_FILE_RENAME', 'Renomear');
define('_FM_FILE_DELETE', 'Excluir');
define('_FM_FILE_VIEW', 'Exibir');
define('_FM_FILE_EDIT', 'Editar');
define('_FM_FILE_SAVE', 'Salvar');
define('_FM_DIR_REMOVE', 'Remover');
define('_FM_DIR_RENAME', 'Renomear');
define('_FM_DIR_ENTER', 'Entrar');
define('_FM_DIR_CREATE', 'Criar Diretório');
define('_FM_FILE_CREATE', 'Criar Arquivo');
define('_FM_FILE_UPLOAD', 'Subir Arquivo');

?>
