<?php
// $Id$
// Module Info

// The name of this module
define("_MI_RSSFIT_NAME","XML (RSS feed)");
define("_MI_RSSFIT_DIRNAME", basename(dirname(dirname(dirname(__FILE__)))));


// A brief description of this module
define("_MI_RSSFIT_DESC","Extendable XML news feed generator");

// admin/menu.php
define("_MI_RSSFIT_ADMENU1","Edit intro");
define("_MI_RSSFIT_ADMENU2","Plug-ins");
define("_MI_RSSFIT_ADMENU3","Feed Info");
define("_MI_RSSFIT_ADMENU4","Sub-feeds");
define("_MI_RSSFIT_ADMENU5","Sticky text");

//	Module Configs
define("_MI_OVERALL_ENTRIES","Total entries to show");
define("_MI_OVERALL_ENTRIES_DESC","Number of entries to show in the RSS feed");
define("_MI_PLUGIN_ENTRIES","Default entry number for plug-ins");
define("_MI_PLUGIN_ENTRIES_DESC","Default number of entries to grab by each plug-in when installed");
define("_MI_ENTRIES_SORT","Entries sort by");
define("_MI_ENTRIES_SORT_DESC","Entries sort order for the RSS feed output");
define("_MI_ENTRIES_SORT_DATE","Date");
define("_MI_ENTRIES_SORT_CAT","Category");
define("_MI_CACHE","Cache lifetime (minutes)");
define("_MI_CACHE_DESC","This option will also be used for the Time-to-Live (TTL) channel element of the RSS output.");
define("_MI_MAXCHAR","Maximum characters of item descriptions");
define("_MI_MAXCHAR_DESC","0 = show entire content");
define("_MI_STRIPHTML","Strip html tags");
define("_MI_STRIPHTML_DESC","Remove html tags from item description elements, including Xoopscodes and Smileys");
define("_MI_ENCODE_UTF8","Encode contents to UTF-8");
define("_MI_ENCODE_UTF8_DESC","Note: probably not functional and recommended to turn off.");

// template explanations
define("_MI_TMPL_INTRO","Introduction in module home page");
define("_MI_TMPL_RSS","RSS feed data");

######### version 1.1 additions #########
define("_MI_OUTOUT_MIME","MIME type of RSS output");
define("_MI_OUTOUT_MIME_XML","XML (text/xml)");
define("_MI_OUTOUT_MIME_HTML","HTML (text/html)");
define("_MI_OUTOUT_MIME_PHP","HTML with php-debug");
