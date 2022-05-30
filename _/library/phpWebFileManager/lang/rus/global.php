<?php

/*
 * phpWebFileManager language file
 *
 * language: russian
 * encoding: unknown
 * date: 19/11/2003
 * author: Maxim Kozlov <frenzied@mail.ru>
 */

/* $Platon: phpWebFileManager/lang/rus/global.php,v 1.2 2005/10/01 16:51:13 nepto Exp $ */

define('_FM_FILE_MANAGER', 'Файловый менеджер');

/* This will be directly passed to date() function.
   It needs the &nbsp; to prevent bad column breaking. */
define('_FM_DATE_FORMAT', 'M&\n\b\s\p;d,&\n\b\s\p;Y');  // Mar 24, 2002

define('_FM_REALLY_DELETE', 'Точно удалить файл');
define('_FM_FROM_DIR', 'из директории');
define('_FM_FROM_ROOT_DIR', 'из корневой директории');
define('_FM_REALLY_REMOVE', 'Точно удалить директорию');
define('_FM_MUST_BE_EMPTY', 'Директория должна быть пустой.');
define('_FM_RENAME_FROM', 'Переименовать с ');
define('_FM_RENAME_TO', 'в');
define('_FM_ENTER_NAME', 'Введите имя');
define('_FM_SELECT_LOCAL', 'Выберите файл с диска');
define('_FM_SELECT_LOCALS', 'Select a local files'); // TODO
define('_FM_REALLY_EDIT', 'Точно изменить файл');
define('_FM_IN_DIR', 'в директории');
define('_FM_IN_ROOT_DIR', 'в корневой директории');

define('_FM_FILE', 'Файл');
define('_FM_DIR', 'Директория');
define('_FM_PARENT_DIR', 'корневая директория');
define('_FM_BACK', 'Назад');
define('_FM_CANCELED', 'Операция отменена.');
define('_FM_CANCEL', 'Отмена');

define('_FM_FILE_DELETE_ERR', 'Ошибка при удадении файла. Файл не был удален.');
define('_FM_FILE_DELETE_OK', 'Файл был успешно удален.');
define('_FM_DIR_REMOVE_ERR', 'Ошибка при удалении директории. Она пустая?');
define('_FM_DIR_REMOVE_OK', 'Директория успешно удалена.');
define('_FM_RENAME_ERR', 'Ошибка при переименовании файла. Файл не был переименован.');
define('_FM_RENAME_OK', 'Файл был переименован.');
define('_FM_DIR_CREATE_ERR', 'Ошибка при создании директории. Может она уже существует?');
define('_FM_DIR_CREATE_OK', 'Директория создана.');
define('_FM_FILE_SAVE_OK', 'Файл успешно сохранен.');
define('_FM_FILE_SAVE_ERR', 'Ошибка при сохранении. Нет места на диске или не хватает прав?');
define('_FM_FILE_EDIT_ERR', 'Невозможно изменить файл..');
define('_FM_FILE_CREATE_ERR1', 'Ошибка создания. Файл уже существует.');
define('_FM_FILE_CREATE_ERR2', 'Ошибка создания. Нет места на диске или не хватает прав?');
define('_FM_FILE_CREATE_OK', 'Файл успешно создан.');
define('_FM_CHMOD_ERR', 'Ошибка смены состояния.');
define('_FM_CHMOD_OK', 'Смена состояния состоялась.');
define('_FM_FILENAME_CHANGED', 'Файл уже существует. Имя файла изменено.');
define('_FM_FILE_UPLOAD_OK', 'Файл успешно закачан.');
define('_FM_FILE_UPLOAD_ERR', 'Ошибка передачи. Файл не был закачан.');

define('_FM_FILE_RENAME', 'Переименовать');
define('_FM_FILE_DELETE', 'Удалить');
define('_FM_FILE_VIEW', 'Смотреть');
define('_FM_FILE_EDIT', 'Изменить');
define('_FM_FILE_SAVE', 'Сохранить');
define('_FM_DIR_REMOVE', 'Удалить');
define('_FM_DIR_RENAME', 'Переименовать');
define('_FM_DIR_ENTER', 'Войти');
define('_FM_DIR_CREATE', 'Создать директорию');
define('_FM_FILE_CREATE', 'Создать файл');
define('_FM_FILE_UPLOAD', 'Закачать файл');

?>
