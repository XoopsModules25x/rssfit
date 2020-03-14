<?php

namespace XoopsModules\Rssfit;

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

//require_once  dirname(__DIR__) . '/include/common.php';

/**
 * Class Utility
 */
class Utility extends Common\SysUtility
{
    //--------------- Custom module methods -----------------------------

    public static function rssfitAdminHeader()
    {
        global $xoopsModule, $xoopsConfig;
        $langf = RSSFIT_ROOT_PATH . 'language/' . $xoopsConfig['language'] . '/modinfo.php';
        if (file_exists($langf)) {
            include $langf;
        } else {
            include RSSFIT_ROOT_PATH . 'language/english/modinfo.php';
        }
        require __DIR__ . '/menu.php';
        for ($i = 0, $iMax = count($adminmenu); $i < $iMax; $i++) {
            $links[$i] = [0 => RSSFIT_URL . $adminmenu[$i]['link'], 1 => $adminmenu[$i]['title']];
        }
        $links[] = [0 => XOOPS_URL . '/modules/system/admin.php?fct=preferences&op=showmod&mod=' . $xoopsModule->getVar('mid'), 1 => _PREFERENCES];
        $admin_links = '<table class="outer" width="100%" cellspacing="1"><tr>';
        for ($i = 0, $iMax = count($links); $i < $iMax; $i++) {
            $admin_links .= '<td class="even" style="width: 16%; text-align: center;"><a href="' . $links[$i][0] . '" accesskey="' . ($i + 1) . '">' . $links[$i][1] . '</a></td>';
        }
        $admin_links .= "</tr></table><br clear=all>\n";
        xoops_cp_header();
        echo $admin_links;
    }

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
     * @param $spec
     * @param $feedHandler
     * @return string
     */
    public static function genSpecMoreInfo($spec, $feedHandler)
    {
        return static::rssfGenAnchor($feedHandler->specUrl($spec), _AM_RSSFIT_EDIT_CHANNEL_QMARK, 'spec', _AM_RSSFIT_EDIT_CHANNEL_MOREINFO);
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
}
