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
 * This file is a dummy for making a RSSFit plug-in, follow the following steps
 * if you really want to do so.
 *
 * Step 0: Stop here if you are not sure what you are doing, it's no fun at all
 * Step 1: Clone this file and rename as something like rssfit.[mod_dir].php
 * Step 2: Replace the class name "RssfitSpecial" with "Rssfit[mod_dir]" at line 60,
 *         i.e. "RssfitNews" for the module "News"
 * Step 3: Modify the definition of $dirname in line 62 to your modules dirname, i.e. 'news'
 * Step 4: Modify the function "grabEntries" method to satisfy your needs
 * Step 5: Move your new plug-in file to the RSSFit plugins folder,
 *         i.e. your-xoops-root/modules/rss/plugins
 * Step 6: Install your plug-in by pointing your browser to
 *         your-xoops-url/modules/rss/admin/?do=plugins
 * Step 7: Finally, tell us about yourself and this file by modifying the
 *         "About this RSSFit plug-in" section located below.
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

class RssfitSpecial
{
    public $dirname = 'special';
    public $modname;
    public $grab;                // will be set to the maximum number of items to grab
    public $module;

    public function loadModule()
    {
        $mod = $GLOBALS['module_handler']->getByDirname($this->dirname);
        if (!$mod || !$mod->getVar('isactive')) {
            return false;
        }
        $this->modname = $mod->getVar('name');
        $this->module = $mod;   // optional, remove this line if there is nothing
        // to do with module info when grabbing entries
        return $mod;
    }

    public function &grabEntries(&$obj)
    {
        global $xoopsDB;
        $ret = array();
        @include_once XOOPS_ROOT_PATH.'/modules/special/class/stuff.php';
        $myts = MyTextSanitizer::getInstance();
        $items = SpecialStuff::getAllPublished($this->grab, 0);
        foreach ($items as $item) {
            $ret[] = array(
                'title' => $myts->undoHtmlSpecialChars($item->title()),
                'link' => XOOPS_URL . '/modules/special/article.php?itemid=' . $item->itemid(),
                'guid' => XOOPS_URL . '/modules/special/article.php?itemid=' . $item->itemid(),
                'timestamp' => $item->published(),
                'description' => $item->hometext(),
                'category' => $this->modname,
                'domain' => XOOPS_URL . '/modules/' . $this->dirname . '/',
            );
        }
        return $ret;
    }
}
