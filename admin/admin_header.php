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
 * @copyright    XOOPS Project (https://xoops.org)
 * @license      GNU GPL 2 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 * @author       XOOPS Development Team
 */

use Xmf\Module\Admin;
use XoopsModules\Rssfit\{
    Helper
};

/** @var Helper $helper */
/** @var Admin $adminObject */

require \dirname(__DIR__) . '/preloads/autoloader.php';

require \dirname(__DIR__, 3) . '/include/cp_header.php';
require \dirname(__DIR__) . '/include/common.php';

$helper      = Helper::getInstance();
$adminObject = Admin::getInstance();

$moduleDirName      = $helper->getDirname();
$moduleDirNameUpper = mb_strtoupper($moduleDirName);

// Load language files
$helper->loadLanguage('admin');
$helper->loadLanguage('modinfo');
$helper->loadLanguage('common');

xoops_cp_header();
