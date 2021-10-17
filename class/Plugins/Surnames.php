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
 * Author: Richard Griffith <richard@geekwright.com>
 * Requirements (or Tested with):
 *  Module: Surnames https://github.com/geekwright/surnames
 *  Version: 1.0
 *  RSSFit verision: 1.3
 *  XOOPS version: 2.5.9
 */

use XoopsModules\Rssfit\{
    AbstractPlugin
};
use XoopsModules\Surnames\Helper as PluginHelper;

if (!\defined('RSSFIT_ROOT_PATH')) {
    exit();
}

/**
 * Class Surnames
 * @package XoopsModules\Rssfit\Plugins
 */
final class Surnames extends AbstractPlugin
{
    public $dirname = 'surnames';

    /**
     * @param $uid
     */
    public function myGetUnameFromId($uid): string
    {
        static $thisUser = false;
        static $lastUid = false;
        static $lastName = '';

        if ($lastUid == $uid) {
            return $lastName;
        }

        if (!\is_object($thisUser)) {
            $memberHandler = \xoops_getHandler('member');
            $thisUser = $memberHandler->getUser($uid);
        }
        $name = \htmlspecialchars($thisUser->getVar('name'), \ENT_QUOTES | \ENT_HTML5);
        if ('' == $name) {
            $name = \htmlspecialchars($thisUser->getVar('uname'), \ENT_QUOTES | \ENT_HTML5);
        }
        $lastUid = $uid;
        $lastName = $name;

        return $name;
    }

    /**
     * @return array
     */
    public function grabEntries(\XoopsMySQLDatabase $xoopsDB):?array
    {
        $myts = \MyTextSanitizer::getInstance();
        $ret  = null;

        $i = -1;
        $lasttime = false;
        $lastuser = false;
        $limit = 10 * $this->grab;

        $sql = "SELECT uid, id, surname, notes, DATE_FORMAT(changed_ts,'%Y-%m-%d') as changedate FROM " . $xoopsDB->prefix('surnames');
        $sql .= ' WHERE approved=1 ORDER BY changedate DESC, uid ';
        $result = $xoopsDB->query($sql, $limit, 0);
        if ($result instanceof \mysqli_result) {
            $ret = [];
            while (false !== ($row = $xoopsDB->fetchArray($result))) {
                $changedate = \strtotime($row['changedate']);
                $uid        = $row['uid'];
                if ($lasttime == $changedate && $lastuser == $uid) {
                    $link    = XOOPS_URL . '/modules/surnames/view.php?id=' . $row['id'];
                    $surname = $row['surname'];
                    $desc    .= "<a href=\"$link\">$surname</a><br>";
                } else {
                    if ($i >= 0) {
                        $ret[$i]['description'] = $desc;
                    }
                    ++$i;
                    $lasttime = $changedate;
                    $lastuser = $uid;
                    if ($i <= $this->grab) {
                        $desc                 = '';
                        $name                 = $this->myGetUnameFromId($row['uid']);
                        $ret[$i]['title']     = $this->modname . ': by ' . $name;
                        $ret[$i]['link']      = XOOPS_URL . '/modules/surnames/list.php?uid=' . $row['uid'];
                        $ret[$i]['timestamp'] = $changedate;

                        $link                = XOOPS_URL . '/modules/surnames/view.php?id=' . $row['id'];
                        $ret[$i]['guid']     = $link;
                        $ret[$i]['category'] = $this->modname;

                        $surname = $row['surname'];
                        $desc    .= "<a href=\"$link\">$surname</a><br>";
                    }
                }
                if ($i > $this->grab) {
                    break;
                }
            }
            if ($i < $this->grab) {
                $ret[$i]['description'] = $desc;
            }
        }
        return $ret;
    }
}
