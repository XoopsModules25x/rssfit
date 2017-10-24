<?php
###############################################################################
##                RSSFit - Extendable XML news feed generator                ##
##                Copyright (c) 2004 - 2006 NS Tai (aka tuff)                ##
##                       <http://www.brandycoke.com/>                        ##
###############################################################################
##                    XOOPS - PHP Content Management System                  ##
##                       Copyright (c) 2000 XOOPS.org                        ##
##                          <http://www.xoops.org/>                          ##
###############################################################################
##  This program is free software; you can redistribute it and/or modify     ##
##  it under the terms of the GNU General Public License as published by     ##
##  the Free Software Foundation; either version 2 of the License, or        ##
##  (at your option) any later version.                                      ##
##                                                                           ##
##  You may not change or alter any portion of this comment or credits       ##
##  of supporting developers from this source code or any supporting         ##
##  source code which is considered copyrighted (c) material of the          ##
##  original comment or credit authors.                                      ##
##                                                                           ##
##  This program is distributed in the hope that it will be useful,          ##
##  but WITHOUT ANY WARRANTY; without even the implied warranty of           ##
##  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            ##
##  GNU General Public License for more details.                             ##
##                                                                           ##
##  You should have received a copy of the GNU General Public License        ##
##  along with this program; if not, write to the Free Software              ##
##  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA ##
###############################################################################
##  Author of this file: NS Tai (aka tuff)                                   ##
##  URL: http://www.brandycoke.com/                                          ##
##  Project: RSSFit                                                          ##
###############################################################################

require 'header.php';
$xoopsOption['template_main'] = 'rssfit_index.tpl';
require XOOPS_ROOT_PATH.'/header.php';
if ($intr = $misc_handler->getObjects(new Criteria('misc_category', 'intro'))) {
    $intro =& $intr[0];
    $setting = $intro->getVar('misc_setting');
    $intro->setDoHtml($setting['dohtml'] ? 1 : 0);
    $intro->setDoBr($setting['dobr'] ? 1 : 0);
    $title = str_replace('{SITENAME}', $xoopsConfig['sitename'], $intro->getVar('misc_title'));
    $content = str_replace('{SITENAME}', $xoopsConfig['sitename'], $intro->getVar('misc_content'));
    $content = str_replace('{SITEURL}', XOOPS_URL.'/', $content);
    if (false !== strpos($content, '{SUB}') && $plugins = $plugins_handler->getObjects(new Criteria('subfeed', 1))) {
        $sublist = '';
        foreach ($plugins as $p) {
            $sub = $myts->stripSlashesGPC($setting['sub']);
            $sub = str_replace('{URL}', $rss->subFeedUrl($p->getVar('rssf_filename')), $sub);
            $sub = str_replace('{TITLE}', $p->getVar('sub_title'), $sub);
            $sub = str_replace('{DESC}', $p->getVar('sub_desc'), $sub);
            $sublist .= $sub;
        }
        $content = str_replace('{SUB}', $sublist, $content);
    } else {
        $content = str_replace('{SUB}', '', $content);
    }
    $xoopsTpl->assign('intro', ['title' => $title, 'content' => $content]);
}
require XOOPS_ROOT_PATH.'/footer.php';
