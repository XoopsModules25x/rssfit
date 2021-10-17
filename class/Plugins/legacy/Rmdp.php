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
 * Author: agamen0n <http://www.tradux.xoopstotal.com.br>
 * Requirements:
 *  Module: RMDP <http://www.xoops-mexico.net>
 *  Version: 1.0
 *  RSSFit version: 1.1
 */
if (!\defined('RSSFIT_ROOT_PATH')) {
    exit();
}

/**
 * Class Rmdp
 */
class Rmdp extends \XoopsObject
{
    public $dirname = 'rmdp';
    public $modname;
    public $module;
    public $grab;

    /**
     * @return \XoopsModule
     */
    public function loadModule():?\XoopsModule
    {
        $mod = $GLOBALS['module_handler']->getByDirname($this->dirname);
        if (!$mod || !$mod->getVar('isactive')) {
            return null;
        }
        $this->modname = $mod->getVar('name');
        $this->module = $mod;

        return $mod;
    }

    /**
     * @param \XoopsMySQLDatabase $xoopsDB
     * @return array
     */
    public function grabEntries(\XoopsMySQLDatabase $xoopsDB):?array
    {
        $ret = null;
        $i = 0;
        $sql = 'SELECT id_soft, id_cat, nombre, fecha, longdesc FROM ' . $xoopsDB->prefix('rmdp_software') . ' ORDER BY fecha DESC';
        $result = $xoopsDB->query($sql, $this->grab, 0);
        if ($result instanceof \mysqli_result) {
            $ret = [];
            while (false !== ($row = $xoopsDB->fetchArray($result))) {
                $ret[$i]['title']       = $row['nombre'];
                $link                   = XOOPS_URL . '/modules/' . $this->dirname . '/down.php?id=' . $row['id_soft'];
                $ret[$i]['link']        = $ret[$i]['guid'] = $link;
                $ret[$i]['timestamp']   = $row['fecha'];
                $ret[$i]['description'] = $row['longdesc'];
                $ret[$i]['category']    = $this->modname;
                $ret[$i]['domain']      = XOOPS_URL . '/modules/' . $this->dirname . '/';
                $i++;
            }
        }
        return $ret;
    }
}
