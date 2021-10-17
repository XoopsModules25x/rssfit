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
 * Author: jayjay <http://www.sint-niklaas.be>
 * Requirements (Tested with):
 *  Module: Xoopstube <https://xoops.org>
 *  Version: 1.0
 *  RSSFit version: 1.21
 *  XOOPS version: 2.0.18.1
 */

use XoopsModules\Rssfit\{
    AbstractPlugin
};
use XoopsModules\Xoopstube\Helper as PluginHelper;

if (!\defined('RSSFIT_ROOT_PATH')) {
    exit();
}

/**
 * Class Xoopstube
 */
class Xoopstube extends AbstractPlugin
{
    /**
     * @var string
     */
    public $dirname = 'xoopstube';

    public function initialize(): void
    {
        if (class_exists(PluginHelper::class)) {
            $this->helper = PluginHelper::getInstance();
            $this->module = $this->helper->getModule();
            //        $this->dirname = $this->helper->getDirname();
        }
    }

    /**
     * @param \XoopsMySQLDatabase $xoopsDB
     * @return array
     */
    public function grabEntries(\XoopsMySQLDatabase $xoopsDB):?array
    {
        $myts = \MyTextSanitizer::getInstance();
        $ret  = null;
        $i    = 0;
        $sql  = 'SELECT l.lid, l.title as ltitle, l.date, l.cid, l.hits, l.description, c.title as ctitle FROM ' . $xoopsDB->prefix('xoopstube_videos') . ' l, ' . $xoopsDB->prefix('xoopstube_cat') . ' c WHERE l.cid=c.cid AND l.status>0 ORDER BY l.date DESC';

        $result = $xoopsDB->query($sql, $this->grab, 0);
        if ($result instanceof \mysqli_result) {
            $ret = [];
            while (false !== ($row = $xoopsDB->fetchArray($result))) {
                $ret[$i]['title']       = $row['ltitle'];
                $link                   = XOOPS_URL . '/modules/' . $this->dirname . '/singlevideo.php?cid=' . $row['cid'] . '&amp;lid=' . $row['lid'];
                $ret[$i]['link']        = $ret[$i]['guid'] = $link;
                $ret[$i]['timestamp']   = $row['date'];
                $ret[$i]['description'] = $myts->displayTarea($row['description']);
                $ret[$i]['category']    = $this->modname;
                $ret[$i]['domain']      = XOOPS_URL . '/modules/' . $this->dirname . '/';
                $i++;
            }
        }
        return $ret;
    }
}
