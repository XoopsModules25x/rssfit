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
* Author: tuff <http://www.brandycoke.com/>
* Requirements:
* Requirements (Tested with):
*  Module: SmartPartner <http://www.smartfactory.ca/>
*  Version: 1.02
*  RSSFit verision: 1.2 / 1.5
*  XOOPS version: 2.0.13.2 / 2.2.3
*/

if( !defined('RSSFIT_ROOT_PATH') ){ exit(); }

/**
 * Class RssfitSmartpartner
 */
class RssfitSmartpartner{
	var $dirname = 'smartpartner';
	var $modname;
	var $grab;

	function RssfitSmartpartner(){
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
		$ret = false;
		include XOOPS_ROOT_PATH."/modules/smartpartner/include/common.php";
		$partners =& $partner_handler->getPartners($this->grab, 0, _SPARTNER_STATUS_ACTIVE, 'weight', 'DESC');
		if( false != $partners && count($partners) > 0 ){
			for( $i=0; $i<count($partners); $i++ ){
				$ret[$i]['link'] = $ret[$i]['guid'] = SMARTPARTNER_URL.'partner.php?id='.$partners[$i]->getVar('id');
				$ret[$i]['title'] = $partners[$i]->getVar('title', 'n');
				$ret[$i]['description'] = $partners[$i]->getVar('summary');
				$ret[$i]['category'] = $this->modname;
				$ret[$i]['domain'] = XOOPS_URL.'/modules/'.$this->dirname.'/';
			}
		}
		return $ret;
	}
}
