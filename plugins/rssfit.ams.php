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
* Author: brash <http://www.it-hq.org/>
* Requirements (Tested with):
*  Module: AMS <http://www.it-hq.org/>
*  Version: 2.41
*  RSSFit verision: 1.2 / 1.5
*  XOOPS version: 2.0.13.2 / 2.2.3
*/

if (!defined('RSSFIT_ROOT_PATH')) {
    exit();
}
class RssfitAms
{
    public $dirname = 'AMS';
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

    public function &grabEntries(&$obj)
    {
        $ret = false;
        @include_once XOOPS_ROOT_PATH.'/modules/AMS/class/class.newsstory.php';
        $myts = \MyTextSanitizer::getInstance();
        $ams = AmsStory::getAllPublished($this->grab, 0);
        if (count($ams) > 0) {
            for ($i=0, $iMax = count($ams); $i < $iMax; $i++) {
                $ret[$i]['title'] = $myts->undoHtmlSpecialChars($ams[$i]->title());
                $ret[$i]['link'] = $ret[$i]['guid'] = XOOPS_URL.'/modules/AMS/article.php?storyid='.$ams[$i]->storyid();
                $ret[$i]['timestamp'] = $ams[$i]->published();
                $ret[$i]['description'] = $ams[$i]->hometext();
                $ret[$i]['category'] = $this->modname;
                $ret[$i]['domain'] = XOOPS_URL.'/modules/'.$this->dirname.'/';
            }
        }
        return $ret;
    }
}
