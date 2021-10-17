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
 * @package      RSSFit - Extendable XML news feed generator
 * @author       NS Tai (aka tuff) <http://www.brandycoke.com>
 * @author       XOOPS Development Team
 */

use Xmf\Request;
use XoopsModules\Rssfit;

if (!preg_match('#/rssfit/admin/#', $_SERVER['SCRIPT_NAME'])) {
    header('Location: index.php');
}

$intr = $miscHandler->getObjects2(new \Criteria('misc_category', 'sticky'));
if ($intr) {
    $sticky = $intr[0];
    unset($intr);
} else {
    $sticky = $miscHandler->create();
}
switch ($op) {
    default:
        $setting = $sticky->getVar('misc_setting');
        $title = new \XoopsFormText(_AM_RSSFIT_STICKY_TITLE, 'title', 50, 255, $sticky->getVar('misc_title', 'e'));
        $title->setDescription(_AM_RSSFIT_EDIT_INTRO_TITLE_DESC);

        $contentTray = new \XoopsFormElementTray(_AM_RSSFIT_STICKY_CONTENT, '<br>');
        $contentTray->setDescription(_AM_RSSFIT_EDIT_INTRO_TEXT_DESC);
        $content = new \XoopsFormTextArea('', 'content', $sticky->getVar('misc_content', 'e'), 10);
        $contentTray->addElement($content);
        $dohtml = new \XoopsFormCheckBox('', 'dohtml', $setting['dohtml']);
        $dohtml->addOption(1, _AM_RSSFIT_DO_HTML);
        $contentTray->addElement($dohtml);
        $dobr = new \XoopsFormCheckBox('', 'dobr', $setting['dobr']);
        $dobr->addOption(1, _AM_RSSFIT_DO_BR);
        $contentTray->addElement($dobr);

        $link = new \XoopsFormText(_AM_RSSFIT_STICKY_LINK, 'link', 50, 255,  htmlspecialchars($setting['link']??'', ENT_QUOTES | ENT_HTML5));

        $applyto = $feedHandler->feedSelectBox(_AM_RSSFIT_STICKY_APPLYTO, $setting['feeds']??null, 10);

        $form = new \XoopsThemeForm(_AM_RSSFIT_STICKY_EDIT, 'editsticky', RSSFIT_ADMIN_URL);
        $form->addElement($title);
        $form->addElement($contentTray);
        $form->addElement($link);
        $form->addElement($applyto);
        $form->addElement($saveCancelTray);
        $form->addElement($hiddenDo);
        $form->addElement(new \XoopsFormHidden('op', 'save'));
        $form->display();
        break;
    case 'save':
        $sticky->setVar('misc_category', 'sticky');
        $sticky->setVar('misc_title', trim($_POST['title']));
        $sticky->setVar('misc_content', $_POST['content']);
        if (!isset($_POST['feeds']) || count($_POST['feeds']) < 1 || in_array(0, $_POST['feeds'])) {
            $feeds = ['0' => 0];
        } else {
            $feeds = $_POST['feeds'];
        }
        $setting = [
            'dohtml' => isset($_POST['dohtml']) ? 1 : 0,
            'dobr'   => isset($_POST['dobr']) ? 1 : 0,
            'feeds'  => $feeds,
            'link'   => isset($_POST['link']) ? trim($_POST['link']) : '',
        ];
        $sticky->setVar('misc_setting', $setting, true);
        if (false !== $miscHandler->insert($sticky)) {
            redirect_header(RSSFIT_ADMIN_URL . '?do=' . $do, 0, _AM_RSSFIT_DBUPDATED);
        } else {
            echo $sticky->getHtmlErrors();
        }
        break;
}
