<?php

declare(strict_types=1);

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

/**
 * About this RSSFit plug-in
 * Author: tuff <http://www.brandycoke.com>
 * Requirements (Tested with):
 *  Module: MyDownloads <https://xoops.org>
 *  Version: 1.1
 *  RSSFit verision: 1.2 / 1.5
 *  XOOPS version: 2.0.13.2 / 2.2.3
 */
if (!\defined('RSSFIT_ROOT_PATH')) {
    exit();
}

/**
 * Class Mydownloads
 * @package XoopsModules\Rssfit\Plugins
 */
class Mydownloads extends \XoopsObject
{
    public $dirname = 'mydownloads';
    public $modname;
    public $grab;

    /**
     * @return \XoopsModule
     */
    public function loadModule():?\XoopsModule
    {
        $mod = $GLOBALS['module_handler']->getByDirname($this->dirname);
        if (!$mod || !$mod->getVar('isactive')) {
            return null;
        }
        $this->modname = $mod->getVar('name');

        return $mod;
    }

    /**
     * @return array
     */
    public function grabEntries(\XoopsMySQLDatabase $xoopsDB):?array
    {
        $myts   = \MyTextSanitizer::getInstance();
        $ret    = false;
        $i      = 0;
        $sql    = 'SELECT l.lid, l.cid, l.title, l.date, t.description FROM ' . $xoopsDB->prefix('mydownloads_downloads') . ' l, ' . $xoopsDB->prefix('mydownloads_text') . ' t WHERE l.status>0 AND l.lid=t.lid ORDER BY date DESC';
        $result = $xoopsDB->query($sql, $this->grab, 0);
        if ($result instanceof \mysqli_result) {
            $ret = [];
            while (false !== ($row = $xoopsDB->fetchArray($result))) {
                $ret[$i]['title']       = $row['title'];
                $link                   = XOOPS_URL . '/modules/' . $this->dirname . '/singlefile.php?cid=' . $row['cid'] . '&amp;lid=' . $row['lid'];
                $ret[$i]['link']        = $ret[$i]['guid'] = $link;
                $ret[$i]['timestamp']   = $row['date'];
                $ret[$i]['description'] = $myts->displayTarea($row['description']);
                $ret[$i]['category']    = $this->modname;
                $ret[$i]['domain']      = XOOPS_URL . '/modules/' . $this->dirname . '/';
                $i++;
            }
        }

        return $ret;
    }
}
