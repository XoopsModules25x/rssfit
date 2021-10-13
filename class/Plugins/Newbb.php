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
*  Module: Newbb <https://xoops.org>
*  Version: 1.0
*  RSSFit verision: 1.2
*  XOOPS version: 2.0.13.2
*/

if (!\defined('RSSFIT_ROOT_PATH')) {
    exit();
}

/**
 * Class Newbb
 * @package XoopsModules\Rssfit\Plugins
 */
class Newbb
{
    public $dirname = 'newbb';
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
        global $xoopsDB;
        require_once XOOPS_ROOT_PATH . '/modules/' . $this->dirname . '/class/class.forumposts.php';
        $myts = \MyTextSanitizer::getInstance();
        $ret = false;
        $i = 0;
        $sql = 'SELECT p.post_id, p.subject, p.post_time, p.forum_id, p.topic_id, p.nohtml, p.nosmiley, f.forum_name, t.post_text FROM '
                . $xoopsDB->prefix('bb_posts')
                . ' p, '
                . $xoopsDB->prefix('bb_forums')
                . ' f, '
                . $xoopsDB->prefix('bb_posts_text')
                . ' t WHERE f.forum_id = p.forum_id AND p.post_id = t.post_id AND f.forum_type != 1 ORDER BY p.post_time DESC';
        if (!$result = $xoopsDB->query($sql, $this->grab, 0)) {
            return $ret;
        }
        while (false !== ($row = $xoopsDB->fetchArray($result))) {
            $link = XOOPS_URL . '/modules/' . $this->dirname . '/viewtopic.php?topic_id=' . $row['topic_id'] . '&amp;forum=' . $row['forum_id'] . '&amp;post_id=' . $row['post_id'] . '#forumpost' . $row['post_id'];
            $ret[$i]['title'] = $row['subject'];
            $ret[$i]['link'] = $ret[$i]['guid'] = $link;
            $ret[$i]['timestamp'] = $row['post_time'];
            $ret[$i]['description'] = $myts->displayTarea($row['post_text'], $row['nohtml'] ? 0 : 1, $row['nosmiley'] ? 0 : 1);
            $ret[$i]['category'] = $row['forum_name'];
            $ret[$i]['domain'] = XOOPS_URL . '/modules/' . $this->dirname . '/viewforum.php?forum=' . $row['forum_id'];
            $i++;
        }

        return $ret;
    }
}
