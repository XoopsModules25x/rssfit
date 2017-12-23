<?php
###############################################################################
##                RSSFit - Extendable XML news feed generator                ##
##                Copyright (c) 2004 - 2006 NS Tai (aka tuff)                ##
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

use Xoopsmodules\rssfit;

$moduleDirName = basename(dirname(__DIR__));
$capsDirName   = strtoupper($moduleDirName);

include __DIR__ . '/../preloads/autoloader.php';

/** @var \XoopsDatabase $db */
/** @var rssfit\Helper $helper */
/** @var rssfit\Utility $utility */
//$db           = \XoopsDatabaseFactory::getDatabase();
$db      = \XoopsDatabaseFactory::getDatabaseConnection();
$helper  = rssfit\Helper::getInstance();
$utility = new rssfit\Utility();
//$configurator = new rssfit\common\Configurator();

$helper->loadLanguage('common');

if (!defined('RSSFIT_CONSTANTS_DEFINED')) {
    define('RSSFIT_ROOT_PATH', XOOPS_ROOT_PATH . '/modules/' . $helper->getDirname() . '/');
    define('RSSFIT_URL', XOOPS_URL . '/modules/' . $helper->getDirname() . '/');
    define('RSSFIT_URL_FEED', RSSFIT_URL . 'rss.php');
    define('RSSFIT_ADMIN_URL', XOOPS_URL . '/modules/' . $helper->getDirname() . '/admin/');
    define('RSSFIT_CONSTANTS_DEFINED', 1);
    define($capsDirName . '_DIRNAME', $GLOBALS['xoopsModule']->dirname());
    define($capsDirName . '_PATH', XOOPS_ROOT_PATH . '/modules/' . constant($capsDirName . '_DIRNAME'));
    //    define($capsDirName . '_URL', XOOPS_URL . '/modules/' . constant($capsDirName . '_DIRNAME'));
    define($capsDirName . '_ADMIN', constant($capsDirName . '_URL') . '/admin/index.php');
    //    define($capsDirName . '_ROOT_PATH', XOOPS_ROOT_PATH . '/modules/' . constant($capsDirName . '_DIRNAME'));
    define($capsDirName . '_AUTHOR_LOGOIMG', constant($capsDirName . '_URL') . '/assets/images/logoModule.png');
    define($capsDirName . '_UPLOAD_URL', XOOPS_UPLOAD_URL . '/' . $moduleDirName); // WITHOUT Trailing slash
    define($capsDirName . '_UPLOAD_PATH', XOOPS_UPLOAD_PATH . '/' . $moduleDirName); // WITHOUT Trailing slash
}

require_once RSSFIT_ROOT_PATH . 'class/RssfeedHandler.php';
//require_once RSSFIT_ROOT_PATH.'include/functions.php';

global $xoopsModule, $xoopsModuleConfig, $xoopsConfig;
$version = number_format($xoopsModule->getVar('version') / 100, 2);
$version = !substr($version, -1, 1) ? substr($version, 0, 3) : $version;
//$version1 = $helper->getModule()->getInfo('version');
define('RSSFIT_VERSION', 'RSSFit ' . $version);

$rss            = new rssfit\RssfeedHandler($xoopsModuleConfig, $xoopsConfig, $xoopsModule);
$myts           = $rss->myts;
$pluginsHandler = $rss->pHandler;
$miscHandler    = $rss->mHandler;

//handlers
//$categoryHandler     = new rssfit\CategoryHandler($db);
//$downloadHandler     = new rssfit\DownloadHandler($db);

$pathIcon16 = Xmf\Module\Admin::iconUrl('', 16);
$pathIcon32 = Xmf\Module\Admin::iconUrl('', 32);
//$pathModIcon16 = $helper->getModule()->getInfo('modicons16');
//$pathModIcon32 = $helper->getModule()->getInfo('modicons32');

$icons = [
    'edit'    => "<img src='" . $pathIcon16 . "/edit.png'  alt=" . _EDIT . "' align='middle'>",
    'delete'  => "<img src='" . $pathIcon16 . "/delete.png' alt='" . _DELETE . "' align='middle'>",
    'clone'   => "<img src='" . $pathIcon16 . "/editcopy.png' alt='" . _CLONE . "' align='middle'>",
    'preview' => "<img src='" . $pathIcon16 . "/view.png' alt='" . _PREVIEW . "' align='middle'>",
    'print'   => "<img src='" . $pathIcon16 . "/printer.png' alt='" . _CLONE . "' align='middle'>",
    'pdf'     => "<img src='" . $pathIcon16 . "/pdf.png' alt='" . _CLONE . "' align='middle'>",
    'add'     => "<img src='" . $pathIcon16 . "/add.png' alt='" . _ADD . "' align='middle'>",
    '0'       => "<img src='" . $pathIcon16 . "/0.png' alt='" . _ADD . "' align='middle'>",
    '1'       => "<img src='" . $pathIcon16 . "/1.png' alt='" . _ADD . "' align='middle'>",
];

// MyTextSanitizer object
$myts = \MyTextSanitizer::getInstance();

$debug = false;
