<?php

declare(strict_types=1);
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
 * @author      XOOPS Development Team
 */

use Xmf\Request;
use XoopsModules\Rssfit\{
    FeedHandler,
    PluginHandler,
    Common,
    Helper,
    Utility
};

/** @var Helper $helper */
/** @var Utility $utility */
/** @var \Xmf\Module\Admin $adminObject */
/** @var FeedHandler $feedHandler */
/** @var PluginHandler $pluginHandler */

require_once __DIR__ . '/admin_header.php';

$helper = Helper::getInstance();
$utility = new Utility();

$do = Request::getString('do', '');
$op = Request::getString('op', 'list');


//$do = \Xmf\Request::getString('do', '');
//$op = \Xmf\Request::getString('op',  \Xmf\Request::getString('op', 'list', 'GET'), 'POST');
//define('RSSFIT_OK', 1);

//$adminObject = \Xmf\Module\Admin::getInstance();

if (file_exists($helper->path('admin/do_' . $do . '.php'))) {
    require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';
    $hiddenDo     = new \XoopsFormHidden('do', $do);
    $buttonSave   = new \XoopsFormButton('', 'submit', _AM_RSSFIT_SAVE, 'submit');
    $buttonSubmit = new \XoopsFormButton('', 'submit', _GO, 'submit');
    $buttonCancel = new \XoopsFormButton('', 'cancel', _CANCEL);
    $buttonCancel->setExtra('onclick="javascript:history.go(-1)"');
    $saveCancelTray = new \XoopsFormElementTray('', '');
    $saveCancelTray->addElement($buttonSave);
    $saveCancelTray->addElement($buttonCancel);
    $adminObject->displayNavigation('?do=' . $do);
    require $helper->path('admin/do_' . $do . '.php');
} else {
    $adminObject->displayNavigation(basename(__FILE__));
    $adminObject->displayIndex();
    echo $utility::getServerStats();
}

require_once __DIR__ . '/admin_footer.php';
