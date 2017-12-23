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
if (function_exists('mb_http_output')) {
    mb_http_output('pass');
}
require __DIR__ . '/header.php';
$charset  = $xoopsModuleConfig['utf8'] ? 'UTF-8' : _CHARSET;
$docache  = $xoopsModuleConfig['cache'] ? true : false;
$template = 'db:rssfit_rss.tpl';
if (3 == $xoopsModuleConfig['mime']) {
    $xoopsLogger->enableRendering();
    $xoopsLogger->usePopup = (2 == $xoopsConfig['debug_mode']);
    $docache               = false;
} else {
    error_reporting(0);
    $xoopsLogger->activated = false;
}

require_once XOOPS_ROOT_PATH . '/class/template.php';
$xoopsTpl = new \XoopsTpl();
if (!$docache) {
    $xoopsTpl->xoops_setCaching(0);
} else {
    $xoopsTpl->xoops_setCaching(2);
    $xoopsTpl->xoops_setCacheTime($xoopsModuleConfig['cache'] * 60);
}

$feed           = [];
$feed['plugin'] = isset($_GET[$rss->feedkey]) ? trim($_GET[$rss->feedkey]) : '';
$rss->checkSubFeed($feed);
if (!$xoopsTpl->is_cached($template, $rss->cached) || !$docache) {
    $xoopsTpl->assign('rss_encoding', $charset);
    $rss->buildFeed($feed);
    $xoopsTpl->assign('feed', $feed);
}

switch ($xoopsModuleConfig['mime']) {
    default:
        header('Content-Type:text/xml; charset=' . $charset);
        break;
    case 2:
    case 3:
        header('Content-Type:text/html; charset=' . $charset);
        break;
}

# if( $xoopsModuleConfig['mime'] == 3 ){
# 	$src = $xoopsTpl->fetch($template, $rss->cached, null);
# 	unset($xoopsOption['template_main']);
# 	require XOOPS_ROOT_PATH.'/header.php';
# 	echo '<textarea cols="90" rows="20">'.$src.'</textarea><br>';
# 	require XOOPS_ROOT_PATH.'/footer.php';
# }

if (function_exists('mb_convert_encoding') && $xoopsModuleConfig['utf8']) {
    echo mb_convert_encoding($xoopsTpl->fetch($template, $rss->cached, null), 'UTF-8', _CHARSET);
} else {
    $xoopsTpl->display($template, $rss->cached);
}
