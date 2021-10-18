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

use XoopsModules\Rssfit\Constants;

/**
 * Class Utility
 */
class Utility extends Common\SysUtility
{
    //--------------- Custom module methods -----------------------------

    public static function sortTimestamp(array $a, array $b): int
    {
        if ($a['timestamp'] == $b['timestamp']) {
            return 0;
        }

        return ($a['timestamp'] > $b['timestamp']) ? -1 : 1;
    }

    public static function genSpecMoreInfo(string $spec, FeedHandler $feedHandler): string
    {
        return static::rssfGenAnchor($feedHandler->specUrl($spec), \_AM_RSSFIT_EDIT_CHANNEL_QMARK, 'spec', \_AM_RSSFIT_EDIT_CHANNEL_MOREINFO);
    }

    /**
     * @param null|string $url
     */
    public static function rssfGenAnchor(string $url = null, string $text = '', string $target = '', string $title = '', string $class = '', string $id = ''): string
    {
        if (null !== $url) {
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
    public static function getRssFeedAsHtml(string $feed_url, int $maxItemCount = 10, bool $show_date = true, bool $show_description = true, int $max_words = 0, int $cache_timeout = 7200, string $cache_prefix = XOOPS_VAR_PATH . '/caches/xoops_cache/rss2html-'): string
    {
        $result = '';
        // get feeds and parse items
        $rss        = new \DOMDocument();
        $cacheFile = $cache_prefix . \md5($feed_url);
        // load from file or load content
        if ($cache_timeout > 0
            && \is_file($cacheFile)
            && (\filemtime($cacheFile) + $cache_timeout > \time())) {
            $rss->load($cacheFile);
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
                $rss->save($cacheFile);
            }
        }
        $feed = [];
        foreach ($rss->getElementsByTagName('item') as $node) {
            if (null !== $node) {
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
        }
        // real good count
        if ($maxItemCount > \count($feed)) {
            $maxItemCount = \count($feed);
        }
        $result .= '<ul class="feed-lists">';
        for ($x = 0; $x < $maxItemCount; $x++) {
            $title  = \str_replace(' & ', ' &amp; ', $feed[$x]['title']);
            $link   = $feed[$x]['link'];
            $result .= '<li class="feed-item">';
            $result .= '<div class="feed-title"><strong><a href="' . $link . '" title="' . $title . '">' . $title . '</a></strong></div>';
            if ($show_date) {
                $date   = \date('l F d, Y', (int)\strtotime($feed[$x]['date']));
                $result .= '<small class="feed-date"><em>Posted on ' . $date . '</em></small>';
            }
            if ($show_description) {
                $description = $feed[$x]['desc'];
                $content     = $feed[$x]['content'];
                // find the img
                $hasImage = \preg_match('/<img.+src=[\'"](?P<src>.+?)[\'"].*>/i', $content, $image);
                // no html tags
                $description = \strip_tags((string)\preg_replace('/(<(script|style)\b[^>]*>).*?(<\/\2>)/s', '$1$3', $description), '');
                // whether cut by number of words
                if ($max_words > 0) {
                    $arr = \explode(' ', $description);
                    if ($max_words < \count($arr)) {
                        $description = '';
                        $wordsCount       = 0;
                        foreach ($arr as $w) {
                            $description .= $w . ' ';
                            ++$wordsCount;
                            if ($wordsCount == $max_words) {
                                break;
                            }
                        }
                        $description .= ' ...';
                    }
                }
                // add img if it exists
                if (1 == $hasImage) {
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

    public static function outputRssFeed(string $feed_url, int $maxItemCount = 10, bool $show_date = true, bool $show_description = true, int $max_words = 0): void
    {
        echo self::getRssFeedAsHtml($feed_url, $maxItemCount, $show_date, $show_description, $max_words);
    }
}
