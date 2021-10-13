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
*  Module: WF-Downloads <http://www.wf-projects.com>
*  Version: 2.0.5a
*  RSSFit verision: 1.2 / 1.5
*  XOOPS version: 2.0.13.2 / 2.2.3
*/

if (!\defined('RSSFIT_ROOT_PATH')) {
    exit();
}

/**
 * Class Wfdownloads
 * @package XoopsModules\Rssfit\Plugins
 */
class Wfdownloads extends \XoopsObject
{
    public $dirname = 'wfdownloads';
    public $modname;
    public $module;
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
        $this->module = $mod;

        return $mod;
    }

    /**
     * @param \XoopsObject $obj
     * @return bool|array
     */
    public function grabEntries(&$obj)
    {
        global $xoopsDB;
        $myts = \MyTextSanitizer::getInstance();
        /** @var \XoopsGroupPermHandler $grouppermHandler */
        $grouppermHandler = xoops_getHandler('groupperm');
        $ret              = false;
        $i                = 0;
        $sql              = 'SELECT lid, cid, title, date, description FROM ' . $xoopsDB->prefix('wfdownloads_downloads') . ' WHERE status > 0 AND offline = 0 ORDER BY date DESC';
        $result           = $xoopsDB->query($sql, $this->grab, 0);
        if ($result instanceof \mysqli_result) {
            $ret = [];
            /** @var \XoopsMemberHandler $memberHandler */
            $memberHandler = xoops_getHandler('member');
            while (false !== ($row = $xoopsDB->fetchArray($result))) {
                if ($grouppermHandler->checkRight('WFDownFilePerm', $row['lid'], \is_object($GLOBALS['xoopsUser']) ? $memberHandler->getGroupsByUser($GLOBALS['xoopsUser']->getVar('uid')) : XOOPS_GROUP_ANONYMOUS, $this->module->getVar('mid'))) {
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
        }
        return $ret;
    }
}
