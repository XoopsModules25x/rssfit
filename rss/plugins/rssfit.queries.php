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
 * Author: Richard Griffith <richard@geekwright.com>
 * Requirements (or Tested with):
 *  Module: Queries https://github.com/geekwright/queries
 *  Version: 1.0
 *  RSSFit verision: 1.3
 *  XOOPS version: 2.5.9
 */

if (!defined('RSSFIT_ROOT_PATH')) {
    exit();
}

class RssfitQueries
{
    public $dirname = 'queries';
    public $modname;
    public $grab;
    public $module;

    public function loadModule()
    {
        $mod = $GLOBALS['module_handler']->getByDirname($this->dirname);
        if (!$mod || !$mod->getVar('isactive')) {
            return false;
        }
        $this->modname = $mod->getVar('name');
        $this->module = $mod;    // optional, remove this line if there is nothing
                                 // to do with module info when grabbing entries
        return $mod;
    }

    public function &grabEntries(&$obj)
    {
        global $xoopsDB;
        $myts = MyTextSanitizer::getInstance();
        $ret = false;

        $i = -1;
        $lasttime=false;
        $lastuser=false;
        $limit=10*$this->grab;

        $sql = "SELECT id, title, posted, querytext FROM ".$xoopsDB->prefix('queries_query');
        $sql.=" WHERE approved=1 ORDER BY posted DESC ";

        $result = $xoopsDB->query($sql, $limit, 0);
        while ($row = $xoopsDB->fetchArray($result)) {
            ++$i;
            if ($i<=$this->grab) {
                $desc=$row['querytext'];
                if (strlen($desc)>200) {
                    $desc=substr($desc, 0, 200).'...';
                }
                $link = XOOPS_URL.'/modules/queries/view.php?id='.$row['id'];
                $ret[$i]['title'] = ($this->modname).': '.$row['title'];
                $ret[$i]['link'] = $link;
                $ret[$i]['timestamp'] = $row['posted'];
                $ret[$i]['guid'] = $link;
                $ret[$i]['category'] = $this->modname;
                $ret[$i]['description'] = $desc;
            }
            if ($i>$this->grab) {
                break;
            }
        }
        return $ret;
    }
}
