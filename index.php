<?php
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
require __DIR__ . '/header.php';
$GLOBALS['xoopsOption']['template_main'] = 'rssfit_index.tpl';
require_once XOOPS_ROOT_PATH . '/header.php';
$intr = $miscHandler->getObjects2(new \Criteria('misc_category', 'intro'));
if ($intr) {
    $intro = $intr[0];
    $setting = $intro->getVar('misc_setting');
    $intro->setDoHtml($setting['dohtml'] ? 1 : 0);
    $intro->setDoBr($setting['dobr'] ? 1 : 0);
    $title = str_replace('{SITENAME}', $xoopsConfig['sitename'], $intro->getVar('misc_title'));
    $content = str_replace('{SITENAME}', $xoopsConfig['sitename'], $intro->getVar('misc_content'));
    $content = str_replace('{SITEURL}', XOOPS_URL . '/', $content);
    if (false !== mb_strpos($content, '{SUB}') && $plugins = $pluginHandler->getObjects2(new \Criteria('subfeed', 1))) {
        $sublist = '';
        foreach ($plugins as $p) {
            $sub = $myts->stripSlashesGPC($setting['sub']);
            $sub = str_replace('{URL}', $feedHandler->subFeedUrl($p->getVar('rssf_filename')), $sub);
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
require XOOPS_ROOT_PATH . '/footer.php';
