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

//use XoopsModules\Oledrion;

if (!defined('RSSFIT_ROOT_PATH')) {
    exit();
}

/**
 * Class Oledrion
 * @package XoopsModules\Rssfit\Plugins
 */
class Oledrion
{
    public $dirname = 'oledrion';
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
     * @param $obj
     * @return bool
     */
    public function &grabEntries(&$obj)
    {
        $ret = false;
        require_once XOOPS_ROOT_PATH . '/modules/oledrion/include/common.php';
        $helper = \XoopsModules\Oledrion\Helper::getInstance();
        $productsHandler = $helper->getHandler('Products');
        $items = $productsHandler->getRecentProducts(new Oledrion\Parameters(['start' => 0, 'limit' => $this->grab]));
        $i = 0;

        if (false !== $items && count($items) > 0) {
            foreach ($items as $item) {
                $ret[$i]['link'] = $ret[$i]['guid'] = $item->getLink();
                $ret[$i]['title'] = $item->getVar('product_title', 'n');
                $ret[$i]['timestamp'] = $item->getVar('product_submitted');
                if ('' != xoops_trim($item->getVar('product_summary'))) {
                    $description = $item->getVar('product_summary');
                } else {
                    $description = $item->getVar('product_description');
                }
                $ret[$i]['description'] = $description;
                $ret[$i]['category'] = $this->modname;
                $ret[$i]['domain'] = XOOPS_URL . '/modules/' . $this->dirname . '/';
                $i++;
            }
        }

        return $ret;
    }
}
