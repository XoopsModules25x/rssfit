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
 * Author: Hervé Thouzard of Instant Zero (http://www.instant-zero.com)
 * Requirements (Tested with):
 *  Module: Buyersguide
 *  Version: 1.33
 * Flux RSS : Derniers Fabricants
 *  RSSFit verision: 1.22
 *  XOOPS version: 2.0.18.1
 */
if (!\defined('RSSFIT_ROOT_PATH')) {
    exit();
}

/**
 * Class Buyersguidenews
 */
class Buyersguidenews
{
    public $dirname = 'buyersguide';
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

        return $mod;
    }

    /**
     * @param \XoopsObject $obj
     * @return bool|array
     */
    public function grabEntries(&$obj)
    {
        $ret = false;
        require_once XOOPS_ROOT_PATH . '/modules/buyersguide/include/common.php';
        $items = $hBgNews->getRecentNews(0, $this->grab);
        $i     = 0;

        if (false !== $items && \count($items) > 0) {
            $ret = [];
            foreach ($items as $item) {
                $ret[$i]['link']        = $ret[$i]['guid'] = $item->getLink();
                $ret[$i]['title']       = $item->getVar('news_title', 'n');
                $ret[$i]['timestamp']   = $item->getVar('news_date');
                $ret[$i]['description'] = $item->getShortenText();
                $ret[$i]['category']    = $this->modname;
                $ret[$i]['domain']      = XOOPS_URL . '/modules/' . $this->dirname . '/';
                $i++;
            }
        }

        return $ret;
    }
}
