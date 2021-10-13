<?php

namespace XoopsModules\Rssfit\Plugins;

/*
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

/**
 * @copyright    XOOPS Project (https://xoops.org)
 * @license      GNU GPL 2 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 * @package      RSSFit - Extendable XML news feed generator
 * @author       NS Tai (aka tuff) <http://www.brandycoke.com>
 * @author       XOOPS Development Team
 */

/*
* About this RSSFit plug-in
* Author: tuff <http://www.brandycoke.com>
* Requirements (Tested with):
*  Module: WF-section <http://www.wf-projects.com>
*  Version: 1.x
*  RSSFit verision: 1.2
*  XOOPS version: 2.0.13.2
*/

if (!\defined('RSSFIT_ROOT_PATH')) {
    exit();
}

/**
 * Class Wfsection
 * @package XoopsModules\Rssfit\Plugins
 */
class Wfsection
{
    public $dirname = 'wfsection';
    public $modname;
    public $grab;

    /**
     * @return false|string
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
     * @param \XoopsObject $obj
     * @return bool|array
     */
    public function grabEntries(&$obj)
    {
        @require_once XOOPS_ROOT_PATH . '/modules/wfsection/include/groupaccess.php';
        global $xoopsDB;
        $ret = false;
        $i = 0;
        $sql = 'SELECT a.articleid, a.title as atitle, a.published, a.expired, a.counter, a.groupid, a.maintext, a.summary, b.title as btitle FROM '
               . $xoopsDB->prefix('wfs_article')
               . ' a, '
               . $xoopsDB->prefix('wfs_category')
               . ' b WHERE a.published < '
               . \time()
               . ' AND a.published > 0 AND (a.expired = 0 OR a.expired > '
               . \time()
               . ') AND a.noshowart = 0 AND a.offline = 0 AND a.categoryid = b.id ORDER BY published DESC';

        $result = $xoopsDB->query($sql, $this->grab, 0);
        while (false !== ($row = $xoopsDB->fetchArray($result))) {
            if (checkAccess($row['groupid'])) {
                $link = XOOPS_URL . '/modules/' . $this->dirname . '/article.php?articleid=' . $row['articleid'];
                $ret[$i]['title'] = $row['atitle'];
                $ret[$i]['link'] = $link;
                $ret[$i]['guid'] = $link;
                $ret[$i]['timestamp'] = $row['published'];
                $ret[$i]['description'] = $myts->displayTarea(!empty($row['summary']) ? $row['summary'] : $row['maintext']);
                $ret[$i]['category'] = $this->modname;
                $ret[$i]['domain'] = XOOPS_URL . '/modules/' . $this->dirname . '/';
                $i++;
            }
        }

        return $ret;
    }
}
