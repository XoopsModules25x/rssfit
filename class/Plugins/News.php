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
* Author: tuff <http://www.brandycoke.com>
* Requirements (Tested with):
*  Module: News <https://xoops.org>
*  Version: 1.1 / 1.3 / 1.42 / 1.44
*  RSSFit verision: 1.2 / 1.5
*  XOOPS version: 2.0.13.2 / 2.2.3
*/

use XoopsModules\Rssfit\{
    AbstractPlugin
};
use XoopsModules\News\NewsStory;
use XoopsModules\News\Utility;
use XoopsModules\News\Helper as PluginHelper;

if (!\defined('RSSFIT_ROOT_PATH')) {
    exit();
}

/**
 * Class News
 * @package XoopsModules\Rssfit\Plugins
 */
final class News extends AbstractPlugin
{
    public $dirname = 'news';

    /**
     * @return array
     */
    public function grabEntries(\XoopsMySQLDatabase $xoopsDB):?array
    {
        $ret = null;
        //        @require_once XOOPS_ROOT_PATH . '/modules/news/class/class.newsstory.php';
        $myts = \MyTextSanitizer::getInstance();
        if ($this->module->getVar('version') >= 130) {
            //            @require_once XOOPS_ROOT_PATH . '/modules/news/include/functions.php';
            $news = NewsStory::getAllPublished($this->grab, 0, Utility::getModuleOption('restrictindex'));
        } else {
            $news = NewsStory::getAllPublished($this->grab, 0);
        }
        if (\count($news) > 0) {
            $ret = [];
            for ($i = 0, $iMax = \count($news); $i < $iMax; ++$i) {
                $ret[$i]['title']       = $this->modname . ': ' . $myts->undoHtmlSpecialChars($news[$i]->title());
                $ret[$i]['link']        = XOOPS_URL . '/modules/news/article.php?storyid=' . $news[$i]->storyid();
                $ret[$i]['guid']        = XOOPS_URL . '/modules/news/article.php?storyid=' . $news[$i]->storyid();
                $ret[$i]['timestamp']   = $news[$i]->published();
                $desc                   = $news[$i]->hometext();
                $ret[$i]['description'] = $news[$i]->hometext();
                $ret[$i]['category']    = $this->modname;
                $ret[$i]['domain']      = XOOPS_URL . '/modules/' . $this->dirname . '/';
            }
        }

        return $ret;
    }
}
