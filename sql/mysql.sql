CREATE TABLE `rssfit_misc` (
  `misc_id` smallint(5) unsigned NOT NULL auto_increment,
  `misc_category` varchar(30) NOT NULL default '',
  `misc_title` varchar(255) NOT NULL default '',
  `misc_content` text NOT NULL,
  `misc_setting` text NOT NULL,
  PRIMARY KEY  (`misc_id`)
) ENGINE=MyISAM;

CREATE TABLE `rssfit_plugins` (
  `rssf_conf_id` int(5) unsigned NOT NULL auto_increment,
  `rssf_filename` varchar(50) NOT NULL default '',
  `rssf_activated` tinyint(1) NOT NULL default '0',
  `rssf_grab` tinyint(2) NOT NULL default '0',
  `rssf_order` tinyint(2) NOT NULL default '0',
  `subfeed` tinyint(1) NOT NULL default '0',
  `sub_entries` char(2) NOT NULL default '',
  `sub_link` varchar(255) NOT NULL default '',
  `sub_title` varchar(255) NOT NULL default '',
  `sub_desc` varchar(255) NOT NULL default '',
  `img_url` varchar(255) NOT NULL default '',
  `img_link` varchar(255) NOT NULL default '',
  `img_title` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`rssf_conf_id`)
) ENGINE=MyISAM;
