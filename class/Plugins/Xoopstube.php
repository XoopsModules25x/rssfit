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
 * @copyright    XOOPS Project https://xoops.org/
 * @license      GNU GPL 2 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 * @package      RSSFit - Extendable XML news feed generator
 * @author       NS Tai (aka tuff) <http://www.brandycoke.com>
 * @author       XOOPS Development Team
 */

/**
 * About this RSSFit plug-in
 * Author: jayjay <http://www.sint-niklaas.be>
 * Requirements (Tested with):
 *  Module: Xoopstube <https://xoops.org>
 *  Version: 1.0
 *  RSSFit version: 1.21
 *  XOOPS version: 2.0.18.1
 */
if (!defined('RSSFIT_ROOT_PATH')) {
    exit();
}

/**
 * Class Xoopstube
 */
class Xoopstube
{
    public $dirname = 'xoopstube';
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

        return $mod;
    }

    /**
     * @param \XoopsObject $obj
     * @return bool
     */
    public function &grabEntries(&$obj)
    {
        global $xoopsDB;
        $myts = \MyTextSanitizer::getInstance();
        $ret = false;
        $i = 0;
        $sql = 'SELECT l.lid, l.title as ltitle, l.date, l.cid, l.hits, l.description, c.title as ctitle FROM ' . $xoopsDB->prefix('xoopstube_videos') . ' l, ' . $xoopsDB->prefix('xoopstube_cat') . ' c WHERE l.cid=c.cid AND l.status>0 ORDER BY l.date DESC';

        $result = $xoopsDB->query($sql, $this->grab, 0);
        while (false !== ($row = $xoopsDB->fetchArray($result))) {
            $ret[$i]['title'] = $row['ltitle'];
            $link = XOOPS_URL . '/modules/' . $this->dirname . '/singlevideo.php?cid=' . $row['cid'] . '&amp;lid=' . $row['lid'];
            $ret[$i]['link'] = $ret[$i]['guid'] = $link;
            $ret[$i]['timestamp'] = $row['date'];
            $ret[$i]['description'] = $myts->displayTarea($row['description']);
            $ret[$i]['category'] = $this->modname;
            $ret[$i]['domain'] = XOOPS_URL . '/modules/' . $this->dirname . '/';
            $i++;
        }

        return $ret;
    }
}
