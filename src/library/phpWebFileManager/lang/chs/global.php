<?php

/*
 * phpWebFileManager language file
 *
 * language: simplyfied chinese
 * encoding: gb2312
 * date: 16/11/2003
 * author: Zhidi Shang <reo@g-d-a.net>
 */

/* $Platon: phpWebFileManager/lang/chs/global.php,v 1.2 2005/10/01 16:50:35 nepto Exp $ */

define('_FM_FILE_MANAGER', '文件管理器');

/* This will be directly passed to date() function.
   It needs the &nbsp; to prevent bad column breaking. */
define('_FM_DATE_FORMAT', 'M&\n\b\s\p;d,&\n\b\s\p;Y');  // Mar 24, 2002

define('_FM_REALLY_DELETE', '确定要删除文件');
define('_FM_FROM_DIR', '从此文件夹：');
define('_FM_FROM_ROOT_DIR', '从底层目录');
define('_FM_REALLY_REMOVE', '确定要删除文件夹');
define('_FM_MUST_BE_EMPTY', '此文件夹不能含有任何文件');
define('_FM_RENAME_FROM', '原名是');
define('_FM_RENAME_TO', '更改为');
define('_FM_ENTER_NAME', '请填入一个名字');
define('_FM_SELECT_LOCAL', '请选择一个本地文件');
define('_FM_SELECT_LOCALS', 'Select a local files'); // TODO
define('_FM_REALLY_EDIT', '确定要编辑文件');
define('_FM_IN_DIR', '在此文件夹');
define('_FM_IN_ROOT_DIR', '在底层目录下');

define('_FM_FILE', '文件');
define('_FM_DIR', '文件夹');
define('_FM_PARENT_DIR', '回上一级文件夹');
define('_FM_BACK', '返回');
define('_FM_CANCELED', '命令被取消');
define('_FM_CANCEL', '取消');

define('_FM_FILE_DELETE_ERR', '文件删除失败，未作任何改动');
define('_FM_FILE_DELETE_OK', '文件已被删除');
define('_FM_DIR_REMOVE_ERR', '文件夹删除失败，你确定它是空的？');
define('_FM_DIR_REMOVE_OK', '文件夹已被删除');
define('_FM_RENAME_ERR', '文件更名失败，未作任何改动');
define('_FM_RENAME_OK', '文件已被删除');
define('_FM_DIR_CREATE_ERR', '文件夹建立失败，同名文件夹存在？');
define('_FM_DIR_CREATE_OK', '文件夹建立成功');
define('_FM_FILE_SAVE_OK', '文件已被存储');
define('_FM_FILE_SAVE_ERR', '文件存储失败，没有权限或是没有空间？');
define('_FM_FILE_EDIT_ERR', '文件编辑失败，文件不可读写');
define('_FM_FILE_CREATE_ERR1', '文件建立失败，同名文件存在');
define('_FM_FILE_CREATE_ERR2', '文件建立失败，没有权限或是没有空间？');
define('_FM_FILE_CREATE_OK', '文件建立成功');
define('_FM_CHMOD_ERR', '权限更改失败');
define('_FM_CHMOD_OK', '权限更改成功');
define('_FM_FILENAME_CHANGED', '同名文件存在，文件名称被更新');
define('_FM_FILE_UPLOAD_OK', '文件顺利上传');
define('_FM_FILE_UPLOAD_ERR', '文件传输错误，文件未被上传');

define('_FM_FILE_RENAME', '改名');
define('_FM_FILE_DELETE', '删除');
define('_FM_FILE_VIEW', '显示');
define('_FM_FILE_EDIT', '编辑');
define('_FM_FILE_SAVE', '存储');
define('_FM_DIR_REMOVE', '移除');
define('_FM_DIR_RENAME', '改名');
define('_FM_DIR_ENTER', '打开');
define('_FM_DIR_CREATE', '新文件夹');
define('_FM_FILE_CREATE', '新文件');
define('_FM_FILE_UPLOAD', '上传文件');

?>
