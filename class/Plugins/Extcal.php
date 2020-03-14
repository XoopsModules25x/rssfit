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

/*
* This file is a dummy for making a RSSFit plug-in, follow the following steps
* if you really want to do so.
* Step 0:   Stop here if you are not sure what you are doing, it's no fun at all
* Step 1:   Clone this file and rename as something like rssfit.[mod_dir].php
* Step 2:   Replace the text "RssfitMyalbum" with "Rssfit[mod_dir]" at line 59 and
*           line 65, i.e. "RssfitNews" for the module "News"
* Step 3:   Modify the word in line 60 from 'Myalbum' to [mod_dir]
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

if (!defined('RSSFIT_ROOT_PATH')) {
    exit();
}

/**
 * Class Extcal
 * @package XoopsModules\Rssfit\Plugins
 */
class Extcal
{
    public $dirname = 'extcal';
    public $classname = 'extcal'; //mb
    public $modname;
    public $grab;
    public $module;    // optional, see line 74

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
     * @param \XoopsObject $obj
     * @return bool
     */
    public function &grabEntries(&$obj)
    {
        global $xoopsDB;
        $myts = \MyTextSanitizer::getInstance();
        $ret = false;

        $i = 0;

        // read confgs to get timestamp format
        $extcal = $this->module;
        $configHandler = xoops_getHandler('config');
        $extcalConfig = $configHandler->getConfigsByCat(0, $extcal->getVar('mid'));
        $long_form = $extcalConfig['date_long'];

        $eventHandler = \XoopsModules\Extcal\Helper::getInstance()->getHandler('Event');
        $catHandler = \XoopsModules\Extcal\Helper::getInstance()->getHandler('Cat');
        $events = $eventHandler->getUpcomingEvent(0, $this->grab, 0);

        if (is_array($events)) {
            foreach ($events as $event) {
                ++$i;

                $cat = $catHandler->getCat($event->getVar('cat_id'), 0);
                $category = $cat->getVar('cat_name');
                $link = XOOPS_URL . '/modules/extcal/event.php?event=' . $event->getVar('event_id');
                $event_start = formatTimestamp($event->getVar('event_start'), $long_form);
                $title = xoops_utf8_encode(htmlspecialchars($event->getVar('event_title'), ENT_QUOTES));
                $description = xoops_utf8_encode(htmlspecialchars($event->getVar('event_desc'), ENT_QUOTES));
                $address = $event->getVar('event_address');

                $desc_link = $event->getVar('event_url');
                if ('' == $desc_link) {
                    $desc_link = $link;
                }
                $desc = "<a href=\"$desc_link\"><b>$title</b></a><br>";
                $desc .= '<table>';
                $desc .= "<tr><td valign='top'>When:</td><td>$event_start</td></tr>";
                if ('' != $address) {
                    $desc .= "<tr><td valign='top'>Where:</td><td>$address</td></tr>";
                }
                $desc .= "<tr><td valign='top'>What:</td><td>$description</td></tr>";
                $desc .= '</table>';

                $ret[$i]['title'] = $category . ': ' . $title;
                $ret[$i]['link'] = $link;
                $ret[$i]['description'] = $desc;
                $ret[$i]['timestamp'] = $event->getVar('event_submitdate');
                //              $ret[$i]['timestamp'] = time();
                $ret[$i]['guid'] = $link;
                $ret[$i]['category'] = $category;
            }
        }

        return $ret;
    }
}
