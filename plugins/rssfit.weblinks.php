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
/*
* About this RSSFit plug-in
* Author: tuff <http://www.brandycoke.com/>
* Requirements (Tested with):
*  Module: MyLinks <http://www.xoops.org/>
*  Version: 1.1
*  RSSFit verision: 1.2 / 1.5
*  XOOPS version: 2.0.13.2 / 2.2.3
*/

if (!defined('RSSFIT_ROOT_PATH')) {
    exit();
}
class RssfitWeblinks
{
    public $dirname = 'weblinks';
    public $modname;
    public $grab;

    public function loadModule()
    {
        $mod = $GLOBALS['module_handler']->getByDirname($this->dirname);
        if (!$mod || !$mod->getVar('isactive')) {
            return false;
        }
        $this->modname = $mod->getVar('name');
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
            $member_handler = xoops_getHandler('member');
            $thisUser = $member_handler->getUser($uid);
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
        $i = 0;
        $sql = 'SELECT lid, title, time_update, description, url, uid FROM ' . $xoopsDB->prefix('weblinks_link') . '  ORDER BY time_update DESC';
        $result = $xoopsDB->query($sql, $this->grab, 0);
        while ($row = $xoopsDB->fetchArray($result)) {
            $title=$row['title'];
            $name = $this->myGetUnameFromId($row['uid']);
            $ret[$i]['title'] = $this->modname . ': ' . $title;
            $link = XOOPS_URL.'/modules/'.$this->dirname.'/singlelink.php?lid='.$row['lid'].'&amp;keywords=';
            $ret[$i]['link'] = $link;
            $ret[$i]['timestamp'] = $row['time_update'];
            $desc = '<p><a href="'.$row['url'].'"><b>'.$title.'</b></a><br /> ';
            $desc .= 'Submitted by: <i>'.$name.'</i><br />';
            $desc .= $myts->displayTarea($row['description']).'</p><br clear="all"/>';
            $ret[$i]['description'] = $desc;
            $ret[$i]['guid'] = $link;
            $ret[$i]['category'] = $this->modname;
            $ret[$i]['domain'] = XOOPS_URL.'/modules/'.$this->dirname.'/';
            $i++;
        }
        return $ret;
    }
}
