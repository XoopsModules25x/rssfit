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
* This file is a dummy for making a RSSFit plug-in, follow the following steps
* if you really want to do so.
* Step 0:	Stop here if you are not sure what you are doing, it's no fun at all
* Step 1:	Clone this file and rename as something like rssfit.[mod_dir].php
* Step 2:	Replace the text "RssfitSample" with "Rssfit[mod_dir]" at line 59 and
* 			line 65, i.e. "RssfitNews" for the module "News"
* Step 3:	Modify the word in line 60 from 'sample' to [mod_dir]
* Step 4:	Modify the function "grabEntries" to satisfy your needs
* Step 5:	Move your new plug-in file to the RSSFit plugins folder,
* 			i.e. your-xoops-root/modules/rssfit/plugins
* Step 6:	Install your plug-in by pointing your browser to
* 			your-xoops-url/modules/rssfit/admin/?do=plugins
* Step 7:	Finally, tell us about yourself and this file by modifying the
* 			"About this RSSFit plug-in" section which is located... somewhere.
*
* [mod_dir]: Name of the driectory of your module, i.e. 'news'
*
* About this RSSFit plug-in
* Author: John Doe <http://www.your.site/>
* Requirements (or Tested with):
*  Module: Blah <http://www.where.to.find.it/>
*  Version: 1.0
*  RSSFit verision: 1.2 / 1.5
*  XOOPS version: 2.0.13.2 / 2.2.3
*/

if (!defined('RSSFIT_ROOT_PATH')) {
    exit();
}

/**
 * Class RssfitSample
 */
class RssfitSample
{
    public $dirname = 'sample';
    public $modname;
    public $grab;
    public $module;    // optional, see line 71

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
        $this->module  = $mod;    // optional, remove this line if there is nothing
        // to do with module info when grabbing entries
        return $mod;
    }

    /**
     * @param $obj
     * @return bool
     */
    public function &grabEntries(&$obj)
    {
        global $xoopsDB;
        $myts = \MyTextSanitizer::getInstance();
        $ret  = false;
        $i    = 0;
        //	The following example code grabs the latest entries from the module MyLinks
        $sql    = 'SELECT l.lid, l.cid, l.title, l.date, t.description FROM ' . $xoopsDB->prefix('mylinks_links') . ' l, ' . $xoopsDB->prefix('mylinks_text') . ' t WHERE l.status > 0 AND l.lid = t.lid ORDER BY date DESC';
        $result = $xoopsDB->query($sql, $this->grab, 0);
        while ($row = $xoopsDB->fetchArray($result)) {
            $link = XOOPS_URL . '/modules/' . $this->dirname . '/singlelink.php?cid=' . $row['cid'] . '&amp;lid=' . $row['lid'];
            /*
            * Required elements of an RSS item
            */
            //	1. Title of an item
            $ret[$i]['title'] = $row['title'];
            //	2. URL of an item
            $ret[$i]['link'] = $link;
            //	3. Item modification date, must be in Unix time format
            $ret[$i]['timestamp'] = $row['date'];
            //	4. The item synopsis, or description, whatever
            $ret[$i]['description'] = $myts->displayTarea($row['description']);
            /*
            * Optional elements of an RSS item
            */
            //	5. The item synopsis, or description, whatever
            $ret[$i]['guid'] = $link;
            //	6. A string + domain that identifies a categorization taxonomy
            $ret[$i]['category'] = $this->modname;
            $ret[$i]['domain']   = XOOPS_URL . '/modules/' . $this->dirname . '/';
            //	7. extra tags examples
            $ret[$i]['extras'] = [];
            //	7a. without attribute
            $ret[$i]['extras']['author'] = ['content' => 'aabbc@c.com'];
            //	7b. with attributes
            $ret[$i]['extras']['enclosure']['attributes'] = ['url' => 'url-to-any-file', 'length' => 1024000, 'type' => 'audio/mpeg'];
            $i++;
        }
        return $ret;
    }
}
