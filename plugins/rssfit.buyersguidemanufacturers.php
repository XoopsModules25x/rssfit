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

/**
 * About this RSSFit plug-in
 * Author: Hervé Thouzard of Instant Zero (http://www.instant-zero.com)
 * Requirements (Tested with):
 *  Module: Buyersguide
 *  Version: 1.33
 * Flux RSS : Derniers Fabricants
 *  RSSFit verision: 1.22
 *  XOOPS version: 2.0.18.1
 */

if (!defined('RSSFIT_ROOT_PATH')) {
    exit();
}

/**
 * Class RssfitBuyersguidemanufacturers
 */
class RssfitBuyersguidemanufacturers
{
    public $dirname = 'buyersguide';
    public $modname;
    public $grab;

    /**
     * @return bool
     */
    public function loadModule()
    {
        $mod = $GLOBALS['module_handler']->getByDirname($this->dirname);
        if (!$mod || !$mod->getVar('isactive')) {
            return false;
        }
        $this->modname = $mod->getVar('name');
        return $mod;
    }

    /**
     * @param $obj
     * @return bool
     */
    public function &grabEntries(&$obj)
    {
        $ret = false;
        include XOOPS_ROOT_PATH . '/modules/buyersguide/include/common.php';
        $items = $hBgManufacturer->getListofActivteManufacturers(0, $this->grab, 'manu_date_added', 'DESC');
        $i     = 0;

        if (false !== $items && count($items) > 0) {
            foreach ($items as $item) {
                $ret[$i]['link']        = $ret[$i]['guid'] = $item->getLink();
                $ret[$i]['title']       = $item->getVar('manu_name', 'n');
                $ret[$i]['timestamp']   = $item->getVar('manu_date_added');
                $ret[$i]['description'] = $item->getVar('manu_description');
                $ret[$i]['category']    = $this->modname;
                $ret[$i]['domain']      = XOOPS_URL . '/modules/' . $this->dirname . '/';
                $i++;
            }
        }
        return $ret;
    }
}
