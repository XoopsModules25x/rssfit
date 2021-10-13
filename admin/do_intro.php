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
 * @package      RSSFit - Extendable XML news feed generator
 * @author       NS Tai (aka tuff) <http://www.brandycoke.com>
 * @author       XOOPS Development Team
 */

use Xmf\Request;
use XoopsModules\Rssfit\{
    MiscHandler
};
/** @var MiscHandler $miscHandler */

if (!preg_match('#/rssfit/admin/#', $_SERVER['SCRIPT_NAME'])) {
    header('Location: index.php');
}

$intr = $miscHandler->getObjects2(new \Criteria('misc_category', 'intro'));
if ($intr) {
    $intro = $intr[0];
    unset($intr);
} else {
    $intro = $miscHandler->create();
}
switch ($op) {
    default:
        $title = new \XoopsFormText(_AM_EDIT_INTRO_TITLE, 'title', 50, 255, $intro->getVar('misc_title', 'e'));
        $title->setDescription(_AM_EDIT_INTRO_TITLE_DESC);

        $setting     = $intro->getVar('misc_setting');
        $contentTray = new \XoopsFormElementTray(_AM_RSSFIT_EDIT_INTRO_TEXT, '<br>');
        $contentTray->setDescription(_AM_RSSFIT_EDIT_INTRO_TEXT_DESC . _AM_RSSFIT_EDIT_INTRO_TEXT_DESC_SUB);
        $contentTray->addElement(new \XoopsFormDhtmlTextArea('', 'content', $intro->getVar('misc_content', 'e'), 15, 60));
        $dohtml = new \XoopsFormCheckbox('', 'dohtml', $setting['dohtml']??'');
        $dohtml->addOption(1, _AM_RSSFIT_DO_HTML);
        $contentTray->addElement($dohtml);
        $dobr = new \XoopsFormCheckbox('', 'dobr', $setting['dobr']??'');
        $dobr->addOption(1, _AM_RSSFIT_DO_BR);
        $contentTray->addElement($dobr);

        $sub = new \XoopsFormTextArea(_AM_RSSFIT_EDIT_INTRO_SUB, 'sub', htmlspecialchars($setting['sub']??'', ENT_QUOTES | ENT_HTML5));
        $sub->setDescription(_AM_RSSFIT_EDIT_INTRO_SUB_DESC);

        $form = new \XoopsThemeForm(_AM_RSSFIT_EDIT_INTRO, 'editintro', RSSFIT_ADMIN_URL);
        $form->addElement($title);
        $form->addElement($contentTray);
        $form->addElement($sub);
        $form->addElement($saveCancelTray);
        $form->addElement($hiddenDo);
        $form->addElement(new \XoopsFormHidden('op', 'save'));
        $form->display();
        break;
    case 'save':
        $intro->setVar('misc_category', 'intro');
        $intro->setVar('misc_title', trim($_POST['title']));
        $intro->setVar('misc_content', $_POST['content']);
        $setting = [
            'dohtml' => isset($_POST['dohtml']) ? 1 : 0,
            'dobr'   => isset($_POST['dobr']) ? 1 : 0,
            'sub'    => isset($_POST['sub']) ? trim($_POST['sub']) : '',
        ];
        $intro->setVar('misc_setting', $setting);
        if (false === $miscHandler->insert($intro)) {
            echo $intro->getHtmlErrors();
        } else {
            redirect_header(RSSFIT_ADMIN_URL . '?do=' . $do, 0, _AM_RSSFIT_DBUPDATED);
        }
        break;
}
