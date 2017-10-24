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

use Xmf\Module\Admin;
use Xmf\Request;

/**
 * @copyright    The XOOPS Project http://sourceforge.net/projects/xoops/
 * @license      GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package
 * @since
 * @author       XOOPS Development Team
 */


include_once __DIR__ . '/admin_header.php';
$moduleAdmin = Admin::getInstance();

$do = Request::getString('do', '');
$op = Request::getString('op', 'list');
define("RSSFIT_OK", 1);

if (file_exists(RSSFIT_ROOT_PATH.'admin/do_'.$do.'.php')) {
    include_once XOOPS_ROOT_PATH.'/class/xoopsformloader.php';
    $hidden_do = new XoopsFormHidden('do', $do);
    $button_save = new XoopsFormButton('', 'submit', _AM_SAVE, 'submit');
    $button_go = new XoopsFormButton('', 'submit', _GO, 'submit');
    $button_cancel = new XoopsFormButton('', 'cancel', _CANCEL);
    $button_cancel->setExtra('onclick="javascript:history.go(-1)"');
    $tray_save_cancel = new XoopsFormElementTray('', '');
    $tray_save_cancel->addElement($button_save);
    $tray_save_cancel->addElement($button_cancel);
    $moduleAdmin->displayNavigation('?do=' . $do);
    require RSSFIT_ROOT_PATH.'admin/do_'.$do.'.php';
} else {
    $moduleAdmin->displayNavigation('index.php');
    $moduleAdmin->displayIndex();
}

include_once 'admin_footer.php';
