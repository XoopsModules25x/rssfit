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
 * @copyright    The XOOPS Project http://sourceforge.net/projects/xoops/
 * @license      GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package     RSSFit
 * @since
 * @author     XOOPS Development Team
 * @version    $Id $
 */
defined("XOOPS_ROOT_PATH") or die("XOOPS root path not defined");

$path = dirname(dirname(dirname(dirname(__FILE__))));
include_once $path . '/mainfile.php';

$module_handler  = xoops_gethandler('module');
$module          = $module_handler->getByDirname(basename(dirname(dirname(__FILE__))));
$pathIcon32 = '../../' . $module->getInfo('icons32');
xoops_loadLanguage('modinfo', $module->dirname());


$pathModuleAdmin = XOOPS_ROOT_PATH . '/' . $module->getInfo('dirmoduleadmin').'/moduleadmin';
if (!file_exists($fileinc = $pathModuleAdmin  . '/language/' . $GLOBALS['xoopsConfig']['language'] . '/' . 'main.php')) {
    $fileinc = $pathModuleAdmin  . '/language/english/main.php';
}
include_once $fileinc;

$adminmenu = array();
$i=0;
$adminmenu[$i]["title"] = _AM_MODULEADMIN_HOME;
$adminmenu[$i]['link'] = "admin/index.php";
$adminmenu[$i]["icon"]  = $pathIcon32 . '/home.png';

++$i;
$adminmenu[$i]['title'] = _MI_RSSFIT_ADMENU1;
$adminmenu[$i]['link'] = "admin/?do=intro";
//$adminmenu[$i]['link'] = "admin/do_intro.php";
$adminmenu[$i]["icon"]  = $pathIcon32 . '/folder_txt.png';
++$i;
$adminmenu[$i]['title'] = _MI_RSSFIT_ADMENU2;
$adminmenu[$i]['link'] = "admin/?do=plugins";
//$adminmenu[$i]['link'] = "admin/do_plugins.php";
$adminmenu[$i]["icon"]  =  'images/icons/32/plugin.png';
++$i;
$adminmenu[$i]['title'] = _MI_RSSFIT_ADMENU3;
$adminmenu[$i]['link'] = "admin/?do=channel";
//$adminmenu[$i]['link'] = "admin/do_channel.php";
$adminmenu[$i]["icon"]  = $pathIcon32 . '/compfile.png';
++$i;
$adminmenu[$i]['title'] = _MI_RSSFIT_ADMENU4;
$adminmenu[$i]['link'] = "admin/?do=subfeeds";
//$adminmenu[$i]['link'] = "admin/do_subfeeds.php";
$adminmenu[$i]["icon"]  = $pathIcon32 . '/groupmod.png';
++$i;
$adminmenu[$i]['title'] = _MI_RSSFIT_ADMENU5;
$adminmenu[$i]['link'] = "admin/?do=sticky";
$adminmenu[$i]["icon"]  = $pathIcon32 . '/attach.png';
++$i;
$adminmenu[$i]['title'] = _AM_MODULEADMIN_ABOUT;
$adminmenu[$i]["link"]  = "admin/about.php";
$adminmenu[$i]["icon"]  = $pathIcon32 . '/about.png';

