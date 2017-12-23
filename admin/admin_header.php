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
 * @package
 * @since
 * @author       XOOPS Development Team
 */

use Xoopsmodules\rssfit;

$path = dirname(dirname(dirname(__DIR__)));
require_once $path . '/include/cp_header.php';

require_once dirname(__DIR__) . '/include/common.php';

$moduleDirName = basename(dirname(__DIR__));
/** @var rssfit\Helper $helper */
$helper = rssfit\Helper::getInstance();

/** @var Xmf\Module\Admin $adminObject */
$adminObject = \Xmf\Module\Admin::getInstance();

$thisModuleDir = $helper->getDirname();

// Load language files
$helper->loadLanguage('admin');

xoops_cp_header();


//if functions.php file exist

//require __DIR__ . '/../include/common.php';

