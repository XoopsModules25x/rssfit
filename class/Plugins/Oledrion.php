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

use XoopsModules\Oledrion\Helper as PluginHelper;
use XoopsModules\Oledrion\Parameters;
use XoopsModules\Rssfit\{
    AbstractPlugin
};

if (!\defined('RSSFIT_ROOT_PATH')) {
    exit();
}

/**
 * Class Oledrion
 * @package XoopsModules\Rssfit\Plugins
 */
final class Oledrion extends AbstractPlugin
{
    public $dirname = 'oledrion';

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
        $myts = \MyTextSanitizer::getInstance();
        $ret  = null;
        require_once XOOPS_ROOT_PATH . '/modules/oledrion/include/common.php';
        $helper          = PluginHelper::getInstance();
        $productsHandler = $helper->getHandler('Products');
        $items           = $productsHandler->getRecentProducts(new Parameters(['start' => 0, 'limit' => $this->grab]));
        $i               = 0;

        if (false !== $items && \count($items) > 0) {
            $ret = [];
            foreach ($items as $item) {
                $ret[$i]['link']      = $ret[$i]['guid'] = $item->getLink();
                $ret[$i]['title']     = $item->getVar('product_title', 'n');
                $ret[$i]['timestamp'] = $item->getVar('product_submitted');
                if ('' != \xoops_trim($item->getVar('product_summary'))) {
                    $description = $item->getVar('product_summary');
                } else {
                    $description = $item->getVar('product_description');
                }
                $ret[$i]['description'] = $description;
                $ret[$i]['category']    = $this->modname;
                $ret[$i]['domain']      = XOOPS_URL . '/modules/' . $this->dirname . '/';
                $i++;
            }
        }

        return $ret;
    }
}
