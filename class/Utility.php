<?php namespace XoopsModules\rss;

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
use XoopsModules\rss;
use XoopsModules\rss\Common;

//require_once __DIR__ . '/../include/common.php';

/**
 * Class Utility
 */
class Utility
{
    use Common\VersionChecks; //checkVerXoops, checkVerPhp Traits

    use Common\ServerStats; // getServerStats Trait

    use Common\FilesManagement; // Files Management Trait

    //--------------- Custom module methods -----------------------------

    public static function sortTimestamp($a, $b)
    {
        if ($a['timestamp'] == $b['timestamp']) {
            return 0;
        }
        return ($a['timestamp'] > $b['timestamp']) ? -1 : 1;
    }

    public static function genSpecMoreInfo($spec=0, &$rss)
    {
        return static::rssfGenAnchor($rss->specUrl($spec), _AM_EDIT_CHANNEL_QMARK, 'spec', _AM_EDIT_CHANNEL_MOREINFO);
    }

    public static function rssfGenAnchor($url='', $text='', $target= '', $title='', $class='', $id='')
    {
        if (!empty($url)) {
            $ret = '';
            $ret .= '<a href="'.$url.'"';
            $ret .= !empty($target) ? ' target="'.$target.'"' : '';
            $ret .= !empty($class) ? ' class="'.$class.'"' : '';
            $ret .= !empty($id) ? ' id="'.$id.'"' : '';
            $ret .= !empty($title) ? ' title="'.$title.'"' : '';
            $ret .= '>'.$text.'</a>';
            return $ret;
        }
        return $text;
    }
}
