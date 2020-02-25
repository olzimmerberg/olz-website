<?php
/*
 * phpWebFileManager - simple file management PHP script
 *
 * index.php - main script file
 * ____________________________________________________________
 *
 * Developed by Ondrej Jombik <nepto@platon.sk>
 * Copyright (c) 2001-2005 Platon Group, http://platon.sk/
 * All rights reserved.
 *
 * See README file for more information about this software.
 * See COPYING file for license information.
 *
 * Download the latest version from
 * http://platon.sk/projects/phpWebFileManager/
 */

/* $Platon: phpWebFileManager/index.php,v 1.53 2005/10/01 17:17:34 nepto Exp $ */

/*
 * phpWebFileManager initialization
 *
 * Config file is read from init.inc.php file.
 */
$fm_init_file = dirname(__FILE__)
	. (strlen(dirname(__FILE__)) > 0 ? '/' : '')
	. 'init.inc.php';

	if (! @file_exists($fm_init_file)) {
		exit;
	}

require_once $fm_init_file;

/*
 * Libraries function inclusion
 */

require_once $PN_PathPrefix . 'functions.inc.php';

if (! function_exists('fm_header')) {
	function fm_header($var_ar) /* {{{ */
	{
		extract($var_ar);
		$message = htmlspecialchars(join("\n", $fm_action_message));
		if (defined('LOADED_AS_MODULE')) {
			OpenTable();
			echo '<font class="pn-title">',_FM_FILE_MANAGER,
			(strlen($fm_dir) > 0 ? ": &nbsp;$fm_dir_he" : ''),
			'</font></td><td align="right">', $message;
			CloseTable();
			OpenTable();
		} else {
			echo '<table class="liste"><tr>';
			echo '<td align="left"><b>', _FM_DIR, ':</b> /', @$fm_dir_he2, '</td>';
			echo '<td align="right">', $message, '</td>';
			echo '</tr></table>', "\n";
			echo '<hr>', "\n";
		}
	} /* }}} */
}

if (! function_exists('fm_footer')) {
	function fm_footer($var_ar) /* {{{ */
	{
		global $fm_cfg;
		extract($var_ar);
		if (defined('LOADED_AS_MODULE')) {
			CloseTable();
			OpenTable();
		} else {
			echo '<hr>',"\n";
			echo '<table class="liste"><tr>';
			echo '<td align="left"><b>', _FM_DIR, ':</b> /', @$fm_dir_he2, '</td>';
			echo '<td align="right">';
		}
		if ($fm_cfg['perm']['dir']['create']) {
			echo '<a href="'.$PHP_SELF_origvars_ue
				. $fm_cfg['cgi'].'dir='.$fm_dir_ue.'&amp;'
				. $fm_cfg['cgi'].'action=confirm_create_directory">'
				. _FM_DIR_CREATE.'</a>';
		} else {
			echo _FM_DIR_CREATE;
		}
		echo ' | ';
		if ($fm_cfg['perm']['file']['create']) {
			echo '<a href="'.$PHP_SELF_origvars_ue
				. $fm_cfg['cgi'].'dir='.$fm_dir_ue.'&amp;'
				. $fm_cfg['cgi'].'action=confirm_create_file">'
				. _FM_FILE_CREATE.'</a>';
		} else {
			echo _FM_FILE_CREATE;
		}
		echo ' | ';
		if ($fm_cfg['perm']['file']['upload']) {
			echo '<a href="'.$PHP_SELF_origvars_ue
				. $fm_cfg['cgi'].'dir='.$fm_dir_ue.'&amp;'
				. $fm_cfg['cgi'].'action=confirm_upload_file">'
				. _FM_FILE_UPLOAD.'</a>';
		} else {
			echo _FM_FILE_UPLOAD;
		}
		if (defined('LOADED_AS_MODULE')) {
			CloseTable();
			include 'footer.php';
		} else {
			echo "\n", '</td></tr></table>', "\n";
		}
	} /* }}} */
}
/*
 * Main phpWebFileManager function
 */

function fm_main() /* {{{ */
{
	global $fm_cfg;

	/* Hidden parameters */
	$fm_origvars            = fm_append_slash($fm_cfg['origvars'], '&');
	$fm_cfg['dir']['root']  = fm_append_slash($fm_cfg['dir']['root']);
	$fm_cfg['url']['root']  = fm_append_slash($fm_cfg['url']['root']);
	$fm_cfg['url']['icons'] = fm_append_slash($fm_cfg['url']['icons']);
	if (strlen($fm_cfg['format']['date']) <= 0) {
		$fm_cfg['format']['date'] = _FM_DATE_FORMAT;
	}

	/*
	 * Getting CGI variables
	 */
	$fm_action       = fm_get_cgi_var($fm_cfg['cgi'].'action',       '');
	$fm_submit       = fm_get_cgi_var($fm_cfg['cgi'].'submit',       '');
	$fm_dir          = fm_get_cgi_var($fm_cfg['cgi'].'dir',          '');
	$fm_filename     = fm_get_cgi_var($fm_cfg['cgi'].'filename',     '');
	$fm_new_filename = fm_get_cgi_var($fm_cfg['cgi'].'new_filename', '');
	$fm_file_data    = fm_get_cgi_var($fm_cfg['cgi'].'file_data',    '');
	$PHP_SELF        = @$_SERVER['PHP_SELF'];
	$PHP_SELF_origvars_ue = htmlspecialchars("$PHP_SELF?$fm_origvars");

	/*
	 * Debugging CGI varaibles
	 */
	if (0) {
		echo '<hr><pre>';
		foreach (array('fm_action', 'fm_submit', 'fm_dir', 'fm_filename', 'fm_new_filename', 'fm_file_data') as $var_name) {
			echo htmlspecialchars(str_pad($var_name, 15).' = '.$$var_name)."\n";
		}
		echo '<hr>';
	}

	/*
	 * Symlink detection (must be before directory modifying)
	 */

	if (! $fm_cfg['perm']['dir']['follow_symlinks']) {
		$fm_test_symlink = $fm_dir;

		while ($fm_test_symlink != '') {
			if ((@file_exists($fm_test_symlink) && @is_link($fm_test_symlink))
					|| (@file_exists($fm_test_symlink) && @is_link("$fm_test_symlink/"))) {
				$fm_dir = $fm_cfg['dir']['start'];
				break;
			}

			$fm_test_symlink = substr($fm_test_symlink,
					0, intval(strrpos($fm_test_symlink, '/')));
		}
	}
	if (! isset($fm_dir) || ! $fm_cfg['perm']['dir']['enter']) {
		$fm_dir = $fm_cfg['dir']['start'];
	}

	/*
	 * Directory modifying. Dirty, but safe.
	 */

	$fm_dir = preg_replace('{//+}','/', "$fm_dir/");
	$fm_dir = preg_replace('{^(|.*/)\.\./([^/\.]|[^\./][^\./]|[^/][^\./]|[^/][^\./]|[^/]{3,})/(.*)$}', '\\1\\3', $fm_dir);
	$fm_dir = preg_replace('{^(|.*/)([^/\.]|[^/\.][^\./]|[^/][^\./]|[^/][^\./]|[^/]{3,})/\.\./(.*)$}', '\\1\\3', $fm_dir);

	while (preg_match('{^(|.*/)\.\./(.*)$}', $fm_dir)) {
		$fm_dir = preg_replace('{^(|.*/)\.\./(.*)$}', '\\1\\2', $fm_dir);
	}
	while (preg_match('{^(|.*/)\./(.*)$}', $fm_dir)) {
		$fm_dir = preg_replace('{^(|.*/)\./(.*)$}', '\\1\\2', $fm_dir);
	}
	while (strlen($fm_dir) > 0 && $fm_dir[0] == '/') {
		$fm_dir = substr($fm_dir, 1, strlen($fm_dir));
	}

	/*
	 * Change to root directory
	 */

	/* Store the starting working directory. */
	$fm_orig_dir = getcwd();

	if (@is_dir($fm_cfg['dir']['root'])) {
		chdir($fm_cfg['dir']['root']);
	}

	$fm_root_dir = getcwd();

	if (@file_exists($fm_dir) && @is_dir($fm_dir)) {
		chdir($fm_dir);

		/* On some *BSD systems with PHP version <= 4.0.6
		   function chdir() is removing last '/' character
		   in $fm_dir variable, so we must improve it. */
		$fm_dir = fm_append_slash($fm_dir);
	} else  {
		$fm_dir = '';
	}

	/*
	 * Prepare URL & HTML encode of some variables.
	 */

	$fm_filename_he = htmlspecialchars($fm_filename);
	$fm_filename_he2 = str_replace(' ', '&nbsp;', $fm_filename_he);
	//$fm_filename_ue = htmlspecialchars(rawurlencode($fm_filename));
	$fm_dir_he = htmlspecialchars($fm_dir);
	$fm_dir_he2 = str_replace(' ', '&nbsp;', $fm_dir_he);
	$fm_dir_ue = htmlspecialchars(rawurlencode($fm_dir));

	/*
	 * ACTIONS
	 */
	$action_message = array();

	/*
	 * save action
	 */
	if ($fm_action == 'save_file' && $fm_cfg['perm']['file']['save']) { /* {{{ */
		if ($fm_submit == _FM_FILE_SAVE) {
			if($fp = @fopen($fm_filename, 'w')) {
				flock($fp, 2);
				fwrite($fp, $fm_file_data, strlen($fm_file_data));
				flock($fp, 3);
				fclose($fp);
				$action_message[] = _FM_FILE_SAVE_OK;
			} else {
				$action_message[] = _FM_FILE_SAVE_ERR;
			}
		} else {
			$action_message[] = _FM_CANCELED;
		}
	} /* }}} */

	/*
	 * delete action
	 */
	if ($fm_action == 'delete_file' && $fm_cfg['perm']['file']['delete']) { /* {{{ */
		if ($fm_submit == _FM_FILE_DELETE) {
			if (@unlink($fm_filename) == false) {
				$action_message[] = _FM_FILE_DELETE_ERR;
			} else {
				$action_message[] = _FM_FILE_DELETE_OK;
			}
		} else {
			$action_message[] = _FM_CANCELED;
		}
	} /* }}} */

	/*
	 * remove direcotry
	 */
	if ($fm_action == 'remove_directory' && $fm_cfg['perm']['dir']['remove']) { /* {{{ */
		if ($fm_submit == _FM_DIR_REMOVE) {
			if (@rmdir($fm_filename) == 0) {
				$action_message[] = _FM_DIR_REMOVE_ERR;
			} else {
				$action_message[] = _FM_DIR_REMOVE_OK;
			}
		} else {
			$action_message[] = _FM_CANCELED;
		}
	} /* }}} */

	/*
	 * rename action
	 */
	if (($fm_action == 'rename_directory' && $fm_cfg['perm']['dir']['rename']) /* {{{ */
			|| ($fm_action == 'rename_file' && $fm_cfg['perm']['file']['rename'])) {
		if ($fm_new_filename != '' &&
				(($fm_submit == _FM_DIR_RENAME && $fm_action == "rename_directory")
				 || ($fm_submit == _FM_FILE_RENAME && $fm_action == 'rename_file')
				)) {
			if (@rename($fm_filename, $fm_new_filename) == false) {
				$action_message[] = _FM_RENAME_ERR;
			} else {
				$action_message[] = _FM_RENAME_OK;
			}
		} else {
			$action_message[] = _FM_CANCELED;
		}
	} /* }}} */

	/*
	 * create directory and create file actions
	 */
	if (($fm_action == 'create_directory' && $fm_cfg['perm']['dir']['create']) /* {{{ */
			|| ($fm_action == 'create_file' && $fm_cfg['perm']['file']['create'])) {
		if ($fm_new_filename != '' &&
				(($fm_submit == _FM_DIR_CREATE && $fm_action == 'create_directory')
				 || ($fm_submit == _FM_FILE_CREATE && $fm_action == 'create_file'))
		   ) {
			if ($fm_action == 'create_directory') {
				if (@mkdir($fm_new_filename, $fm_cfg['mode']['dir']) == false) {
					$action_message[] = _FM_DIR_CREATE_ERR;
				} else {
					$action_message[] = _FM_DIR_CREATE_OK;
				}
			} else {
				if (file_exists($fm_new_filename))
					$action_message[] = _FM_FILE_CREATE_ERR1;
				else {
					if (($fh = @fopen($fm_new_filename, 'w')) == false) {
						$action_message[] = _FM_FILE_CREATE_ERR2;
					} else {
						fclose($fh);
						$action_message[] = _FM_FILE_CREATE_OK;

						if ($fm_cfg['mode']['file'] != 0) {
							if (@chmod($fm_new_filename, $fm_cfg['mode']['file']) == false) {
								$action_message[] = ' '.  _FM_CHMOD_ERR;
							} else {
								$action_message[] = ' '.  _FM_CHMOD_OK;
							}
						}
					}
				}
			}
		} else {
			$action_message[] = _FM_CANCELED;
		}
	} /* }}} */

	/*
	 * file upload action
	 */
	if ($fm_action == 'upload_file' && $fm_cfg['perm']['file']['upload']) { /* {{{ */
		for ($j = 0; $j < $fm_cfg['perm']['file']['upload']; $j++) {
			$fm_userfile      = @$_FILES[$fm_cfg['cgi'].'userfile']['tmp_name'][$j];
			$fm_userfile_name = @$_FILES[$fm_cfg['cgi'].'userfile']['name'][$j];

			if ($fm_submit == _FM_FILE_UPLOAD && $fm_userfile_name != '') {
				$tmp_target_filename = $target_filename = $fm_userfile_name;
				for ($i = 2; file_exists($tmp_target_filename); $i++) {
					if ($i == 2) {
						$action_message[] = _FM_FILENAME_CHANGED;
					}
					$tmp_target_filename = $target_filename . '-' . $i;
				}
				$target_filename = $tmp_target_filename;
				if (@copy($fm_userfile, $target_filename)) {
					$action_message[] = _FM_FILE_UPLOAD_OK;
					if ($fm_cfg['mode']['file'] != 0) {
						if (@chmod($target_filename, $fm_cfg['mode']['file']) == false) {
							$action_message[] = ' '._FM_CHMOD_ERR;
						} else {
							$action_message[] = ' '._FM_CHMOD_OK;
						}
					}
				} else {
					$action_message[] = _FM_FILE_UPLOAD_ERR;
				}
			} else {
				$action_message[] = _FM_CANCELED;
			}
		}
	} /* }}} */

	/*
	 * file edit action
	 */
	if (in_array($fm_action, array('confirm_edit_file', 'edit_file')) /* {{{ */
			&& $fm_cfg['perm']['file']['edit']) {
		if (is_readable($fm_filename) && is_writeable($fm_filename)) {
			if ($fm_action == 'confirm_edit_file' || $fm_submit == _FM_FILE_EDIT) {
				// will display edit dialog
			} else {
				$action_message[] = _FM_CANCELED;
			}
		} else {
			$action_message[] = _FM_FILE_EDIT_ERR;
		}
	} /* }}} */

	/*
	 * Form
	 */

	echo '<form enctype="multipart/form-data" method="post"'
		. ' action="' . htmlspecialchars($PHP_SELF) . '">'."\n";
	echo '<input type="hidden" value="'.$fm_filename_he.'" name="'.$fm_cfg['cgi'].'filename">'."\n";
	echo '<input type="hidden" value="'.$fm_dir_he.'" name="'.$fm_cfg['cgi'].'dir">'."\n";
	echo fm_get_origvars_html($fm_origvars);

	/*
	 * Variable array
	 */
	$var_ar = array(
			'fm_filename'          => $fm_filename_he,
			'fm_filename_he'       => $fm_filename_he,
			'fm_filename_he2'      => $fm_filename_he2,
			'fm_dir'               => $fm_dir,
			'fm_dir_he'            => $fm_dir_he,
			'fm_dir_he2'           => $fm_dir_he2,
			'fm_dir_ue'            => $fm_dir_ue,
			'fm_action_message'    => array_unique($action_message),
			'PHP_SELF'             => $PHP_SELF,
			'PHP_SELF_origvars_ue' => $PHP_SELF_origvars_ue,
			);

	/*
	 * Title writting with current working direcotry information.
	 */

	fm_header($var_ar);

	switch ($fm_action) {

		/*
		 * confirm delete files action
		 */
		case 'confirm_delete_file':
			echo '<input type="hidden" value="delete_file" name="'.$fm_cfg['cgi'].'action">';
			echo _FM_REALLY_DELETE.'<br><br><i>'.$fm_filename_he2.'</i><br><br>';
			echo $fm_dir == ''
				? _FM_FROM_ROOT_DIR.'?<br><br>'
				: _FM_FROM_DIR.'<br><br><i>'.$fm_dir_he2.'</i><br><br>';
			echo '<input type="submit" name="'.$fm_cfg['cgi'].'submit" value="'._FM_FILE_DELETE.'">&nbsp;';
			echo '<input type="submit" name="'.$fm_cfg['cgi'].'submit" value="'._FM_CANCEL.'">';
			break;

			/*
			 * confirm remove directory action
			 */
		case 'confirm_remove_directory':
			echo '<input type="hidden" value="remove_directory" name="'.$fm_cfg['cgi'].'action">';
			echo _FM_REALLY_REMOVE.'<br><br><i>'.$fm_filename_he2.'</i><br><br>';
			echo _FM_MUST_BE_EMPTY.'<br><br>';
			echo '<input type="submit" name="'.$fm_cfg['cgi'].'submit" value="'._FM_DIR_REMOVE.'">&nbsp;';
			echo '<input type="submit" name="'.$fm_cfg['cgi'].'submit" value="'._FM_CANCEL.'">';
			break;

			/*
			 * confirm rename files action and confirm rename directory action
			 */
		case 'confirm_rename_file':
		case 'confirm_rename_directory':
			echo '<input type="hidden" value="';
			echo $fm_action == 'confirm_rename_directory' ? 'rename_directory' : 'rename_file';
			echo '" name="'.$fm_cfg['cgi'].'action">';
			echo _FM_RENAME_FROM.'<br><br><i>'.$fm_filename_he2.'</i><br><br>';
			echo _FM_RENAME_TO.'<br><br>';
			echo '<input type="text" value="'.$fm_filename_he2.'" name="'.$fm_cfg['cgi'].'new_filename">';
			echo '<br><br><input type="submit" name="'.$fm_cfg['cgi'].'submit" value="';
			echo $fm_action == 'confirm_rename_directory' ? _FM_DIR_RENAME : _FM_FILE_RENAME;
			echo '">&nbsp;';
			echo '<input type="submit" name="'.$fm_cfg['cgi'].'submit" value="'._FM_CANCEL.'">';
			break;

			/*
			 * confirm create directory and confirm create file actions
			 */
		case 'confirm_create_directory':
		case 'confirm_create_file':
		case _FM_FILE_CREATE:
		case _FM_DIR_CREATE:
			$create_dir = $fm_action == 'confirm_create_directory'
				|| $fm_action == _FM_DIR_CREATE;
			echo '<input type="hidden" value="';
			echo $create_dir ? 'create_directory' : 'create_file';
			echo '" name="'.$fm_cfg['cgi'].'action">';
			echo _FM_ENTER_NAME . ':<br><br>';
			echo '<input type="text" value="" name="'.$fm_cfg['cgi'].'new_filename"><br><br>';
			echo '<input type="submit" name="'.$fm_cfg['cgi'].'submit" value="';
			echo $create_dir ? _FM_DIR_CREATE : _FM_FILE_CREATE;
			echo '">&nbsp;';
			echo '<input type="submit" name="'.$fm_cfg['cgi'].'submit" value="'._FM_CANCEL.'">';
			break;

			/*
			 * confirm upload file action
			 */
		case 'confirm_upload_file':
		case _FM_FILE_UPLOAD:
			echo '<input type="hidden" value="upload_file" name="'.$fm_cfg['cgi'].'action">';
			echo ($fm_cfg['perm']['file']['upload'] > 1 ? _FM_SELECT_LOCALS : _FM_SELECT_LOCAL).':<br><br>';
			for ($i = 0; $i < $fm_cfg['perm']['file']['upload']; $i++) {
				echo '<input type="file" value="" name="'.$fm_cfg['cgi'].'userfile['.$i.']"><br><br>';
			}
			echo '<input type="submit" name="'.$fm_cfg['cgi'].'submit" value="'._FM_FILE_UPLOAD.'">&nbsp;';
			echo '<input type="submit" name="'.$fm_cfg['cgi'].'submit" value="'._FM_CANCEL.'">';
			break;

			/*
			 * confirm edit file and edit file form
			 */
		case 'confirm_edit_file':
			if (count($action_message) <= 0) {
				// Two file checks:
				//  1. File size check - if too big, ask if really edit
				//  2. File content check - if looks binary, ask if really edit
				$max_size = 10 * 1024;
				if (($size = @filesize($fm_filename)) > $max_size) {
					$file_edit_delayed = 1;
				} else {
					if (! is_resource($fp = @fopen($fm_filename, 'r'))) {
						$file_edit_delayed = 1;
					} else {
						$tmp_buf = @fread($fp, $max_size);
						@fclose($fp);
						if (! is_string($tmp_buf)) {
							$file_edit_delayed = 1;
						} else {
							$tmp_buf = @preg_replace('|[^\x00-\x1f]|', '', $tmp_buf);
							$tmp_buf = @preg_replace("|[\n\r\t]|", '', $tmp_buf);
							$file_edit_delayed = @strlen($tmp_buf) > 0;
						}
						unset($tmp_buf);
					}
				}
				if ($file_edit_delayed) {
					echo '<input type="hidden" value="edit_file" name="'.$fm_cfg['cgi'].'action">';
					echo _FM_REALLY_EDIT
						. '<br><br><i>'.$fm_filename_he2.'</i><br><br>';
					echo $fm_dir == ''
						? _FM_IN_ROOT_DIR . '?<br><br>'
						: _FM_IN_DIR . '<br><br><i>'.$fm_dir_he2.'</i><br><br>';
					echo '<input type="submit" name="'.$fm_cfg['cgi'].'submit" value="'._FM_FILE_EDIT.'">&nbsp;';
					echo '<input type="submit" name="'.$fm_cfg['cgi'].'submit" value="'._FM_CANCEL.'">';
					break;
				}
			}
		case 'edit_file':
			if (count($action_message) <= 0 && ($fp = @fopen($fm_filename, 'r')) != false) {
				echo '<input type="hidden" value="save_file" name="'.$fm_cfg['cgi'].'action">';
				// Please write me, if you want to have cols and rows configurable.
				echo '<textarea name="',$fm_cfg['cgi'],'file_data"';
				echo ' rows="',$fm_cfg['textarea']['rows'],'" cols="',$fm_cfg['textarea']['cols'],'">';
				while (! @feof($fp)) {
					echo htmlspecialchars(@fread($fp, 1024));
				}
				@fclose($fp);
				echo '</textarea><br><br>';
				echo '<input type="submit" name="'.$fm_cfg['cgi'].'submit" value="'._FM_FILE_SAVE.'">&nbsp;';
				echo '<input type="submit" name="'.$fm_cfg['cgi'].'submit" value="'._FM_CANCEL.'">';
				break;
			}

		default: // File listing

			/*
			 * Column display counting
			 */

			$rename_column = $fm_cfg['perm']['dir']['rename'] || $fm_cfg['perm']['file']['rename'];
			$delete_column = $fm_cfg['perm']['dir']['remove'] || $fm_cfg['perm']['file']['delete'];
			$view_column = $fm_cfg['perm']['dir']['enter']    || $fm_cfg['perm']['file']['view'];
			$edit_column = $fm_cfg['perm']['file']['edit'];

			/*
			 * Setting up view URL
			 */
			if ($view_column && $fm_cfg['perm']['file']['view']) {
				if (strlen($fm_cfg['url']['root']) > 0 && $fm_cfg['url']['root']{0} == '/') {
					$view_url_prefix_ue = $fm_cfg['url']['root'].$fm_dir_ue;
				} else {
					$view_url = parse_url((strcasecmp($_SERVER['HTTPS'], 'on') ? 'http' : 'https')
							.'://'.$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME']);
					$view_url['path'] = fm_get_path_without_scriptname($view_url)
						. (strlen($fm_cfg['url']['root']) > 0 ? $fm_cfg['url']['root'] : $fm_cfg['dir']['root'])
						. $fm_dir_ue;
					unset($view_url['fragment']);
					unset($view_url['query']);
					$view_url_prefix_ue = fm_glue_url($view_url);
					unset($view_url);
					//if($_SESSION['auth']=='all') echo $fm_cfg['dir']['root']."***";

				}
				$view_url_prefix_ue = str_replace('%2F', '/', $view_url_prefix_ue);
				$view_url_prefix_ue = htmlspecialchars($view_url_prefix_ue);
			}


			/*
			 * Colspan counting
			 */

			$colspan = 1;
			if ($fm_cfg['show']['icons'])	$colspan++;
			if ($fm_cfg['show']['size'])	$colspan++;
			if ($fm_cfg['show']['date'])	$colspan++;
			if ($rename_column)				$colspan++;
			if ($delete_column)				$colspan++;
			if ($view_column)				$colspan++;
			if ($edit_column)				$colspan++;

			/*
			 * Table tag and parent directory link
			 */

			echo '<table cellpadding="2" class="liste_ftp">' . "\n";

			$line = 0;
			if (strlen(getcwd()) > strlen($fm_root_dir)) {
				echo '<tr valign="middle"';
				if (! empty($fm_cfg['color']['odd']))
					echo ' bgcolor="'.$fm_cfg['color']['odd'].'"';
				echo '>';
				if ($fm_cfg['show']['icons']) {
					echo '<td align="center">';
					echo '<a href="'.$PHP_SELF_origvars_ue.$fm_cfg['cgi'].'dir='.$fm_dir_ue.'..">';
					echo '<img alt="'._FM_BACK.'" title="'._FM_BACK.'" border="0" class="noborder" src="'
						. $fm_cfg['url']['icons'].$fm_cfg['icons']['sys']['UP-DIR'].'"></a>';
					echo '</td>';
				}
				echo '<td width="100%" colspan="'.($colspan - 1).'">';
				echo '<a href="'.$PHP_SELF_origvars_ue.$fm_cfg['cgi'].'dir='.$fm_dir_ue.'..">';
				echo '('._FM_PARENT_DIR.')</a>';
				echo '</td>';
				echo '</tr>'."\n";
				$line++;
			}

			// Fetching directories and files data
			$dir_obj    = dir('./');
			$list_dirs  = array();
			$list_files = array();
			while (1) {
				$entry = $dir_obj->read();
				if ($entry === FALSE) {
					break;
				}
				if ($entry == '.' || $entry == '..') {
					continue;
				}
				if (@is_dir($entry)) {
					$list_dirs[]  = $entry;
				} else {
					$list_files[] = $entry;
				}
			}
			$dir_obj->close();
			// Alphabetical order
			sort($list_dirs);
			sort($list_files);

			$list_ptr = null;
			$is_dir   = null;
			while (1) {
				if ($list_ptr == null) {
					$list_ptr =& $list_dirs;
					$is_dir   =  TRUE;
				}
				if (count($list_ptr) <= 0) {
					if (count($list_files) <= 0) {
						break;
					}
					$list_ptr =& $list_files;
					$is_dir   =  FALSE;
				}
				$entry = array_shift($list_ptr);

				$entry_he  = htmlspecialchars($entry);
				$entry_he2 = str_replace(' ', '&nbsp;', $entry_he);
				$entry_ue  = htmlspecialchars(rawurlencode($entry));

				echo '<tr valign="middle"';
				$line++;
				if ($line % 2) {
					if (! empty($fm_cfg['color']['odd']))
						echo ' bgcolor="'.$fm_cfg['color']['odd'].'"';
				} else {
					if (! empty($fm_cfg['color']['even']))
						echo ' bgcolor="'.$fm_cfg['color']['even'].'"';
				}
				echo '>',"\n";
				if ($is_dir) {
					if ($fm_cfg['show']['icons']) {
						echo '<td align="center">';
						if (is_link($entry)) {
							echo '<b>~</b>';
						} else {
							if ($fm_cfg['perm']['dir']['enter'])  {
								echo '<a href="'.$PHP_SELF_origvars_ue;
								echo $fm_cfg['cgi'].'dir='.$fm_dir_ue.$entry_ue.'">';
							}
							echo '<img alt="'._FM_DIR.'" title="'._FM_DIR.'" border="0" class="noborder" src="'
								. $fm_cfg['url']['icons'].$fm_cfg['icons']['sys']['DIR'].'">';
							if ($fm_cfg['perm']['dir']['enter'])
								echo '</a>';
						}
						echo '</td>',"\n";
					}
					echo '<td width="100%">';
					if ($fm_cfg['perm']['dir']['enter']
							&& ($fm_cfg['perm']['dir']['follow_symlinks']
								|| ! is_link($entry))) {
						echo '<a href="'.$PHP_SELF_origvars_ue;
						echo $fm_cfg['cgi'].'dir='.$fm_dir_ue.$entry_ue.'">';
						echo $entry_he2.'</a>';
					} else {
						echo $entry_he2;
					}
				} else {
					if ($fm_cfg['show']['icons']) {
						echo '<td align="center">';
						if (is_link($entry)) {
							echo '<b>@</b>';
						} else {
							$icon_file = '';
							$icon_alt  = '';
							$ext  = substr(strtolower(strrchr($entry_he , '.')), 1);
							if (isset($fm_cfg['icons']['name'][$entry])) {
								$icon_file = $fm_cfg['icons']['name'][$entry];
								$icon_alt  = strtoupper($entry).' '.strtolower(_FM_FILE);
							} elseif (isset($fm_cfg['icons']['ext'][$ext])) {
								$icon_file = $fm_cfg['icons']['ext'][$ext];
								$icon_alt  = strtoupper($ext).' '.strtolower(_FM_FILE);
							}
							if ($icon_file == '') {
								echo '&nbsp;';
							} else {
								if ($fm_cfg['perm']['file']['view'])  {
									//echo '<a target="_blank" href="'.$view_url_prefix_ue.$entry_ue.'">';
									echo '<a target="_blank" href="index.php?ftp_mode=get_file&pfad='.urlencode($fm_dir_ue.$entry_ue).'">';
									//echo $view_url_prefix_ue."***".$entry_ue;

								}
								echo '<img alt="'.$icon_alt.'" title="'.$icon_alt.'" border="0" class="noborder" src="'
									. $fm_cfg['url']['icons'].$icon_file.'">';
								if ($fm_cfg['perm']['file']['view']) {
									echo '</a>';
								}
							}
						}
						echo '</td>',"\n";
					}
					echo '<td width="100%">' . $entry_he2;
				}
				echo '</td>',"\n";

				if (! $is_dir) {
					if ($fm_cfg['show']['size']) {
						echo '<td align="right" nowrap="nowrap"><small>';
						$size = @filesize($entry);
						if (is_numeric($size)) {
							$unit = 'B';
							if ( $size > 1073741824 ) {
								$size = round($size / 1073741824, 1);
								$unit = 'GB';
							} elseif ( $size > 1048576 ) {
								$size = round($size / 1048576, 1);
								$unit = 'MB';
							} elseif ( $size > 1024 ) {
								$size = round($size / 1024, 1);
								$unit = 'kB';
							}
							echo $size . '&nbsp;' . $unit;
						}
						echo '</small></td>',"\n";
					}
					if ($fm_cfg['show']['date']) {
						echo '<td align="center" nowrap="nowrap"><small>';
						echo date($fm_cfg['format']['date'], @filemtime($entry));
						echo '</small></td>',"\n";
					}
					if ($rename_column) {
						echo '<td><small>';
						if ($fm_cfg['perm']['file']['rename']) {
							echo '[<a href="'.$PHP_SELF_origvars_ue
								. $fm_cfg['cgi'].'dir='.$fm_dir_ue.'&amp;'
								. $fm_cfg['cgi'].'action=confirm_rename_file'.'&amp;'
								. $fm_cfg['cgi'].'filename='.$entry_ue.'">'
								. _FM_FILE_RENAME.'</a>]';
						}
						echo '</small></td>',"\n";
					}
					if ($delete_column) {
						echo '<td><small>';
						if ($fm_cfg['perm']['file']['delete']) {
							echo '[<a href="'.$PHP_SELF_origvars_ue
								. $fm_cfg['cgi'].'dir='.$fm_dir_ue.'&amp;'
								. $fm_cfg['cgi'].'action=confirm_delete_file'.'&amp;'
								. $fm_cfg['cgi'].'filename='.$entry_ue.'">'
								. _FM_FILE_DELETE.'</a>]';
						}
						echo '</small></td>',"\n";
					}
					if ($view_column) {
						echo '<td><small>';
						if ($fm_cfg['perm']['file']['view']) {
							echo '[<a target="_blank" href="index.php?ftp_mode=get_file&pfad='.urlencode($fm_dir_ue.$entry_ue).'">'
								. _FM_FILE_VIEW.'</a>]';
						}
						echo '</small></td>',"\n";
					}
					if ($edit_column) {
						echo '<td><small>';
						if ($fm_cfg['perm']['file']['edit']
								&& @is_readable($entry)
								&& @is_writeable($entry)) {
							echo '[<a href="'.$PHP_SELF_origvars_ue
								. $fm_cfg['cgi'].'dir='.$fm_dir_ue.'&amp;'
								. $fm_cfg['cgi'].'action=confirm_edit_file'.'&amp;'
								. $fm_cfg['cgi'].'filename='.$entry_ue.'">'
								. _FM_FILE_EDIT.'</a>]';
						}
						echo '</small></td>',"\n";
					}
				} else {
					if ($fm_cfg['show']['size']) {
						echo '<td><small>&nbsp;</small></td>',"\n";
					}
					if ($fm_cfg['show']['date']) {
						echo '<td><small>&nbsp;</small></td>',"\n";
					}
					if ($rename_column) {
						echo '<td><small>';
						if ($fm_cfg['perm']['dir']['rename']) {
							echo '[<a href="'.$PHP_SELF_origvars_ue
								. $fm_cfg['cgi'].'dir='.$fm_dir_ue.'&amp;'
								. $fm_cfg['cgi'].'action=confirm_rename_directory'.'&amp;'
								. $fm_cfg['cgi'].'filename='.$entry_ue.'">'
								. _FM_DIR_RENAME.'</a>]';
						}
						echo '</small></td>',"\n";
					}
					if ($delete_column) {
						echo '<td><small>';
						if ($fm_cfg['perm']['dir']['remove']) {
							echo '[<a href="'.$PHP_SELF_origvars_ue
								. $fm_cfg['cgi'].'dir='.$fm_dir_ue.'&amp;'
								. $fm_cfg['cgi'].'action=confirm_remove_directory'.'&amp;'
								. $fm_cfg['cgi'].'filename='.$entry_ue.'">'
								. _FM_DIR_REMOVE.'</a>]';
						}
						echo '</small></td>',"\n";
					}
					if ($view_column) {
						echo '<td><small>';
						if ($fm_cfg['perm']['dir']['enter']
								&& ($fm_cfg['perm']['dir']['follow_symlinks']
									|| ! is_link($entry))) {
							echo '[<a href="'.$PHP_SELF_origvars_ue
								. $fm_cfg['cgi'].'dir='.$fm_dir_ue.$entry_ue.'">'
								. _FM_DIR_ENTER.'</a>]';
						}
						echo '</small></td>',"\n";
					}
					if ($edit_column) {
						echo '<td><small>&nbsp;</small></td>',"\n";
					}
				}
				echo '</tr>',"\n";
			}
			echo '</table>',"\n";
	}

	/* Now return back to the initial working directory. */
	chdir($fm_orig_dir);

	fm_footer($var_ar);

	echo '</form>',"\n";
} /* }}} */

/*
 * Main function call
 */

fm_main();

/* vim: set tabstop=4 shiftwidth=4:
 * vim600: fdm=marker fdl=0 fdc=0
 */

?>
