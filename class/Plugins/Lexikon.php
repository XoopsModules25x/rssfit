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
 * Requirements (Tested with):
 *  Module: Wordbook <http://dev.xoops.org/modules/xfmod/project/?wordbook>
 *  Version: 1.17
 *  RSSFit version: 1.1 / 1.5
 *  XOOPS verson: 2.0.13.2 / 2.2.3 (!)
 */

use XoopsModules\Rssfit\{
    AbstractPlugin
};
use XoopsModules\Lexikon\Helper as PluginHelper;

if (!\defined('RSSFIT_ROOT_PATH')) {
    exit();
}

/**
 * Class lexikon
 */
final class Lexikon extends AbstractPlugin
{
    public $dirname = 'lexikon';

    /**
     * @return \XoopsModule
     */
    public function loadModule(): ?\XoopsModule{

        $mod = null;
        if (\class_exists(PluginHelper::class)) {
            $this->helper = PluginHelper::getInstance();
            $this->module = $this->helper->getModule();
            $this->modname = $this->module->getVar('name');
            $mod = $this->module;
            //        $this->dirname = $this->helper->getDirname();
        }

        return $mod;
    }

    /**
     * @return array
     */
    public function grabEntries(\XoopsMySQLDatabase $xoopsDB):?array
    {
        $myts = \MyTextSanitizer::getInstance();
        //$permiscHandler = xoops_getHandler('groupperm');
        $ret    = null;
        $i      = 0;
        $sql    = 'SELECT entryID, categoryID, term, definition, datesub FROM ' . $xoopsDB->prefix('lxentries') . ' WHERE submit = 0 AND offline = 0 ORDER BY datesub DESC';
        $result = $xoopsDB->query($sql, $this->grab, 0);
        if ($result instanceof \mysqli_result) {
            $ret = [];
            while (false !== ($row = $xoopsDB->fetchArray($result))) {
                //  required
                $ret[$i]['title'] = $row['term'];
                $link             = XOOPS_URL . '/modules/' . $this->dirname . '/entry.php?entryID=' . $row['entryID'];
                //$ret[$i]['link'] = $ret[$i]['guid'] = $link;
                $ret[$i]['link']        = $link;
                $ret[$i]['timestamp']   = $row['datesub'];
                $ret[$i]['description'] = $myts->displayTarea($row['definition']);
                //  optional
                //5. The item synopsis, or description, whatever
                //$ret[$i]['guid'] = $link;
                //  6. A string + domain that identifies a categorization taxonomy
                $ret[$i]['category'] = $this->modname;
                $ret[$i]['domain']   = XOOPS_URL . '/modules/' . $this->dirname . '/';
                /*$ret[$i]['extras'] = [];
                //  7a. without attribute
                $ret[$i]['extras']['author'] = array('content' => 'aabbc@c.com');
                //  7b. with attributes
                $ret[$i]['extras']['enclosure']['attributes'] = array('url' => 'url-to-any-file', 'length' => 1024000, 'type' => 'audio/mpeg');
                */
                $i++;
            }
        }

        return $ret;
    }
}
