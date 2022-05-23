<?php

/*
 * phpWebFileManager language file
 *
 * language: chinese (traditional)
 * encoding: big5
 * date: 15/03/2003
 * author: 劉冠麟 <leo@sayya.org>
 */

/* $Platon: phpWebFileManager/lang/zho/global.php,v 1.2 2005/10/01 16:51:27 nepto Exp $ */

define('_FM_FILE_MANAGER', '檔案管理員');

/* This will be directly passed to date() function.
   It needs the &nbsp; to prevent bad column breaking. */
define('_FM_DATE_FORMAT', 'M&\n\b\s\p;d,&\n\b\s\p;Y');  // Mar 24, 2002

define('_FM_REALLY_DELETE', '確定要刪除檔案嗎？');
define('_FM_FROM_DIR', '從目錄');
define('_FM_FROM_ROOT_DIR', '從根目錄');
define('_FM_REALLY_REMOVE', '確定要刪除這個目錄？');
define('_FM_MUST_BE_EMPTY', '這個目錄必須先是空的。');
define('_FM_RENAME_FROM', '將檔案名稱：');
define('_FM_RENAME_TO', '改為：');
define('_FM_ENTER_NAME', '輸入名稱：');
define('_FM_SELECT_LOCAL', '請選擇本機檔案');
define('_FM_SELECT_LOCALS', 'Select a local files'); // TODO
define('_FM_REALLY_EDIT', '確定要編輯檔案');
define('_FM_IN_DIR', '在目錄');
define('_FM_IN_ROOT_DIR', '在根目錄');

define('_FM_FILE', '檔案');
define('_FM_DIR', '所在目錄');
define('_FM_PARENT_DIR', '回上層目錄');
define('_FM_BACK', '回去');
define('_FM_CANCELED', '操作已被取消。');
define('_FM_CANCEL', '取消');

define('_FM_FILE_DELETE_ERR', '刪除這個檔案失敗！');
define('_FM_FILE_DELETE_OK', '檔案刪除了。');
define('_FM_DIR_REMOVE_ERR', '刪除目錄失敗，這個目錄是空的嗎？');
define('_FM_DIR_REMOVE_OK', '目錄刪除成功。');
define('_FM_RENAME_ERR', '檔案更名失敗！');
define('_FM_RENAME_OK', '檔案更名成功。');
define('_FM_DIR_CREATE_ERR', '建立目錄失敗，這個目錄已存在？');
define('_FM_DIR_CREATE_OK', '建立目錄成功。');
define('_FM_FILE_SAVE_OK', '檔案存檔成功。');
define('_FM_FILE_SAVE_ERR', '存檔失敗！可能是磁碟已滿或是權限不足。');
define('_FM_FILE_EDIT_ERR', '無法編輯檔案，這個檔案無法讀取或無法寫入。');
define('_FM_FILE_CREATE_ERR1', '這個檔案已經存在，建立失敗！');
define('_FM_FILE_CREATE_ERR2', '建立失敗！可能是磁碟已滿或是權限不足。');
define('_FM_FILE_CREATE_OK', '檔案建立成功。');
define('_FM_CHMOD_ERR', '更改權限錯誤！');
define('_FM_CHMOD_OK', '權限更改完成。');
define('_FM_FILENAME_CHANGED', '目錄中已經有相同的檔案名稱，檔案名稱已被更改。');
define('_FM_FILE_UPLOAD_OK', '檔案已經上傳成功。');
define('_FM_FILE_UPLOAD_ERR', '上傳時發生錯誤，上傳失敗。');

define('_FM_FILE_RENAME', '更名');
define('_FM_FILE_DELETE', '刪除');
define('_FM_FILE_VIEW', '開啟');
define('_FM_FILE_EDIT', '編輯');
define('_FM_FILE_SAVE', '儲存');
define('_FM_DIR_REMOVE', '搬移');
define('_FM_DIR_RENAME', '更名');
define('_FM_DIR_ENTER', '進入');
define('_FM_DIR_CREATE', '建立子目錄');
define('_FM_FILE_CREATE', '建立新檔');
define('_FM_FILE_UPLOAD', '檔案上傳');
define('_FM_ADM_ENTER', '管理者模式');

?>
