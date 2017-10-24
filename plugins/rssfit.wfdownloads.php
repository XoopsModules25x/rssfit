<?php
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
*  Module: WF-Downloads <http://www.wf-projects.com/>
*  Version: 2.0.5a
*  RSSFit verision: 1.2 / 1.5
*  XOOPS version: 2.0.13.2 / 2.2.3
*/

if (!defined('RSSFIT_ROOT_PATH')) {
    exit();
}
class Rssfitwfdownloads extends XoopsObject
{
    public $dirname = 'wfdownloads';
    public $modname;
    public $module;
    public $grab;

    public function loadModule()
    {
        $mod = $GLOBALS['module_handler']->getByDirname($this->dirname);
        if (!$mod || !$mod->getVar('isactive')) {
            return false;
        }
        $this->modname = $mod->getVar('name');
        $this->module = $mod;
        return $mod;
    }

    public function &grabEntries(&$obj)
    {
        global $xoopsDB;
        $myts = MyTextSanitizer::getInstance();
        $perm_handler = xoops_getHandler('groupperm');
        $ret = false;
        $i = 0;
        $sql = "SELECT lid, cid, title, date, description FROM ".$xoopsDB->prefix("wfdownloads_downloads")." WHERE status > 0 AND offline = 0 ORDER BY date DESC";
        $result = $xoopsDB->query($sql, $this->grab, 0);
        while ($row = $xoopsDB->fetchArray($result)) {
            if ($perm_handler->checkRight('WFDownFilePerm', $row['lid'], is_object($GLOBALS['xoopsUser']) ? $GLOBALS['member_handler']->getGroupsByUser($GLOBALS['xoopsUser']->getVar('uid')) : XOOPS_GROUP_ANONYMOUS, $this->module->getVar('mid'))) {
                $ret[$i]['title'] = $row['title'];
                $link = XOOPS_URL.'/modules/'.$this->dirname.'/singlefile.php?cid='.$row['cid'].'&amp;lid='.$row['lid'];
                $ret[$i]['link'] = $ret[$i]['guid'] = $link;
                $ret[$i]['timestamp'] = $row['date'];
                $ret[$i]['description'] = $myts->displayTarea($row['description']);
                $ret[$i]['category'] = $this->modname;
                $ret[$i]['domain'] = XOOPS_URL.'/modules/'.$this->dirname.'/';
                $i++;
            }
        }
        return $ret;
    }
}
