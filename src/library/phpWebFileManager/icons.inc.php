<?php

/*
 * phpWebFileManager - simple file management PHP script
 *
 * icons.inc.php - icons configuration file
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

/* $Platon: phpWebFileManager/icons.inc.php,v 1.14 2005/10/01 17:17:34 nepto Exp $ */

/* If you made any modifications in this file, please send me your
   version or diff. I want to improve this. New pretty icons are also
   welcome.  -- Nepto */

/* TODO: GIMP files (all formats),
   MS-Access files and other databases, especially SQL dumps;
   also CVS files in CVS/ subdirectory, other files such as .cvsignore, etc. */

$fm_cfg['icons']['sys'] = array(
		'UP-DIR' => 'back.gif',
		'DIR'    => 'dir.gif'
		);

$fm_cfg['icons']['name'] = array(
		//'core'      => 'bomb.gif'
		'core'        => 'burst.gif', // huh?
		'AUTHOR'      => 'text.gif',
		'AUTHORS'     => 'text.gif',
		'COPYING'     => 'text.gif',
		'LICENSE'     => 'text.gif',
		'TODO'        => 'text.gif',
		'README'      => 'text.gif',
		'INSTALL'     => 'text.gif',
		'ChangeLog'   => 'text.gif',
		'CHANGELOG'   => 'text.gif',
		'CHANGES'     => 'text.gif',
		'NEWS'        => 'text.gif',
		'API'         => 'text.gif',
		'BUGS'        => 'text.gif',
		'FEATURES'    => 'text.gif',
		'SITES'       => 'text.gif',
		'MAINTAINER'  => 'text.gif',
		'MAINTAINERS' => 'text.gif',
		);

$fm_cfg['icons']['ext'] = array(

		/* This is extracted from Apache configuration file,
		   but I don't have these small icons.
		   -- Nepto [5/8/2002] */
		/*
		   'wrl'    => 'world2.gif',
		   'wrl.gz' => 'world2.gif',
		   'vrml'   => 'world2.gif',
		   'vrm'    => 'world2.gif',
		   'iv'     => 'world2.gif',
		   'ps'     => 'a.gif',
		   'ai'     => 'a.gif',
		   'eps'    => 'a.gif',
		   'html'   => 'layout.gif',
		   'shtml'  => 'layout.gif',
		   'htm'    => 'layout.gif',
		   'pdf'    => 'layout.gif',
		   'c'      => 'c.gif',
		   'pl'     => 'p.gif',
		   'py'     => 'p.gif',
		   'php'    => 'p.gif',
		   'php3'   => 'p.gif',
		   'for'    => 'f.gif',
		   'dvi'    => 'dvi.gif',
		   'uu'     => 'uuencoded.gif',
		   'conf'   => 'script.gif',
		   'sh'     => 'script.gif',
		   'shar'   => 'script.gif',
		   'csh'    => 'script.gif',
		   'ksh'    => 'script.gif',
		   'tcl'    => 'script.gif',
		   'tex'    => 'tex.gif'
		 */

	/* These small icons I have. */
	'bin'    => 'binary.gif',
	'exe'    => 'binary.gif',
	'hqx'    => 'binhex.gif',
	'tar'    => 'tar.gif',
	'Z'      => 'compressed.gif',
	'z'      => 'compressed.gif',
	'tgz'    => 'compressed.gif',
	'gz'     => 'compressed.gif',
	'bz2'    => 'compressed.gif',
	'txt'    => 'txt.gif',
	/* End of Apache extraction */

	/* Now phpWebFileManager specific. It needs to improve anyway. */
	'sit'    => 'sit.gif',
	'sitx'   => 'sitx.gif',
	'psd'    => 'photoshop.gif',
	'indd'   => 'in-design.gif',
	'qxd'    => 'quark.gif',
	'dmg'    => 'imm-disco.gif',
	'ai'     => 'illustrator.gif',

	'css'    => 'css.gif',

	'csv'    => 'csv.gif',

	'bz'     => 'compressed.gif',
	'rar'    => 'compressed.gif',
	'arj'    => 'compressed.gif',
	'uc'     => 'compressed.gif', /* Ultra compressor */
	'uc2'    => 'compressed.gif',

	'uu'     => 'uu.gif',

	'sdw'    => 'doc.gif',
	'sgml'   => 'doc.gif',
	'xml'    => 'doc.gif',

	'html'   => 'html.gif',

	'bmp'    => 'image.gif',
	'pcx'    => 'image.gif',
	'gif'    => 'image.gif',
	'tiff'   => 'image.gif',
	'tif'    => 'image.gif',
	'jpg'    => 'image.gif',
	'jpe'    => 'image.gif',
	'jpeg'   => 'image.gif',

	'js'     => 'js.gif',

	'avi'    => 'movie.gif',
	'rm'     => 'movie.gif',
	'mpg'    => 'movie.gif',
	'mpeg'   => 'movie.gif',
	'wmv'    => 'movie.gif',

	'mov'    => 'quicktime.gif',
	'qt'     => 'quicktime.gif',

	'mp3'    => 'mp3.gif',
	'mp2'    => 'mp3.gif',
	'mp1'    => 'mp3.gif',

	'md5'    => 'key.gif',
	'md5sum' => 'key.gif',
	'md5key' => 'key.gif',

	'patch'  => 'patch.gif',
	'diff'   => 'patch.gif',

	'ocd'    => 'ocad.gif',

	'pdf'    => 'pdf.gif',

	'perl'   => 'perl.gif', // not used, but just for sure
	'pl'     => 'perl.gif',
	'pm'     => 'perl.gif',
	'cgi'    => 'perl.gif', // temporary entry?
	'fcgi'   => 'perl.gif',

	'php'    => 'php.gif',
	'php3'   => 'php.gif',
	'php4'   => 'php.gif',
	'phtml'  => 'php.gif',
	'inc'    => 'php.gif',

	'png'    => 'image.gif',

	'ps'     => 'ps.gif',
	'eps'    => 'ps.gif',
	'dvi'    => 'ps.gif',

	'rtf'    => 'rtf.gif',

	'wma'    => 'sound.gif',
	'wav'    => 'sound.gif',

	'xm'     => 'sound2.gif',
	'mod'    => 'sound2.gif',
	'mid'    => 'sound2.gif',

	'sub'    => 'sub.gif',

	'sql'    => 'sql.gif',
	'pks'    => 'sql.gif',
	'pkb'    => 'sql.gif',
	'fnc'    => 'sql.gif',
	'proc'   => 'sql.gif',

	'tmpl'   => 'tmpl.gif',
	'tpl'    => 'tmpl.gif',
	'tphp'   => 'tmpl.gif',
	'tpl.php'=> 'tmpl.gif',
	'tt2'    => 'tmpl.gif',

	'doc'    => 'word.gif',
	'docx'    => 'word.gif',
	'xls'    => 'excel.gif',
	'xlsx'    => 'excel.gif',
	'ppt'    => 'powerpoint.gif',

	'zip'    => 'zip.gif'
	);

?>
