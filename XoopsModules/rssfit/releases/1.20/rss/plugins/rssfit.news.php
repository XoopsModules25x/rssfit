<?php
// $Id$
###############################################################################
##                RSSFit - Extendable XML news feed generator                ##
##                Copyright (c) 2004 - 2005 NS Tai (aka tuff)                ##
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
/*
* About this RSSFit plug-in
* Author: tuff <http://www.brandycoke.com/>
* Requirements (Tested with):
*  Module: News <http://www.xoops.org/>
*  Version: 1.1 / 1.3 / 1.42 / 1.44
*  RSSFit verision: 1.2 / 1.5
*  XOOPS version: 2.0.13.2 / 2.2.3
*/

if( !defined('RSSFIT_ROOT_PATH') ){ exit(); }
class RssfitNews{
	var $dirname = 'news';
	var $modname;
	var $grab;
	
	function RssfitNews(){
	}
	
	function loadModule(){
		$mod =& $GLOBALS['module_handler']->getByDirname($this->dirname);
		if( !$mod || !$mod->getVar('isactive') ){
			return false;
		}
		$this->modname = $mod->getVar('name');
		$this->module =& $mod;
		return $mod;
	}
	
	function &grabEntries(&$obj){
		$ret = false;
		@include_once XOOPS_ROOT_PATH.'/modules/news/class/class.newsstory.php';
		$myts =& MyTextSanitizer::getInstance();
		if( $this->module->getVar('version') >= 130 ){
			@include_once XOOPS_ROOT_PATH.'/modules/news/include/functions.php';
			$news = NewsStory::getAllPublished($this->grab, 0, getmoduleoption('restrictindex'));
		}else{
			$news = NewsStory::getAllPublished($this->grab, 0);
		}
		if( count($news) > 0 ){
			for( $i=0; $i<count($news); $i++ ){
				$ret[$i]['title'] = $myts->undoHtmlSpecialChars($news[$i]->title());
				$ret[$i]['link'] = XOOPS_URL.'/modules/news/article.php?storyid='.$news[$i]->storyid();
				$ret[$i]['guid'] = XOOPS_URL.'/modules/news/article.php?storyid='.$news[$i]->storyid();
				$ret[$i]['timestamp'] = $news[$i]->published();
				$desc = $news[$i]->hometext();
				$ret[$i]['description'] = $news[$i]->hometext();
				$ret[$i]['category'] = $this->modname;
				$ret[$i]['domain'] = XOOPS_URL.'/modules/'.$this->dirname.'/';
			}
		}
		return $ret;
	}
}
?>