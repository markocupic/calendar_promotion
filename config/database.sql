-- **********************************************************
-- *                                                        *
-- * IMPORTANT NOTE                                         *
-- *                                                        *
-- * Do not import this file manually but use the TYPOlight *
-- * install tool to create and maintain database tables!   *
-- *                                                        *
-- **********************************************************


-- --------------------------------------------------------


-- 
-- Table `tl_event_archive`
-- 

CREATE TABLE `tl_calendar_promotion_archive` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `pid` int(10) NOT NULL default '0',
  `year` int(4) unsigned NOT NULL default '0',
  `tolerance` int(3) unsigned NOT NULL default '0',
  `sorting` int(10) unsigned NOT NULL default '0',
  `tstamp` int(10) unsigned NOT NULL default '0',
  `eventtitle` varchar(64) NOT NULL default '',
  `eventtype` varchar(64) NOT NULL default '',
  `singleSRC` varchar(255) NOT NULL default '',
  `errormessage` text NOT NULL,

  KEY `pid` (`pid`)
   PRIMARY KEY  (`id`),
) ENGINE=MyISAM  CHARSET=utf8;

--
-- Table `tl_calendar_promotion`
--
CREATE TABLE `tl_calendar_promotion` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `pid` int(10) NOT NULL default '0',
  `sorting` int(10) unsigned NOT NULL default '0',
  `tstamp` int(10) unsigned NOT NULL default '0',
  `eventtstamp` int(10) unsigned NOT NULL default '0',
  `href` varchar(64) NOT NULL default '',
  `singleSRC` varchar(255) NOT NULL default '',
  `title` varchar(64) NOT NULL default '',
  `description` text NOT NULL,
  `displayorder` int(10) NOT NULL default '0',
  `mbwidth` int(10) unsigned NOT NULL default '0',
  `mbheight` int(10) unsigned NOT NULL default '0',

   PRIMARY KEY  (`id`),
   KEY `pid` (`pid`)
) ENGINE=MyISAM  CHARSET=utf8;


--
-- Table `tl_content`
--
CREATE TABLE `tl_content` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `calendar_promotion_archive` int(10) NOT NULL default '0',
  `calendar_promotion_template` varchar(255) NOT NULL default '',
   PRIMARY KEY  (`id`),
   KEY `pid` (`pid`)
) ENGINE=MyISAM  CHARSET=utf8;