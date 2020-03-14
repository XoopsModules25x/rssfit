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
 * @param $a
 * @param $b
 * @return int
 */
function sortTimestamp($a, $b)
{
    if ($a['timestamp'] == $b['timestamp']) {
        return 0;
    }

    return ($a['timestamp'] > $b['timestamp']) ? -1 : 1;
}

/**
 * @param $spec
 * @param $feedHandler
 * @return string
 */
function genSpecMoreInfo($spec, $feedHandler)
{
    return rssfGenAnchor($feedHandler->specUrl($spec), _AM_EDIT_CHANNEL_QMARK, 'spec', _AM_EDIT_CHANNEL_MOREINFO);
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
function rssfGenAnchor($url = '', $text = '', $target = '', $title = '', $class = '', $id = '')
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
