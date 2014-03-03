<?php
// $Id$
###############################################################################
##                RSSFit - Extendable XML news feed generator                ##
##                   Copyright (c) 2004 NS Tai (aka tuff)                    ##
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
*  Module: WF-Downloads <http://smartyfactory.ca/>
*  Version: 3.1
*  RSSFit verision: 1.21
*  XOOPS version: 2.0.14
*/

if( !defined('RSSFIT_ROOT_PATH') ){ exit(); }
class Rssfitwfdownloads_podcast extends XoopsObject{
	var $dirname = 'wfdownloads';
	var $modname;
	var $module;
	var $grab;
	
	function Rssfitwfdownloads_podcast(){
	}
	
	function loadModule(){
		$mod =& $GLOBALS['module_handler']->getByDirname($this->dirname);
		if( !$mod || !$mod->getVar('isactive') || $mod->getVar('version') < 310 ){
			return false;
		}
		$this->modname = $mod->getVar('name');
		$this->module =& $mod;
		return $mod;
	}
	
	function &grabEntries(&$obj){
		global $xoopsDB;
		$myts =& MyTextSanitizer::getInstance();
		$perm_handler =& xoops_gethandler('groupperm');
		$ret = false;
		$i = 0;
		$sql = "SELECT lid, cid, title, date, description, filetype, size FROM ".$xoopsDB->prefix("wfdownloads_downloads")." WHERE status > 0 AND offline = 0 AND (expired > ".time()." OR expired = 0) AND published <= ".time()." ORDER BY date DESC";
		$result = $xoopsDB->query($sql, $this->grab, 0);
		while( $row = $xoopsDB->fetchArray($result) ){
			if( (isset($perms[$row['cid']]) && $perms[$row['cid']] == true) || $perm_handler->checkRight('WFDownCatPerm', $row['cid'], is_object($GLOBALS['xoopsUser']) ? $GLOBALS['member_handler']->getGroupsByUser($GLOBALS['xoopsUser']->getVar('uid')) : XOOPS_GROUP_ANONYMOUS, $this->module->getVar('mid')) ){
				$perms[$row['cid']] = true;
				$ret[$i]['title'] = $row['title'];
				$link = XOOPS_URL.'/modules/'.$this->dirname.'/singlefile.php?cid='.$row['cid'].'&amp;lid='.$row['lid'];
				$ret[$i]['link'] = $ret[$i]['guid'] = $link;
				$ret[$i]['timestamp'] = $row['date'];
				$ret[$i]['description'] = $myts->displayTarea($row['description']);
				$ret[$i]['category'] = $this->modname;
				$ret[$i]['domain'] = XOOPS_URL.'/modules/'.$this->dirname.'/';
			//	enclosure tag, a.k.a podcast
				$ret[$i]['extras']['enclosure']['attributes']
					= array('url' => XOOPS_URL.'/modules/'.$this->dirname.'/visit.php?cid='.$row['cid'].'&amp;lid='.$row['lid'],
							'length' => $row['size'],
							'type' => $row['filetype']
							);
				$i++;
			}else{
				$perms[$row['cid']] = false;
			}
		}
		return $ret;
	}
}
?>