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
		'UP-DIR' => 'back_16.svg',
		'DIR'    => 'folder_16.svg'
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
	'bin'    => 'link_any_16.svg',
	'exe'    => 'link_any_16.svg',
	'hqx'    => 'link_any_16.svg',
	'tar'    => 'link_zip_16.svg',
	'Z'      => 'link_zip_16.svg',
	'z'      => 'link_zip_16.svg',
	'tgz'    => 'link_zip_16.svg',
	'gz'     => 'link_zip_16.svg',
	'bz2'    => 'link_zip_16.svg',
	'txt'    => 'link_txt_16.svg',
	/* End of Apache extraction */

	/* Now phpWebFileManager specific. It needs to improve anyway. */
	'sit'    => 'link_any_16.svg',
	'sitx'   => 'link_any_16.svg',
	'psd'    => 'link_any_16.svg',
	'indd'   => 'link_any_16.svg',
	'qxd'    => 'link_any_16.svg',
	'dmg'    => 'link_any_16.svg',
	'ai'     => 'link_any_16.svg',

	'css'    => 'link_css_16.svg',

	'csv'    => 'link_any_16.svg',

	'bz'     => 'compressed.gif',
	'rar'    => 'compressed.gif',
	'arj'    => 'compressed.gif',
	'uc'     => 'compressed.gif', /* Ultra compressor */
	'uc2'    => 'compressed.gif',

	'uu'     => 'link_any_16.svg',

	'sdw'    => 'link_doc_16.svg',
	'sgml'   => 'link_doc_16.svg',
	'xml'    => 'link_doc_16.svg',

	'html'   => 'link_html_16.svg',

	'bmp'    => 'link_image_16.svg',
	'pcx'    => 'link_image_16.svg',
	'gif'    => 'link_image_16.svg',
	'tiff'   => 'link_image_16.svg',
	'tif'    => 'link_image_16.svg',
	'jpg'    => 'link_image_16.svg',
	'jpe'    => 'link_image_16.svg',
	'jpeg'   => 'link_image_16.svg',

	'js'     => 'link_js_16.svg',

	'avi'    => 'link_movie_16.svg',
	'rm'     => 'link_movie_16.svg',
	'mpg'    => 'link_movie_16.svg',
	'mpeg'   => 'link_movie_16.svg',
	'wmv'    => 'link_movie_16.svg',

	'mov'    => 'link_movie_16.svg',
	'qt'     => 'link_movie_16.svg',

	'mp3'    => 'link_any_16.svg',
	'mp2'    => 'link_any_16.svg',
	'mp1'    => 'link_any_16.svg',

	'md5'    => 'link_any_16.svg',
	'md5sum' => 'link_any_16.svg',
	'md5key' => 'link_any_16.svg',

	'patch'  => 'link_any_16.svg',
	'diff'   => 'link_any_16.svg',

	'ocd'    => 'link_ocd_16.svg',

	'pdf'    => 'link_pdf_16.svg',

	'perl'   => 'link_any_16.svg', // not used, but just for sure
	'pl'     => 'link_any_16.svg',
	'pm'     => 'link_any_16.svg',
	'cgi'    => 'link_any_16.svg', // temporary entry?
	'fcgi'   => 'link_any_16.svg',

	'php'    => 'link_any_16.svg',
	'php3'   => 'link_any_16.svg',
	'php4'   => 'link_any_16.svg',
	'phtml'  => 'link_any_16.svg',
	'inc'    => 'link_any_16.svg',

	'png'    => 'link_image_16.svg',

	'ps'     => 'link_any_16.svg',
	'eps'    => 'link_any_16.svg',
	'dvi'    => 'link_any_16.svg',

	'rtf'    => 'link_doc_16.svg',

	'wma'    => 'link_any_16.svg',
	'wav'    => 'link_any_16.svg',

	'xm'     => 'link_any_16.svg',
	'mod'    => 'link_any_16.svg',
	'mid'    => 'link_any_16.svg',

	'sub'    => 'link_any_16.svg',

	'sql'    => 'link_any_16.svg',
	'pks'    => 'link_any_16.svg',
	'pkb'    => 'link_any_16.svg',
	'fnc'    => 'link_any_16.svg',
	'proc'   => 'link_any_16.svg',

	'tmpl'   => 'tmpl.gif',
	'tpl'    => 'tmpl.gif',
	'tphp'   => 'tmpl.gif',
	'tpl.php'=> 'tmpl.gif',
	'tt2'    => 'tmpl.gif',

	'doc'    => 'link_doc_16.svg',
	'docx'    => 'link_doc_16.svg',
	'xls'    => 'link_xls_16.svg',
	'xlsx'    => 'link_xls_16.svg',
	'ppt'    => 'link_ppt_16.svg',

	'zip'    => 'link_zip_16.svg'
	);

?>
