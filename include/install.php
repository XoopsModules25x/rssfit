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
use XoopsModules\Rssfit;

require_once \dirname(__DIR__) . '/preloads/autoloader.php';
require_once \dirname(__DIR__, 3) . '/mainfile.php';

/**
 * @param \XoopsModule $xoopsMod
 *
 * @return bool
 */
function xoops_module_install_rssfit(\XoopsModule $xoopsMod)
{
    global $xoopsDB, $xoopsConfig;

    $moduleDirName = \basename(\dirname(__DIR__));

    $helper = Rssfit\Helper::getInstance();

    $myts = \MyTextSanitizer::getInstance();
//    rssfInstallLangFile($xoopsMod, $xoopsConfig['language']);
    xoops_loadLanguage('install', $moduleDirName);
    $intro_setting = ['dohtml' => 1, 'dobr' => 1, 'sub' => stripslashes(_INSTALL_INTRO_SUB)];
    $sql[] = 'INSERT INTO `'
                     . $xoopsDB->prefix($helper->getDirname() . '_misc')
                     . '` VALUES (1, '
                     . $xoopsDB->quoteString('intro')
                     . ', '
                     . $xoopsDB->quoteString(stripslashes(_INTRO_TITLE))
                     . ', '
                     . $xoopsDB->quoteString(stripslashes(_INTRO_CONTENT))
                     . ', '
                     . $xoopsDB->quoteString(serialize($intro_setting))
                     . ')';
    $sql[] = rssfInsertChannel($xoopsMod);
    $sql[]         = 'INSERT INTO ' . $xoopsDB->prefix($helper->getDirname() . '_misc') . ' VALUES ' . "(null, 'sticky', '', '', " . $xoopsDB->quoteString(serialize(['dohtml' => 0, 'dobr' => 0, 'feeds' => [0 => '0'], 'link' => XOOPS_URL])) . ')';
    foreach ($sql as $s) {
        if (false === $xoopsDB->query($s)) {
            echo '<span style="color: #ff0000;"><b>' . $xoopsDB->error() . '<b></span><br>' . $s . '<br><br>';

            return false;
        }
    }

    return true;
}

/**
 * @param \XoopsModule $xoopsMod
 * @param int          $oldversion version number of prevviously installed version
 *
 * @return bool
 */
function xoops_module_update_rssfit(\XoopsModule $xoopsMod, $oldversion)
{
    global $xoopsDB, $xoopsConfig;
    $helper = Rssfit\Helper::getInstance();
//    rssfInstallLangFile($xoopsMod, $xoopsConfig['language']);
    $moduleDirName = \basename(\dirname(__DIR__));
    xoops_loadLanguage('install', $moduleDirName);
    [$rows] = $xoopsDB->fetchRow($xoopsDB->query('SELECT COUNT(*) FROM ' . $xoopsDB->prefix($helper->getDirname() . '_misc') . " WHERE misc_category = 'channel'"));
    if (!$rows) {
        //        $sql[]         = 'ALTER TABLE `' . $xoopsDB->prefix($helper->getDirname() . '_misc') . '` ADD `misc_setting` TEXT NOT NULL;';
        //        $sql[]         = 'ALTER TABLE `' . $xoopsDB->prefix($helper->getDirname() . '_misc') . '` CHANGE `misc_category` `misc_category` VARCHAR( 30 ) NOT NULL;';
        $intro_setting = ['dohtml' => 1, 'dobr' => 1, 'sub' => _INSTALL_INTRO_SUB];
        $sql[] = 'UPDATE `' . $xoopsDB->prefix($helper->getDirname() . '_misc') . '` SET misc_setting = ' . $xoopsDB->quoteString(serialize($intro_setting)) . " WHERE misc_category = 'intro'";
        //        $sql[]         = 'ALTER TABLE `'
        //                         . $xoopsDB->prefix($helper->getDirname() . '_plugins')
        //                         . "` ADD `subfeed` TINYINT( 1 ) DEFAULT '0' NOT NULL, ADD `sub_entries` VARCHAR( 2 ) NOT NULL, ADD `sub_link` VARCHAR( 255 ) NOT NULL, ADD `sub_title` VARCHAR( 255 ) NOT NULL, ADD `sub_desc` VARCHAR( 255 ) NOT NULL, ADD `img_url` VARCHAR( 255 ) NOT NULL, ADD `img_link` VARCHAR( 255 ) NOT NULL, ADD `img_title` VARCHAR( 255 ) NOT NULL;";
        $sql[] = 'UPDATE `' . $xoopsDB->prefix($helper->getDirname() . '_plugins') . '` SET sub_entries = 5';
        $sql[] = rssfInsertChannel($xoopsMod);
        $sql[] = 'INSERT INTO ' . $xoopsDB->prefix($helper->getDirname() . '_misc') . ' VALUES ' . "('', 'sticky', '', '', " . $xoopsDB->quoteString(serialize(['dohtml' => 0, 'dobr' => 0, 'feeds' => [0 => '0'], 'link' => XOOPS_URL])) . ')';
        foreach ($sql as $s) {
            if (false === $xoopsDB->query($s)) {
                echo '<span style="color: #ff0000;"><b>' . $xoopsDB->error() . '<b></span><br>' . $s . '<br><br>';

                return false;
            }
        }
    }

    return true;
}

/**
 * @param $xoopsMod
 * @param $lang
 */
//function rssfInstallLangFile($xoopsMod, $lang)
//{
//    $file = XOOPS_ROOT_PATH . '/modules/' . $xoopsMod->getVar('dirname') . '/language/%s/install.php';
//    if (is_file(sprintf($file, $lang))) {
//        include sprintf($file, $lang);
//    } else {
//        include sprintf($file, 'english');
//    }
//}

/**
 * @param $xoopsMod
 *
 * @return string
 */
function rssfInsertChannel($xoopsMod)
{
    global $xoopsDB, $xoopsConfig;
    $helper = Rssfit\Helper::getInstance();
    $url = $xoopsDB->quoteString(XOOPS_URL);
    $sitename = $xoopsDB->quoteString($xoopsConfig['sitename']);
    [$copyright] = $xoopsDB->fetchRow($xoopsDB->query('SELECT conf_value FROM ' . $xoopsDB->prefix('config') . " WHERE conf_name = 'meta_copyright' AND conf_modid = 1 AND conf_catid = " . XOOPS_CONF_METAFOOTER));

    return 'INSERT INTO '
           . $xoopsDB->prefix($helper->getDirname() . '_misc')
           . ' VALUES '
           . "(0, 'channel', 'title', "
           . $sitename
           . ", '')"
           . ", (0, 'channel', 'link', "
           . $url
           . ", '')"
           . ", (0, 'channel', 'description', "
           . $xoopsDB->quoteString($xoopsConfig['slogan'])
           . ", ''), (0, 'channel', 'copyright', "
           . $xoopsDB->quoteString($copyright)
           . ", ''), (0, 'channel', 'managingEditor', "
           . $xoopsDB->quoteString($xoopsConfig['adminmail'] . ' (' . $xoopsConfig['sitename'] . ')')
           . ", ''), (0, 'channel', 'webMaster', "
           . $xoopsDB->quoteString($xoopsConfig['adminmail'] . ' (' . $xoopsConfig['sitename'] . ')')
           . ", '')"
           . ", (0, 'channel', 'category', '', '')"
           . ", (0, 'channel', 'generator', "
           . $xoopsDB->quoteString(XOOPS_VERSION . ' / RSSFit ' . $xoopsMod->getInfo('version'))
           . ", ''), (0, 'channel', 'docs', "
           . $xoopsDB->quoteString('http://blogs.law.harvard.edu/tech/rss')
           . ", '')"
           . ", (0, 'channelimg', 'url', "
           . $xoopsDB->quoteString(XOOPS_URL . '/images/logo.gif')
           . ", '')"
           . ", (0, 'channelimg', 'title', "
           . $sitename
           . ", '')"
           . ", (0, 'channelimg', 'link', "
           . $url
           . ", '')"
           . ';';
}
