<?php
###############################################################################
##                RSSFit - Extendable XML news feed generator                ##
##                Copyright (c) 2004 - 2006 NS Tai (aka tuff)                ##
##                       <http://www.brandycoke.com/>                        ##
###############################################################################
##                    XOOPS - PHP Content Management System                  ##
##                       Copyright (c) 2000 XOOPS.org                        ##
##                          <http://www.xoops.org/>                          ##
###############################################################################
##  This program is free software; you can redistribute it and/or modify     ##
##  it under the terms of the GNU General Public License as published by     ##
##  the Free Software Foundation; either version 2 of the License, or        ##
##  (at your option) any later version.                                      ##
##                                                                           ##
##  You may not change or alter any portion of this comment or credits       ##
##  of supporting developers from this source code or any supporting         ##
##  source code which is considered copyrighted (c) material of the          ##
##  original comment or credit authors.                                      ##
##                                                                           ##
##  This program is distributed in the hope that it will be useful,          ##
##  but WITHOUT ANY WARRANTY; without even the implied warranty of           ##
##  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            ##
##  GNU General Public License for more details.                             ##
##                                                                           ##
##  You should have received a copy of the GNU General Public License        ##
##  along with this program; if not, write to the Free Software              ##
##  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA ##
###############################################################################
##  Author of this file: NS Tai (aka tuff)                                   ##
##  URL: http://www.brandycoke.com/                                          ##
##  Project: RSSFit                                                          ##
###############################################################################

use Xoopsmodules\rssfit;

include_once __DIR__ . '/admin_header.php';

if (!preg_match('#/rssfit/admin/#', $_SERVER['PHP_SELF'])) {
    header('Location: index.php');
}

if ($intr = $miscHandler->getObjects(new \Criteria('misc_category', 'sticky'))) {
    $sticky = $intr[0];
    unset($intr);
} else {
    $sticky = $miscHandler->create();
}
switch ($op) {
    default:
        $setting = $sticky->getVar('misc_setting');
        $title   = new \XoopsFormText(_AM_RSSFIT_STICKY_TITLE, 'title', 50, 255, $sticky->getVar('misc_title', 'e'));
        $title->setDescription(_AM_RSSFIT_EDIT_INTRO_TITLE_DESC);

        $tray_content = new \XoopsFormElementTray(_AM_RSSFIT_STICKY_CONTENT, '<br>');
        $tray_content->setDescription(_AM_RSSFIT_EDIT_INTRO_TEXT_DESC);
        $content = new \XoopsFormTextArea('', 'content', $sticky->getVar('misc_content', 'e'), 10);
        $tray_content->addElement($content);
        $dohtml = new \XoopsFormCheckbox('', 'dohtml', $setting['dohtml']);
        $dohtml->addOption(1, _AM_RSSFIT_DO_HTML);
        $tray_content->addElement($dohtml);
        $dobr = new \XoopsFormCheckbox('', 'dobr', $setting['dobr']);
        $dobr->addOption(1, _AM_RSSFIT_DO_BR);
        $tray_content->addElement($dobr);

        $link = new \XoopsFormText(_AM_RSSFIT_STICKY_LINK, 'link', 50, 255, $myts->htmlSpecialChars($myts->stripSlashesGPC($setting['link'])));

        $applyto = $rss->feedSelectBox(_AM_RSSFIT_STICKY_APPLYTO, $setting['feeds'], 10);

        $form = new \XoopsThemeForm(_AM_RSSFIT_STICKY_EDIT, 'editsticky', RSSFIT_ADMIN_URL);
        $form->addElement($title);
        $form->addElement($tray_content);
        $form->addElement($link);
        $form->addElement($applyto);
        $form->addElement($tray_save_cancel);
        $form->addElement($hidden_do);
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
            'link'   => isset($_POST['link']) ? trim($_POST['link']) : ''
        ];
        $sticky->setVar('misc_setting', $setting, true);
        if (false === $miscHandler->insert($sticky)) {
            echo $sticky->getHtmlErrors();
        } else {
            redirect_header(RSSFIT_ADMIN_URL . '?do=' . $do, 0, _AM_RSSFIT_DBUPDATED);
        }
        break;
}
