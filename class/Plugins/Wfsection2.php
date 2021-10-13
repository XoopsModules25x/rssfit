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
*  Module: WF-section <http://www.wf-projects.com>
*  Version: 2.07 b3
*  RSSFit verision: 1.2 / 1.5
*  XOOPS version: 2.0.13.2 / 2.2.3
*/

use XoopsModules\Rssfit;

if (!\defined('RSSFIT_ROOT_PATH')) {
    exit();
}

/**
 * Class Wfsection2
 * @package XoopsModules\Rssfit\Plugins
 */
class Wfsection2
{
    public $dirname = 'wfsection';
    public $modname;
    public $grab;

    /**
     * @return false|string
     */
    public function loadModule()
    {
        $mod = $GLOBALS['module_handler']->getByDirname($this->dirname);
        if (!$mod || !$mod->getVar('isactive') || $mod->getVar('version') < 200) {
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
        $ret = false;
        $articles = Rssfit\WfsArticle::getAllArticle($this->grab, 0, 'online');
        if (\count($articles) > 0) {
            $ret = [];
            $xoopsModuleConfig['shortartlen'] = 0;
            $myts = \MyTextSanitizer::getInstance();
            for ($i = 0, $iMax = \count($articles); $i < $iMax; ++$i) {
                $link = XOOPS_URL . '/modules/wfsection/article.php?articleid=' . $articles[$i]->articleid();
                $ret[$i]['title'] = $myts->undoHtmlSpecialChars($articles[$i]->title());
                $ret[$i]['link'] = $link;
                $ret[$i]['guid'] = $link;
                $ret[$i]['timestamp'] = $articles[$i]->published();
                $ret[$i]['description'] = $articles[$i]->summary();
                $ret[$i]['category'] = $this->modname;
                $ret[$i]['domain'] = XOOPS_URL . '/modules/' . $this->dirname . '/';
            }
        }

        return $ret;
    }
}
