<?php

declare(strict_types=1);

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
    public $helper;
    public $channelreq;
    public $subHandler;
    public $pluginObject;
    public $myts;
    public $modConfig;
    public $xoopsConfig;
    public $cached        = '';
    public $charset       = _CHARSET;
    public $feedkey       = 'feed';
    public $pluginFile    = '%s.php';
    public $substrRemove  = [',', '/', ';', ':', '(', '{', '[', ' '];
    public $substrAdd     = ['.', '!', '?', '}', ']', ')', '%'];
    public $substrEndwith = '...';
    public $specUrl       = 'http://blogs.law.harvard.edu/tech/rss';
    public $specs         = [
        'req'   => 'requiredChannelElements',
        'opt'   => 'optionalChannelElements',
        'cloud' => 'ltcloudgtSubelementOfLtchannelgt',
        'img'   => 'ltimagegtSubelementOfLtchannelgt',
    ];
    public $escaped       = [
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
     */
    public function __construct(array $modConfig, array $xoopsConfig, \XoopsModule $xoopsModule)
    {
        $this->myts          = \MyTextSanitizer::getInstance();
        $this->rssmod        = $xoopsModule;
        $this->helper        = Helper::getInstance();
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

    public function getChannel(array &$feed): void
    {
        $channel  = [];
        $elements = $this->miscHandler->getObjects2(new \Criteria('misc_category', 'channel'));
        if (\is_array($elements) && !empty($elements)) {
            foreach ($elements as $e) {
                if ('' !== $e->getVar('misc_content')) {
                    $channel[$e->getVar('misc_title')] = $e->getVar('misc_content', 'n');
                }
            }
            $channel['language']      = _LANGCODE;
            $channel['lastBuildDate'] = $this->rssTimeStamp(\time());
            if ($this->modConfig['cache']) {
                $channel['ttl'] = (string)$this->modConfig['cache'];
            }
        }
        if (!empty($feed['plugin'])) {
            if (\is_object($this->pluginObject) && \is_object($this->subHandler)) {
                $channel['title']       = $this->pluginObject->getVar('sub_title', 'n');
                $channel['link']        = $this->pluginObject->getVar('sub_link', 'n');
                $channel['description'] = $this->pluginObject->getVar('sub_desc', 'n');
                $image                  = [
                    'url'   => $this->pluginObject->getVar('img_url', 'n'),
                    'title' => $this->pluginObject->getVar('img_title', 'n'),
                    'link'  => $this->pluginObject->getVar('img_link', 'n'),
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

    public function getSticky(array &$feed): bool
    {
        if (!$intr = $this->miscHandler->getObjects2(new \Criteria('misc_category', 'sticky'))) {
            return false;
        }
        $sticky = &$intr[0];
        unset($intr);
        $setting = $sticky->getVar('misc_setting');
        if (\in_array(0, $setting['feeds']) || '' === $sticky->getVar('misc_title') || '' === $sticky->getVar('misc_content')) {
            return false;
        }
        if ((\in_array(-1, $setting['feeds']) && empty($feed['plugin']))
            || (!empty($feed['plugin']) && \in_array($this->pluginObject->getVar('rssf_conf_id'), $setting['feeds']))) {
            $feed['sticky']['title'] = $sticky->getVar('misc_title', 'n');
            $feed['sticky']['link']  = $setting['link'];
            $sticky->setDoHtml((bool)$setting['dohtml']);
            $sticky->setDoBr((bool)$setting['dobr']);
            $feed['sticky']['description'] = $sticky->getVar('misc_content');
            $this->cleanupChars($feed['sticky']['title']);
            $this->cleanupChars($feed['sticky']['link']);
            $this->cleanupChars($feed['sticky']['description'], !$setting['dohtml'], false);
            $this->wrapCdata($feed['sticky']['description']);
            $feed['sticky']['pubdate'] = $this->rssTimeStamp(\time());
        }

        return true;
    }

    public function getItems(array &$feed): void
    {
        $entries = [];
        $db      = \XoopsDatabaseFactory::getDatabaseConnection();
        if (!empty($feed['plugin'])) {
            $this->pluginObject->setVar('rssf_grab', $this->pluginObject->getVar('sub_entries'));
            $this->subHandler->grab = $this->pluginObject->getVar('sub_entries');
            $grab                   = $this->subHandler->grabEntries($db);
            if (null !== $grab && \count($grab) > 0) {
                foreach ($grab as $g) {
                    $entries[] = $g;
                }
            }
        } elseif ($plugins = $this->pluginHandler->getObjects2(new \Criteria('rssf_activated', '1'))) {
            foreach ($plugins as $p) {
                $handler = $this->pluginHandler->checkPlugin($p);
                if ($handler) {
                    $handler->grab = $p->getVar('rssf_grab');
                    $grab          = $handler->grabEntries($db);
                    if (null !== $grab && \count($grab) > 0) {
                        foreach ($grab as $g) {
                            $entries[] = $g;
                        }
                    }
                }
            }
        }
        if (\count($entries) > 0) {
            foreach ($entries as $i => &$iValue) {
                $this->cleanupChars($iValue['title']);
                $strip = (bool)$this->modConfig['strip_html'];
                $this->cleanupChars($iValue['description'], $strip, false, true);
                $this->wrapCdata($iValue['description']);
                $entries[$i]['category'] = $this->myts->undoHtmlSpecialChars($iValue['category']);
                $this->cleanupChars($iValue['category']);
                if (!isset($iValue['timestamp'])) {
                    $entries[$i]['timestamp'] = $this->rssmod->getVar('last_update');
                }
                $entries[$i]['pubdate'] = $this->rssTimeStamp((int)$iValue['timestamp']);
            }
            unset($iValue);

            if (empty($feed['plugin']) && 'd' === $this->modConfig['sort']) {
                \uasort($entries, [$this, 'sortTimestamp']);
            }
            if (empty($feed['plugin']) && \count($entries) > $this->modConfig['overall_entries']) {
                $entries = \array_slice($entries, 0, $this->modConfig['overall_entries']);
            }
        }

        $feed['items'] = &$entries;
    }

    public function doSubstr(string $text): string
    {
        $ret      = $text;
        $len      = \function_exists('mb_strlen') ? mb_strlen($ret, $this->charset) : mb_strlen($ret);
        $maxChars = $this->helper->getConfig('max_char');
        if ($len > $maxChars && $maxChars > 0) {
            $ret = $this->substrDetect($ret, 0, $maxChars - 1);
            if (false === $this->strrposDetect($ret, ' ')) {
                if (false !== $this->strrposDetect($text, ' ')) {
                    $ret = $this->substrDetect($text, 0, (int)mb_strpos($text, ' '));
                }
            }
            if (\in_array($this->substrDetect($text, $maxChars - 1, 1), $this->substrAdd)) {
                $ret .= $this->substrDetect($text, $maxChars - 1, 1);
            } else {
                if (false !== $this->strrposDetect($ret, ' ')) {
                    $ret = $this->substrDetect($ret, 0, $this->strrposDetect($ret, ' '));
                }
                if (\in_array($this->substrDetect($ret, -1, 1), $this->substrRemove)) {
                    $ret = $this->substrDetect($ret, 0, -1);
                }
            }
            $ret .= $this->substrEndwith;
        }

        return $ret;
    }

    public function substrDetect(string $text, int $start, int $len): string
    {
        if (\function_exists('mb_strcut')) {
            return mb_strcut($text, $start, $len, _CHARSET);
        }

        return mb_substr($text, $start, $len);
    }

    /**
     * @return false|int
     */
    public function strrposDetect(string $text, string $find)
    {
        if (\function_exists('mb_strrpos')) {
            return mb_strrpos($text, $find, 0, _CHARSET);
        }

        return mb_strrpos($text, $find);
    }

    public function rssTimeStamp(int $time): ?string
    {
        return \date('D, j M Y H:i:s O', $time);
    }

    public function sortTimestamp(array $a, array $b): int
    {
        if ($a['timestamp'] === $b['timestamp']) {
            return 0;
        }

        return ($a['timestamp'] > $b['timestamp']) ? -1 : 1;
    }

    public function cleanupChars(string &$text, bool $strip = true, bool $dospec = true, bool $dosub = false): void
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
        if (XOOPS_USE_MULTIBYTES !== 1 || false === stripos($this->charset, "utf-8")) {
            $text = \str_replace(\array_map('\chr', \array_keys($this->escaped)), $this->escaped, $text);
        }
    }

    public function wrapCdata(string &$text): void
    {
        $text = '<![CDATA[' . \str_replace(['<![CDATA[', ']]>'], ['&lt;![CDATA[', ']]&gt;'], $text) . ']]>';
    }

    public function &getActivatedSubfeeds(string $fields = '', string $type = ''): ?array
    {
        $ret  = null;
        $subs = $this->pluginHandler->getObjects2(new \Criteria('subfeed', '1'), $fields);
        if (\is_array($subs) && !empty($subs)) {
            $ret = [];
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
     * @param null|string|array $selected
     */
    public function feedSelectBox(string $caption = '', $selected = null, int $size = 1, bool $multi = true, bool $none = true, bool $main = true, string $name = 'feeds', string $type = 'id'): \XoopsFormSelect
    {
        $select = new \XoopsFormSelect($caption, $name, $selected, $size, $multi);
        if ($none) {
            $select->addOption('0', '-------');
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
     * @return string
     */
    public function specUrl(string $key = '0'): ?string
    {
        if (isset($this->specs[$key])) {
            return $this->specUrl . '#' . $this->specs[$key];
        }

        return null;
    }

    /**
     * @return string
     */
    public function subFeedUrl(string $filename = ''): ?string
    {
        if (!empty($filename)) {
            $filename = \str_replace(['rssfit.', '.php'], '', $filename);

            return \RSSFIT_URL_FEED . '?' . $this->feedkey . '=' . $filename;
        }

        return null;
    }

    public function checkSubFeed(array &$feed): void
    {
        if (!empty($feed['plugin'])) {
            $criteria = new \CriteriaCompo();
            $criteria->add(new \Criteria('rssf_filename', \sprintf($this->pluginFile, $feed['plugin'])));
            $criteria->add(new \Criteria('subfeed', '1'));
            $sub     = $this->pluginHandler->getObjects2($criteria);
            $handler = null;
            if (isset($sub[0])) {
                $handler = $this->pluginHandler->checkPlugin($sub[0]);
            }
            if ($handler) {
                $this->pluginObject = $sub[0];
                $this->subHandler   = $handler;
                $this->cached       = 'mod_' . $this->rssmod->getVar('dirname') . '|' . \md5(\str_replace(XOOPS_URL, '', $GLOBALS['xoopsRequestUri']));
            } else {
                $feed['plugin'] = '';
            }
        }
    }

    public function buildFeed(array &$feed): void
    {
        $this->getChannel($feed);
        $this->getItems($feed);
        $this->getSticky($feed);
    }
}
