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
 *  Module: Surnames https://github.com/geekwright/surnames
 *  Version: 1.0
 *  RSSFit verision: 1.3
 *  XOOPS version: 2.5.9
 */

if (!defined('RSSFIT_ROOT_PATH')) {
    exit();
}

class RssfitSurnames
{
    public $dirname = 'surnames';
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

    public function myGetUnameFromId($uid)
    {
        static $thisUser=false;
        static $lastUid=false;
        static $lastName='';

        if ($lastUid==$uid) {
            return $lastName;
        }

        if (!is_object($thisUser)) {
            $memberHandler = xoops_getHandler('member');
            $thisUser = $memberHandler->getUser($uid);
        }
        $name = htmlspecialchars($thisUser->getVar('name'));
        if ('' == $name) {
            $name = htmlspecialchars($thisUser->getVar('uname'));
        }
        $lastUid=$uid;
        $lastName=$name;
        return $name;
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

        $sql = "SELECT uid, id, surname, notes, DATE_FORMAT(changed_ts,'%Y-%m-%d') as changedate FROM ".$xoopsDB->prefix('surnames');
        $sql .= ' WHERE approved=1 ORDER BY changedate DESC, uid ';
        $result = $xoopsDB->query($sql, $limit, 0);
        while ($row = $xoopsDB->fetchArray($result)) {
            $changedate=strtotime($row['changedate']);
            $uid=$row['uid'];
            if ($lasttime==$changedate && $lastuser==$uid) {
                $link = XOOPS_URL.'/modules/surnames/view.php?id='.$row['id'];
                $surname=$row['surname'];
                $desc .= "<a href=\"$link\">$surname</a><br />";
            } else {
                if ($i>=0) {
                    $ret[$i]['description'] = $desc;
                }
                ++$i;
                $lasttime=$changedate;
                $lastuser=$uid;
                if ($i<=$this->grab) {
                    $desc= '';
                    $name = $this->myGetUnameFromId($row['uid']);
                    $ret[$i]['title'] = $this->modname . ': by ' . $name;
                    $ret[$i]['link'] = XOOPS_URL.'/modules/surnames/list.php?uid='.$row['uid'];
                    $ret[$i]['timestamp'] = $changedate;

                    $link = XOOPS_URL.'/modules/surnames/view.php?id='.$row['id'];
                    $ret[$i]['guid'] = $link;
                    $ret[$i]['category'] = $this->modname;

                    $surname=$row['surname'];
                    $desc .= "<a href=\"$link\">$surname</a><br />";
                }
            }
            if ($i>$this->grab) {
                break;
            }
        }
        if ($i<$this->grab) {
            $ret[$i]['description'] = $desc;
        }
        return $ret;
    }
}
