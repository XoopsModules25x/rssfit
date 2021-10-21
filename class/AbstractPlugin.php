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
    public $module;
    public $helper;
    public $dirname = '';


    public function loadModule(): ?\XoopsModule
    {
        $mod = null;
        if (null !== $this->helper) {
            $this->module  = $this->helper->getModule();
            $this->modname = $this->module->getVar('name');
            $mod           = $this->module;
        }
        return $mod;
    }

    public function grabEntries(\XoopsMySQLDatabase $xoopsDB): ?array
    {
        return null;
    }
}
