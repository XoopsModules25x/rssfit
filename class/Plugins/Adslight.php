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
 * Adslight RSSFit plugin based on Jobs RSSFit plugin by www.jlmzone.com
 * Done by Bjuti (www.bjuti.info)
 *  Last release date:  Jan. 18 2010
 *  RSSFit version: 1.2 / 1.5
 *  XOOPS version: 2.0.13.2 / 2.2.3 / 2.3.2b / 2.4.3
 */

use XoopsModules\Adslight\Helper as PluginHelper;
use XoopsModules\Rssfit\{
    AbstractPlugin
};

if (!\defined('RSSFIT_ROOT_PATH')) {
    exit();
}

/**
 * Class Adslight
 */
final class Adslight extends AbstractPlugin
{
    public function __construct() {
        if (\class_exists(PluginHelper::class)) {
            $this->helper = PluginHelper::getInstance();
            $this->dirname = $this->helper->dirname();
        }
    }


    public function grabEntries(\XoopsMySQLDatabase $xoopsDB): ?array
    {
        $ret    = null;
        $i      = 0;
        $sql    = 'SELECT lid, title, status, desctext, date from ' . $xoopsDB->prefix('adslight_listing') . " WHERE valid = 'Yes' ORDER BY date DESC";
        $result = $xoopsDB->query($sql, $this->grab, 0);
        if ($result instanceof \mysqli_result) {
            $ret = [];
            while (false !== ($row = $xoopsDB->fetchArray($result))) {
                $link                   = XOOPS_URL . '/modules/' . $this->dirname . '/viewads.php?lid=' . $row['lid'] ?? '';
                $ret[$i]['title']       = $row['title'];
                $ret[$i]['link']        = $link;
                $ret[$i]['timestamp']   = $row['date'];
                $ret[$i]['description'] = $row['desctext'];  // $myts->displayTarea($row['desctext']);
                $ret[$i]['extras']      = [];
                $i++;
            }
        }

        return $ret;
    }
}
