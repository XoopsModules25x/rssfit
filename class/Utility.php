<?php namespace Xoopsmodules\rssfit;

/*
 Utility Class Definition

 You may not change or alter any portion of this comment or credits of
 supporting developers from this source code or any supporting source code
 which is considered copyrighted (c) material of the original comment or credit
 authors.

 This program is distributed in the hope that it will be useful, but
 WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */
/**
 * Module:  xSitemap
 *
 * @package      \module\xsitemap\class
 * @license      http://www.fsf.org/copyleft/gpl.html GNU public license
 * @copyright    https://xoops.org 2001-2017 &copy; XOOPS Project
 * @author       ZySpec <owners@zyspec.com>
 * @author       Mamba <mambax7@gmail.com>
 * @since        File available since version 1.54
 */

use Xmf\Request;
use Xoopsmodules\rssfit;
use Xoopsmodules\rssfit\common;

require_once __DIR__ . '/common/VersionChecks.php';
require_once __DIR__ . '/common/ServerStats.php';
require_once __DIR__ . '/common/FilesManagement.php';

//require_once __DIR__ . '/../include/common.php';

/**
 * Class Utility
 */
class Utility
{
    use common\VersionChecks; //checkVerXoops, checkVerPhp Traits

    use common\ServerStats; // getServerStats Trait

    use common\FilesManagement; // Files Management Trait

    //--------------- Custom module methods -----------------------------

    /**
     * @param $a
     * @param $b
     * @return int
     */
    public static function sortTimestamp($a, $b)
    {
        if ($a['timestamp'] == $b['timestamp']) {
            return 0;
        }
        return ($a['timestamp'] > $b['timestamp']) ? -1 : 1;
    }

    /**
     * @param int $spec
     * @param     $rss
     * @return string
     */
    public static function genSpecMoreInfo($spec = 0, $rss)
    {
        return static::rssfGenAnchor($rss->specUrl($spec), _AM_RSSFIT_EDIT_CHANNEL_QMARK, 'spec', _AM_RSSFIT_EDIT_CHANNEL_MOREINFO);
    }

    /**
     * @param string $url
     * @param string $text
     * @param string $target
     * @param string $title
     * @param string $class
     * @param string $id
     * @return string
     */
    public static function rssfGenAnchor($url = '', $text = '', $target = '', $title = '', $class = '', $id = '')
    {
        if (!empty($url)) {
            $ret = '';
            $ret .= '<a href="' . $url . '"';
            $ret .= !empty($target) ? ' target="' . $target . '"' : '';
            $ret .= !empty($class) ? ' class="' . $class . '"' : '';
            $ret .= !empty($id) ? ' id="' . $id . '"' : '';
            $ret .= !empty($title) ? ' title="' . $title . '"' : '';
            $ret .= '>' . $text . '</a>';
            return $ret;
        }
        return $text;
    }

    /**
     * @param \XoopsModule $xoopsMod
     * @param              $lang
     */
    public static function rssfInstallLangFile(\XoopsModule $xoopsMod, $lang)
    {
        $file = XOOPS_ROOT_PATH . '/modules/' . $xoopsMod->getVar('dirname') . '/language/%s/install.php';
        if (file_exists(sprintf($file, $lang))) {
            include sprintf($file, $lang);
        } else {
            include sprintf($file, 'english');
        }
    }

    /**
     * @param \XoopsModule $xoopsMod
     *
     * @return string
     */
    public static function rssfInsertChannel(\XoopsModule $xoopsMod)
    {
        global $xoopsConfig;
        $helper   = rssfit\Helper::getInstance();
        $url      = $GLOBALS['xoopsDB']->quoteString(XOOPS_URL);
        $sitename = $GLOBALS['xoopsDB']->quoteString($xoopsConfig['sitename']);
        list($copyright) = $GLOBALS['xoopsDB']->fetchRow($GLOBALS['xoopsDB']->query('SELECT conf_value FROM ' . $GLOBALS['xoopsDB']->prefix('config') . " WHERE conf_name = 'meta_copyright' AND conf_modid = 1 AND conf_catid = " . XOOPS_CONF_METAFOOTER));
        return 'INSERT INTO '
               . $GLOBALS['xoopsDB']->prefix($helper->getDirname() . '_misc')
               . ' VALUES '
               . "('', 'channel', 'title', "
               . $sitename
               . ", '')"
               . ", ('', 'channel', 'link', "
               . $url
               . ", '')"
               . ", ('', 'channel', 'description', "
               . $GLOBALS['xoopsDB']->quoteString($xoopsConfig['slogan'])
               . ", ''), ('', 'channel', 'copyright', "
               . $GLOBALS['xoopsDB']->quoteString($copyright)
               . ", ''), ('', 'channel', 'managingEditor', "
               . $GLOBALS['xoopsDB']->quoteString($xoopsConfig['adminmail'] . ' (' . $xoopsConfig['sitename'] . ')')
               . ", ''), ('', 'channel', 'webMaster', "
               . $GLOBALS['xoopsDB']->quoteString($xoopsConfig['adminmail'] . ' (' . $xoopsConfig['sitename'] . ')')
               . ", '')"
               . ", ('', 'channel', 'category', '', '')"
               . ", ('', 'channel', 'generator', "
               . $GLOBALS['xoopsDB']->quoteString(XOOPS_VERSION . ' / RSSFit ' . $xoopsMod->getInfo('version'))
               . ", ''), ('', 'channel', 'docs', "
               . $GLOBALS['xoopsDB']->quoteString('http://blogs.law.harvard.edu/tech/rss')
               . ", '')"
               . ", ('', 'channelimg', 'url', "
               . $GLOBALS['xoopsDB']->quoteString(XOOPS_URL . '/images/logo.gif')
               . ", '')"
               . ", ('', 'channelimg', 'title', "
               . $sitename
               . ", '')"
               . ", ('', 'channelimg', 'link', "
               . $url
               . ", '')"
               . ';';
    }
}
