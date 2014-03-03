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

if( !defined("RSSFIT_OK") ){
	header('Location: index.php');
}

switch($op){
default:
//	rssfitAdminHeader();
	if( $elements =& $rss->mHandler->getObjects(new Criteria('misc_category', 'channel'), '*', 'title') && $img =& $rss->mHandler->getObjects(new Criteria('misc_category', 'channelimg'), '*', 'title') ){
		$form = new XoopsThemeForm(_AM_EDIT_CHANNEL, 'editchannel', RSSFIT_ADMIN_URL);
		$form->addElement(new XoopsFormLabel('', '<b>'._AM_EDIT_CHANNEL_REQUIRED.'</b> '.genSpecMoreInfo('req', $rss)));
		$form->addElement(new XoopsFormText('title', 'ele['.$elements['title']->getVar('misc_id').']', 50, 255, $elements['title']->getVar('misc_content', 'e')), true);
		$form->addElement(new XoopsFormText('link', 'ele['.$elements['link']->getVar('misc_id').']', 50, 255, $elements['link']->getVar('misc_content', 'e')), true);
		$form->addElement(new XoopsFormTextArea('description', 'ele['.$elements['description']->getVar('misc_id').']', $elements['description']->getVar('misc_content', 'e')), true);

		$form->addElement(new XoopsFormLabel('', '<b>'._AM_EDIT_CHANNEL_OPTIONAL.'</b> '.genSpecMoreInfo('opt', $rss)));
		$form->addElement(new XoopsFormText('copyright', 'ele['.$elements['copyright']->getVar('misc_id').']', 50, 255, $elements['copyright']->getVar('misc_content', 'e')));
		$form->addElement(new XoopsFormText('managingEditor', 'ele['.$elements['managingEditor']->getVar('misc_id').']', 50, 255, $elements['managingEditor']->getVar('misc_content', 'e')));
		$form->addElement(new XoopsFormText('webMaster', 'ele['.$elements['webMaster']->getVar('misc_id').']', 50, 255, $elements['webMaster']->getVar('misc_content', 'e')));
		$form->addElement(new XoopsFormText('category', 'ele['.$elements['category']->getVar('misc_id').']', 50, 255, $elements['category']->getVar('misc_content', 'e')));
		$form->addElement(new XoopsFormText('generator', 'ele['.$elements['generator']->getVar('misc_id').']', 50, 255, $elements['generator']->getVar('misc_content', 'e')));
		$form->addElement(new XoopsFormText('docs', 'ele['.$elements['docs']->getVar('misc_id').']', 50, 255, $elements['docs']->getVar('misc_content', 'e')));

		$form->addElement(new XoopsFormLabel('', '<b>'._AM_EDIT_CHANNEL_IMAGE.'</b> '.genSpecMoreInfo('img', $rss)));
		$form->addElement(new XoopsFormText('url', 'ele['.$img['url']->getVar('misc_id').']', 50, 255, $img['url']->getVar('misc_content', 'e')));
		$form->addElement(new XoopsFormText('link', 'ele['.$img['link']->getVar('misc_id').']', 50, 255, $img['link']->getVar('misc_content', 'e')));
		$form->addElement(new XoopsFormText('title', 'ele['.$img['title']->getVar('misc_id').']', 50, 255, $img['title']->getVar('misc_content', 'e')));

		$form->addElement($tray_save_cancel);
		$form->addElement($hidden_do);
		$form->addElement(new XoopsFormHidden('op', 'save'));
		$form->display();
	}else{
		echo '<p>'._AM_DB_RECORD_MISSING.'</p>';
	}
break;
case 'save':
	extract($_POST);
	$ids = array_keys($ele);
	$errors = array();
	foreach( $ids as $i ){
		$criteria = new Criteria('misc_id', $i);
		$fields = array('misc_content' => trim($ele[$i]));
		if( $err = $rss->mHandler->modifyObjects($criteria, $fields) ){
			$errors[] = $err;
		}
	}
	if( count($errors) > 0 ){
//		rssfitAdminHeader();
		foreach( $errors as $e ){
			echo $e."<br /><br />\n";
		}
	}else{
		redirect_header(RSSFIT_ADMIN_URL.'?do='.$do, 0, _AM_DBUPDATED);
	}
break;
}

include_once 'admin_footer.php';
