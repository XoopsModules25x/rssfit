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
/*
* About this RSSFit plug-in
* Author: jayjay <http://www.sint-niklaas.be/>
* Requirements (Tested with):
*  Module: piCal <http://www.xoops.org/>
*  Version: 1.0
*  RSSFit version: 1.21
*  XOOPS version: 2.0.18.1
*/

if( !defined('RSSFIT_ROOT_PATH') ){ exit(); }

/**
 * Class RssfitpiCal
 */
class RssfitpiCal{
	var $dirname = 'piCal';
	var $modname;
	var $grab;

	function RssfitpiCal(){
	}

    /**
     * @return bool
     */
    function loadModule(){
		$mod =& $GLOBALS['module_handler']->getByDirname($this->dirname);
		if( !$mod || !$mod->getVar('isactive') ){
			return false;
		}
		$this->modname = $mod->getVar('name');
		return $mod;
	}

    /**
     * @param $obj
     *
     * @return bool
     */function &grabEntries(&$obj){
		global $xoopsDB;
		$myts =& MyTextSanitizer::getInstance();
		$ret = false;
		$i = 0;
		$sql = "SELECT id, uid, summary, location, description, categories, start, end, UNIX_TIMESTAMP(dtstamp) as dtstamp FROM ".$xoopsDB->prefix("pical_event")." WHERE admission>0 AND (rrule_pid=0 OR rrule_pid=id) ORDER BY dtstamp DESC";
		$result = $xoopsDB->query($sql, $this->grab, 0);
		while( $row = $xoopsDB->fetchArray($result) ){
			$ret[$i]['title'] = $row['summary'];
			$link = XOOPS_URL.'/modules/'.$this->dirname.'/index.php?event_id='.$row['id'];
			$ret[$i]['link'] = $ret[$i]['guid'] = $link;
			$ret[$i]['timestamp'] = $row['dtstamp'];
			$ret[$i]['description'] = $myts->displayTarea($row['description']);
			$ret[$i]['category'] = $this->modname;
			$ret[$i]['domain'] = XOOPS_URL.'/modules/'.$this->dirname.'/';
			$i++;
		}
		return $ret;
	}
}
