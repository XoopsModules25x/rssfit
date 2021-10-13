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
*  Module: SmartSection <http://www.smartfactory.ca>
*  Version: 1.0.4 Beta 2 / 1.1 Beta 1 / 1.05 Beta 1
*  RSSFit verision: 1.2 / 1.5
*  XOOPS version: 2.0.13.2 / 2.2.3
*/

use XoopsModules\Publisher\Helper as PublisherHelper;

if (!\defined('RSSFIT_ROOT_PATH')) {
    exit();
}

/**
 * Class Publisher
 * @package XoopsModules\Rssfit\Plugins
 */
class Publisher
{
    public $dirname = 'publisher';
    public $modname;
    public $grab;

    /**
     * @return false|string
     */
    public function loadModule()
    {
        //        $mod = $GLOBALS['module_handler']->getByDirname($this->dirname);

        $helper = PublisherHelper::getInstance();
        $mod    = $helper->getModule();

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
        require_once XOOPS_ROOT_PATH . '/modules/publisher/include/common.php';
        $helper                  = PublisherHelper::getInstance();
        $publisherItemiscHandler = $helper->getHandler('Item');
        $items                   = $publisherItemiscHandler->getAllPublished($this->grab, 0);
        if (false !== $items && \count($items) > 0) {
        $ret = [];
            for ($i = 0, $iMax = \count($items); $i < $iMax; ++$i) {
                $ret[$i]['link'] = $ret[$i]['guid'] = $items[$i]->getItemUrl();
                $ret[$i]['title'] = $items[$i]->getVar('title', 'n');
                $ret[$i]['timestamp'] = $items[$i]->getVar('datesub');
                $ret[$i]['description'] = $items[$i]->getVar('summary');
                $ret[$i]['category'] = $this->modname;
                $ret[$i]['domain'] = XOOPS_URL . '/modules/' . $this->dirname . '/';
            }
        }

        return $ret;
    }
}
