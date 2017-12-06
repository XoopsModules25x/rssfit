CREATE TABLE `rssfit_misc` (
  `misc_id`       SMALLINT(5) UNSIGNED NOT NULL AUTO_INCREMENT,
  `misc_category` VARCHAR(30)          NOT NULL DEFAULT '',
  `misc_title`    VARCHAR(255)         NOT NULL DEFAULT '',
  `misc_content`  TEXT                 NOT NULL,
  `misc_setting`  TEXT                 NOT NULL,
  PRIMARY KEY (`misc_id`)
)
  ENGINE = MyISAM;

CREATE TABLE `rssfit_plugins` (
  `rssf_conf_id`   INT(5) UNSIGNED NOT NULL AUTO_INCREMENT,
  `rssf_filename`  VARCHAR(50)     NOT NULL DEFAULT '',
  `rssf_activated` TINYINT(1)      NOT NULL DEFAULT '0',
  `rssf_grab`      TINYINT(2)      NOT NULL DEFAULT '0',
  `rssf_order`     TINYINT(2)      NOT NULL DEFAULT '0',
  `subfeed`        TINYINT(1)      NOT NULL DEFAULT '0',
  `sub_entries`    CHAR(2)         NOT NULL DEFAULT '',
  `sub_link`       VARCHAR(255)    NOT NULL DEFAULT '',
  `sub_title`      VARCHAR(255)    NOT NULL DEFAULT '',
  `sub_desc`       VARCHAR(255)    NOT NULL DEFAULT '',
  `img_url`        VARCHAR(255)    NOT NULL DEFAULT '',
  `img_link`       VARCHAR(255)    NOT NULL DEFAULT '',
  `img_title`      VARCHAR(255)    NOT NULL DEFAULT '',
  PRIMARY KEY (`rssf_conf_id`)
)
  ENGINE = MyISAM;
