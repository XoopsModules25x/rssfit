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


use XoopsModules\Rssfit\{
    AbstractPlugin
};
use XoopsModules\Wgsimpleacc\Helper as PluginHelper;

if (!\defined('RSSFIT_ROOT_PATH')) {
    exit();
}

/**
 * Class Sample
 * @package XoopsModules\Rssfit\Plugins
 */
final class Wgsimpleacc extends AbstractPlugin
{
    public function __construct() {
        if (\class_exists(PluginHelper::class)) {
            $this->helper = PluginHelper::getInstance();
            $this->dirname = $this->helper->dirname();
        }
    }

    public function grabEntries(\XoopsMySQLDatabase $xoopsDB): ?array
    {
        $ret  = null;
        $i    = 0;
        //  The following example code grabs the latest entries from the module
        $sql  = 'SELECT t.tra_id, t.tra_datecreated, t.tra_year, t.tra_nb, t.tra_desc, a1.acc_key, a1.acc_name, a2.all_name, a3.as_name, c.cli_name ';
        $sql .= 'FROM (((' . $xoopsDB->prefix('wgsimpleacc_transactions') . ' t INNER JOIN ' . $xoopsDB->prefix('wgsimpleacc_accounts') . ' a1 ';
        $sql .= 'ON t.tra_accid = a1.acc_id) INNER JOIN ' . $xoopsDB->prefix('wgsimpleacc_allocations') . ' a2 ON t.tra_allid = a2.all_id) ';
        $sql .= 'INNER JOIN ' . $xoopsDB->prefix('wgsimpleacc_assets') . ' a3 ON t.tra_asid = a3.as_id) ';
        $sql .= 'LEFT JOIN ' . $xoopsDB->prefix('wgsimpleacc_clients') . ' c ON t.tra_cliid = c.cli_id ';
        $sql .= 'ORDER BY t.tra_datecreated DESC';

        $result = $xoopsDB->query($sql, $this->grab, 0);
        if ($result instanceof \mysqli_result) {
            $ret = [];
            while (false !== ($row = $xoopsDB->fetchArray($result))) {
                $link = XOOPS_URL . '/modules/' . $this->dirname . '/transactions.php?op=show&amp;tra_id=' . $row['tra_id'];
                /*
                * Required elements of an RSS item
                */
                //  1. Title of an item
                $ret[$i]['title'] = $row['tra_year'] . '/' . $row['tra_nb'];
                //  2. URL of an item
                $ret[$i]['link'] = $link;
                //  3. Item modification date, must be in Unix time format
                $ret[$i]['timestamp'] = $row['tra_datecreated'];
                //  4. The item synopsis, or description, whatever
                $ret[$i]['description'] = \strip_tags($row['tra_desc']);
                /*
                * Optional elements of an RSS item
                */
                //  5. The item synopsis, or description, whatever
                $ret[$i]['guid'] = $link;
                //  6. A string + domain that identifies a categorization taxonomy
                $ret[$i]['category'] = $this->modname;
                $ret[$i]['domain']   = XOOPS_URL . '/modules/' . $this->dirname . '/';
                //  7. extra tags examples
                $ret[$i]['extras'] = [];
                //  7a. without attribute
                $ret[$i]['extras']['account'] = ['content' => $row['acc_name']];
                $ret[$i]['extras']['allocation'] = ['content' => $row['all_name']];
                if ('' !== \strip_tags($row['cli_name'])) {
                    $ret[$i]['extras']['client'] = ['content' => \strip_tags($row['cli_name'])];
                }
                //  7b. with attributes
                //$ret[$i]['extras']['enclosure']['attributes'] = ['url' => 'url-to-any-file', 'length' => 1024000, 'type' => 'audio/mpeg'];
                $i++;
            }
        }
        return $ret;
    }
}
