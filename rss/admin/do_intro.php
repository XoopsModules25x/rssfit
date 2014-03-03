<?php
// $Id$
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

include_once dirname(__FILE__) . '/admin_header.php';
require_once dirname(dirname(__FILE__)) . '/include/common.php';

if( !defined("RSSFIT_OK") ){
	header('Location: main.php');
}

if( $intr =& $misc_handler->getObjects(new Criteria('misc_category', 'intro')) ){
	$intro =& $intr[0];
	unset($intr);
}else{
	$intro =& $misc_handler->create();
}
switch($op){
default:
//	rssfitAdminHeader();


	$title = new XoopsFormText(_AM_EDIT_INTRO_TITLE, 'title', 50, 255, $intro->getVar('misc_title', 'e'));
	$title->setDescription(_AM_EDIT_INTRO_TITLE_DESC);

	$setting = $intro->getVar('misc_setting');
	$tray_content = new XoopsFormElementTray(_AM_EDIT_INTRO_TEXT, '<br />');
	$tray_content->setDescription(_AM_EDIT_INTRO_TEXT_DESC._AM_EDIT_INTRO_TEXT_DESC_SUB);
	$tray_content->addElement(new XoopsFormDhtmlTextArea('', 'content', $intro->getVar('misc_content', 'e'), 15, 60));
	$dohtml = new XoopsFormCheckbox('', 'dohtml', $setting['dohtml']);
	$dohtml->addOption(1, _AM_DO_HTML);
	$tray_content->addElement($dohtml);
	$dobr = new XoopsFormCheckbox('', 'dobr', $setting['dobr']);
	$dobr->addOption(1, _AM_DO_BR);
	$tray_content->addElement($dobr);

	$sub = new XoopsFormTextArea(_AM_EDIT_INTRO_SUB, 'sub', htmlSpecialChars($myts->stripSlashesGPC($setting['sub'])));
	$sub->setDescription(_AM_EDIT_INTRO_SUB_DESC);

	$form = new XoopsThemeForm(_AM_EDIT_INTRO, 'editintro', RSSFIT_ADMIN_URL);
	$form->addElement($title);
	$form->addElement($tray_content);
	$form->addElement($sub);
	$form->addElement($tray_save_cancel);
	$form->addElement($hidden_do);
	$form->addElement(new XoopsFormHidden('op', 'save'));
	$form->display();
break;

case 'save':
	$intro->setVar('misc_category', 'intro');
	$intro->setVar('misc_title', trim($_POST['title']));
	$intro->setVar('misc_content', $_POST['content']);
	$setting = array( 'dohtml' => isset($_POST['dohtml']) ? 1 : 0,
					  'dobr' => isset($_POST['dobr']) ? 1 : 0,
					  'sub' => isset($_POST['sub']) ? trim($_POST['sub']) : ''
					);
	$intro->setVar('misc_setting', $setting);
	if( false == $misc_handler->insert($intro) ){
//		rssfitAdminHeader();
		echo $intro->getHtmlErrors();
	}else{
		redirect_header(RSSFIT_ADMIN_URL.'?do='.$do, 0, _AM_DBUPDATED);
	}
break;
}

include_once 'admin_footer.php';
