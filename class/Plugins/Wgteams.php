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
use XoopsModules\Wgteams\Helper as PluginHelper;

if (!\defined('RSSFIT_ROOT_PATH')) {
    exit();
}

/**
 * Class Sample
 * @package XoopsModules\Rssfit\Plugins
 */
final class Wgteams extends AbstractPlugin
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
        //  The following example code grabs the latest entries from the module wgteams
        $sql  = 'SELECT r.rel_date_create, t.team_id, t.team_name, m.member_firstname, m.member_lastname ';
        $sql .= 'FROM (' . $xoopsDB->prefix('wgteams_relations') . ' r INNER JOIN ' . $xoopsDB->prefix('wgteams_teams') . ' t ON r.rel_team_id = t.team_id) ';
        $sql .= 'INNER JOIN ' . $xoopsDB->prefix('wgteams_members') . ' m ON r.rel_member_id = m.member_id ORDER BY r.rel_date_create DESC';
        $result = $xoopsDB->query($sql, $this->grab, 0);
        if ($result instanceof \mysqli_result) {
            $ret = [];
            while (false !== ($row = $xoopsDB->fetchArray($result))) {
                $link = XOOPS_URL . '/modules/' . $this->dirname . '/index.php?team_id=' . $row['team_id'];
                /*
                * Required elements of an RSS item
                */
                //  1. Title of an item
                $ret[$i]['title'] = $row['team_name'];
                //  2. URL of an item
                $ret[$i]['link'] = $link;
                //  3. Item modification date, must be in Unix time format
                $ret[$i]['timestamp'] = $row['rel_date_create'];
                //  4. The item synopsis, or description, whatever
                $ret[$i]['description'] = $row['member_firstname'] . ' ' . $row['member_lastname'];
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
                //$ret[$i]['extras']['author'] = ['content' => 'aabbc@c.com'];
                //  7b. with attributes
                //$ret[$i]['extras']['enclosure']['attributes'] = ['url' => 'url-to-any-file', 'length' => 1024000, 'type' => 'audio/mpeg'];
                $i++;
            }
        }
        return $ret;
    }
}
