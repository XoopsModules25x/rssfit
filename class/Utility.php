<?php

declare(strict_types=1);

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
 * @author       ZySpec <zyspec@yahoo.com>
 * @author       Mamba <mambax7@gmail.com>
 * @since        File available since version 1.54
 */

//require_once  \dirname(__DIR__) . '/include/common.php';

use XoopsModules\Rssfit;
use XoopsModules\Rssfit\Constants;

/**
 * Class Utility
 */
class Utility extends Common\SysUtility
{
    //--------------- Custom module methods -----------------------------

    /**
     * @param $a
     * @param $b
     * @return int
     */
    public static function sortTimestamp($a, $b): int
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
    public static function genSpecMoreInfo($spec, $feedHandler): string
    {
        return static::rssfGenAnchor($feedHandler->specUrl($spec), \_AM_RSSFIT_EDIT_CHANNEL_QMARK, 'spec', \_AM_RSSFIT_EDIT_CHANNEL_MOREINFO);
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
    public static function rssfGenAnchor($url = '', $text = '', $target = '', $title = '', $class = '', $id = ''): string
    {
        if (!empty($url)) {
            $ret = '<a href="' . $url . '"';
            $ret .= !empty($target) ? ' target="' . $target . '"' : '';
            $ret .= !empty($class) ? ' class="' . $class . '"' : '';
            $ret .= !empty($id) ? ' id="' . $id . '"' : '';
            $ret .= !empty($title) ? ' title="' . $title . '"' : '';
            $ret .= '>' . $text . '</a>';

            return $ret;
        }

        return $text;
    }

    // Check http://www.systutorials.com/136102/a-php-function-for-fetching-rss-feed-and-outputing-feed-items-as-html/ for description

    // RSS to HTML
    /*
        $tiem_cnt: max number of feed items to be displayed
        $max_words: max number of words (not real words, HTML words)
        if <= 0: no limitation, if > 0 display at most $max_words words
     */
    public static function get_rss_feed_as_html($feed_url, $max_item_cnt = 10, $show_date = true, $show_description = true, $max_words = 0, $cache_timeout = 7200, $cache_prefix = XOOPS_VAR_PATH . '/caches/xoops_cache/rss2html-'): string
    {
        $result = '';
        // get feeds and parse items
        $rss        = new \DOMDocument();
        $cache_file = $cache_prefix . \md5($feed_url);
        // load from file or load content
        if ($cache_timeout > 0
            && \is_file($cache_file)
            && (\filemtime($cache_file) + $cache_timeout > \time())) {
            $rss->load($cache_file);
        } else {
            $rss->load($feed_url);
            /*
            // if load() doesn't work, you might try this
            $ch = curl_init($feed_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $content = curl_exec($ch);
            curl_close($ch);
            $rss->loadXML($content);
            */
            if ($cache_timeout > 0) {
                $rss->save($cache_file);
            }
        }
        $feed = [];
        foreach ($rss->getElementsByTagName('item') as $node) {
            $item    = [
                'title'   => $node->getElementsByTagName('title')->item(0)->nodeValue,
                'desc'    => $node->getElementsByTagName('description')->item(0)->nodeValue,
                'content' => $node->getElementsByTagName('description')->item(0)->nodeValue,
                'link'    => $node->getElementsByTagName('link')->item(0)->nodeValue,
                'date'    => $node->getElementsByTagName('pubDate')->item(0)->nodeValue,
            ];
            $content = $node->getElementsByTagName('encoded'); // <content:encoded>
            if ($content->length > 0) {
                $item['content'] = $content->item(0)->nodeValue;
            }
            $feed[] = $item;
        }
        // real good count
        if ($max_item_cnt > \count($feed)) {
            $max_item_cnt = \count($feed);
        }
        $result .= '<ul class="feed-lists">';
        for ($x = 0; $x < $max_item_cnt; $x++) {
            $title  = \str_replace(' & ', ' &amp; ', $feed[$x]['title']);
            $link   = $feed[$x]['link'];
            $result .= '<li class="feed-item">';
            $result .= '<div class="feed-title"><strong><a href="' . $link . '" title="' . $title . '">' . $title . '</a></strong></div>';
            if ($show_date) {
                $date   = \date('l F d, Y', \strtotime($feed[$x]['date']));
                $result .= '<small class="feed-date"><em>Posted on ' . $date . '</em></small>';
            }
            if ($show_description) {
                $description = $feed[$x]['desc'];
                $content     = $feed[$x]['content'];
                // find the img
                $has_image = \preg_match('/<img.+src=[\'"](?P<src>.+?)[\'"].*>/i', $content, $image);
                // no html tags
                $description = \strip_tags(\preg_replace('/(<(script|style)\b[^>]*>).*?(<\/\2>)/s', '$1$3', $description), '');
                // whether cut by number of words
                if ($max_words > 0) {
                    $arr = \explode(' ', $description);
                    if ($max_words < \count($arr)) {
                        $description = '';
                        $w_cnt       = 0;
                        foreach ($arr as $w) {
                            $description .= $w . ' ';
                            ++$w_cnt;
                            if ($w_cnt == $max_words) {
                                break;
                            }
                        }
                        $description .= ' ...';
                    }
                }
                // add img if it exists
                if (1 == $has_image) {
                    $description = '<img class="feed-item-image" src="' . $image['src'] . '" />' . $description;
                }
                $result .= '<div class="feed-description">' . $description;
                $result .= ' <a href="' . $link . '" title="' . $title . '">Continue Reading &raquo;</a>' . '</div>';
            }
            $result .= '</li>';
        }
        $result .= '</ul>';
        return $result;
    }

    public static function output_rss_feed($feed_url, $max_item_cnt = 10, $show_date = true, $show_description = true, $max_words = 0): void
    {
        echo self::get_rss_feed_as_html($feed_url, $max_item_cnt, $show_date, $show_description, $max_words);
    }
}
