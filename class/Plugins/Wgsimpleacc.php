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

/*
* This file is a dummy for making a RSSFit plug-in, follow the following steps
* if you really want to do so.
* Step 0:   Stop here if you are not sure what you are doing, it's no fun at all
* Step 1:   Clone this file and rename as something like rssfit.[mod_dir].php
* Step 2:   Replace the text "RssfitSample" with "Rssfit[mod_dir]" at line 59 and
*           line 65, i.e. "RssfitNews" for the module "News"
* Step 3:   Modify the word in line 60 from 'sample' to [mod_dir]
* Step 4:   Modify the function "grabEntries" to satisfy your needs
* Step 5:   Move your new plug-in file to the RSSFit plugins folder,
*           i.e. your-xoops-root/modules/rssfit/plugins
* Step 6:   Install your plug-in by pointing your browser to
*           your-xoops-url/modules/rssfit/admin/?do=plugins
* Step 7:   Finally, tell us about yourself and this file by modifying the
*           "About this RSSFit plug-in" section which is located... somewhere.
*
* [mod_dir]: Name of the driectory of your module, i.e. 'news'
*
* About this RSSFit plug-in
* Author: John Doe <http://www.your.site>
* Requirements (or Tested with):
*  Module: Blah <http://www.where.to.find.it>
*  Version: 1.0
*  RSSFit verision: 1.2 / 1.5
*  XOOPS version: 2.0.13.2 / 2.2.3
*/

if (!\defined('RSSFIT_ROOT_PATH')) {
    exit();
}

/**
 * Class Sample
 * @package XoopsModules\Rssfit\Plugins
 */
class Wgsimpleacc
{
    public $dirname = 'wgsimpleacc';
    public $modname;
    public $grab;
    public $module;    // optional, see line 71

    /**
     * @return false|string
     */
    public function loadModule()
    {
        $mod = $GLOBALS['module_handler']->getByDirname($this->dirname);
        if (!$mod || !$mod->getVar('isactive')) {
            return false;
        }
        $this->modname = $mod->getVar('name');
        $this->module  = $mod;    // optional, remove this line if there is nothing
        // to do with module info when grabbing entries
        return $mod;
    }

    /**
     * @param \XoopsObject $obj
     * @return bool|array
     */
    public function grabEntries(&$obj)
    {
        global $xoopsDB;
        $myts = \MyTextSanitizer::getInstance();
        $ret  = false;
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
                if (\strlen(\strip_tags($row['cli_name'])) > 0) {
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
