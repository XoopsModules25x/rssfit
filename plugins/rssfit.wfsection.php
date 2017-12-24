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
*  Module: WF-section <http://www.wf-projects.com/>
*  Version: 1.x
*  RSSFit verision: 1.2
*  XOOPS version: 2.0.13.2
*/

defined('RSSFIT_ROOT_PATH') || exit('RSSFIT root path not defined');

/**
 * Class RssfitWfsection
 */
class RssfitWfsection
{
    public $dirname = 'wfsection';
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
        if ($mod->getVar('version') >= 200) {
            return false;
        }
        return $mod;
    }

    /**
     * @param null $obj
     * @return bool
     */
    public function &grabEntries($obj=null)
    {
        @include_once XOOPS_ROOT_PATH . '/modules/wfsection/include/groupaccess.php';
        global $xoopsDB;
        $ret = false;
        $i   = 0;
        $sql = 'SELECT a.articleid, a.title AS atitle, a.published, a.expired, a.counter, a.groupid, a.maintext, a.summary, b.title AS btitle FROM '
               . $xoopsDB->prefix('wfs_article')
               . ' a, '
               . $xoopsDB->prefix('wfs_category')
               . ' b WHERE a.published < '
               . time()
               . ' AND a.published > 0 AND (a.expired = 0 OR a.expired > '
               . time()
               . ') AND a.noshowart = 0 AND a.offline = 0 AND a.categoryid = b.id ORDER BY published DESC';

        $result = $xoopsDB->query($sql, $this->grab, 0);
        while ($row = $xoopsDB->fetchArray($result)) {
            if (checkAccess($row['groupid'])) {
                $link                   = XOOPS_URL . '/modules/' . $this->dirname . '/article.php?articleid=' . $row['articleid'];
                $ret[$i]['title']       = $row['atitle'];
                $ret[$i]['link']        = $link;
                $ret[$i]['guid']        = $link;
                $ret[$i]['timestamp']   = $row['published'];
                $ret[$i]['description'] = $myts->displayTarea(!empty($row['summary']) ? $row['summary'] : $row['maintext']);
                $ret[$i]['category']    = $this->modname;
                $ret[$i]['domain']      = XOOPS_URL . '/modules/' . $this->dirname . '/';
                $i++;
            }
        }
        return $ret;
    }
}
