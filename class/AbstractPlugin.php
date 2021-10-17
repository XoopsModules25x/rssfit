<?php

declare(strict_types=1);

namespace XoopsModules\Rssfit;

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

if (!\defined('RSSFIT_ROOT_PATH')) {
    exit();
}

/**
 * Class AbstractPlugin
 * @package XoopsModules\Rssfit
 */
abstract class AbstractPlugin implements PluginInterface
{
    public $modname;
    public $grab;
    public $module;    // optional, see line 67
    public $helper;

    public function loadModule(): ?\XoopsModule
    {
        $mod = $GLOBALS['module_handler']->getByDirname($this->dirname);
        if (!$mod || !$mod->getVar('isactive')) {
            return null;
        }

        if (!$mod->getVar('isactive')) {
            return null;
        }
        $this->modname = $mod->getVar('name');
        $this->module = $mod;   // optional, remove this line if there is nothing to do with module info when grabbing entries

        return $mod;
    }

    public function grabEntries(\XoopsMySQLDatabase $xoopsDB): ?array
    {
    }
}
