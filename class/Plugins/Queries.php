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

/**
 * About this RSSFit plug-in
 * Author: Richard Griffith <richard@geekwright.com>
 * Requirements (or Tested with):
 *  Module: Queries https://github.com/geekwright/queries
 *  Version: 1.0
 *  RSSFit verision: 1.3
 *  XOOPS version: 2.5.9
 */
if (!defined('RSSFIT_ROOT_PATH')) {
    exit();
}

/**
 * Class Queries
 * @package XoopsModules\Rssfit\Plugins
 */
class Queries
{
    public $dirname = 'queries';
    public $modname;
    public $grab;
    public $module;

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
        $this->module = $mod;    // optional, remove this line if there is nothing
        // to do with module info when grabbing entries
        return $mod;
    }

    /**
     * @param $obj
     * @return bool
     */
    public function &grabEntries(&$obj)
    {
        global $xoopsDB;
        $myts = \MyTextSanitizer::getInstance();
        $ret = false;

        $i = -1;
        $lasttime = false;
        $lastuser = false;
        $limit = 10 * $this->grab;

        $sql = 'SELECT id, title, posted, querytext FROM ' . $xoopsDB->prefix('queries_query');
        $sql .= ' WHERE approved=1 ORDER BY posted DESC ';

        $result = $xoopsDB->query($sql, $limit, 0);
        while (false !== ($row = $xoopsDB->fetchArray($result))) {
            ++$i;
            if ($i <= $this->grab) {
                $desc = $row['querytext'];
                if (mb_strlen($desc) > 200) {
                    $desc = mb_substr($desc, 0, 200) . '...';
                }
                $link = XOOPS_URL . '/modules/queries/view.php?id=' . $row['id'];
                $ret[$i]['title'] = $this->modname . ': ' . $row['title'];
                $ret[$i]['link'] = $link;
                $ret[$i]['timestamp'] = $row['posted'];
                $ret[$i]['guid'] = $link;
                $ret[$i]['category'] = $this->modname;
                $ret[$i]['description'] = $desc;
            }
            if ($i > $this->grab) {
                break;
            }
        }

        return $ret;
    }
}
