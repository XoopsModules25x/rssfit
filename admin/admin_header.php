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
 * @package
 * @since
 * @author       XOOPS Development Team
 */
include dirname(__DIR__) . '/preloads/autoloader.php';

require dirname(dirname(dirname(__DIR__))) . '/include/cp_header.php';
require dirname(__DIR__) . '/include/common.php';

$moduleDirName = basename(dirname(__DIR__));

$helper = \XoopsModules\Rssfit\Helper::getInstance();

/** @var \Xmf\Module\Admin $adminObject */
$adminObject = \Xmf\Module\Admin::getInstance();

//$thisModuleDir = $GLOBALS['xoopsModule']->getVar('dirname');

// Load language files
$helper->loadLanguage('admin');
$helper->loadLanguage('modinfo');
$helper->loadLanguage('common');

xoops_cp_header();
