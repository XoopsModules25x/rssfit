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
* Author: brash <http://www.it-hq.org>
* Requirements (Tested with):
*  Module: AMS <http://www.it-hq.org>
*  Version: 2.41
*  RSSFit verision: 1.2 / 1.5
*  XOOPS version: 2.0.13.2 / 2.2.3
*/

use XoopsModules\Rssfit\{
    AbstractPlugin
};
use XoopsModules\Ams\Story;
//use XoopsModules\Ams\Helper as PluginHelper;

if (!\defined('RSSFIT_ROOT_PATH')) {
    exit();
}

/**
 * Class Ams
 * @package XoopsModules\Rssfit\Plugins
 */
final class Ams extends AbstractPlugin
{
    public $dirname = 'ams';

    /**
     * @return array
     */
    public function grabEntries(\XoopsMySQLDatabase $xoopsDB):?array
    {
        $ret  = null;
        //        @require_once XOOPS_ROOT_PATH . '/modules/AMS/class/class.newsstory.php';
        $myts = \MyTextSanitizer::getInstance();
        $ams  = Story::getAllPublished($this->grab, 0);
        if (\count($ams) > 0) {
            $ret = [];
            for ($i = 0, $iMax = \count($ams); $i < $iMax; ++$i) {
                $ret[$i]['title']       = $myts->undoHtmlSpecialChars($ams[$i]->title());
                $ret[$i]['link']        = $ret[$i]['guid'] = XOOPS_URL . "/modules/$this->dirname/article.php?storyid=" . $ams[$i]->storyid();
                $ret[$i]['timestamp']   = $ams[$i]->published();
                $ret[$i]['description'] = $ams[$i]->hometext();
                $ret[$i]['category']    = $this->modname;
                $ret[$i]['domain']      = XOOPS_URL . '/modules/' . $this->dirname . '/';
            }
        }

        return $ret;
    }
}
