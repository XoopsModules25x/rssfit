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
*  Module: SmartSection <http://www.smartfactory.ca>
*  Version: 1.0.4 Beta 2 / 1.1 Beta 1 / 1.05 Beta 1
*  RSSFit verision: 1.2 / 1.5
*  XOOPS version: 2.0.13.2 / 2.2.3
*/

use XoopsModules\Publisher\{
    Helper as PluginHelper,
    ItemHandler,
};
use XoopsModules\Rssfit\{
    AbstractPlugin
};

if (!\defined('RSSFIT_ROOT_PATH')) {
    exit();
}

/**
 * Class Publisher
 */
final class Publisher extends AbstractPlugin
{
    public $dirname = 'publisher';

    public function loadModule(): ?\XoopsModule
    {
        $mod = null;
        if (\class_exists(PluginHelper::class)) {
            $this->helper  = PluginHelper::getInstance();
            $this->module  = $this->helper->getModule();
            $this->modname = $this->module->getVar('name');
            $mod           = $this->module;
            //        $this->dirname = $this->helper->getDirname();
        }

        return $mod;
    }

    /**
     * @return array
     */
    public function grabEntries(\XoopsMySQLDatabase $xoopsDB): ?array
    {
        $ret = null;
        /** @var ItemHandler $itemHandler */
        $itemHandler = $this->helper->getHandler('Item');
        $items       = $itemHandler->getAllPublished($this->grab, 0);
        if (\count($items) > 0) {
            $ret = [];
            for ($i = 0, $iMax = \count($items); $i < $iMax; ++$i) {
                $ret[$i]['guid']        = $items[$i]->getItemUrl();
                $ret[$i]['link']        = $ret[$i]['guid'];
                $ret[$i]['title']       = $items[$i]->getVar('title', 'n');
                $ret[$i]['timestamp']   = $items[$i]->getVar('datesub');
                $ret[$i]['description'] = $items[$i]->getVar('summary');
                $ret[$i]['category']    = $this->modname;
                $ret[$i]['domain']      = XOOPS_URL . '/modules/' . $this->dirname . '/';
            }
        }

        return $ret;
    }
}
