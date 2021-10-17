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

/*
* About this RSSFit plug-in
*
* Author :
*   DuGris - http://www.dugris.info
*
* Requirements:
*   Module : RSSFit  - http://www.brandycoke.com
*   verision : 1.20
*
*   Module : wflinks - http://www.wf-projects.com
*   Version : 1.03
*/

if (!\defined('RSSFIT_ROOT_PATH')) {
    exit();
}

/**
 * Class Wflinks
 */
class Wflinks
{
    public $dirname = 'wflinks';
    public $modname;
    public $mid;
    public $grab;

    /**
     * @return false|\XoopsModule
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
     * @return bool|array
     */
    public function grabEntries(&$obj)
    {
        global $xoopsDB, $xoopsUser;

        $groups = \is_object($xoopsUser) ? $xoopsUser->getGroups() : XOOPS_GROUP_ANONYMOUS;
        /** @var \XoopsGroupPermHandler $grouppermHandler */
        $grouppermHandler = \xoops_getHandler('groupperm');

        $myts   = \MyTextSanitizer::getInstance();
        $ret    = false;
        $i      = 0;
        $sql    = 'SELECT lid, cid, title, date, description FROM ' . $xoopsDB->prefix('wflinks_links') . ' WHERE status>0 ORDER BY date DESC';
        $result = $xoopsDB->query($sql, $this->grab, 0);
        if ($result instanceof \mysqli_result) {
            $ret = [];
            while (false !== ($row = $xoopsDB->fetchArray($result))) {
                if ($grouppermHandler->checkRight('WFLinkCatPerm', $row['cid'], $groups, $this->mid)) {
                    //  required
                    $ret[$i]['title']       = $row['title'];
                    $ret[$i]['link']        = $ret[$i]['guid'] = XOOPS_URL . '/modules/' . $this->dirname . '/singlelink.php?cid=' . $row['cid'] . '&lid=' . $row['lid'];
                    $ret[$i]['timestamp']   = $row['date'];
                    $ret[$i]['description'] = $row['description'];
                    //  optional
                    $ret[$i]['category'] = $this->modname;
                    $ret[$i]['domain']   = XOOPS_URL . '/modules/' . $this->dirname . '/';
                    $i++;
                }
            }
        }

        return $ret;
    }
}
