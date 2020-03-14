<?php
/*
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

/**
 * @copyright    XOOPS Project https://xoops.org/
 * @license      GNU GPL 2 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 * @package      RSSFit - Extendable XML news feed generator
 * @author       NS Tai (aka tuff) <http://www.brandycoke.com>
 * @author       XOOPS Development Team
 */
use XoopsModules\Rssfit;

if (!defined('RSSFIT_CONSTANTS_DEFINED')) {
    define('RSSFIT_ROOT_PATH', XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->getVar('dirname') . '/');
    define('RSSFIT_URL', XOOPS_URL . '/modules/' . $xoopsModule->getVar('dirname') . '/');
    define('RSSFIT_URL_FEED', RSSFIT_URL . 'rss.php');
    define('RSSFIT_ADMIN_URL', XOOPS_URL . '/modules/' . $xoopsModule->getVar('dirname') . '/admin/');
    define('RSSFIT_CONSTANTS_DEFINED', 1);
}

//require_once RSSFIT_ROOT_PATH . 'class/rssfeed.php';
//require_once RSSFIT_ROOT_PATH.'include/functions.php';

$version = number_format($xoopsModule->getVar('version') / 100, 2);
$version = !mb_substr($version, -1, 1) ? mb_substr($version, 0, 3) : $version;
define('RSSFIT_VERSION', 'RSSFit ' . $version);

$rss = new Rssfit\FeedHandler($xoopsModuleConfig, $xoopsConfig, $xoopsModule);
$myts = $rss->myts;
$pluginHandler = $rss->pluginHandler;
$miscHandler = $rss->miscHandler;
