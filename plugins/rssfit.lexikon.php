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

/**
 * About this RSSFit plug-in
 * Requirements (Tested with):
 *  Module: Wordbook <http://dev.xoops.org/modules/xfmod/project/?wordbook>
 *  Version: 1.17
 *  RSSFit version: 1.1 / 1.5
 *  XOOPS verson: 2.0.13.2 / 2.2.3 (!)
 */

if (!defined('RSSFIT_ROOT_PATH')) {
    exit();
}

/**
 * Class Rssfitlexikon
 */
class Rssfitlexikon extends XoopsObject
{
    public $dirname = 'lexikon';
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
        //$perm_handler = xoops_gethandler('groupperm');
        $ret = false;
        $i = 0;
        $sql = "SELECT entryID, categoryID, term, definition, datesub FROM ".$xoopsDB->prefix("lxentries")." WHERE submit = 0 AND offline = 0 ORDER BY datesub DESC";
        $result = $xoopsDB->query($sql, $this->grab, 0);
        while ($row = $xoopsDB->fetchArray($result)) {
            //	required
            $ret[$i]['title'] = $row['term'];
            $link = XOOPS_URL.'/modules/'.$this->dirname.'/entry.php?entryID='.$row['entryID'];
            //$ret[$i]['link'] = $ret[$i]['guid'] = $link;
            $ret[$i]['link'] =  $link;
            $ret[$i]['timestamp'] = $row['datesub'];
            $ret[$i]['description'] = $myts->displayTarea($row['definition']);
            //	optional
            //5. The item synopsis, or description, whatever
            //$ret[$i]['guid'] = $link;
            //	6. A string + domain that identifies a categorization taxonomy
            $ret[$i]['category'] = $this->modname;
            $ret[$i]['domain'] = XOOPS_URL.'/modules/'.$this->dirname.'/';
            /*$ret[$i]['extras'] = array();
            //	7a. without attribute
            $ret[$i]['extras']['author'] = array('content' => 'aabbc@c.com');
            //	7b. with attributes
            $ret[$i]['extras']['enclosure']['attributes'] = array('url' => 'url-to-any-file', 'length' => 1024000, 'type' => 'audio/mpeg');
            */
            $i++;
        }
        return $ret;
    }
}
