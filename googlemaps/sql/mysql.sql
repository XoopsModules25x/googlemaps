-- --------------------------------------------------------

-- 
-- Table structure for table `xoops_gmap_category`
-- 

CREATE TABLE `gmap_category` (
  `map_id` int(4) NOT NULL auto_increment,
  `name` varchar(50) NOT NULL default '',
  `lon` double NOT NULL default '0',
  `lat` double NOT NULL default '0',
  `zoom` tinyint(2) NOT NULL default '0',
  `active` tinyint(2) NOT NULL default '1',
  `order` tinyint(3) NOT NULL default '1',
  PRIMARY KEY  (`map_id`)
) ENGINE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `xoops_gmap_pl`
-- 

CREATE TABLE `gmap_pl` (
  `id` int(11) NOT NULL auto_increment,
  `map_id` int(4) NOT NULL default '0',
  `point_id1` int(4) NOT NULL default '0',
  `point_id2` int(4) NOT NULL default '0',
  `active` tinyint(2) NOT NULL default '1',
  PRIMARY KEY  (`id`),
  KEY ipoint1 (`point_id1`),
  KEY ipoint2 (`point_id2`)
) ENGINE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `xoops_gmap_points`
-- 

CREATE TABLE `gmap_points` (
  `id` int(4) NOT NULL auto_increment,
  `map_id` tinyint(2) NOT NULL default '1',
  `lat` double NOT NULL default '0',
  `lon` double NOT NULL default '0',
  `title` varchar(100) NOT NULL default '',
  `html` text NOT NULL,
  `zoom` tinyint(2) NOT NULL default '0',
  `submitter` mediumint(8) unsigned NOT NULL default '0',
  `status` tinyint(2) NOT NULL default '0',
  `date` int(12) NOT NULL default '0',
  `order` int(4) NOT NULL default '9',
  PRIMARY KEY  (`id`),
  KEY isubmitter (`submitter`),
  KEY imap_id (`map_id`),
  KEY istatus (`status`)
) ENGINE=MyISAM;

UPDATE `gmap_points` SET `zoom`= 17 - `zoom` WHERE 0<1;
UPDATE `gmap_category` SET `zoom`= 17 - `zoom` WHERE 0<1;