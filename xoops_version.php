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
 * @license      GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package     RSSFit - Extendable XML news feed generator
 * @since
 * @author       XOOPS Development Team
 * @author       NS Tai (aka tuff) <http://www.brandycoke.com/>
 */


defined('XOOPS_ROOT_PATH') || exit('XOOPS root path not defined');

$moduleDirName = basename(__DIR__);

// ------------------- Informations ------------------- //
$modversion = [
    'version'             => 1.31,
    'module_status'       => 'Beta 2',
    'release_date'        => '2017/12/20',
    'name'                => _MI_RSSFIT_NAME,
    'description'         => _MI_RSSFIT_DESC,
    'official'            => 0,
    //1 indicates official XOOPS module supported by XOOPS Dev Team, 0 means 3rd party supported
    'author'              => 'NS Tai (aka tuff)',
    'credits'             => 'XOOPS Development Team, Brandycoke Productions',
    'author_mail'         => 'author-email',
    'author_website_url'  => 'https://xoops.org',
    'author_website_name' => 'XOOPS',
    'license'             => 'GPL 2.0 or later',
    'license_url'         => 'www.gnu.org/licenses/gpl-2.0.html/',
    'help'                => 'page=help',
    // ------------------- Folders & Files -------------------
    'release_info'        => 'Changelog',
    'release_file'        => XOOPS_URL . "/modules/$moduleDirName/docs/changelog.txt",
    //
    'manual'              => 'link to manual file',
    'manual_file'         => XOOPS_URL . "/modules/$moduleDirName/docs/install.txt",
    // images
    'image'               => 'assets/images/logoModule.png',
    'iconsmall'           => 'assets/images/iconsmall.png',
    'iconbig'             => 'assets/images/iconbig.png',
    'dirname'             => $moduleDirName,
    // Local path icons
    'modicons16'          => 'assets/images/icons/16',
    'modicons32'          => 'assets/images/icons/32',
    //About
    'demo_site_url'       => 'https://xoops.org',
    'demo_site_name'      => 'XOOPS Demo Site',
    'support_url'         => 'https://xoops.org/modules/newbb/viewforum.php?forum=28/',
    'support_name'        => 'Support Forum',
    'submit_bug'          => 'https://github.com/XoopsModules25x/' . $moduleDirName . '/issues',
    'module_website_url'  => 'www.xoops.org',
    'module_website_name' => 'XOOPS Project',
    // ------------------- Min Requirements -------------------
    'min_php'             => '5.5',
    'min_xoops'           => '2.5.9',
    'min_admin'           => '1.2',
    'min_db'              => ['mysql' => '5.5'],
    // ------------------- Admin Menu -------------------
    'system_menu'         => 1,
    'hasAdmin'            => 1,
    'adminindex'          => 'admin/index.php',
    'adminmenu'           => 'admin/menu.php',
    // ------------------- Main Menu -------------------
    'hasMain'             => 1,
    // ------------------- Install/Update -------------------
    'onInstall'           => 'include/oninstall.php',
    'onUpdate'            => 'include/onupdate.php',
    'onUninstall'         => 'include/onuninstall.php',
    // -------------------  PayPal ---------------------------
    'paypal'              => [
        'business'      => 'foundation@xoops.org',
        'item_name'     => 'Donation : ' . _MI_RSSFIT_NAME,
        'amount'        => 0,
        'currency_code' => 'USD'
    ],
    // ------------------- Mysql -----------------------------
    'sqlfile'             => ['mysql' => 'sql/mysql.sql'],
    // ------------------- Tables ----------------------------
    'tables'              => [
        $moduleDirName . '_' . 'plugins',
        $moduleDirName . '_' . 'misc',
    ],
];

// ------------------- Help files ------------------- //
$modversion['helpsection'] = [
    ['name' => _MI_RSSFIT_OVERVIEW, 'link' => 'page=help'],
    ['name' => _MI_RSSFIT_DISCLAIMER, 'link' => 'page=disclaimer'],
    ['name' => _MI_RSSFIT_LICENSE, 'link' => 'page=license'],
    ['name' => _MI_RSSFIT_SUPPORT, 'link' => 'page=support'],
];

// Templates
$modversion['templates']   = [];
$modversion['templates'][] = [
    'file'        => 'rssfit_index.tpl',
    'description' => _MI_RSSFIT_TMPL_INTRO,
];
$modversion['templates'][] = [
    'file'        => 'rssfit_rss.tpl',
    'description' => _MI_RSSFIT_TMPL_RSS,
];

//	Module Configs
// $xoopsModuleConfig['overall_entries']

$modversion['config'][] = [
    'name'        => 'overall_entries',
    'title'       => '_MI_RSSFIT_OVERALL_ENTRIES',
    'description' => '_MI_RSSFIT_OVERALL_ENTRIES_DESC',
    'formtype'    => 'textbox',
    'valuetype'   => 'int',
    'default'     => 20,
];

// $xoopsModuleConfig['plugin_entries']
$modversion['config'][] = [
    'name'        => 'plugin_entries',
    'title'       => '_MI_RSSFIT_PLUGIN_ENTRIES',
    'description' => '_MI_RSSFIT_PLUGIN_ENTRIES_DESC',
    'formtype'    => 'textbox',
    'valuetype'   => 'int',
    'default'     => 5,
];

// $xoopsModuleConfig['sort']
$modversion['config'][] = [
    'name'        => 'sort',
    'title'       => '_MI_RSSFIT_ENTRIES_SORT',
    'description' => '_MI_RSSFIT_ENTRIES_SORT_DESC',
    'formtype'    => 'select',
    'valuetype'   => 'text',
    'default'     => 'd',
    'options'     => [_MI_RSSFIT_ENTRIES_SORT_DATE => 'd', _MI_RSSFIT_ENTRIES_SORT_CAT => 'c'],
];

// $xoopsModuleConfig['cache']
$modversion['config'][] = [
    'name'        => 'cache',
    'title'       => '_MI_RSSFIT_CACHE',
    'description' => '_MI_RSSFIT_CACHE_DESC',
    'formtype'    => 'textbox',
    'valuetype'   => 'int',
    'default'     => 0,
];

// $xoopsModuleConfig['max_char']
$modversion['config'][] = [
    'name'        => 'max_char',
    'title'       => '_MI_RSSFIT_MAXCHAR',
    'description' => '_MI_RSSFIT_MAXCHAR_DESC',
    'formtype'    => 'textbox',
    'valuetype'   => 'int',
    'default'     => 0,
];

// $xoopsModuleConfig['strip_html']
$modversion['config'][] = [
    'name'        => 'strip_html',
    'title'       => '_MI_RSSFIT_STRIPHTML',
    'description' => '_MI_RSSFIT_STRIPHTML_DESC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 0,
];

// $xoopsModuleConfig['utf8']
$modversion['config'][] = [
    'name'        => 'utf8',
    'title'       => '_MI_RSSFIT_ENCODE_UTF8',
    'description' => '_MI_RSSFIT_ENCODE_UTF8_DESC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 0,
];

// $xoopsModuleConfig['mime']
$modversion['config'][] = [
    'name'        => 'mime',
    'title'       => '_MI_RSSFIT_OUTOUT_MIME',
    'description' => '',
    'formtype'    => 'select',
    'valuetype'   => 'int',
    'default'     => 1,
    'options'     => [_MI_RSSFIT_OUTOUT_MIME_XML => 1, _MI_RSSFIT_OUTOUT_MIME_HTML => 2, _MI_RSSFIT_OUTOUT_MIME_PHP => 3],
];
