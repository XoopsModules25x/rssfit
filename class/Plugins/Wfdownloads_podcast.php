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
*  Module: WF-Downloads <http://smartyfactory.ca>
*  Version: 3.1
*  RSSFit verision: 1.21
*  XOOPS version: 2.0.14
*/

if (!\defined('RSSFIT_ROOT_PATH')) {
    exit();
}

/**
 * Class Wfdownloads_podcast
 * @package XoopsModules\Rssfit\Plugins
 */
class Wfdownloads_podcast extends \XoopsObject
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
        if (!$mod || !$mod->getVar('isactive') || $mod->getVar('version') < 310) {
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
        $grouppermHandler = \xoops_getHandler('groupperm');
        $ret = false;
        $i = 0;
        $sql            = 'SELECT lid, cid, title, date, description, filetype, size FROM ' . $xoopsDB->prefix('wfdownloads_downloads') . ' WHERE status > 0 AND offline = 0 AND (expired > ' . \time() . ' OR expired = 0) AND published <= ' . \time() . ' ORDER BY date DESC';
        $result = $xoopsDB->query($sql, $this->grab, 0);
        if ($result instanceof \mysqli_result) {
            $ret = [];
            /** @var \XoopsMemberHandler $memberHandler */
            $memberHandler = xoops_getHandler('member');
            while (false !== ($row = $xoopsDB->fetchArray($result))) {
                if ((isset($perms[$row['cid']]) && true === $perms[$row['cid']])
                    || $grouppermHandler->checkRight('WFDownCatPerm', $row['cid'], \is_object($GLOBALS['xoopsUser']) ? $memberHandler->getGroupsByUser($GLOBALS['xoopsUser']->getVar('uid')) : XOOPS_GROUP_ANONYMOUS, $this->module->getVar('mid'))) {
                    $perms[$row['cid']]     = true;
                    $ret[$i]['title']       = $row['title'];
                    $link                   = XOOPS_URL . '/modules/' . $this->dirname . '/singlefile.php?cid=' . $row['cid'] . '&amp;lid=' . $row['lid'];
                    $ret[$i]['link']        = $ret[$i]['guid'] = $link;
                    $ret[$i]['timestamp']   = $row['date'];
                    $ret[$i]['description'] = $myts->displayTarea($row['description']);
                    $ret[$i]['category']    = $this->modname;
                    $ret[$i]['domain']      = XOOPS_URL . '/modules/' . $this->dirname . '/';
                    //  enclosure tag, a.k.a podcast
                    $ret[$i]['extras']['enclosure']['attributes'] = [
                        'url'    => XOOPS_URL . '/modules/' . $this->dirname . '/visit.php?cid=' . $row['cid'] . '&amp;lid=' . $row['lid'],
                        'length' => $row['size'],
                        'type'   => $row['filetype'],
                    ];
                    $i++;
                } else {
                    $perms[$row['cid']] = false;
                }
            }
        }
        return $ret;
    }
}
