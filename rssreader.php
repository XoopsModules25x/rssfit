<?php

declare(strict_types=1);

use XoopsModules\Rssfit\{
    Helper,
    Utility
};

$GLOBALS['xoopsOption']['template_main'] = 'rssfit_reader.tpl';
require_once __DIR__ . '/header.php';
require_once XOOPS_ROOT_PATH . '/header.php';

$helper    = Helper::getInstance();
$maxChar   = $helper->getConfig('max_char');
$max_words = 200;

// output RSS feed to HTML
Utility::outputRssFeed($helper->url('rss.php'), 20, true, true, $max_words);

require_once XOOPS_ROOT_PATH . '/footer.php';
