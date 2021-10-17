<?php

declare(strict_types=1);
global $xoopsConfig;
define('_AM_RSSFIT_SAVE', 'Save');
define('_AM_RSSFIT_OK', 'Okay');
define('_AM_RSSFIT_DBUPDATED', 'Database Updated Successfully!');
define('_AM_RSSFIT_ACTION', 'Action');
define('_AM_RSSFIT_ID', 'ID');

define('_AM_RSSFIT_EDIT_INTRO', 'Edit introduction');
define('_AM_RSSFIT_EDIT_INTRO_TITLE', 'Introduction title');
define('_AM_RSSFIT_EDIT_INTRO_TITLE_DESC1', '{SITENAME} will print ');
define('_AM_RSSFIT_EDIT_INTRO_TITLE_DESC', _AM_RSSFIT_EDIT_INTRO_TITLE_DESC1 . $xoopsConfig['sitename']);
define('_AM_RSSFIT_EDIT_INTRO_TEXT', 'Introduction text');
define('_AM_RSSFIT_EDIT_INTRO_TEXT_DESC1', '<br><br>{SITEURL} will print ');
define('_AM_RSSFIT_EDIT_INTRO_TEXT_DESC', _AM_RSSFIT_EDIT_INTRO_TITLE_DESC . _AM_RSSFIT_EDIT_INTRO_TEXT_DESC1 . XOOPS_URL . '/');

define('_AM_RSSFIT_EDIT_PLUGIN', 'Manage Plug-ins');
define('_AM_RSSFIT_PLUGIN_ACTIVATED', 'Activated Plug-ins');
define('_AM_RSSFIT_PLUGIN_INACTIVE', 'Inactive Plug-ins');
define('_AM_RSSFIT_PLUGIN_NONINSTALLED', 'Non-installed Plug-ins');
define('_AM_RSSFIT_PLUGIN_MODNAME', 'Module');
define('_AM_RSSFIT_PLUGIN_FILENAME', 'File name');
define('_AM_RSSFIT_PLUGIN_SHOWXENTRIES', 'Entries to show');
define('_AM_RSSFIT_PLUGIN_ORDER', 'Display order');
define('_AM_RSSFIT_PLUGIN_DEACTIVATE', 'Deactivate');
define('_AM_RSSFIT_PLUGIN_ACTIVATE', 'Activated');
define('_AM_RSSFIT_PLUGIN_INSTALL', 'Install');
define('_AM_RSSFIT_PLUGIN_UNINSTALL', 'Uninstall');

//  errors
define('_AM_RSSFIT_PLUGIN_UNKNOWNERROR', 'Unknown error');
define('_AM_RSSFIT_PLUGIN_FILENOTFOUND', 'Plug-in file not found');
define('_AM_RSSFIT_PLUGIN_MODNOTFOUND', 'Module not found');
define('_AM_RSSFIT_PLUGIN_CLASSNOTFOUND', 'Plug-in not compatible (Class not exist)');
define('_AM_RSSFIT_PLUGIN_FUNCNOTFOUND', 'Plug-in not compatible (Function not exist)');

################### version 1.1 additions ###################
define('_AM_RSSFIT_XOOPS_VERSION_WRONG', 'Version of XOOPS does not meet the system requirement. RSSFit may not work properly.');
define('_AM_RSSFIT_DB_RECORD_MISSING', 'Could not found essential database records, please reinstall RSSFit');
define('_AM_RSSFIT_MAINFEED', 'Main feed');
define('_AM_RSSFIT_DO_HTML', 'Use HTML tags');
define('_AM_RSSFIT_DO_BR', 'Convert line breaks');
define('_AM_RSSFIT_EDIT_CHANNEL', 'Edit feed info');
define('_AM_RSSFIT_EDIT_CHANNEL_QMARK', ' [?]');
define('_AM_RSSFIT_EDIT_CHANNEL_MOREINFO', 'More Info');
define('_AM_RSSFIT_EDIT_CHANNEL_REQUIRED', 'Required feed information');
define('_AM_RSSFIT_EDIT_CHANNEL_OPTIONAL', 'Optional feed information');
define('_AM_RSSFIT_EDIT_CHANNEL_IMAGE', 'Feed image properties');
define('_AM_RSSFIT_PLUGIN_NONE', 'You have no plugin installed');
define('_AM_RSSFIT_SUB_LIST', 'Sub-feeds');
define('_AM_RSSFIT_SUB_FILENAME_URL', 'Plug-in file name / sub-feed URL');
define('_AM_RSSFIT_SUB_ACTIVATE', 'Activated');
define('_AM_RSSFIT_SUB_CONFIGURE', 'Configure');
define('_AM_RSSFIT_SUB_EDIT', 'Configure sub-feed: %s');
define('_AM_RSSFIT_SUB_PLUGIN_NONE', 'Plugin not installed');
define('_AM_RSSFIT_SUB_TITLE', 'Title');
define('_AM_RSSFIT_SUB_LINK', 'Link');
define('_AM_RSSFIT_SUB_DESC', 'Description');
define('_AM_RSSFIT_STICKY_EDIT', 'Edit sticky text');
define('_AM_RSSFIT_STICKY_TITLE', 'Sticky text title');
define('_AM_RSSFIT_STICKY_CONTENT', 'Sticky text content');
define('_AM_RSSFIT_STICKY_LINK', 'Sticky text link');
define('_AM_RSSFIT_STICKY_APPLYTO', 'Apply to feeds');
define('_AM_RSSFIT_EDIT_INTRO_TEXT_DESC_SUB', '<br><br>{SUB} will print a list of available sub-feeds');
define('_AM_RSSFIT_EDIT_INTRO_SUB', 'HTML tags for listing sub-feeds');
define('_AM_RSSFIT_EDIT_INTRO_SUB_DESC', "Extra tags:<br>{URL} (sub-feed's URL)<br>{TITLE} (sub-feed's title)<br>{DESC} (sub-feed's description)");
//1.30
define('_AM_RSSFIT_UPGRADEFAILED0', "Update failed - couldn't rename field '%s'");
define('_AM_RSSFIT_UPGRADEFAILED1', "Update failed - couldn't add new fields");
define('_AM_RSSFIT_UPGRADEFAILED2', "Update failed - couldn't rename table '%s'");
define('_AM_RSSFIT_ERROR_COLUMN', 'Could not create column in database : %s');
define('_AM_RSSFIT_ERROR_BAD_XOOPS', 'This module requires XOOPS %s+ (%s installed)');
define('_AM_RSSFIT_ERROR_BAD_PHP', 'This module requires PHP version %s+ (%s installed)');
define('_AM_RSSFIT_ERROR_TAG_REMOVAL', 'Could not remove tags from Tag Module');

define('_AM_RSSFIT_FOLDERS_DELETED_OK', 'Upload Folders have been deleted');

// Error Msgs
define('_AM_RSSFIT_ERROR_BAD_DEL_PATH', 'Could not delete %s directory');
define('_AM_RSSFIT_ERROR_BAD_REMOVE', 'Could not delete %s');
define('_AM_RSSFIT_ERROR_NO_PLUGIN', 'Could not load plugin');
//1.31
define('_AM_RSSFIT_SUB_URL', 'URL');
