<?php
// $Id$
###############################################################################
##                RSSFit - Extendable XML news feed generator                ##
##                Copyright (c) 2004 - 2005 NS Tai (aka tuff)                ##
##                       <http://www.brandycoke.com/>                        ##
###############################################################################
##                    XOOPS - PHP Content Management System                  ##
##                       Copyright (c) 2000 XOOPS.org                        ##
##                          <http://www.xoops.org/>                          ##
###############################################################################
##  This program is free software; you can redistribute it and/or modify     ##
##  it under the terms of the GNU General Public License as published by     ##
##  the Free Software Foundation; either version 2 of the License, or        ##
##  (at your option) any later version.                                      ##
##                                                                           ##
##  You may not change or alter any portion of this comment or credits       ##
##  of supporting developers from this source code or any supporting         ##
##  source code which is considered copyrighted (c) material of the          ##
##  original comment or credit authors.                                      ##
##                                                                           ##
##  This program is distributed in the hope that it will be useful,          ##
##  but WITHOUT ANY WARRANTY; without even the implied warranty of           ##
##  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            ##
##  GNU General Public License for more details.                             ##
##                                                                           ##
##  You should have received a copy of the GNU General Public License        ##
##  along with this program; if not, write to the Free Software              ##
##  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA ##
###############################################################################
##  Author of this file: NS Tai (aka tuff)                                   ##
##  URL: http://www.brandycoke.com/                                          ##
##  Project: RSSFit                                                          ##
###############################################################################

if( !defined("RSSFIT_CONSTANTS_DEFINED") ){
	define("RSSFIT_ROOT_PATH", XOOPS_ROOT_PATH.'/modules/'.$xoopsModule->getVar('dirname').'/');
	define("RSSFIT_URL", XOOPS_URL.'/modules/'.$xoopsModule->getVar('dirname').'/');
	define("RSSFIT_URL_FEED", RSSFIT_URL.'rss.php');
	define("RSSFIT_ADMIN_URL", XOOPS_URL.'/modules/'.$xoopsModule->getVar('dirname').'/admin/');
	define("RSSFIT_CONSTANTS_DEFINED", 1);
}

require_once RSSFIT_ROOT_PATH.'class/rssfeed.php';
require_once RSSFIT_ROOT_PATH.'include/functions.php';

$version = number_format($xoopsModule->getVar('version')/100, 2);
$version = !substr($version, -1, 1) ? substr($version, 0, 3) : $version;
define('RSSFIT_VERSION', 'RSSFit '.$version);

$rss =& new RssfeedHandler($xoopsModuleConfig, $xoopsConfig, $xoopsModule);
$myts =& $rss->myts;
$plugins_handler =& $rss->pHandler;
$misc_handler =& $rss->mHandler;

?>