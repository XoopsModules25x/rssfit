<?php
//	$Id$
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
/*
* About this RSSFit plug-in
* Author: tuff <http://www.brandycoke.com/>
* Requirements (Tested with):
*  Module: WF-section <http://www.wf-projects.com/>
*  Version: 2.07 b3
*  RSSFit verision: 1.2 / 1.5
*  XOOPS version: 2.0.13.2 / 2.2.3
*/

if( !defined('RSSFIT_ROOT_PATH') ){ exit(); }
class RssfitWfsection2{
	var $dirname = 'wfsection';
	var $modname;
	var $grab;
	
	function RssfitWfsection2(){
	}
	
	function loadModule(){
		$mod =& $GLOBALS['module_handler']->getByDirname($this->dirname);
		if( !$mod || !$mod->getVar('isactive') || $mod->getVar('version') < 200 ){
			return false;
		}
		$this->modname = $mod->getVar('name');
		return $mod;
	}
	
	function &grabEntries(&$obj){
		@include_once XOOPS_ROOT_PATH.'/modules/wfsection/class/common.php';
		@include_once XOOPS_ROOT_PATH.'/modules/wfsection/class/wfsarticle.php';
		$ret = false;
		$articles = WfsArticle::getAllArticle($this->grab, 0, 'online');
		if( count($articles) > 0 ){
			$xoopsModuleConfig['shortartlen'] = 0;
			$myts =& MyTextSanitizer::getInstance();
			for( $i=0; $i<count($articles); $i++ ){
				$link = XOOPS_URL.'/modules/wfsection/article.php?articleid='.$articles[$i]->articleid();
				$ret[$i]['title'] = $myts->undoHtmlSpecialChars($articles[$i]->title());
				$ret[$i]['link'] = $link;
				$ret[$i]['guid'] = $link;
				$ret[$i]['timestamp'] = $articles[$i]->published();
				$ret[$i]['description'] = $articles[$i]->summary();
				$ret[$i]['category'] = $this->modname;
				$ret[$i]['domain'] = XOOPS_URL.'/modules/'.$this->dirname.'/';
			}
		}
		return $ret;
	}
}
?>