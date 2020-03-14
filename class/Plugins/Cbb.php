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

/*
* About this RSSFit plug-in
* Author: tuff <http://www.brandycoke.com>
* Requirements (Tested with):
*  Module: CBB <D.J. (phppp), XOOPS Project | <https://xoops.org>
*  Version: 1.15 / 2.30 / 2.31
*  RSSFit verision: 1.2 / 1.5
*  XOOPS version: 2.0.13.2 / 2.2.3
*/

if (!defined('RSSFIT_ROOT_PATH')) {
    exit();
}

/**
 * Class Cbb
 * @package XoopsModules\Rssfit\Plugins
 */
class Cbb
{
    public $dirname = 'newbb';
    public $modname;
    public $module;
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
        $this->module = $mod;

        return $mod;
    }

    /**
     * @param $obj
     * @return mixed
     */
    public function &grabEntries(&$obj)
    {
        @require XOOPS_ROOT_PATH . '/modules/newbb/include/functions.php';
        global $xoopsDB;
        $xoopsModule = $this->module;
        $myts = \MyTextSanitizer::getInstance();
        $i = 0;
        $forumiscHandler = \XoopsModules\Newbb\Helper::getInstance()->getHandler('Forum');
        $topicHandler = \XoopsModules\Newbb\Helper::getInstance()->getHandler('Topic');
        $newbbConfig = $GLOBALS['configHandler']->getConfigsByCat(0, $this->module->getVar('mid'));

        $access_forums = $forumiscHandler->getForums(0, 'access');
        $available_forums = [];
        foreach ($access_forums as $forum) {
            if ($topicHandler->getPermission($forum)) {
                $available_forums[$forum->getVar('forum_id')] = $forum;
            }
        }
        unset($access_forums);

        if (count($available_forums) > 0) {
            ksort($available_forums);
            $cond = ' AND t.forum_id IN (' . implode(',', array_keys($available_forums)) . ')';
            unset($available_forums);
            $cond .= $newbbConfig['enable_karma'] ? ' AND p.post_karma = 0' : '';
            $cond .= $newbbConfig['allow_require_reply'] ? ' AND p.require_reply = 0' : '';
            $query = 'SELECT p.post_id, p.subject, p.post_time, p.forum_id, p.topic_id, p.dohtml, p.dosmiley, p.doxcode, p.dobr, f.forum_name, pt.post_text FROM '
                      . $xoopsDB->prefix('bb_posts')
                      . ' p, '
                      . $xoopsDB->prefix('bb_forums')
                      . ' f, '
                      . $xoopsDB->prefix('bb_topics')
                      . ' t, '
                      . $xoopsDB->prefix('bb_posts_text')
                      . ' pt WHERE f.forum_id = p.forum_id AND p.post_id = pt.post_id AND p.topic_id = t.topic_id AND t.approved = 1 AND p.approved = 1 AND f.forum_id = t.forum_id '
                      . $cond
                      . ' ORDER BY p.post_time DESC';
            $result = $xoopsDB->query($query, $this->grab);
            while (false !== ($row = $xoopsDB->fetchArray($result))) {
                $link = XOOPS_URL . '/modules/' . $this->dirname . '/viewtopic.php?topic_id=' . $row['topic_id'] . '&amp;forum=' . $row['forum_id'] . '&amp;post_id=' . $row['post_id'] . '#forumpost' . $row['post_id'];
                $ret[$i]['title'] = $row['subject'];
                $ret[$i]['link'] = $ret[$i]['guid'] = $link;
                $ret[$i]['timestamp'] = $row['post_time'];
                $ret[$i]['description'] = $myts->displayTarea($row['post_text'], $row['dohtml'], $row['dosmiley'], $row['doxcode'], 1, $row['dobr']);
                $ret[$i]['category'] = $row['forum_name'];
                $ret[$i]['domain'] = XOOPS_URL . '/modules/' . $this->dirname . '/viewforum.php?forum=' . $row['forum_id'];
                $i++;
            }
        }

        return $ret;
    }
}
