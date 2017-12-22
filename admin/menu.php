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
 * @package      RSSFit
 * @since
 * @author       XOOPS Development Team
 */

use Xoopsmodules\rss;

require_once __DIR__ . '/../class/Helper.php';
//require_once __DIR__ . '/../include/common.php';
$helper = rss\Helper::getInstance();
// get path to icons
$pathIcon32 = \Xmf\Module\Admin::menuIconPath('');
$pathModIcon32 = $helper->getModule()->getInfo('modicons32');

$adminmenu = [];

$adminmenu[] = [
    'title' => _MI_RSSFIT_INDEX,
    'link'  => 'admin/index.php',
    'icon'  => $pathIcon32 . '/home.png',
];

$adminmenu[] = [
    'title' => _MI_RSSFIT_ADMENU1,
    'link'  => 'admin/?do=intro',
    //'link' =>  "admin/do_intro.php",
    'icon'  => $pathIcon32 . '/folder_txt.png',
];

$adminmenu[] = [
    'title' => _MI_RSSFIT_ADMENU2,
    'link'  => 'admin/?do=plugins',
    //'link' =>  "admin/do_plugins.php",
    'icon'  => 'assets/images/icons/32/plugin.png',
];

$adminmenu[] = [
    'title' => _MI_RSSFIT_ADMENU3,
    'link'  => 'admin/?do=channel',
    //'link' =>  "admin/do_channel.php",
    'icon'  => $pathIcon32 . '/compfile.png',
];

$adminmenu[] = [
    'title' => _MI_RSSFIT_ADMENU4,
    'link'  => 'admin/?do=subfeeds',
    //'link' =>  "admin/do_subfeeds.php",
    'icon'  => $pathIcon32 . '/groupmod.png',
];

$adminmenu[] = [
    'title' => _MI_RSSFIT_ADMENU5,
    'link'  => 'admin/?do=sticky',
    'icon'  => $pathIcon32 . '/attach.png',
];

$adminmenu[] = [
    'title' => _MI_RSSFIT_ABOUT,
    'link'  => 'admin/about.php',
    'icon'  => $pathIcon32 . '/about.png',
];
