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

$GLOBALS['xoopsOption']['template_main'] = 'rssfit_index.tpl';
global $xoopsConfig, $xoopsTpl;

require_once __DIR__ . '/header.php';
require_once XOOPS_ROOT_PATH . '/header.php';

/** @var \XoopsModules\Rssfit\MiscHandler $miscHandler */
$intr = $miscHandler->getObjects2(new \Criteria('misc_category', 'intro'));
if ($intr) {
    $intro   = $intr[0];
    $setting = $intro->getVar('misc_setting');
    $intro->setDoHtml($setting['dohtml'] ? true : false);
    $intro->setDoBr($setting['dobr'] ? true : false);
    $title   = str_replace('{SITENAME}', $xoopsConfig['sitename'], $intro->getVar('misc_title'));
    $content = str_replace(['{SITENAME}', '{SITEURL}'], [$xoopsConfig['sitename'], XOOPS_URL . '/'], $intro->getVar('misc_content'));
    /** @var \XoopsModules\Rssfit\PluginHandler $pluginHandler */
    if (false !== mb_strpos($content, '{SUB}') && $plugins = $pluginHandler->getObjects2(new \Criteria('subfeed', '1'))) {
        $sublist = '';
        foreach ($plugins as $p) {
            $sub     = ($setting['sub']);
            $sub     = str_replace('{URL}', $feedHandler->subFeedUrl($p->getVar('rssf_filename')), $sub);
            $sub     = str_replace(['{TITLE}', '{DESC}'], [$p->getVar('sub_title'), $p->getVar('sub_desc')], $sub);
            $sublist .= $sub;
        }
        $content = str_replace('{SUB}', $sublist, $content);
    } else {
        $content = str_replace('{SUB}', '', $content);
    }
    $xoopsTpl->assign('intro', ['title' => $title, 'content' => $content]);
}
require_once XOOPS_ROOT_PATH . '/footer.php';
