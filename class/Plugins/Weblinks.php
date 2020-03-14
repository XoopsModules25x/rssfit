<?php namespace XoopsModules\Rssfit\Plugins;
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
 * @author       NS Tai (aka tuff) <http://www.brandycoke.com/>
 * @author       XOOPS Development Team
 */
/*
* About this RSSFit plug-in
* Author: tuff <http://www.brandycoke.com/>
* Requirements (Tested with):
*  Module: MyLinks <https://xoops.org/>
*  Version: 1.1
*  RSSFit verision: 1.2 / 1.5
*  XOOPS version: 2.0.13.2 / 2.2.3
*/

if (!defined('RSSFIT_ROOT_PATH')) {
    exit();
}

/**
 * Class Weblinks
 * @package XoopsModules\Rssfit\Plugins
 */
class Weblinks
{
    public $dirname = 'weblinks';
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
     * @param $uid
     * @return string
     */
    public function myGetUnameFromId($uid)
    {
        static $thisUser = false;
        static $lastUid = false;
        static $lastName = '';

        if ($lastUid == $uid) {
            return $lastName;
        }

        if (!is_object($thisUser)) {
            $memberHandler = xoops_getHandler('member');
            $thisUser      = $memberHandler->getUser($uid);
        }
        $name = htmlspecialchars($thisUser->getVar('name'), ENT_QUOTES | ENT_HTML5);
        if ('' == $name) {
            $name = htmlspecialchars($thisUser->getVar('uname'), ENT_QUOTES | ENT_HTML5);
        }
        $lastUid  = $uid;
        $lastName = $name;

        return $name;
    }

    /**
     * @param $obj
     * @return bool
     */
    public function &grabEntries(&$obj)
    {
        global $xoopsDB;
        $myts   = \MyTextSanitizer::getInstance();
        $ret    = false;
        $i      = 0;
        $sql    = 'SELECT lid, title, time_update, description, url, uid FROM ' . $xoopsDB->prefix('weblinks_link') . '  ORDER BY time_update DESC';
        $result = $xoopsDB->query($sql, $this->grab, 0);
        while (false !== ($row = $xoopsDB->fetchArray($result))) {
            $title                  = $row['title'];
            $name                   = $this->myGetUnameFromId($row['uid']);
            $ret[$i]['title']       = $this->modname . ': ' . $title;
            $link                   = XOOPS_URL . '/modules/' . $this->dirname . '/singlelink.php?lid=' . $row['lid'] . '&amp;keywords=';
            $ret[$i]['link']        = $link;
            $ret[$i]['timestamp']   = $row['time_update'];
            $desc                   = '<p><a href="' . $row['url'] . '"><b>' . $title . '</b></a><br> ';
            $desc                   .= 'Submitted by: <i>' . $name . '</i><br>';
            $desc                   .= $myts->displayTarea($row['description']) . '</p><br clear="all"/>';
            $ret[$i]['description'] = $desc;
            $ret[$i]['guid']        = $link;
            $ret[$i]['category']    = $this->modname;
            $ret[$i]['domain']      = XOOPS_URL . '/modules/' . $this->dirname . '/';
            $i++;
        }

        return $ret;
    }
}
