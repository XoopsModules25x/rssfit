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

/**
 * @param $xoopsMod
 *
 * @return bool
 */
function xoops_module_install_rss(&$xoopsMod){
	global $xoopsDB, $xoopsConfig;
	$myts = MyTextSanitizer::getInstance();
	rssfInstallLangFile($xoopsMod, $xoopsConfig['language']);
	$intro_setting = array('dohtml'=>1, 'dobr'=>1, 'sub'=>stripslashes(_INSTALL_INTRO_SUB));
	$sql[] = "INSERT INTO `".$xoopsDB->prefix('rssfit_misc')."` VALUES (1, ".$xoopsDB->quoteString('intro').", ".$xoopsDB->quoteString(stripslashes(_INTRO_TITLE)).", ".$xoopsDB->quoteString(stripslashes(_INTRO_CONTENT)).", ".$xoopsDB->quoteString(serialize($intro_setting)).")";
	$sql[] = rssfInsertChannel($xoopsMod);
	$sql[] = "INSERT INTO ".$xoopsDB->prefix('rssfit_misc')." VALUES "."('', 'sticky', '', '', ".$xoopsDB->quoteString(serialize(array('dohtml'=>0, 'dobr'=>0, 'feeds'=>array(0=>'0'), 'link'=>XOOPS_URL))).")";
	foreach( $sql as $s ){
		if( false == $xoopsDB->query($s) ){
			echo '<span style="color: #ff0000;"><b>'.$xoopsDB->error().'<b></span><br />'.$s.'<br /><br />';
			return false;
		}
	}
	return true;
}

/**
 * @param $xoopsMod
 * @param $oldversion
 *
 * @return bool
 */
function xoops_module_update_rss(&$xoopsMod, $oldversion){
	global $xoopsDB, $xoopsConfig;
	rssfInstallLangFile($xoopsMod, $xoopsConfig['language']);
	list($rows) = $xoopsDB->fetchRow($xoopsDB->query("SELECT COUNT(*) FROM ".$xoopsDB->prefix('rssfit_misc')." WHERE misc_category = 'channel'"));
	if( !$rows ){
		$sql[] = 'ALTER TABLE `'.$xoopsDB->prefix('rssfit_misc').'` ADD `misc_setting` TEXT NOT NULL;';
		$sql[] = 'ALTER TABLE `'.$xoopsDB->prefix('rssfit_misc').'` CHANGE `misc_category` `misc_category` VARCHAR( 30 ) NOT NULL;';
		$intro_setting = array('dohtml'=>1, 'dobr'=>1, 'sub'=>_INSTALL_INTRO_SUB);
		$sql[] = "UPDATE `".$xoopsDB->prefix('rssfit_misc')."` SET misc_setting = ".$xoopsDB->quoteString(serialize($intro_setting))." WHERE misc_category = 'intro'";
		$sql[] = "ALTER TABLE `".$xoopsDB->prefix('rssfit_plugins')."` ADD `subfeed` TINYINT( 1 ) DEFAULT '0' NOT NULL, ADD `sub_entries` VARCHAR( 2 ) NOT NULL, ADD `sub_link` VARCHAR( 255 ) NOT NULL, ADD `sub_title` VARCHAR( 255 ) NOT NULL, ADD `sub_desc` VARCHAR( 255 ) NOT NULL, ADD `img_url` VARCHAR( 255 ) NOT NULL, ADD `img_link` VARCHAR( 255 ) NOT NULL, ADD `img_title` VARCHAR( 255 ) NOT NULL;";
		$sql[] = "UPDATE `".$xoopsDB->prefix('rssfit_plugins')."` SET sub_entries = 5";
		$sql[] = rssfInsertChannel($xoopsMod);
		$sql[] = "INSERT INTO ".$xoopsDB->prefix('rssfit_misc')." VALUES "."('', 'sticky', '', '', ".$xoopsDB->quoteString(serialize(array('dohtml'=>0, 'dobr'=>0, 'feeds'=>array(0=>'0'), 'link'=>XOOPS_URL))).")";
		foreach( $sql as $s ){
			if( false == $xoopsDB->query($s) ){
				echo '<span style="color: #ff0000;"><b>'.$xoopsDB->error().'<b></span><br />'.$s.'<br /><br />';
				return false;
			}
		}
	}
	return true;
}

/**
 * @param $xoopsMod
 * @param $lang
 */
function rssfInstallLangFile(&$xoopsMod, $lang){
	$file = XOOPS_ROOT_PATH.'/modules/'.$xoopsMod->getVar('dirname')
			.'/language/%s/install.php';
	if( file_exists(sprintf($file, $lang)) ){
		include(sprintf($file, $lang));
	}else{
		include(sprintf($file, 'english'));
	}
}

/**
 * @param $xoopsMod
 *
 * @return string
 */
function rssfInsertChannel(&$xoopsMod){
	global $xoopsDB, $xoopsConfig;
	$url = $xoopsDB->quoteString(XOOPS_URL);
	$sitename = $xoopsDB->quoteString($xoopsConfig['sitename']);
	list($copyright) = $xoopsDB->fetchRow($xoopsDB->query("SELECT conf_value FROM ".$xoopsDB->prefix('config')." WHERE conf_name = 'meta_copyright' AND conf_modid = 1 AND conf_catid = ".XOOPS_CONF_METAFOOTER));
	return "INSERT INTO ".$xoopsDB->prefix('rssfit_misc')
			." VALUES "."('', 'channel', 'title', ".$sitename.", '')"
			.", ('', 'channel', 'link', ".$url.", '')"
			.", ('', 'channel', 'description', "
			.$xoopsDB->quoteString($xoopsConfig['slogan'])
			.", ''), ('', 'channel', 'copyright', "
			.$xoopsDB->quoteString($copyright)
			.", ''), ('', 'channel', 'managingEditor', "
			.$xoopsDB->quoteString($xoopsConfig['adminmail']
			.' ('.$xoopsConfig['sitename'].')')
			.", ''), ('', 'channel', 'webMaster', "
			.$xoopsDB->quoteString($xoopsConfig['adminmail']
			.' ('.$xoopsConfig['sitename'].')').", '')"
			.", ('', 'channel', 'category', '', '')"
			.", ('', 'channel', 'generator', "
			.$xoopsDB->quoteString(XOOPS_VERSION
			.' / RSSFit '.$xoopsMod->getInfo('version'))
			.", ''), ('', 'channel', 'docs', "
			.$xoopsDB->quoteString('http://blogs.law.harvard.edu/tech/rss').", '')"
			.", ('', 'channelimg', 'url', "
			.$xoopsDB->quoteString(XOOPS_URL.'/images/logo.gif').", '')"
			.", ('', 'channelimg', 'title', ".$sitename.", '')"
			.", ('', 'channelimg', 'link', ".$url.", '')"
			.";";
}

