<?php

declare(strict_types=1);
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
    FeedHandler,
    Helper
};

/** @var FeedHandler $feedHandler */

if (function_exists('mb_http_output')) {
    mb_http_output('pass');
}
require_once __DIR__ . '/header.php';
$helper = Helper::getInstance();

$charset  = $helper->getConfig('utf8') ? 'UTF-8' : _CHARSET;
$docache  = (bool)$helper->getConfig('cache');
$template = 'db:rssfit_rss.tpl';
if (3 == $helper->getConfig('mime')) {
    $xoopsLogger->enableRendering();
    $xoopsLogger->usePopup = (2 == $xoopsConfig['debug_mode']);
    $docache               = false;
} else {
    error_reporting(0);
    $xoopsLogger->activated = false;
}

require_once XOOPS_ROOT_PATH . '/class/template.php';
$xoopsTpl = new \XoopsTpl();
if ($docache) {
    $xoopsTpl->caching = 2;
    $xoopsTpl->xoops_setCacheTime($helper->getConfig('cache') * 60);
} else {
    $xoopsTpl->caching = 0;
}

$feed           = [];
$feed['plugin'] = isset($_GET['feed']) ? trim($_GET['feed']) : '';

$feedHandler->checkSubFeed($feed);
if (!$docache || !$xoopsTpl->is_cached($template, $feedHandler->cached)) {
    $xoopsTpl->assign('rss_encoding', $charset);
    $feedHandler->buildFeed($feed);
    $xoopsTpl->assign('feed', $feed);
}

switch ($helper->getConfig('mime')) {
    default:
        header('Content-Type:text/xml; charset=' . $charset);
        break;
    case 2:
    case 3:
        header('Content-Type:text/html; charset=' . $charset);
        break;
}

# if( $helper->getConfig('mime') == 3 ){
#   $src = $xoopsTpl->fetch($template, $feedHandler->cached, null);
#   unset($xoopsOption['template_main']);
#   require_once XOOPS_ROOT_PATH.'/header.php';
#   echo '<textarea cols="90" rows="20">'.$src.'</textarea><br>';
#   require_once XOOPS_ROOT_PATH.'/footer.php';
# }

if (function_exists('mb_convert_encoding') && $helper->getConfig('utf8')) {
    echo mb_convert_encoding($xoopsTpl->fetch($template, $feedHandler->cached, null), 'UTF-8', _CHARSET);
} else {
    $xoopsTpl->display($template, $feedHandler->cached);
}
