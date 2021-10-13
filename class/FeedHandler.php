<?php

namespace XoopsModules\Rssfit;

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

use  XoopsModules\Rssfit;

if (!\defined('RSSFIT_ROOT_PATH')) {
    exit();
}

/**
 * Class FeedHandler
 * @package XoopsModules\Rssfit
 */
class FeedHandler
{
    public $rssmod;
    public $pluginHandler;
    public $miscHandler;
    public $channelreq;
    public $subHandler;
    public $plugin_obj;
    public $myts;
    public $modConfig;
    public $xoopsConfig;
    public $cached         = '';
    public $charset        = _CHARSET;
    public $feedkey        = 'feed';
    public $plugin_file    = 'rssfit.%s.php';
    public $substr_remove  = [',', '/', ';', ':', '(', '{', '[', ' '];
    public $substr_add     = ['.', '!', '?', '}', ']', ')', '%'];
    public $substr_endwith = '...';
    public $spec_url       = 'http://blogs.law.harvard.edu/tech/rss';
    public $specs          = [
        'req'   => 'requiredChannelElements',
        'opt'   => 'optionalChannelElements',
        'cloud' => 'ltcloudgtSubelementOfLtchannelgt',
        'img'   => 'ltimagegtSubelementOfLtchannelgt',
    ];
    public $escaped        = [
        128 => '&#8364;',
        130 => '&#8218;',
        131 => '&#402;',
        132 => '&#8222;',
        133 => '&#8230;',
        134 => '&#8224;',
        135 => '&#8225;',
        136 => '&#710;',
        137 => '&#8240;',
        138 => '&#352;',
        139 => '&#8249;',
        140 => '&#338;',
        142 => '&#381;',
        145 => '&#8216;',
        146 => '&#8217;',
        147 => '&#8220;',
        148 => '&#8221;',
        149 => '&#8226;',
        150 => '&#8211;',
        151 => '&#8212;',
        152 => '&#732;',
        153 => '&#8482;',
        154 => '&#353;',
        155 => '&#8250;',
        156 => '&#339;',
        158 => '&#382;',
        159 => '&#376;',
    ];

    /**
     * FeedHandler constructor.
     * @param $modConfig
     * @param $xoopsConfig
     * @param $xoopsModule
     */
    public function __construct($modConfig, $xoopsConfig, $xoopsModule)
    {
        $this->myts          = \MyTextSanitizer::getInstance();
        $this->rssmod        = $xoopsModule;
        $this->pluginHandler = Helper::getInstance()->getHandler('Plugin');
        $this->miscHandler   = Helper::getInstance()->getHandler('Misc');
        $this->modConfig     = $modConfig;
        $this->xoopsConfig   = $xoopsConfig;
        $this->channelreq    = [
            'title'       => $this->xoopsConfig['sitename'],
            'link'        => XOOPS_URL,
            'description' => $this->xoopsConfig['slogan'],
        ];
    }

    /**
     * @param $feed
     */
    public function getChannel(&$feed)
    {
        $channel  = [];
        $elements = $this->miscHandler->getObjects2(new \Criteria('misc_category', 'channel'));
        if ($elements) {
            foreach ($elements as $e) {
                if ('' != $e->getVar('misc_content')) {
                    $channel[$e->getVar('misc_title')] = $e->getVar('misc_content', 'n');
                }
            }
            $channel['language']      = _LANGCODE;
            $channel['lastBuildDate'] = $this->rssTimeStamp(\time());
            if ($this->modConfig['cache']) {
                $channel['ttl'] = $this->modConfig['cache'];
            }
        }
        if (!empty($feed['plugin'])) {
            if (\is_object($this->plugin_obj) && \is_object($this->subHandler)) {
                $channel['title']       = $this->plugin_obj->getVar('sub_title', 'n');
                $channel['link']        = $this->plugin_obj->getVar('sub_link', 'n');
                $channel['description'] = $this->plugin_obj->getVar('sub_desc', 'n');
                $image                  = [
                    'url'   => $this->plugin_obj->getVar('img_url', 'n'),
                    'title' => $this->plugin_obj->getVar('img_title', 'n'),
                    'link'  => $this->plugin_obj->getVar('img_link', 'n'),
                ];
            }
        } else {
            $img = $this->miscHandler->getObjects2(new \Criteria('misc_category', 'channelimg'), '*', 'title');
            if ($img) {
                $image = [
                    'url'   => $img['url']->getVar('misc_content', 'n'),
                    'title' => $img['title']->getVar('misc_content', 'n'),
                    'link'  => $img['link']->getVar('misc_content', 'n'),
                ];
            }
        }
        if (empty($channel['title']) || empty($channel['link']) || empty($channel['description'])) {
            $channel = \array_merge($channel, $this->channelreq);
        }
        foreach ($channel as $k => $v) {
            $this->cleanupChars($channel[$k]);
        }
        if (!empty($image)) {
            foreach ($image as $k => $v) {
                $this->cleanupChars($image[$k]);
            }
            $feed['image'] = &$image;
        }
        $feed['channel'] = &$channel;
    }

    /**
     * @param $feed
     * @return bool
     */
    public function getSticky(&$feed)
    {
        if (!$intr = $this->miscHandler->getObjects2(new \Criteria('misc_category', 'sticky'))) {
            return false;
        }
        $sticky = &$intr[0];
        unset($intr);
        $setting = $sticky->getVar('misc_setting');
        if (\in_array(0, $setting['feeds']) || '' == $sticky->getVar('misc_title') || '' == $sticky->getVar('misc_content')) {
            return false;
        }
        if ((\in_array(-1, $setting['feeds']) && empty($feed['plugin']))
            || (!empty($feed['plugin']) && \in_array($this->plugin_obj->getVar('rssf_conf_id'), $setting['feeds']))) {
            $feed['sticky']['title'] = $sticky->getVar('misc_title', 'n');
            $feed['sticky']['link']  = $setting['link'];
            $sticky->setDoHtml($setting['dohtml']);
            $sticky->setDoBr($setting['dobr']);
            $feed['sticky']['description'] = $sticky->getVar('misc_content');
            $this->cleanupChars($feed['sticky']['title']);
            $this->cleanupChars($feed['sticky']['link']);
            $this->cleanupChars($feed['sticky']['description'], $setting['dohtml'] ? 0 : 1, false);
            $this->wrapCdata($feed['sticky']['description']);
            $feed['sticky']['pubdate'] = $this->rssTimeStamp(\time());
        }

        return true;
    }

    /**
     * @param $feed
     */
    public function getItems(&$feed)
    {
        $entries = [];
        if (!empty($feed['plugin'])) {
            $this->plugin_obj->setVar('rssf_grab', $this->plugin_obj->getVar('sub_entries'));
            $this->subHandler->grab = $this->plugin_obj->getVar('sub_entries');
            $grab                   = $this->subHandler->grabEntries($this->plugin_obj);
            if (false !== $grab && \count($grab) > 0) {
                foreach ($grab as $g) {
                    $entries[] = $g;
                }
            }
        } elseif ($plugins = $this->pluginHandler->getObjects2(new \Criteria('rssf_activated', 1))) {
            foreach ($plugins as $p) {
                $handler = $this->pluginHandler->checkPlugin($p);
                if ($handler) {
                    $handler->grab = $p->getVar('rssf_grab');
                    $grab          = $handler->grabEntries($p);
                    if (false !== $grab && \count($grab) > 0) {
                        foreach ($grab as $g) {
                            $entries[] = $g;
                        }
                    }
                }
            }
        }
        if (count($entries) > 0) {
            for ($i = 0, $iMax = count($entries); $i < $iMax; $i++) {
                $this->cleanupChars($entries[$i]['title']);
                $strip = $this->modConfig['strip_html'] ? 1 : 0;
                $this->cleanupChars($entries[$i]['description'], $strip, 0, 1);
                $this->wrapCdata($entries[$i]['description']);
                $entries[$i]['category'] = $this->myts->undoHtmlSpecialChars($entries[$i]['category']);
                $this->cleanupChars($entries[$i]['category']);
                if (!isset($entries[$i]['timestamp'])) {
                    $entries[$i]['timestamp'] = $this->rssmod->getVar('last_update');
                }
                $entries[$i]['pubdate'] = $this->rssTimeStamp($entries[$i]['timestamp']);
            }
            if (empty($feed['plugin']) && 'd' === $this->modConfig['sort']) {
                uasort($entries, 'sortTimestamp');
            }
            if (count($entries) > $this->modConfig['overall_entries'] && empty($feed['plugin'])) {
                $entries = array_slice($entries, 0, $this->modConfig['overall_entries']);
            }
        }

        $feed['items'] = &$entries;
    }

    /**
     * @param $text
     * @return string
     */
    public function doSubstr($text)
    {
        $ret = $text;
        $len = \function_exists('mb_strlen') ? mb_strlen($ret, $this->charset) : mb_strlen($ret);
        if ($len > $this->modConfig['max_char'] && $this->modConfig['max_char'] > 0) {
            $ret = $this->substrDetect($ret, 0, $this->modConfig['max_char'] - 1);
            if (false === $this->strrposDetect($ret, ' ')) {
                if (false !== $this->strrposDetect($text, ' ')) {
                    $ret = $this->substrDetect($text, 0, mb_strpos($text, ' '));
                }
            }
            if (\in_array($this->substrDetect($text, $this->modConfig['max_char'] - 1, 1), $this->substr_add)) {
                $ret .= $this->substrDetect($text, $this->modConfig['max_char'] - 1, 1);
            } else {
                if (false !== $this->strrposDetect($ret, ' ')) {
                    $ret = $this->substrDetect($ret, 0, $this->strrposDetect($ret, ' '));
                }
                if (\in_array($this->substrDetect($ret, -1, 1), $this->substr_remove)) {
                    $ret = $this->substrDetect($ret, 0, -1);
                }
            }
            $ret .= $this->substr_endwith;
        }

        return $ret;
    }

    /**
     * @param $text
     * @param $start
     * @param $len
     * @return false|mixed|string
     */
    public function substrDetect($text, $start, $len)
    {
        if (\function_exists('mb_strcut')) {
            return mb_strcut($text, $start, $len, _CHARSET);
        }

        return mb_substr($text, $start, $len);
    }

    /**
     * @param $text
     * @param $find
     * @return false|int
     */
    public function strrposDetect($text, $find)
    {
        if (\function_exists('mb_strrpos')) {
            return mb_strrpos($text, $find, 0, _CHARSET);
        }

        return mb_strrpos($text, $find);
    }

    /**
     * @param $time
     * @return false|string
     */
    public function rssTimeStamp($time)
    {
        return \date('D, j M Y H:i:s O', $time);
    }

    /**
     * @param $a
     * @param $b
     * @return int
     */
    public function sortTimestamp($a, $b)
    {
        if ($a['timestamp'] == $b['timestamp']) {
            return 0;
        }

        return ($a['timestamp'] > $b['timestamp']) ? -1 : 1;
    }

    /**
     * @param       $text
     * @param bool  $strip
     * @param bool  $dospec
     * @param false $dosub
     */
    public function cleanupChars(&$text, $strip = true, $dospec = true, $dosub = false)
    {
        if ($strip) {
            $text = \strip_tags($text);
        }
        if ($dosub) {
            $text = $this->doSubstr($text);
        }
        if ($dospec) {
            $text = \htmlspecialchars($text, \ENT_QUOTES, $this->charset);
            $text = \preg_replace('/&amp;(#\d+);/i', '&$1;', $text);
        }
        if (!\preg_match('/utf-8/i', $this->charset) || XOOPS_USE_MULTIBYTES != 1) {
            $text = \str_replace(\array_map('\chr', \array_keys($this->escaped)), $this->escaped, $text);
        }
    }

    /**
     * @param $text
     */
    public function wrapCdata(&$text)
    {
        $text = '<![CDATA[' . \str_replace(['<![CDATA[', ']]>'], ['&lt;![CDATA[', ']]&gt;'], $text) . ']]>';
    }

    /**
     * @param string $fields
     * @param string $type
     * @return bool|array
     */
    public function &getActivatedSubfeeds($fields = '', $type = '')
    {
        $ret  = false;
        $subs = $this->pluginHandler->getObjects2(new \Criteria('subfeed', 1), $fields);
        if ($subs) {
            $ret  = [];
            switch ($type) {
                default:
                    $ret = &$subs;
                    break;
                case 'list':
                    foreach ($subs as $s) {
                        $ret[$s->getVar('rssf_conf_id')] = $s->getVar('sub_title');
                    }
                    break;
            }
        }

        return $ret;
    }

    /**
     * @param string $caption
     * @param string $selected
     * @param int    $size
     * @param bool   $multi
     * @param bool   $none
     * @param bool   $main
     * @param string $name
     * @param string $type
     * @return \XoopsFormSelect
     */
    public function feedSelectBox($caption = '', $selected = '', $size = 1, $multi = true, $none = true, $main = true, $name = 'feeds', $type = 'id')
    {
        $select = new \XoopsFormSelect($caption, $name, $selected, $size, $multi);
        if ($none) {
            $select->addOption(0, '-------');
        }
        if ($main) {
            $select->addOption('-1', \_AM_RSSFIT_MAINFEED);
        }
        $subs = $this->getActivatedSubfeeds('sublist', 'list');
        if ($subs) {
            foreach ($subs as $k => $v) {
                $select->addOption($k, $v);
            }
        }

        return $select;
    }

    /**
     * @param int $key
     * @return false|string
     */
    public function specUrl($key = 0)
    {
        if (isset($this->specs[$key])) {
            return $this->spec_url . '#' . $this->specs[$key];
        }

        return false;
    }

    /**
     * @param string $filename
     * @return false|string
     */
    public function subFeedUrl($filename = '')
    {
        if (!empty($filename)) {
            $filename = \str_replace(['rssfit.', '.php'], '', $filename);

            return \RSSFIT_URL_FEED . '?' . $this->feedkey . '=' . $filename;
        }

        return false;
    }

    /**
     * @param $feed
     */
    public function checkSubFeed(&$feed)
    {
        if (!empty($feed['plugin'])) {
            $criteria = new \CriteriaCompo();
            $criteria->add(new \Criteria('rssf_filename', \sprintf($this->plugin_file, $feed['plugin'])));
            $criteria->add(new \Criteria('subfeed', 1));
            $sub     = $this->pluginHandler->getObjects2($criteria);
            $handler = false;
            if (isset($sub[0])) {
                $handler = $this->pluginHandler->checkPlugin($sub[0]);
            }
            if ($handler) {
                $this->plugin_obj = $sub[0];
                $this->subHandler = $handler;
                $this->cached     = 'mod_' . $this->rssmod->getVar('dirname') . '|' . \md5(\str_replace(XOOPS_URL, '', $GLOBALS['xoopsRequestUri']));
            } else {
                $feed['plugin'] = '';
            }
        }
    }

    /**
     * @param $feed
     */
    public function buildFeed(&$feed)
    {
        $this->getChannel($feed);
        $this->getItems($feed);
        $this->getSticky($feed);
    }
}
