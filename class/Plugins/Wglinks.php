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
use XoopsModules\Wglinks\Helper as PluginHelper;

if (!\defined('RSSFIT_ROOT_PATH')) {
    exit();
}

/**
 * Class Sample
 * @package XoopsModules\Rssfit\Plugins
 */
final class Wglinks extends AbstractPlugin
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
        $sql  = 'SELECT l.link_id, l.link_name, l.link_detail, l.link_date_created, l.link_state, c.cat_name ';
        $sql .= 'FROM ' . $xoopsDB->prefix('wglinks_links') . ' l  INNER JOIN ' . $xoopsDB->prefix('wglinks_categories') . ' c ';
        $sql .= 'ON l.link_catid = c.cat_id WHERE (((l.link_state)=1)) ORDER BY l.link_date_created DESC';

        $result = $xoopsDB->query($sql, $this->grab, 0);
        if ($result instanceof \mysqli_result) {
            $ret = [];
            while (false !== ($row = $xoopsDB->fetchArray($result))) {
                $link = XOOPS_URL . '/modules/' . $this->dirname . '/index.php?op=show&amp;link_id=' . $row['link_id'];
                /*
                * Required elements of an RSS item
                */
                //  1. Title of an item
                $ret[$i]['title'] = $row['link_name'];
                //  2. URL of an item
                $ret[$i]['link'] = $link;
                //  3. Item modification date, must be in Unix time format
                $ret[$i]['timestamp'] = $row['link_date_created'];
                //  4. The item synopsis, or description, whatever
                $ret[$i]['description'] = $row['link_detail'];
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
                $ret[$i]['extras']['cat'] = ['content' => $row['cat_name']];
                //  7b. with attributes
                //$ret[$i]['extras']['enclosure']['attributes'] = ['url' => 'url-to-any-file', 'length' => 1024000, 'type' => 'audio/mpeg'];
                $i++;
            }
        }
        return $ret;
    }
}
