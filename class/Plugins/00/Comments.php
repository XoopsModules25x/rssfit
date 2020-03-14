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
* Author: Graham Davies (gravies) <http://www.grahamdavies.net>
* Modified by: tuff <http://www.brandycoke.com>
* Requirements (Tested with):
*  Module: any module that support XOOPS system comments
*  RSSFit verision: 1.2 / 1.5
*  XOOPS version: 2.0.13.2 / 2.2.3
*/

if (!defined('RSSFIT_ROOT_PATH')) {
    exit();
}

/**
 * Class Comments
 * @package XoopsModules\Rssfit\Plugins
 */
class Comments
{
    public $dirname = 'system';
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
        $ret = false;
        require_once XOOPS_ROOT_PATH . '/include/comment_constants.php';
        $commentHandler = xoops_getHandler('comment');
        $criteria = new \CriteriaCompo(new \Criteria('com_status', XOOPS_COMMENT_ACTIVE));
        $criteria->setLimit($this->grab);
        $criteria->setSort('com_created');
        $criteria->setOrder('DESC');
        $comments = $commentHandler->getObjects($criteria, true);
        $comment_config = [];
        if (count($comments) > 0) {
            $modules = $GLOBALS['module_handler']->getObjects(new \Criteria('hascomments', 1), true);
            foreach (array_keys($comments) as $i) {
                $mid = $comments[$i]->getVar('com_modid');
                if (!isset($comment_config[$mid])) {
                    $comment_config[$mid] = $modules[$mid]->getInfo('comments');
                }
                $ret[$i]['title'] = 'Comments: ' . $comments[$i]->getVar('com_title', 'n');
                $link = XOOPS_URL
                                          . '/modules/'
                                          . $modules[$mid]->getVar('dirname')
                                          . '/'
                                          . $comment_config[$mid]['pageName']
                                          . '?'
                                          . $comment_config[$mid]['itemName']
                                          . '='
                                          . $comments[$i]->getVar('com_itemid')
                                          . '&amp;com_id='
                                          . $i
                                          . '&amp;com_rootid='
                                          . $comments[$i]->getVar('com_rootid')
                                          . '&amp;'
                                          . $comments[$i]->getVar('com_exparams')
                                          . '#comment'
                                          . $i;
                $ret[$i]['link'] = $ret[$i]['guid'] = $link;
                $ret[$i]['timestamp'] = $comments[$i]->getVar('com_created');
                $ret[$i]['description'] = $comments[$i]->getVar('com_text');
                $ret[$i]['category'] = $modules[$mid]->getVar('name', 'n');
                $ret[$i]['domain'] = XOOPS_URL . '/modules/' . $modules[$mid]->getVar('dirname') . '/';
            }
        }

        return $ret;
    }
}
